<?php
if ('eshop_orders.php' == basename($_SERVER['SCRIPT_FILENAME']))
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

if (isset($_GET['action']) )
	$action_status = attribute_escape($_GET['action']);
else
	$_GET['action']=$action_status = 'Pending';


if (!function_exists('displayorders')) {
	function displayorders($type){
		global $wpdb;
		//these should be global, but it wasn't working *sigh*
		$phpself=wp_specialchars($_SERVER['REQUEST_URI']);
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		
		if(isset($_POST['change'])){
			if($_POST['move'][0]!=''){
				foreach($_POST['move'] as $v=>$ch){
					$mark=$_POST['mark'];
					$query2=$wpdb->get_results("UPDATE $dtable set status='$mark' where checkid='$ch'");
				}
				echo '<p class="updated fade">Order status changed successfully.</p>';
			}else{
				echo '<p class="error">No orders were selected.</p>';
			}
		}
		
		//pager for when you have lots and lots of orders :)
		include_once ("pager-class.php");
		$cda=$cdd=$ctn=$cca=$cna='';
		if(isset($_GET['by'])){
			switch ($_GET['by']) {
				case'dd'://date descending
					$sortby='ORDER BY custom_field DESC';
					$cdd=' class="current"';
					break;
				case'tn'://transaction id numerically
					$sortby='ORDER BY transid ASC';
					$ctn=' class="current"';
					break;
				case'na'://name alphabetically (last name)
					$sortby='ORDER BY last_name ASC';
					$cna=' class="current"';
					break;
				case'ca'://company name alphabetically
					$sortby='ORDER BY company ASC';
					$cca=' class="current"';
					break;
				case'da'://date ascending
				default:
					$sortby='ORDER BY custom_field ASC';
					$cda=' class="current"';
			}
		}else{
			$cda=' class="current"';
		}
		
		$range=10;
		$max = $wpdb->get_var("SELECT COUNT(id) FROM $dtable WHERE id > 0 AND status='$type'");
		if($max>0){
			if(get_option('eshop_records')!='' && is_numeric(get_option('eshop_records'))){
				$records=get_option('eshop_records');
			}else{
				$records='10';
			}
			if(isset($_GET['viewall']))$records=$max;

			$pager = new eshopPager( 
				$max ,          //see above
				$records,            // how many records to display at one time
				@$_GET['_p'] 	//this is the current page no carried via _GET
			);

			$pager->set_range($range);
			$thispage=$pager->get_limit();
			$c=$pager->get_limit_offset();
			//
			$myrowres=$wpdb->get_results("Select * From $dtable where status='$type' $sortby LIMIT $thispage");
			
			$calt=0;
			$apge=wp_specialchars($_SERVER['PHP_SELF']).'?page='.$_GET['page'].'&amp;action='.$_GET['action'];
			echo '<ul id="eshopsubmenu">';
			echo '<li><span>Sort Orders by &raquo;</span></li>';
			echo '<li><a href="'.$apge.'&amp;by=da"'.$cda.'>Date Ascending</a></li>';
			echo '<li><a href="'.$apge.'&amp;by=dd"'.$cdd.'>Date Descending</a></li>';
			echo '<li><a href="'.$apge.'&amp;by=tn"'.$ctn.'>ID Number</a></li>';
			echo '<li><a href="'.$apge.'&amp;by=ca"'.$cca.'>Company</a></li>';
			echo '<li><a href="'.$apge.'&amp;by=na"'.$cna.'>Customer</a></li>';

			echo '</ul>';
		
			echo "<form id=\"orderstatus\" action=\"".$phpself."\" method=\"post\">";
			echo '<div class="orderlist tablecontainer">';
			echo '<table id="listing" class="hidealllabels" summary="order listing">
			<caption class="offset">eshop Order Listing</caption>
			<thead>
			<tr>
			<th id="line" title="Line number">#</th>
			<th id="date">Date/Time</th>
			<th id="customer">Customer</th>
			<th id="items">Items</th>
			<th id="price">Price</th>
			<th id="downloads">Contains Downloads</th>
			<th id="transid">Transaction ID</th>
			<th id="bulk" title="Bulk operations">Bulk</th>
			</tr></thead><tbody>'."\n";
			$move=array();
			foreach($myrowres as $myrow){
				//total + products
				$c++;//count for the  number of results.
				$checkid=$myrow->checkid;
				$itemrowres=$wpdb->get_results("Select * From $itable where checkid='$checkid'");
				$total=0;
				$x=-1;
				foreach($itemrowres as $itemrow){
					$value=$itemrow->item_qty * $itemrow->item_amt;
					$total=$total+$value;
					$x++;
				}
				//
				$status=$type;
				if($x>0){
					$custom=$myrow->custom_field;
					$cyear=substr($custom, 0, 4);
					$cmonth=substr($custom, 4, 2);
					$cday=substr($custom, 6, 2);
					$chours=substr($custom, 8, 2);
					$cminutes=substr($custom, 10, 2);
					$thisdate=$cyear."-".$cmonth."-".$cday.' at '.$chours.':'.$cminutes;
					$calt++;
					$alt = ($calt % 2) ? '' : ' class="alt"';
					if($myrow->company!=''){
						$company=' of '.$myrow->company;
					}else{
						$company='';
					}
					$currsymbol=get_option('eshop_currency_symbol');
					echo '<tr'.$alt.'>
					<td headers="line" id="numb'.$c.'">'.$c.'</td>
					<td headers="date numb'.$c.'">'.$thisdate.'</td>
					<td headers="customer numb'.$c.'"><a href="'.$phpself.'&amp;view='.$myrow->id.'" title="View complete order details">'.$myrow->first_name.' '.$myrow->last_name.$company.'</a></td>
					<td headers="items numb'.$c.'">'.$x.'</td>
					<td headers="price numb'.$c.'" class="right">'.$currsymbol.number_format($total, 2).'</td>
					<td headers="downloads numb'.$c.'" class="right">'.$myrow->downloads.'</td>
					<td headers="transid numb'.$c.'">'.$myrow->transid.'</td>'.
					'<td headers="bulk numb'.$c.'"><label for="move'.$c.'">Move #'.$c.'</label><input type="checkbox" value="'.$checkid.'" name="move[]" id="move'.$c.'" />'
					."</td></tr>\n";
				}

			}
			echo "</tbody></table></div>\n";
			//paginate
				echo '<div class="paginate"><p class="checkers">Bulk:<a href="javascript:checkedAll(\'orderstatus\', true)" title="Select all of the checkboxes above">Check</a><span class="offset"> | </span><a href="javascript:checkedAll(\'orderstatus\', false)" title="Deselect all of the checkboxes above">Uncheck</a></p><p>';
					if($pager->_pages > 1){
						echo $pager->get_title('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>'). '<br />';
					}else{
						echo $pager->get_title('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>'). '<br />';
					}
					echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ','&laquo; First Page','Last Page &raquo;').'';
					//echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ').'<br />';
					if($pager->_pages >= 2){
						echo ' &raquo; <a class="pag-view" href="'.wp_specialchars($_SERVER['REQUEST_URI']).'&amp;_p=1&amp;action='.$_GET['action'].'&amp;viewall=yes" title="View all '.$status.' orders">View All &raquo;</a>';
					}
					echo '</p></div>';
			//end
			
			
			//moved order status box
				?>
				<fieldset id="changestat"><legend>Change Orders Status</legend>
				<p class="submit eshop"><label for="mark">Mark orders as:</label>
				<select name="mark" id="mark">
				<option value="Sent">Shipped</option>
				<option value="Completed">Active</option>
				<option value="Pending">Pending</option>
				<option value="Failed">Failed</option>
				<option value="Deleted">Deleted</option>
				</select>
				<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>" />
				<input type="hidden" name="change" value="yes" />
				<input type="submit" id="submit1" value="Change" /></p>
				</fieldset></form>
				<?php
	//order status box code end
			
			
			
			if($type=='Deleted'){
			?>
				<div id="eshopformleft"><form id="ordersdelete" action="<?php echo wp_specialchars($_SERVER['REQUEST_URI']); ?>" method="post">
				<fieldset><legend>Complete Order Deletion</legend>
				<p class="submit eshop"><label for="dhours">Orders that are 
				<select name="dhours" id="dhours">
				<option value="72" selected="selected">72</option>
				<option value="36">48</option>
				<option value="24">24</option>
				<option value="16">16</option>
				<option value="8">8</option>
				<option value="4">4</option>
				<option value="0">0</option>
				</select> hours old</label>
				<input type="hidden" name="dall" value="yes" />
				<input type="submit" id="submit2" value="Delete" /></p>
				</fieldset></form></div>
			<?php
			}
			
			
		}else{
			if($type=='Completed'){$type='Active';}
			if($type=='Sent'){$type='Shipped';}
			echo "<p class=\"notice\">There are no <span>".$type."</span> orders.</p>";
		}
	}
}
if (!function_exists('displaystats')) {
	function displaystats(){
		global $wpdb;
		//these should be global, but it wasn't working *sigh*
		$phpself=wp_specialchars($_SERVER['REQUEST_URI']);
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		$array=array('Pending','Completed','Sent','Failed','Deleted');
		echo '<h3>Order Stats</h3><ul class="eshop-stats">';
		foreach($array as $k=>$type){
			$max = $wpdb->get_var("SELECT COUNT(id) FROM $dtable WHERE id > 0 AND status='$type'");
			switch($type){
				case 'Completed':
					$type='Active';
					break;
				case 'Sent':
					$type='Shipped';
					break;
			}			
			echo '<li><strong>'.$max.'</strong> '.$type.' orders</li>';
		}
		echo '</ul>';
		
		$metatable=$wpdb->prefix.'postmeta';
		$count = $wpdb->get_var("SELECT COUNT(post_id) FROM $metatable where meta_key='Option 1' AND meta_value!=''");
		$stocked = $wpdb->get_results("SELECT post_id FROM $metatable where meta_key='Option 1' AND meta_value!=''");
		$countprod=$countfeat=0;
		foreach($stocked as $stock){
			$fcount = $wpdb->get_var("SELECT meta_value FROM $metatable where post_id='$stock->post_id' and meta_key='Featured Product'");
			if($fcount=='Yes'){
				$countfeat++;
			}
			$pcount = $wpdb->get_var("SELECT meta_value FROM $metatable where post_id='$stock->post_id' and meta_key='Stock Available'");
			if($pcount=='Yes'){
				$countprod++;
			}
		}
		$stktable = $wpdb->prefix ."eshop_stock";
		$stkpurc=0;
		$stkpurc=$wpdb->get_var("Select SUM(purchases) From $stktable");
		if($stkpurc<1){
			$stkpurc=0;
		}

		?>
		<h3>Product stats</h3>
		<ul class="eshop-stats">
		<li><strong><?php echo $count; ?></strong> products.</li>
		<li><strong><?php echo $countprod; ?></strong> products in stock.</li>
		<li><strong><?php echo $countfeat; ?></strong> featured products.</li>
		<li><strong><?php echo $stkpurc; ?></strong> purchases.</li>

		</ul>	
		<?php
		//work out totals for quick stats
		$dltable = $wpdb->prefix ."eshop_downloads";
		$total=$purchased=0;
		$total=$wpdb->get_var("Select SUM(downloads) From $dltable");
		$purchased=$wpdb->get_var("Select SUM(purchases) From $dltable");
		if($total<1){
			$total=0;
		}
		if($purchased<1){
			$purchased=0;
		}
		?>
		<h3>Product Download Stats</h3>
		<ul class="eshop-stats">
		<li><strong><?php echo $total; ?></strong> Total Downloads</li>
		<li><strong><?php echo $purchased; ?></strong> Total Purchases</li>
		</ul>  
		<?php
		
	}
}
if (!function_exists('deleteorder')) {
	function deleteorder($delid){
		global $wpdb;
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		$checkid=$wpdb->get_var("Select checkid From $dtable where id='$delid' && status='Deleted'");
		$delquery2=$wpdb->get_results("DELETE FROM $itable WHERE checkid='$checkid'");
		$delquery=$wpdb->get_results("DELETE FROM $dtable WHERE checkid='$checkid'");
		echo '<p class="success">That order has now been deleted from the system.</p>';
	}
}

//sub sub menu - may change to a little form:
$phpself='?page='.$_GET['page'];
$dtable=$wpdb->prefix.'eshop_orders';
$itable=$wpdb->prefix.'eshop_order_items';
$stable=$wpdb->prefix.'eshop_states';
$ctable=$wpdb->prefix.'eshop_countries';

/*
##########
##########
*/
if(isset($_GET['viewemail']) || isset($_POST['thisemail'])){
	include 'eshop_email.php';
}else{



//paypal tries upto 4 days after a transaction.
$delit=4;
$wpdb->query("UPDATE $dtable set status='Deleted' where status='Pending' && edited < DATE_SUB(NOW(), INTERVAL $delit DAY)");

//try and remove all orders that only have downloadable products
$moveit=$wpdb->get_results("Select checkid From $dtable where downloads='yes'");

foreach($moveit as $mrow){
	$pdownload=$numbrows=0;
	$result=$wpdb->get_results("Select post_id From $itable where checkid='$mrow->checkid' AND item_id!='postage'");
	foreach($result as $crow){
		$post_id=$crow->post_id;
		$mtable=$wpdb->prefix.'postmeta';
		$dlchk= $wpdb->get_var("SELECT meta_value FROM $mtable WHERE meta_key='Product Download' AND post_id='$post_id'");
		if($dlchk!='' && $dlchk!='0'){
			//item is a download
			$pdownload++;
		}
		$numbrows++;
	}
	if($pdownload==$numbrows){
		//in theory this will only activate if the order only contains downloads
		$wpdb->query("UPDATE $dtable set status='Sent' where status='Completed' && checkid='$mrow->checkid'");
	}
}



echo '<div class="wrap">';
if(isset($_GET['view'])){
	$view=$_GET['view'];
	$status=$wpdb->get_var("Select status From $dtable where id='$view'");
	if($status=='Completed'){$status='Active Order';}
	if($status=='Pending'){$status='Pending Order';}
	if($status=='Sent'){$status='Shipped Order';}
	if($status=='Deleted'){$status='Deleted Order';}
	if($status=='Failed'){$status='Failed Order';}
	$state=$status;
}elseif(isset($_GET['action'])){
	switch ($_GET['action']) {
		case 'Dispatch':
			$state='Active Orders';
			break;
		case 'Pending':
			$state='Pending Orders';
			break;
		case 'Failed':
			$state='Failed Orders';
			break;
		case 'Sent':
			$state='Shipped Orders';
			break;
		case 'Deleted':
			$state='Deleted Orders';
			break;
		case 'Stats':
			$state='eShop Order Stats';
			break;
		default:
			break;
	}
}else{
	die ('<h2 class="error">Error</h2>');
}

echo '<h2>'.$state."</h2>\n";
echo '<ul class="subsubsub">';

$stati=array('Stats'=>'Stats','Pending' => 'Pending','Dispatch'=>'Active','Sent'=>'Shipped','Failed'=>'Failed','Deleted'=>'Deleted');
foreach ( $stati as $status => $label ) {
	$class = '';
	if ( $status == $action_status )
		$class = ' class="current"';

	$status_links[] = "<li><a href=\"?page=eshop_orders.php&amp;action=$status\"$class>" . $label . '</a>';
}
echo implode(' | </li>', $status_links) . '</li>';
echo '</ul>';

if(isset($_GET['delid']) && !isset($_GET['view'])){
	deleteorder($_GET['delid']);
	unset($_GET['view']);
	$_GET['action']=$_POST['action'];
	$_GET['action']='Deleted';
}
if(isset($_POST['dall'])){
	$dhours=$_POST['dhours'];
	if($_POST['dhours']=='0' ||$_POST['dhours']=='4'||$_POST['dhours']=='8'||$_POST['dhours']=='16'||$_POST['dhours']=='24'||$_POST['dhours']=='48'||$_POST['dhours']=='72'){
		$delay=$wpdb->escape($_POST['dhours']);
		$replace=$delay.' hours';
		if($delay==24){$replace='1 day';}
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		$myrows=$wpdb->get_results("Select checkid From $dtable where status='Deleted' && edited < DATE_SUB(NOW(), INTERVAL $delay HOUR)");
		foreach($myrows as $myrow){
			$checkid=$myrow->checkid;
			$delquery2=$wpdb->query("DELETE FROM $itable WHERE checkid='$checkid'");
			$query2=$wpdb->query("DELETE FROM $dtable WHERE status='Deleted' && checkid='$checkid' && edited < DATE_SUB(NOW(), INTERVAL $delay HOUR)");
		}
		echo '<p class="success">Deleted orders older than '.$replace.' have now been <strong>completely</strong> deleted.</p>';
	}else{
		echo '<p class="error">There was an error, and nothing has been deleted.</p>';
	}
}
if(isset($_POST['mark']) && !isset($_POST['change'])){
	$mark=$_POST['mark'];
	$checkid=$_POST['checkid'];
	$query2=$wpdb->get_results("UPDATE $dtable set status='$mark' where checkid='$checkid'");
	echo '<p class="success">Order status changed successfully.</p>';
}
if (isset($_GET['view'])){
	$view=$_GET['view'];
	$dquery=$wpdb->get_results("Select * From $dtable where id='$view'");
	foreach($dquery as $drow){
		$status=$drow->status;
		$checkid=$drow->checkid;
		$custom=$drow->custom_field;
		$transid=$drow->transid;
	}
	if($status=='Completed'){$status='Active';}
	if($status=='Pending'){$status='Pending';}
	if($status=='Sent'){$status='Shipped';}
	//moved order status box
	echo "<div id=\"eshopformfloat\"><form id=\"orderstatus\" action=\"".$phpself."\" method=\"post\">";
	?>
	<fieldset><legend>Change Order Status</legend>
	<p class="submit eshop"><label for="mark">Mark order as:</label>
	<select name="mark" id="mark">
	<option value="Sent">Shipped</option>
	<option value="Completed">Active</option>
	<option value="Pending">Pending</option>
	<option value="Failed">Failed</option>
	<option value="Deleted">Deleted</option>
	</select>
	<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>" />
	<input type="hidden" name="checkid" value="<?php echo $checkid; ?>" />
	<input type="submit" id="submit3" value="Change" /></p>
	</fieldset></form></div>
	<?php
	//order status box code end
	echo '<h3 class="status"><span>'.$status.'</span> Order Details</h3>';
	$result=$wpdb->get_results("Select * From $itable where checkid='$checkid' ORDER BY id ASC");
	$total=0;
	$calt=0;
	$currsymbol=get_option('eshop_currency_symbol');
	?>
	<div class="orders tablecontainer">
	<p>Transaction ID: <strong><?php echo $transid; ?></strong></p>
	<table id="listing" summary="Table for order details">
	<caption>Order Details</caption>
	<thead>
	<tr>
	<th id="opname">Product Name</th>
	<th id="oitem">Item or Unit Data</th>
	<th id="odown">Download?</th>
	<th id="oqty">Quantity</th>
	<th id="oprice">Price</th></tr>
	</thead>
	<tbody>
	<?php
	foreach($result as $myrow){
		$value=$myrow->item_qty * $myrow->item_amt;
		$total=$total+$value;
		$itemid=$myrow->item_id;
		//check if downloadable product
		$post_id=$myrow->post_id;
		$mtable=$wpdb->prefix.'postmeta';
		$dlchk= $wpdb->get_var("SELECT meta_value FROM $mtable WHERE meta_key='Product Download' AND post_id='$post_id'");
		if($dlchk!='' && $dlchk!='0'){
			//item is a download
			$downloadable='<span class="downprod">Yes</span>';
		}else{
			$downloadable='<span class="offlineprod">No</span>';
		}
		// add in a check if postage here as well as a link to the product
		if($itemid=='postage'){
			$showit='Shipping';
			$downloadable=$itemid='&nbsp;';
		}else{
			$showit=$myrow->optname;
		}
		$calt++;
		$alt = ($calt % 2) ? '' : ' class="alt"';
		echo '<tr'.$alt.'>
		<td id="onum'.$calt.'" headers="opname">'.$showit.'</td>
		<td headers="opname onum'.$calt.'">'.$itemid.'</td>
		<td headers="opname onum'.$calt.'">'.$downloadable.'</td>
		<td headers="opname onum'.$calt.'">'.$myrow->item_qty.'</td>
		<td headers="opname onum'.$calt.'" class="right">'.$currsymbol.number_format($value, 2)."</td></tr>\n";
	}
	echo "<tr><td colspan=\"4\" class=\"totalr\">Total &raquo; </td><td class=\"total\">".$currsymbol.number_format($total, 2)."</td></tr>\n";
	echo "</tbody></table>\n";
	$cyear=substr($custom, 0, 4);
	$cmonth=substr($custom, 4, 2);
	$cday=substr($custom, 6, 2);
	$chours=substr($custom, 8, 2);
	$cminutes=substr($custom, 10, 2);
	$thisdate=$cyear."-".$cmonth."-".$cday.' at '.$chours.':'.$cminutes;
	echo "<p>Order placed on <strong>".$thisdate."</strong>.</p>\n";
	
	echo "</div>\n";
	echo "<p class=\"orderaddress\">\n";
	foreach($dquery as $drow){

		echo "Name: ".$drow->first_name." ".$drow->last_name."<br />\n";
		echo "Company: ".$drow->company."<br />\n";
		echo "Phone: ".$drow->phone."<br />\n";
		echo "Email: <a href=\"".$phpself."&amp;viewemail=".$view."\">".$drow->email."</a><br />\n";

		echo "Address: ".$drow->address1.", ".$drow->address2."<br />\n";
		echo "City: ".$drow->city."<br />\n";
		echo "Zip/Post code: ".$drow->zip."<br />\n";
		if($drow->country=='US'){
			$qcode=$wpdb->escape($drow->state);
			$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
			$statezone = $wpdb->get_var("SELECT zone FROM $stable WHERE code='$qcode' limit 1");
			echo "State: ".$qstate."<br />";
		}
		$qcode=$wpdb->escape($drow->country);
		$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qcode' limit 1");
		$countryzone = $wpdb->get_var("SELECT zone FROM $ctable WHERE code='$qcode' limit 1");
		echo "Country: ".$qcountry."<br />";
		if(get_option('eshop_shipping_zone')=='country'){
			$qzone=$countryzone;
		}else{
			$qzone=$statezone;
		}
		echo "Shipping Zone: <strong>".$qzone."</strong></p>\n";
		if($drow->ship_name!='' && $drow->ship_address!='' && $drow->ship_city!='' && $drow->ship_postcode!=''){
			echo "<p class=\"shippingaddress\"><strong>Shipping Address</strong><br />";
			echo "Name: ".$drow->ship_name."<br />\n";
			echo "Company: ".$drow->ship_company."<br />\n";
			echo "Phone: ".$drow->ship_phone."<br />\n";
			echo "Address: ".$drow->ship_address."<br />\n";
			echo "City: ".$drow->ship_city."<br />\n";
			echo "Zip/Post code: ".$drow->ship_postcode."<br />\n";
			if($drow->ship_country=='US'){
				$qcode=$wpdb->escape($drow->ship_state);
				$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
				$statezone = $wpdb->get_var("SELECT zone FROM $stable WHERE code='$qcode' limit 1");
				echo "State: ".$qstate."<br />";
			}
			$qcode=$wpdb->escape($drow->ship_country);
			$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qcode' limit 1");
			$countryzone = $wpdb->get_var("SELECT zone FROM $ctable WHERE code='$qcode' limit 1");
			echo "Country: ".$qcountry."<br />";
			if(get_option('eshop_shipping_zone')=='country'){
				$qzone=$countryzone;
			}else{
				$qzone=$statezone;
			}
			echo "Shipping Zone: <strong>".$qzone."</strong></p>\n";
		}
		if($drow->memo!=''){
			echo '<p><strong>Customer paypal memo:</strong><br />'.$drow->memo.'</p>';
		}
		if($drow->reference!=''){
				echo '<p><strong>Customer reference:</strong><br />'.$drow->reference.'</p>';
		}
		if($drow->comments!=''){
				echo '<p><strong>Customer order comments:</strong><br />'.$drow->comments.'</p>';
		}
	}
	if($status=='Deleted'){$delete="<p class=\"delete\"><a href=\"".$phpself."&amp;delid=".$view."\">Completely delete this order?</a><br /><small><strong>Warning:</strong> this order will be completely deleted and cannot be recovered at a later date.</small></p>";}else{$delete='';};
	echo $delete;
}else{

	if (empty($_GET['action'])) $_GET['action'] = 'Dispatch';  
	switch ($_GET['action']) {
		case 'Dispatch':
			displayorders('Completed');
			break;
		case 'Pending':
			displayorders('Pending');
			break;
		case 'Failed':
			displayorders('Failed');
			break;
		case 'Sent':
			displayorders('Sent');
			break;
		case 'Deleted':
			displayorders('Deleted');
			break;
		case 'stats':
		default:
			displaystats('Stats');
			break;
	}
}

echo '<br class="clearbr" />&nbsp;</div>';

}
eshop_show_credits();
?>