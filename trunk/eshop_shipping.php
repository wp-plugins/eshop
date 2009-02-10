<?php
if ('eshop_shipping.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');
     
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


// for what was the US state list - ensures the menu is changed 
$dtable=$wpdb->prefix.'eshop_states';

if(isset($_POST['submitstate'])){
	update_option('eshop_shipping_state',$wpdb->escape($_POST['eshop_shipping_state']));
}

$echosub= '<ul class="subsubsub">';
$stati=array('shipping'=>__('Shipping Rates','eshop'),'countries' => __('Countries','eshop'),'states'=>get_option('eshop_shipping_state').' '.__('State/County/Province','eshop'));
foreach ( $stati as $status => $label ) {
	$class = '';
	if ( $status == $action_status )
		$class = ' class="current"';

	$status_links[] = "<li><a href=\"?page=eshop_shipping.php&amp;action=$status\"$class>" . $label . '</a>';
}
$echosub.= implode(' | </li>', $status_links) . '</li>';
$echosub.= '</ul><br class="clear" />';



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
		$build="INSERT INTO $dtable (`code`,`country`,`zone`,`list`) VALUES";
		$count=count($_POST['code']);
		for($i=0;$i<=$count-1;$i++){
			//so if none of them are empty
			if(($_POST['code'][$i]!='' && $_POST['country'][$i]!='' && $_POST['zone'][$i]!='') && !isset($_POST['delete'][$i])){
			//complicated error checking - cannot check state name so easily
				if(isset($_POST['list'][$i]))
					$list[$i]='0';
				else
					$list[$i]='1';

				if(!preg_match("/[A-Z]/", $_POST['code'][$i])){
					$error.="<li>".__('Code:','eshop').$_POST['code'][$i]." ".__('is not valid.','eshop')." ".__('State:','eshop').$_POST['country'][$i].",".__('Zone:','eshop').$_POST['zone'][$i]."</li>\n";
				}elseif(!preg_match("/[0-9]/", $_POST['zone'][$i]) || strlen($_POST['zone'][$i])!='1'){
					$error.="<li>".__('Zone:','eshop').$_POST['zone'][$i]." ".__('is not valid.','eshop')." ".__('Code:','eshop').$_POST['code'][$i].", ".__('State:','eshop').$_POST['country'][$i]."</li>\n";
				}else{
					//all must be ok
					$build.=" ('".$wpdb->escape($_POST['code'][$i])."','".$wpdb->escape($_POST['country'][$i])."','".$wpdb->escape($_POST['zone'][$i])."','".$wpdb->escape($list[$i])."'),";
				}
			}elseif($_POST['code'][$i]=='' && $_POST['country'][$i]=='' && $_POST['zone'][$i]==''){
				//ie no new state added
				//had to put this line here as I don't know where else it should go!
				//it hides the additional input if it wasn't used.
			}elseif(!isset($_POST['delete'][$i])){
				//if not set for deletion then there was an error
				$error.="<li>".__('Code:','eshop').$_POST['code'][$i].", ".__('Country:','eshop').$_POST['country'][$i].", ".__('Zone:','eshop').$_POST['zone'][$i]."</li>\n";
			}
		}
		$build=trim($build,",");
		//check to stop someone being dumb enough to try and delete all the countries
		if($count>1){
			$query=$wpdb->query($build);
		}else{
			$error='<li>'.__('You cannot delete all the Countries!','eshop').'</li>'."\n";
		}
	}
	//each time re-request from the database
	$query=$wpdb->get_results("SELECT * from $dtable GROUP BY list,country");
	if($error!=''){
		echo'<div id="message" class="error fade"><p>'.__('<strong>Error</strong> the following were not valid:','eshop').'<ul>'.$error.'</ul></div>'."\n";
	}elseif(isset($_POST['submit'])){
		echo'<div id="message" class="updated fade"><p>'.__('Country Shipping Zones changed successfully.','eshop').'</p></div>'."\n";
	}
	?>
	<div class="wrap">
	<h2><?php _e('Country Shipping Zones','eshop'); ?></h2>
	<?php echo $echosub; ?>
	<p><?php _e('&#8220;Code&#8221; is the 2 letter state abbreviation, followed by &#8220;Country Name,&#8221; then the shipping &#8220;Zone&#8221; (use 1-5).','eshop'); ?></p>
	<p><?php _e('&#8220;List&#8221; promotes that country to appear at the top of the list.','eshop'); ?></p>

	<div id="eshopformfloat">
	<form id="filterzones" action="" method="post">
	<fieldset><legend><?php _e('Filter','eshop'); ?></legend>
	<label for="filter"><?php _e('Zone','eshop'); ?></label><select id="filter" name="filter">
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
	<p class="submit"><input type="submit" id="submitfilter" name="submitfilter" value="<?php _e('Submit','eshop'); ?>" /></p>
	</fieldset>
	</form>
	</div>

	<form id="zoneform" action="" method="post">
	<fieldset><legend><?php _e('Shipping Zones','eshop'); ?></legend>
	<table class="hidealllabels" summary="<?php _e('Countries, with their 2 letter code, and applicable zone','eshop'); ?>">
	<caption><?php _e('Countries','eshop'); ?></caption>

	<thead>
	<tr>
	<th id="code"><?php _e('Code','eshop'); ?></th>
	<th id="country"><?php _e('Country','eshop'); ?></th>
	<th id="zone"><?php _e('Zone','eshop'); ?></th>
		<th id="list"><?php _e('List','eshop'); ?></th>
	<th id="delete"><?php _e('Delete','eshop'); ?></th>
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
			echo '<td headers="code" id="headcode'.$x.'"><label for="code'.$x.'">'.__('Code','eshop').'</label><input id="code'.$x.'" name="code[]" type="text" value="'.$row->code.'" size="2" maxlength="2" /></td>'."\n";
			echo '<td headers="country headcode'.$x.'"><label for="country'.$x.'">'.__('Country name','eshop').'</label><input id="country'.$x.'" name="country[]" type="text" value="'.$row->country.'" size="30" maxlength="50" /></td>'."\n";
			echo '<td headers="zone headcode'.$x.'"><label for="zone'.$x.'">'.__('Zone','eshop').'</label><input id="zone'.$x.'" name="zone[]" type="text" value="'.$row->zone.'" size="2" maxlength="1" /></td>'."\n";
				if($row->list == '0') $sel='checked="checked" '; else $sel ='';
				echo '<td headers="list headcode'.$x.'"><label for="list'.$x.'">'.__('List','eshop').'</label><input id="list'.$x.'" name="list['.$x.']" type="checkbox" value="0" '.$sel.'/></td>'."\n";
			echo '<td headers="delete headcode'.$x.'"><label for="delete'.$x.'">'.__('Delete','eshop').'</label><input id="delete'.$x.'" name="delete['.$x.']" type="checkbox" value="delete" /></td>'."\n";
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
	echo '<td headers="code" id="headcode'.$x.'"><label for="code'.$x.'">'.__('Code','eshop').'</label><input id="code'.$x.'" name="code[]" type="text" value="" size="2" maxlength="2" /></td>'."\n";
	echo '<td headers="country headcode'.$x.'"><label for="country'.$x.'">'.__('Country name','eshop').'</label><input id="country'.$x.'" name="country[]" type="text" value="" size="30" maxlength="50" /></td>'."\n";
	echo '<td headers="zone headcode'.$x.'"><label for="zone'.$x.'">'.__('Zone','eshop').'</label><input id="zone'.$x.'" name="zone[]" type="text" value="" size="2" maxlength="1" /></td>'."\n";
	echo '<td>&nbsp;</td>';
	echo '</tr>'."\n";
	?>
	</tbody>
	</table>
	<?php
	echo $hidden;
	?>
	<p class="submit eshop"><input type="submit" name="submit" class="button-primary" id="submit" value="<?php _e('Update Shipping Zones','eshop'); ?>" /></p>
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

		$build="UPDATE $dtable SET ";
		$i=0;
		foreach($_POST['id'] as $id){
			//so if none of them are empty
			if(isset($_POST['delete'][$id])){
				$wpdb->query("DELETE from $dtable WHERE id='".$_POST['delete'][$id]."' limit 1");
			}elseif($id=='0' && $_POST['code'][$i]!='' && $_POST['stateName'][$i]!='' && $_POST['zone'][$i]!=''){
				if(!preg_match("/[a-zA-Z]/", $_POST['code'][$i])){
					$error.="<li>".__('Code:','eshop').$_POST['code'][$i]." ".__('is not valid.','eshop')." ".__('State:','eshop').$_POST['stateName'][$i].",".__('Zone:','eshop').$_POST['zone'][$i]."</li>\n";
				}elseif(!preg_match("/[0-9]/", $_POST['zone'][$i]) || strlen($_POST['zone'][$i])!='1'){
					$error.="<li>".__('Zone:','eshop').$_POST['zone'][$i]." ".__('is not valid.','eshop')." ".__('Code:','eshop').$_POST['code'][$i].", ".__('State:','eshop').$_POST['stateName'][$i]."</li>\n";
				}else{
					//all must be ok
					$buildit="INSERT INTO $dtable (code,stateName,zone,list) VALUES ('".$wpdb->escape($_POST['code'][$i])."','".$wpdb->escape($_POST['stateName'][$i])."','".$wpdb->escape($_POST['zone'][$i])."','".get_option('eshop_shipping_state')."')";
					$wpdb->query($buildit);
				}
			}elseif($_POST['code'][$i]!='' && $_POST['stateName'][$i]!='' && $_POST['zone'][$i]!='' && !isset($_POST['delete'][$i])){
			//complicated error checking - cannot check state name so easily
				if(!preg_match("/[A-Z]/", $_POST['code'][$i])){
					$error.="<li>".__('Code:','eshop').$_POST['code'][$i]." ".__('is not valid.','eshop')." ".__('State:','eshop').$_POST['stateName'][$i].",".__('Zone:','eshop').$_POST['zone'][$i]."</li>\n";
				}elseif(!preg_match("/[0-9]/", $_POST['zone'][$i]) || strlen($_POST['zone'][$i])!='1'){
					$error.="<li>".__('Zone:','eshop').$_POST['zone'][$i]." ".__('is not valid.','eshop')." ".__('Code:','eshop').$_POST['code'][$i].", ".__('State:','eshop').$_POST['stateName'][$i]."</li>\n";
				}else{
					//all must be ok
					$buildit=$build." code='".$wpdb->escape($_POST['code'][$i])."',stateName='".$wpdb->escape($_POST['stateName'][$i])."',zone='".$wpdb->escape($_POST['zone'][$i])."' where id='$id'";
					$wpdb->query($buildit);
				}
			}elseif($_POST['code'][$i]=='' && $_POST['stateName'][$i]=='' && $_POST['zone'][$i]==''){
				//ie no new state added
				//had to put this line here as I don't know where else it should go!
				//it hides the additional input if it wasn't used.
			}elseif(!isset($_POST['delete'][$i])){
				//if not set for deletion then there was an error
				$error.="<li>".__('Code:','eshop').$_POST['code'][$i].", ".__('State:','eshop').$_POST['stateName'][$i].", ".__('Zone:','eshop').$_POST['zone'][$i]."</li>\n";
			}
			$i++;
		}
	}
	if($error!=''){
		echo'<div id="message" class="error fade"><p>'.__('<strong>Error</strong> the following were not valid:','eshop').'<ul>'.$error.'</ul></div>'."\n";
	}elseif(isset($_POST['submit'])){
		echo'<div id="message" class="updated fade"><p>'.get_option('eshop_shipping_state').' '.__('Specific Shipping Zones changed successfully','eshop').'.</p></div>'."\n";
	}
	//each time re-request from the database
	$getstate=get_option('eshop_shipping_state');

	$query=$wpdb->get_results("SELECT * from $dtable WHERE list='$getstate' ORDER BY stateName");
	?>
	<div class="wrap">
	<h2><?php echo get_option('eshop_shipping_state').' '.__('State/County/Province Shipping Zones','eshop'); ?></h2>
	<?php echo $echosub; ?>
	<p><?php _e('&#8220;Code&#8221; is the 4 letter(maximum usual is 2) abbreviation and must be unique, followed by &#8220;Name&#8221;, then the shipping &#8220;Zone&#8221; (use 1-5).','eshop'); ?></p>
	<p><?php _e('Example: AZ, Arizona,4','eshop'); ?></p>
	<div id="eshopformfloat">
	<form id="eshop_shipping_state_form" action="" method="post">
	<fieldset><legend><?php _e('Set State/County/Province','eshop'); ?></legend>
	<label for="eshop_shipping_state"><?php _e('List','eshop'); ?></label>
	<select id="eshop_shipping_state" name="eshop_shipping_state">
	<?php
		$ctable=$wpdb->prefix.'eshop_countries';
		$currentlocations=$wpdb->get_results("SELECT * from $ctable ORDER BY country");
		foreach ($currentlocations as $row){
			if($row->code == get_option('eshop_shipping_state')){
				$sel=' selected="selected"';
			}else{
				$sel='';
			}
			echo '<option value="'. $row->code .'"'. $sel .'>'. $row->country .'</option>';
		}
		?>
	</select><br />
	<p class="submit"><input type="submit" id="submitstate" name="submitstate" value="<?php _e('Submit','eshop'); ?>" /></p>
	</fieldset>
	</form>
	</div>

	<form id="zoneform" action="" method="post">
	<fieldset><legend><?php _e('Shipping Zones','eshop'); ?></legend>

	<table class="hidealllabels" summary="<?php _e('States, with their 2 letter code, and applicable zone','eshop'); ?>">
	<caption><?php _e('<abbr title="United States">US</abbr> States','eshop'); ?></caption>
	<thead>
	<tr>
	<th id="code"><?php _e('Code','eshop'); ?></th>
	<th id="statename"><?php _e('Name','eshop'); ?></th>
	<th id="zone"><?php _e('Zone','eshop'); ?></th>
	<th id="delete"><?php _e('Delete','eshop'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	
	foreach ($query as $row){
	$x=$row->id;
	echo '<tr>';
	echo '<td headers="code" id="headcode'.$x.'"><label for="code'.$x.'">'.__('Code','eshop').'</label><input id="code'.$x.'" name="code[]" type="text" value="'.$row->code.'" size="4" maxlength="4" /><input id="id'.$x.'" name="id[]" type="hidden" value="'.$row->id.'" /></td>'."\n";
	echo '<td headers="statename headcode'.$x.'"><label for="state'.$x.'">'.__('Statename','eshop').'</label><input id="state'.$x.'" name="stateName[]" type="text" value="'.$row->stateName.'" size="30" maxlength="50" /></td>'."\n";
	echo '<td headers="zone headcode'.$x.'"><label for="zone'.$x.'">'.__('Zone','eshop').'</label><input id="zone'.$x.'" name="zone[]" type="text" value="'.$row->zone.'" size="2" maxlength="1" /></td>'."\n";
	echo '<td headers="delete headcode'.$x.'"><label for="delete'.$x.'">'.__('Delete','eshop').'</label><input id="delete'.$x.'" name="delete['.$x.']" type="checkbox" value="'.$row->id.'" /></td>'."\n";
	echo '</tr>'."\n";
	}
	$x=0;
	echo '<tr>';
	echo '<td headers="code" id="headcode'.$x.'"><label for="code'.$x.'">'.__('Code','eshop').'</label><input id="code'.$x.'" name="code[]" type="text" value="" size="4" maxlength="4" /><input id="id'.$x.'" name="id[]" type="hidden" value="'.$x.'" /></td>'."\n";
	echo '<td headers="statename headcode'.$x.'"><label for="state'.$x.'">'.__('Statename','eshop').'</label><input id="state'.$x.'" name="stateName[]" type="text" value="" size="30" maxlength="50" /></td>'."\n";
	echo '<td headers="zone headcode'.$x.'"><label for="zone'.$x.'">'.__('Zone','eshop').'</label><input id="zone'.$x.'" name="zone[]" type="text" value="" size="2" maxlength="1" /></td>'."\n";
	echo '<td>&nbsp;</td>';
	echo '</tr>'."\n";

	?>
	</tbody>
	</table>

	<p class="submit eshop"><input type="submit" name="submit" class="button-primary" id="submit" value="<?php _e('Update Shipping Zones','eshop'); ?>" /></p>
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
		update_option('eshop_unknown_state',$wpdb->escape($_POST['eshop_unknown_state']));
	}
	if(isset($_POST['submit'])){
		foreach($_POST as $k=>$v){
			$class=substr($k,0,1);
			$items=substr($k,1,1);
			$zone=substr($k,2);
			$zonenum=substr($k,-1);

			if(!is_numeric($v) && ($k!='submit'&& $k!='eshop_shipping')){
				$error.='<li>'.__('Class','eshop').' '.$class.': '.__('Zone','eshop').' '.$zonenum.'</li>'."\n";
			}elseif($k!='submit' && $k!='eshop_shipping'){
				$query=$wpdb->query("UPDATE $dtable set $zone='$v' where class='$class' and items='$items'");
			//echo "<p>UPDATE $dtable set $zone='$v' where class='$class' and items='$items'</p>";
			}
		}
	}
	if($error!=''){
		echo'<div id="message" class="error fade"><p>'.__('<strong>Error</strong> the following were not valid amounts:','eshop').'</p><ul>'.$error.'</ul></div>'."\n";
	}elseif(isset($_POST['shipmethod'])||isset($_POST['submit'])){
		echo'<div id="message" class="updated fade"><p>'.__('Shipping Rates changed successfully.','eshop').'</p></div>'."\n";
	}
	echo '<div class="wrap">';
	echo '<h2>'.__('Shipping Rates','eshop').'</h2>'."\n";
	?>
	<?php echo $echosub; ?>
	<p><?php _e('The following are the shipping rates by class and zone.','eshop'); ?></p>
	<p><?php _e('<strong>Warning:</strong> changing which method you use <em>may</em> affect the full table
	of shipping rates. To change from one method to another, first <em>update</em>
	your choice, and the relevant table will then appear.','eshop'); ?></p>
	<form id="shipformmethod" action="" method="post">
	<fieldset><legend><?php _e('Shipping rate calculation','eshop'); ?></legend>
	<?php
	for($i=1;$i<=3;$i++){
		$selected='';
		if($i == get_option('eshop_shipping')){$selected=' checked="checked"';}
		if($i==1){
			$extra=' <small>'.__('( per quantity of 1, prices reduced for additional items )','eshop').'</small>';
		}elseif($i==2){
			$extra=' <small>'.__('( once per shipping class no matter what quantity is ordered )','eshop').'</small>';
		}elseif($i==3){
			$extra=' <small>'.__('( one overall charge no matter what quantity is ordered )','eshop').'</small>';
		}	
		echo '<input type="radio" name="eshop_shipping" id="eshop_shipping'.$i.'" value="'.$i.'" '.$selected.'/><label for="eshop_shipping'.$i.'">Method '.$i.$extra.'</label><br />';
	}
	?>
	<label for="eshop_shipping_zone"><?php _e('Shipping Zones by','eshop'); ?></label>
	<select id="eshop_shipping_zone" name="eshop_shipping_zone">
	<?php
	if('country' == get_option('eshop_shipping_zone')){
		echo '<option value="country" selected="selected">'.__('Country','eshop').'</option>';
		echo '<option value="state">'.__('State/County/Province','eshop').'</option>';
	}else{
		echo '<option value="country">'.__('Country','eshop').'</option>';
		echo '<option value="state" selected="selected">'.__('State/County/Province','eshop').'</option>';
	}
	?>
	</select><br />
	<label for="eshop_unknown_state"><?php _e('Default Zone for unknown State/County/Province','eshop'); ?></label>
		<select id="eshop_unknown_state" name="eshop_unknown_state">
		<?php
		for($i=1;$i<=5;$i++){
		?>
			<option value="<?php echo $i; ?>"<?php if($i==get_option('eshop_unknown_state')) echo ' selected="selected"'; ?>><?php echo $i; ?></option>
		<?php
		}
		?>
	</select><br />
	<label for="eshop_show_zones"><?php _e('Show Shipping Zones on Shipping Page','eshop'); ?></label>
	<select id="eshop_show_zones" name="eshop_show_zones">
	<?php
	if('yes' == get_option('eshop_show_zones')){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />
	
	<p class="submit eshop"><input type="submit" name="shipmethod" class="button-primary" id="submitit" value="<?php _e('Update Shipping rate calculation','eshop'); ?>" /></p>

	</fieldset>
	</form>
	<form id="shipform" action="" method="post">
	<fieldset><legend><span class="offset"><?php _e('Shipping Classes and Zones','eshop'); ?></span></legend>
	<table class="hidealllabels" summary="Shipping rates">
	<caption><?php _e('Shipping rates by class and zone','eshop'); ?></caption>
	<tr>
	<th id="class"><?php _e('Class','eshop'); ?></th>
	<th id="zone1"><?php _e('Zone 1','eshop'); ?></th>
	<th id="zone2"><?php _e('Zone 2','eshop'); ?></th>
	<th id="zone3"><?php _e('Zone 3','eshop'); ?></th>
	<th id="zone4"><?php _e('Zone 4','eshop'); ?></th>
	<th id="zone5"><?php _e('Zone 5','eshop'); ?></th>
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
					echo '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>'.__('(First Item)','eshop').'</small></th>'."\n";
				}else{
					echo '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>'.__('(Additional Items)','eshop').'</small></th>'."\n";
				}
				echo '<td headers="zone1 cname'.$x.'"><label for="'.$row->class.$row->items.'zone1">'.__('Zone 1 class','eshop').' '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone1" name="'.$row->class.$row->items.'zone1" type="text" value="'.$row->zone1.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone2 cname'.$x.'"><label for="'.$row->class.$row->items.'zone2">'.__('Zone 2 class','eshop').' '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone2" name="'.$row->class.$row->items.'zone2" type="text" value="'.$row->zone2.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone3 cname'.$x.'"><label for="'.$row->class.$row->items.'zone3">'.__('Zone 3 class','eshop').' '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone3" name="'.$row->class.$row->items.'zone3" type="text" value="'.$row->zone3.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone4 cname'.$x.'"><label for="'.$row->class.$row->items.'zone4">'.__('Zone 4 class','eshop').' '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone4" name="'.$row->class.$row->items.'zone4" type="text" value="'.$row->zone4.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone5 cname'.$x.'"><label for="'.$row->class.$row->items.'zone5">'.__('Zone 5 class','eshop').' '.$row->class.$row->items.'</label><input id="'.$row->class.$row->items.'zone5" name="'.$row->class.$row->items.'zone5" type="text" value="'.$row->zone5.'" size="6" maxlength="6" /></td>'."\n";
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
				echo '<td headers="zone1 cname'.$x.'"><label for="'.$row->class.$row->items.'zone1">'.__('Zone 1 class','eshop').' '.$row->class.'</label><input id="'.$row->class.$row->items.'zone1" name="'.$row->class.$row->items.'zone1" type="text" value="'.$row->zone1.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone2 cname'.$x.'"><label for="'.$row->class.$row->items.'zone2">'.__('Zone 2 class','eshop').' '.$row->class.'</label><input id="'.$row->class.$row->items.'zone2" name="'.$row->class.$row->items.'zone2" type="text" value="'.$row->zone2.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone3 cname'.$x.'"><label for="'.$row->class.$row->items.'zone3">'.__('Zone 3 class','eshop').' '.$row->class.'</label><input id="'.$row->class.$row->items.'zone3" name="'.$row->class.$row->items.'zone3" type="text" value="'.$row->zone3.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone4 cname'.$x.'"><label for="'.$row->class.$row->items.'zone4">'.__('Zone 4 class','eshop').' '.$row->class.'</label><input id="'.$row->class.$row->items.'zone4" name="'.$row->class.$row->items.'zone4" type="text" value="'.$row->zone4.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone5 cname'.$x.'"><label for="'.$row->class.$row->items.'zone5">'.__('Zone 5 class','eshop').' '.$row->class.'</label><input id="'.$row->class.$row->items.'zone5" name="'.$row->class.$row->items.'zone5" type="text" value="'.$row->zone5.'" size="6" maxlength="6" /></td>'."\n";
				echo '</tr>';
				$x++;
			}
			break;
		case '3'://( one overall charge no matter how many are ordered )
			$x=1;
			$query=$wpdb->get_results("SELECT * from $dtable where items='1' and class='".__('A','eshop')."' ORDER BY 'class'  ASC");

			foreach ($query as $row){
				echo '<tr'.$alt.'>';
				echo '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>'.__('(Overall charge)','eshop').'</small></th>'."\n";
				echo '<td headers="zone1 cname'.$x.'"><label for="'.$row->class.$row->items.'zone1">'.__('Zone 1','eshop').'</label><input id="'.$row->class.$row->items.'zone1" name="'.$row->class.$row->items.'zone1" type="text" value="'.$row->zone1.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone2 cname'.$x.'"><label for="'.$row->class.$row->items.'zone2">'.__('Zone 2','eshop').'</label><input id="'.$row->class.$row->items.'zone2" name="'.$row->class.$row->items.'zone2" type="text" value="'.$row->zone2.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone3 cname'.$x.'"><label for="'.$row->class.$row->items.'zone3">'.__('Zone 3','eshop').'</label><input id="'.$row->class.$row->items.'zone3" name="'.$row->class.$row->items.'zone3" type="text" value="'.$row->zone3.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone4 cname'.$x.'"><label for="'.$row->class.$row->items.'zone4">'.__('Zone 4','eshop').'</label><input id="'.$row->class.$row->items.'zone4" name="'.$row->class.$row->items.'zone4" type="text" value="'.$row->zone4.'" size="6" maxlength="6" /></td>'."\n";
				echo '<td headers="zone5 cname'.$x.'"><label for="'.$row->class.$row->items.'zone5">'.__('Zone 5','eshop').'</label><input id="'.$row->class.$row->items.'zone5" name="'.$row->class.$row->items.'zone5" type="text" value="'.$row->zone5.'" size="6" maxlength="6" /></td>'."\n";
				echo '</tr>';
			}
			break;
	}
	?>
	</table>
	<p class="submit eshop"><input type="submit" name="submit" class="button-primary" id="submit" value="<?php _e('Update Shipping Rates','eshop'); ?>" /></p>
	</fieldset>
	</form>
	</div>
<?php
	break;
}
?>
<?php eshop_show_credits(); ?>