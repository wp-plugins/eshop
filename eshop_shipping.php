<?php
if ('eshop_shipping.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');
     
/*
See eshop.php for information and license terms
*/
if (file_exists(ABSPATH . 'wp-includes/l10n.php')) {
    require_once(ABSPATH . 'wp-includes/l10n.php');
}
else {
    require_once(ABSPATH . 'wp-includes/wp-l10n.php');
}
global $wpdb;
//had to recreate these 2 functions here - the include didn't work!
//only use after magic quote check
if (!function_exists('stripslashes_this')) {
	function stripslashes_this($array) {
		return is_array($array) ? array_map('stripslashes_this', $array) : stripslashes($array);
	}
}
//sanitises input array!
if (!function_exists('sanitise_this')) {
	function sanitise_this($array) {
		return is_array($array) ? array_map('sanitise_this', $array) : wp_specialchars($array,ENT_QUOTES);
	}
}
if (get_magic_quotes_gpc()) {
    $_COOKIE = stripslashes_this($_COOKIE);
    $_FILES = stripslashes_this($_FILES);
    $_GET = stripslashes_this($_GET);
    $_POST = stripslashes_this($_POST);
    $_REQUEST = stripslashes_this($_REQUEST);
}

if (isset($_GET['action']) )
	$action_status = attribute_escape($_GET['action']);
else
	$_GET['action']=$action_status = 'shipping';

$echosub= '<ul class="subsubsub">';
$stati=array('shipping'=>'Shipping Rates','countries' => 'Countries','states'=>'US States');
foreach ( $stati as $status => $label ) {
	$class = '';
	if ( $status == $action_status )
		$class = ' class="current"';

	$status_links[] = "<li><a href=\"?page=eshop_shipping.php&amp;action=$status\"$class>" . $label . '</a>';
}
$echosub.= implode(' | </li>', $status_links) . '</li>';
$echosub.= '</ul>';



switch ($_GET['action']){
case ('countries'):
	$dtable=$wpdb->prefix.'eshop_countries';
	$error='';
	if(isset($_POST['submit'])){
		//sanitise for display purposes.
		$_POST['code']=sanitise_this($_POST['code']);
		$_POST['country']=sanitise_this($_POST['country']);
		$_POST['zone']=sanitise_this($_POST['zone']);

		//warning this truncates the table and then recreates it
		$query=$wpdb->query("TRUNCATE TABLE $dtable");
		//create the query
		$build="INSERT INTO $dtable (`code`,`country`,`zone`) VALUES";
		$count=count($_POST['code']);
		for($i=0;$i<=$count-1;$i++){
			//so if none of them are empty
			if(($_POST['code'][$i]!='' && $_POST['country'][$i]!='' && $_POST['zone'][$i]!='') && !isset($_POST['delete'][$i])){
			//complicated error checking - cannot check state name so easily
				if(!preg_match("/[A-Z]/", $_POST['code'][$i])){
					$error.="<li>Code:".$_POST['code'][$i]." is not valid. State:".$_POST['country'][$i].",Zone:".$_POST['zone'][$i]."</li>\n";
				}elseif(!preg_match("/[0-9]/", $_POST['zone'][$i]) || strlen($_POST['zone'][$i])!='1'){
					$error.="<li>Zone:".$_POST['zone'][$i]." is not valid. Code:".$_POST['code'][$i].", State:".$_POST['country'][$i]."</li>\n";
				}else{
					//all must be ok
					$build.=" ('".$wpdb->escape($_POST['code'][$i])."','".$wpdb->escape($_POST['country'][$i])."','".$wpdb->escape($_POST['zone'][$i])."'),";
				}
			}elseif($_POST['code'][$i]=='' && $_POST['country'][$i]=='' && $_POST['zone'][$i]==''){
				//ie no new state added
				//had to put this line here as I don't know where else it should go!
				//it hides the additional input if it wasn't used.
			}elseif(!isset($_POST['delete'][$i])){
				//if not set for deletion then there was an error
				$error.="<li>Code:".$_POST['code'][$i].", Country:".$_POST['country'][$i].", Zone:".$_POST['zone'][$i]."</li>\n";
			}
		}
		$build=trim($build,",");
		//check to stop someone being dumb enough to try and delete all the countries
		if($count>1){
			$query=$wpdb->query($build);
		}else{
			$error='<li>You cannot delete all the Countries!</li>'."\n";
		}
	}
	//each time re-request from the database
	$query=$wpdb->get_results("SELECT * from $dtable ORDER BY country");
	if($error!=''){
		echo'<div id="message" class="error fade"><p><strong>Error</strong> the following were not valid:<ul>'.$error.'</ul></div>'."\n";
	}elseif(isset($_POST['submit'])){
		echo'<div id="message" class="updated fade"><p>Country Shipping Zones changed successfully.</p></div>'."\n";
	}
	?>
	<div class="wrap">
	<h2>Country Shipping Zones</h2>
	<?php echo $echosub; ?>
	<p>&#8220;Code&#8221; is the 2 letter state abbreviation, followed by &#8220;Country Name,&#8221; then the shipping &#8220;Zone&#8221; (use 1-5).</p>

	<div id="eshopformfloat">
	<form id="filterzones" action="" method="post">
	<fieldset><legend>Filter</legend>
	<label for="filter">Zone</label><select id="filter" name="filter">
	<?php
	for($x=0;$x<=5;$x++){
		if(!isset($_POST['filter'])){
			$_POST['filter']=0;
		}
		$text=$x;
		if($x==0){$text='All';}
		if($_POST['filter']==$x){
			$add=' selected="selected"';
		}else{
			$add='';
		}
		echo '<option value="'.$x.'"'.$add.'>'.$text.'</option>';
	}
	?>
	</select>
	<p class="submit"><input type="submit" id="submitfilter" name="submitfilter" value="Submit" /></p>
	</fieldset>
	</form>
	</div>

	<form id="zoneform" action="" method="post">
	<fieldset><legend>Shipping Zones</legend>
	<table class="hidealllabels" summary="Countries, with their 2 letter code, and applicable zone">
	<caption>Countries</caption>

	<thead>
	<tr>
	<th id="code">Code</th>
	<th id="country">Country</th>
	<th id="zone">Zone</th>
	<th id="delete">Delete</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$x=0;
	$hidden='';
	foreach ($query as $row){
		if((isset($_POST['filter']) && $_POST['filter']== $row->zone) || 
		isset($_POST['filter']) && $_POST['filter']==0){
			echo '<tr>';
			echo '<td headers="code" id="headcode'.$x.'"><label for="code'.$x.'">Code</label><input id="code'.$x.'" name="code[]" type="text" value="'.$row->code.'" size="2" maxlength="2" /></td>'."\n";
			echo '<td headers="country headcode'.$x.'"><label for="country'.$x.'">Country name</label><input id="country'.$x.'" name="country[]" type="text" value="'.$row->country.'" size="30" maxlength="50" /></td>'."\n";
			echo '<td headers="zone headcode'.$x.'"><label for="zone'.$x.'">Zone</label><input id="zone'.$x.'" name="zone[]" type="text" value="'.$row->zone.'" size="2" maxlength="1" /></td>'."\n";
			echo '<td headers="delete headcode'.$x.'"><label for="delete'.$x.'">Delete</label><input id="delete'.$x.'" name="delete['.$x.']" type="checkbox" value="delete" /></td>'."\n";
			echo '</tr>'."\n";
		}else{
			$hidden.='
			<input id="code'.$x.'" name="code[]" type="hidden" value="'.$row->code.'" />
			<input id="country'.$x.'" name="country[]" type="hidden" value="'.$row->country.'" />
			<input id="zone'.$x.'" name="zone[]" type="hidden" value="'.$row->zone.'" />
			';
		}
		$x++;
	}
	echo '<tr>';
	echo '<td headers="code" id="headcode'.$x.'"><label for="code'.$x.'">Code</label><input id="code'.$x.'" name="code[]" type="text" value="" size="2" maxlength="2" /></td>'."\n";
	echo '<td headers="country headcode'.$x.'"><label for="country'.$x.'">Country name</label><input id="country'.$x.'" name="country[]" type="text" value="" size="30" maxlength="50" /></td>'."\n";
	echo '<td headers="zone headcode'.$x.'"><label for="zone'.$x.'">Zone</label><input id="zone'.$x.'" name="zone[]" type="text" value="" size="2" maxlength="1" /></td>'."\n";
	echo '<td>&nbsp;</td>';
	echo '</tr>'."\n";
	?>
	</tbody>
	</table>
	<?php
	echo $hidden;
	?>
	<p class="submit eshop"><input type="submit" name="submit" id="submit" value="Update Shipping Zones" /></p>
	</fieldset>
	</form>

	</div>
	<?php
		break;
case ('states'):
	$dtable=$wpdb->prefix.'eshop_states';
	$error='';
	if(isset($_POST['submit'])){
		//sanitise for display purposes.
		$_POST['code']=sanitise_this($_POST['code']);
		$_POST['stateName']=sanitise_this($_POST['stateName']);
		$_POST['zone']=sanitise_this($_POST['zone']);

		//warning this truncates the table and then recreates it
		$query=$wpdb->query("TRUNCATE TABLE $dtable");
		//create the query
		$build="INSERT INTO $dtable (`code`,`stateName`,`zone`) VALUES";
		$count=count($_POST['code']);
		for($i=0;$i<=$count-1;$i++){
			//so if none of them are empty
			if(($_POST['code'][$i]!='' && $_POST['stateName'][$i]!='' && $_POST['zone'][$i]!='') && !isset($_POST['delete'][$i])){
			//complicated error checking - cannot check state name so easily
				if(!preg_match("/[A-Z]/", $_POST['code'][$i])){
					$error.="<li>Code:".$_POST['code'][$i]." is not valid. State:".$_POST['stateName'][$i].",Zone:".$_POST['zone'][$i]."</li>\n";
				}elseif(!preg_match("/[0-9]/", $_POST['zone'][$i]) || strlen($_POST['zone'][$i])!='1'){
					$error.="<li>Zone:".$_POST['zone'][$i]." is not valid. Code:".$_POST['code'][$i].", State:".$_POST['stateName'][$i]."</li>\n";
				}else{
					//all must be ok
					$build.=" ('".$wpdb->escape($_POST['code'][$i])."','".$wpdb->escape($_POST['stateName'][$i])."','".$wpdb->escape($_POST['zone'][$i])."'),";
				}
			}elseif($_POST['code'][$i]=='' && $_POST['stateName'][$i]=='' && $_POST['zone'][$i]==''){
				//ie no new state added
				//had to put this line here as I don't know where else it should go!
				//it hides the additional input if it wasn't used.
			}elseif(!isset($_POST['delete'][$i])){
				//if not set for deletion then there was an error
				$error.="<li>Code:".$_POST['code'][$i].", State:".$_POST['stateName'][$i].", Zone:".$_POST['zone'][$i]."</li>\n";
			}
		}
		$build=trim($build,",");
		//check to stop someone being dumb enough to try and delete all the states
		if($count>1){
			$query=$wpdb->query($build);
		}else{
			$error='<li>You cannot delete all the States!</li>'."\n";
		}
	}
	if($error!=''){
		echo'<div id="message" class="error fade"><p><strong>Error</strong> the following were not valid:<ul>'.$error.'</ul></div>'."\n";
	}elseif(isset($_POST['submit'])){
		echo'<div id="message" class="updated fade"><p>US State Shipping Zones changed successfully.</p></div>'."\n";
	}
	//each time re-request from the database
	$query=$wpdb->get_results("SELECT * from $dtable ORDER BY stateName");
	?>
	<div class="wrap">
	<h2>US State Shipping Zones</h2>
	<?php echo $echosub; ?>
	<p>&#8220;Code&#8221; is the 2 letter state abbreviation, followed by &#8220;State Name,&#8221; then the shipping &#8220;Zone&#8221; (use 1-5).</p>

	<form id="zoneform" action="" method="post">
	<fieldset><legend>Shipping Zones</legend>

	<table class="hidealllabels" summary="US States, with their 2 letter code, and applicable zone">
	<caption><abbr title="United States">US</abbr> States</caption>
	<thead>
	<tr>
	<th id="code">Code</th>
	<th id="statename">State Name</th>
	<th id="zone">Zone</th>
	<th id="delete">Delete</th>
	</tr>
	</thead>
	<tbody>
	<?php
	$x=0;
	foreach ($query as $row){
	echo '<tr>';
	echo '<td headers="code" id="headcode'.$x.'"><label for="code'.$x.'">Code</label><input id="code'.$x.'" name="code[]" type="text" value="'.$row->code.'" size="2" maxlength="2" /></td>'."\n";
	echo '<td headers="statename headcode'.$x.'"><label for="state'.$x.'">Statename</label><input id="state'.$x.'" name="stateName[]" type="text" value="'.$row->stateName.'" size="30" maxlength="50" /></td>'."\n";
	echo '<td headers="zone headcode'.$x.'"><label for="zone'.$x.'">Zone</label><input id="zone'.$x.'" name="zone[]" type="text" value="'.$row->zone.'" size="2" maxlength="1" /></td>'."\n";
	echo '<td headers="delete headcode'.$x.'"><label for="delete'.$x.'">Delete</label><input id="delete'.$x.'" name="delete['.$x.']" type="checkbox" value="delete" /></td>'."\n";
	echo '</tr>'."\n";
	$x++;
	}
	echo '<tr>';
	echo '<td headers="code" id="headcode'.$x.'"><label for="code'.$x.'">Code</label><input id="code'.$x.'" name="code[]" type="text" value="" size="2" maxlength="2" /></td>'."\n";
	echo '<td headers="statename headcode'.$x.'"><label for="state'.$x.'">Statename</label><input id="state'.$x.'" name="stateName[]" type="text" value="" size="30" maxlength="50" /></td>'."\n";
	echo '<td headers="zone headcode'.$x.'"><label for="zone'.$x.'">Zone</label><input id="zone'.$x.'" name="zone[]" type="text" value="" size="2" maxlength="1" /></td>'."\n";
	echo '<td>&nbsp;</td>';
	echo '</tr>'."\n";

	?>
	</tbody>
	</table>

	<p class="submit eshop"><input type="submit" name="submit" id="submit" value="Update Shipping Zones" /></p>
	</fieldset>
	</form>

	</div>
	<?php
	break;
case ('shipping'):
default:
	$dtable=$wpdb->prefix.'eshop_shipping_rates';
	$error='';

	if(isset($_POST['shipmethod'])){
		update_option('eshop_shipping',$wpdb->escape($_POST['eshop_shipping']));
		update_option('eshop_shipping_zone',$wpdb->escape($_POST['eshop_shipping_zone']));
		update_option('eshop_show_zones',$wpdb->escape($_POST['eshop_show_zones']));
	}
	if(isset($_POST['submit'])){
		foreach($_POST as $k=>$v){
			$class=substr($k,0,1);
			$items=substr($k,1,1);
			$zone=substr($k,2);
			$zonenum=substr($k,-1);

			if(!is_numeric($v) && ($k!='submit'&& $k!='eshop_shipping')){
				$error.='<li>Class '.$class.': Zone '.$zonenum.'</li>'."\n";
			}elseif($k!='submit' && $k!='eshop_shipping'){
				$query=$wpdb->query("UPDATE $dtable set $zone='$v' where class='$class' and items='$items'");
			//echo "<p>UPDATE $dtable set $zone='$v' where class='$class' and items='$items'</p>";
			}
		}
	}
	if($error!=''){
		echo'<div id="message" class="error fade"><p><strong>Error</strong> the following were not valid amounts:</p><ul>'.$error.'</ul></div>'."\n";
	}elseif(isset($_POST['shipmethod'])||isset($_POST['submit'])){
		echo'<div id="message" class="updated fade"><p>Shipping Rates changed successfully.</p></div>'."\n";
	}
	echo '<div class="wrap">';
	echo '<h2>Shipping Rates</h2>'."\n";
	?>
	<?php echo $echosub; ?>
	<p>The following are the shipping rates by class and zone.</p>
	<p><strong>Warning:</strong> changing which method you use <em>may</em> affect the full table
	of shipping rates. To change from one method to another, first <em>update</em>
	your choice, and the relevant table will then appear.</p>
	<form id="shipformmethod" action="" method="post">
	<fieldset><legend>Shipping rate calculation</legend>
	<?php
	for($i=1;$i<=3;$i++){
		$selected='';
		if($i == get_option('eshop_shipping')){$selected=' checked="checked"';}
		if($i==1){
			$extra=' <small>( per quantity of 1, prices reduced for additional items )</small>';
		}elseif($i==2){
			$extra=' <small>( once per shipping class no matter what quantity is ordered )</small>';
		}elseif($i==3){
			$extra=' <small>( one overall charge no matter what quantity is ordered )</small>';
		}	
		echo '<input type="radio" name="eshop_shipping" id="eshop_shipping'.$i.'" value="'.$i.'" '.$selected.'/><label for="eshop_shipping'.$i.'">Method '.$i.$extra.'</label><br />';
	}
	?>
	<label for="eshop_shipping_zone">Shipping Zones by</label>
	<select id="eshop_shipping_zone" name="eshop_shipping_zone">
	<?php
	if('country' == get_option('eshop_shipping_zone')){
		echo '<option value="country" selected="selected">Country</option>';
		echo '<option value="state">US State</option>';
	}else{
		echo '<option value="country">Country</option>';
		echo '<option value="state" selected="selected">US State</option>';
	}
	?>
	</select><br />
	<label for="eshop_show_zones">Show Shipping Zones on Shipping Page</label>
	<select id="eshop_show_zones" name="eshop_show_zones">
	<?php
	if('yes' == get_option('eshop_show_zones')){
		echo '<option value="yes" selected="selected">Yes</option>';
		echo '<option value="no">No</option>';
	}else{
		echo '<option value="yes">Yes</option>';
		echo '<option value="no" selected="selected">No</option>';
	}
	?>
	</select><br />
	<p class="submit eshop"><input type="submit" name="shipmethod" id="submitit" value="Update Shipping rate calculation" /></p>

	</fieldset>
	</form>
	<form id="shipform" action="" method="post">
	<fieldset><legend><span class="offset">Shipping Classes and Zones</span></legend>
	<table class="hidealllabels" summary="Shipping rates">
	<caption>Shipping rates by class and zone</caption>
	<tr>
	<th id="class">Class</th>
	<th id="zone1">Zone 1</th>
	<th id="zone2">Zone 2</th>
	<th id="zone3">Zone 3</th>
	<th id="zone4">Zone 4</th>
	<th id="zone5">Zone 5</th>
	</tr>
	<?php
	/* although this could be condensed, I'll split each method up for ease and future expansion */
	switch (get_option('eshop_shipping')){
		case '1':// ( per quantity of 1, prices reduced for additional items )
			$x=1;
			$calt=0;
			$query=$wpdb->get_results("SELECT * from $dtable ORDER BY class ASC, items ASC");

			foreach ($query as $row){
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alt"';
				echo '<tr'.$alt.'>';
				if($row->items==1){
					echo '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>(First Item)</small></th>'."\n";
				}else{
					echo '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>(Additional Items)</small></th>'."\n";
				}
				echo '<td headers="zone1 cname'.$x.'"><label for="'.$row->class.$row->items.'zone1">Zone 1 class '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone1" name="'.$row->class.$row->items.'zone1" type="text" value="'.$row->zone1.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone2 cname'.$x.'"><label for="'.$row->class.$row->items.'zone2">Zone 2 class '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone2" name="'.$row->class.$row->items.'zone2" type="text" value="'.$row->zone2.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone3 cname'.$x.'"><label for="'.$row->class.$row->items.'zone3">Zone 3 class '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone3" name="'.$row->class.$row->items.'zone3" type="text" value="'.$row->zone3.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone4 cname'.$x.'"><label for="'.$row->class.$row->items.'zone4">Zone 4 class '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone4" name="'.$row->class.$row->items.'zone4" type="text" value="'.$row->zone4.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone5 cname'.$x.'"><label for="'.$row->class.$row->items.'zone5">Zone 5 class '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone5" name="'.$row->class.$row->items.'zone5" type="text" value="'.$row->zone5.'" size="6" maxlength="6" /></td>'."\n";
				echo '</tr>';
				$x++;
			}
			break;
		case '2'://( once per shipping class no matter what quantity is ordered )
			$x=1;
			$calt=0;
			$query=$wpdb->get_results("SELECT * from $dtable where items='1' ORDER BY 'class'  ASC");
			foreach ($query as $row){
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alt"';
				echo '<tr'.$alt.'>';
				echo '<th id="cname'.$x.'" headers="class">'.$row->class.'</th>'."\n";
				echo '<td headers="zone1 cname'.$x.'"><label for="'.$row->class.$row->items.'zone1">Zone 1 class '.$row->class.'</label><input id="'.$row->class.$row->items.'zone1" name="'.$row->class.$row->items.'zone1" type="text" value="'.$row->zone1.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone2 cname'.$x.'"><label for="'.$row->class.$row->items.'zone2">Zone 2 class '.$row->class.'</label><input id="'.$row->class.$row->items.'zone2" name="'.$row->class.$row->items.'zone2" type="text" value="'.$row->zone2.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone3 cname'.$x.'"><label for="'.$row->class.$row->items.'zone3">Zone 3 class '.$row->class.'</label><input id="'.$row->class.$row->items.'zone3" name="'.$row->class.$row->items.'zone3" type="text" value="'.$row->zone3.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone4 cname'.$x.'"><label for="'.$row->class.$row->items.'zone4">Zone 4 class '.$row->class.'</label><input id="'.$row->class.$row->items.'zone4" name="'.$row->class.$row->items.'zone4" type="text" value="'.$row->zone4.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone5 cname'.$x.'"><label for="'.$row->class.$row->items.'zone5">Zone 5 class '.$row->class.'</label><input id="'.$row->class.$row->items.'zone5" name="'.$row->class.$row->items.'zone5" type="text" value="'.$row->zone5.'" size="6" maxlength="6" /></td>'."\n";
				echo '</tr>';
				$x++;
			}
			break;
		case '3'://( one overall charge no matter how many are ordered )
			$x=1;
			$query=$wpdb->get_results("SELECT * from $dtable where items='1' and class='A' ORDER BY 'class'  ASC");

			foreach ($query as $row){
				echo '<tr'.$alt.'>';
				echo '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>(Overall charge)</small></th>'."\n";
				echo '<td headers="zone1 cname'.$x.'"><label for="'.$row->class.$row->items.'zone1">Zone 1</label><input id="'.$row->class.$row->items.'zone1" name="'.$row->class.$row->items.'zone1" type="text" value="'.$row->zone1.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone2 cname'.$x.'"><label for="'.$row->class.$row->items.'zone2">Zone 2</label><input id="'.$row->class.$row->items.'zone2" name="'.$row->class.$row->items.'zone2" type="text" value="'.$row->zone2.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone3 cname'.$x.'"><label for="'.$row->class.$row->items.'zone3">Zone 3</label><input id="'.$row->class.$row->items.'zone3" name="'.$row->class.$row->items.'zone3" type="text" value="'.$row->zone3.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone4 cname'.$x.'"><label for="'.$row->class.$row->items.'zone4">Zone 4</label><input id="'.$row->class.$row->items.'zone4" name="'.$row->class.$row->items.'zone4" type="text" value="'.$row->zone4.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone5 cname'.$x.'"><label for="'.$row->class.$row->items.'zone5">Zone 5</label><input id="'.$row->class.$row->items.'zone5" name="'.$row->class.$row->items.'zone5" type="text" value="'.$row->zone5.'" size="6" maxlength="6" /></td>'."\n";
				echo '</tr>';
			}
			break;
	}
	?>
	</table>
	<p class="submit eshop"><input type="submit" name="submit" id="submit" value="Update Shipping Rates" /></p>
	</fieldset>
	</form>
	</div>
<?php
	break;
}
?>
<?php eshop_show_credits(); ?>