<?php
if ('eshop_orders.php' == basename($_SERVER['SCRIPT_FILENAME']))
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

if (!function_exists('displaymyorders')) {
	function displaymyorders(){
		global $wpdb,$eshopoptions;
		global $current_user;
		get_currentuserinfo();
		$user_id=$current_user->ID;
		//these should be global, but it wasn't working *sigh*
		$phpself=esc_url($_SERVER['REQUEST_URI']);
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		
		$sortby='ORDER BY custom_field DESC';
		
		$max = $wpdb->get_var("SELECT COUNT(id) FROM $dtable WHERE id > 0 AND user_id='$user_id'");
		if($max>0){
			if($eshopoptions['records']!='' && is_numeric($eshopoptions['records'])){
				$records=$eshopoptions['records'];
			}else{
				$records='10';
			}
			if(isset($_GET['_p']) && is_numeric($_GET['_p']))$epage=$_GET['_p'];
			else $epage='1';
			if(!isset($_GET['eshopall'])){
				$page_links = paginate_links( array(
					'base' => add_query_arg( '_p', '%#%' ),
					'format' => '',
					'total' => ceil($max / $records),
					'current' => $epage,
					'type'=>'array'
					));
				$offset=($epage*$records)-$records;
			}else{
				$page_links = paginate_links( array(
					'base' => add_query_arg( '_p', '%#%' ),
					'format' => '',
					'total' => ceil($max / $records),
					'current' => $epage,
					'type'=>'array',
					'show_all' => true,
				));
				$offset='0';
				$records=$max;
			}
			//
			$myrowres=$wpdb->get_results("Select * From $dtable where user_id='$user_id' $sortby LIMIT $offset, $records");
			$calt=0;

			echo '<div class="orderlist tablecontainer">';
			echo '<table id="listing" class="hidealllabels" summary="order listing">
			<caption class="offset">'.__('eshop Order Listing','eshop').'</caption>
			<thead>
			<tr>
			<th id="line" title="Line number">#</th>
			<th id="date">'.__('Date/Time','eshop').'</th>
			<th id="customer">'.__('Customer','eshop').'</th>
			<th id="items">'.__('Items','eshop').'</th>
			<th id="price">'.__('Price','eshop').'</th>
			<th id="state">'.__('Order Status','eshop').'</th>
			<th id="transid">'.__('Transaction ID','eshop').'</th>
			</tr></thead><tbody>'."\n";
			$move=array();
			$c=0;
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
				$status=$myrow->status;
				if($status=='Completed'){$status=__('Awaiting Dispatch','eshop');}
				if($status=='Pending'){$status=__('Pending','eshop');}
				if($status=='Waiting'){$status=__('Awaiting Payment','eshop');}
				if($status=='Sent'){$status=__('Sent','eshop');}
				if($status=='Deleted'){$status=__('Deleted','eshop');}
				if($status=='Failed'){$status=__('Failed','eshop');}
				if($x>0){
					$custom=$myrow->custom_field;
					$cyear=substr($custom, 0, 4);
					$cmonth=substr($custom, 4, 2);
					$cday=substr($custom, 6, 2);
					$chours=substr($custom, 8, 2);
					$cminutes=substr($custom, 10, 2);
					$thisdate=$cyear."-".$cmonth."-".$cday.' '.__('at','eshop').' '.$chours.':'.$cminutes;
					$calt++;
					$alt = ($calt % 2) ? '' : ' class="alt"';
					if($myrow->company!=''){
						$company=__(' of ','eshop').$myrow->company;
					}else{
						$company='';
					}
					$currsymbol=$eshopoptions['currency_symbol'];
					echo '<tr'.$alt.'>
					<td headers="line" id="numb'.$c.'">'.$c.'</td>
					<td headers="date numb'.$c.'">'.$thisdate.'</td>
					<td headers="customer numb'.$c.'"><a href="'.$phpself.'&amp;view='.$myrow->id.'" title="'.__('View complete order details','eshop').'">'.$myrow->first_name.' '.$myrow->last_name.$company.'</a></td>
					<td headers="items numb'.$c.'">'.$x.'</td>
					<td headers="price numb'.$c.'" class="right">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($total, 2)).'</td>
					<td headers="state numb'.$c.'" class="right">'.$status.'</td>
					<td headers="transid numb'.$c.'">'.$myrow->transid.'</td>'."</tr>\n";
				}

			}
			echo "</tbody></table></div>\n";
			//paginate
				echo '<div class="paginate">';
				if($records!=$max){
					$eecho = $page_links;
				}
				echo sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>',
					number_format_i18n( ( $epage - 1 ) * $records + 1 ),
					number_format_i18n( min( $epage * $records, $max ) ),
					number_format_i18n( $max)
				);
				if(isset($eecho)){
					$thispage=esc_url(add_query_arg('eshopall', 'yes', $_SERVER['REQUEST_URI']));
					echo "<ul class='page-numbers'>\n\t<li>".join("</li>\n\t<li>", $eecho)."</li>\n<li>".'<a href="'.$thispage.'">View All</a>'."</li>\n</ul>\n";
				}
				echo '<br /></div>';
			//end
						
		}else{
			echo '<p class="notice">'.__('There are no orders to display.','eshop').".</p>";
		}
	}
}

$dtable=$wpdb->prefix.'eshop_orders';
$itable=$wpdb->prefix.'eshop_order_items';
$stable=$wpdb->prefix.'eshop_states';
$ctable=$wpdb->prefix.'eshop_countries';
$eshopoptions = get_option('eshop_plugin_settings');
echo '<div class="wrap">';
echo '<div id="eshopicon" class="icon32"></div><h2>'.__('My Orders','eshop')."</h2>\n";

if (isset($_GET['view']) && is_numeric($_GET['view'])){
	$view=$_GET['view'];
	$dquery=$wpdb->get_results("Select * From $dtable where id='$view'");
	foreach($dquery as $drow){
		$status=$drow->status;
		$checkid=$drow->checkid;
		$custom=$drow->custom_field;
		$transid=$drow->transid;
		$user_notes=$drow->user_notes;
		$paidvia=$drow->paidvia;
	}
	if($status=='Completed'){$status=__('Awaiting Dispatch','eshop');}
	if($status=='Pending'){$status=__('Pending','eshop');}
	if($status=='Waiting'){$status=__('Awaiting Payment','eshop');}
	if($status=='Sent'){$status=__('Sent','eshop');}
	if($status=='Deleted'){$status=__('Deleted','eshop');}
	if($status=='Failed'){$status=__('Failed','eshop');}
	echo '<h3 class="status">'.__('Order Details','eshop').' - <span>'.$status.'</span></h3>';
	$result=$wpdb->get_results("Select * From $itable where checkid='$checkid' ORDER BY id ASC");
	$total=0;
	$calt=0;
	$currsymbol=$eshopoptions['currency_symbol'];
	?>
	<div class="orders tablecontainer">
	<p><?php _e('Transaction ID:','eshop'); ?> <strong><?php echo $transid; ?></strong></p>
	<?php
	if($user_notes!=''){
		echo '<div id="eshop_admin_note" class="noprint"><h4>'.__('Note:','eshop')."</h4>\n";
		echo nl2br(htmlspecialchars(stripslashes($user_notes))).'</div>'."\n";
	}
	?>
	
	<table id="listing" summary="<?php _e('Table for order details','eshop'); ?>">
	<caption><?php _e('Order Details','eshop'); ?></caption>
	<thead>
	<tr>
	<th id="opname"><?php _e('Product Name','eshop'); ?></th>
	<th id="oitem"><?php _e('Item or Unit Data','eshop'); ?></th>
	<th id="odown"><?php _e('Download?','eshop'); ?></th>
	<th id="oqty"><?php _e('Quantity','eshop'); ?></th>
	<th id="oprice"><?php _e('Price','eshop'); ?></th></tr>
	</thead>
	<tbody>
	<?php
	foreach($result as $myrow){
		$value=$myrow->item_qty * $myrow->item_amt;
		$total=$total+$value;
		$itemid=$myrow->item_id;
		if($myrow->optsets!='')
			$itemid.='<br />'.$myrow->optsets;
		//check if downloadable product
		$dordtable=$wpdb->prefix.'eshop_download_orders';
		$downstable=$wpdb->prefix.'eshop_downloads';
		if($myrow->down_id!='0'){
			//item is a download
			$dltable=$wpdb->prefix.'eshop_downloads';
			$dlinfo= $wpdb->get_row("SELECT d.downloads, d.id FROM $dordtable as d, $downstable as dl WHERE d.checkid='$myrow->checkid' AND dl.id='$myrow->down_id' AND d.files=dl.files");
			$downloadable='<span class="downprod">'.__('Yes - remaining:','eshop');
			$downloadable .=' '.$dlinfo->downloads.'</span>';
		}else{
			$downloadable='';
		}
	
		// add in a check if postage here as well as a link to the product
		$showit=$myrow->optname;
		$calt++;
		$alt = ($calt % 2) ? '' : ' class="alt"';
		echo '<tr'.$alt.'>
		<td id="onum'.$calt.'" headers="opname">'.$showit.'</td>
		<td headers="opname onum'.$calt.'">'.$itemid.'</td>
		<td headers="opname onum'.$calt.'">'.$downloadable.'</td>
		<td headers="opname onum'.$calt.'">'.$myrow->item_qty.'</td>
		<td headers="opname onum'.$calt.'" class="right">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($value, 2))."</td></tr>\n";
	}
	if($transid==__('Processing&#8230;','eshop'))
		echo "<tr><td colspan=\"4\" class=\"totalr\">".__('Total &raquo;','eshop')." </td><td class=\"total\">".sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($total, 2))."</td></tr>\n";
	else
		echo "<tr><td colspan=\"4\" class=\"totalr\">".sprintf(_x('Total paid via %1$s &raquo;','eshop'),ucfirst($paidvia))." </td><td class=\"total\">".sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($total, 2))."</td></tr>\n";
	echo "</tbody></table>\n";
			
	$cyear=substr($custom, 0, 4);
	$cmonth=substr($custom, 4, 2);
	$cday=substr($custom, 6, 2);
	$chours=substr($custom, 8, 2);
	$cminutes=substr($custom, 10, 2);
	$thisdate=$cyear."-".$cmonth."-".$cday.__(' at ','eshop').$chours.':'.$cminutes;
	echo "<p>".__('Order placed on','eshop')." <strong>".$thisdate."</strong>.";
	echo "</p>\n</div>\n";
	if($drow->reference!=''){
		echo '<p><strong>'.__('Reference:','eshop').'</strong> '.$drow->reference.'</p>';
	}
	echo "<div class=\"orderaddress\"><h4>".__('Invoice','eshop')."</h4>";
	foreach($dquery as $drow){

		echo '<p><strong>'.__("Name: ",'eshop').'</strong>'.$drow->first_name." ".$drow->last_name."<br />\n";
		if($drow->company!='') echo '<strong>'.__("Company: ",'eshop').'</strong>'.$drow->company."<br />\n";
		echo '<strong>'.__('Email:','eshop').'</strong>'.$drow->email."<br />\n";
		if('no' == $eshopoptions['downloads_only']){
			echo '<strong>'.__("Phone: ",'eshop').'</strong>'.$drow->phone."</p>\n";

			echo '<h5>'.__('Address','eshop').'</h5>';
			$address=$drow->address1;
			if($drow->address2!='') $address.= ', '.$drow->address2;

			echo '<address>'.$drow->first_name." ".$drow->last_name."<br />\n";
			if($drow->company!='') echo __("Company: ",'eshop').$drow->company."<br />\n";
			echo $address."<br />\n";
			echo $drow->city."<br />\n";
			$qcode=$wpdb->escape($drow->state);
			$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE id='$qcode' limit 1");
			if($qstate!=''){
				echo $qstate."<br />";
				$statezone = $wpdb->get_var("SELECT zone FROM $stable WHERE id='$qcode' limit 1");
			}else{
				echo $drow->state."<br />";
			}
			echo $drow->zip."<br />\n";

			$qcode=$wpdb->escape($drow->country);
			$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qcode' limit 1");
			$countryzone = $wpdb->get_var("SELECT zone FROM $ctable WHERE code='$qcode' limit 1");
			echo $qcountry."</address>";
			if($eshopoptions['shipping_zone']=='country'){
				$qzone=$countryzone;
			}else{
				$qzone=$statezone;
				if($statezone=='') $qzone=$eshopoptions['unknown_state'];
			}
			echo '<p>'.__('Shipping Zone: ','eshop')."<strong>".$qzone."</strong></p></div>\n";
			if($drow->ship_name!='' && $drow->ship_address!='' && $drow->ship_city!='' && $drow->ship_postcode!=''){
				echo "<div class=\"shippingaddress\"><h4>".__('Shipping','eshop')."</h4>";
				echo '<p><strong>'.__("Name: ",'eshop').'</strong>'.$drow->ship_name."<br />\n";
				if($drow->ship_company!='') echo '<strong>'.__("Company: ",'eshop').'</strong>'.$drow->ship_company."<br />\n";
				echo '<strong>'.__("Phone: ",'eshop').'</strong>'.$drow->ship_phone."</p>\n";
				echo '<h5>'.__('Address','eshop').'</h5>';
				echo '<address>'.$drow->ship_name.'<br />'."\n";
				if($drow->ship_company!='') echo $drow->ship_company."<br />\n";
				echo $drow->ship_address."<br />\n";
				echo $drow->ship_city."<br />\n";
				$qcode=$wpdb->escape($drow->ship_state);
				$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE id='$qcode' limit 1");
				if($qstate!=''){
					$statezone = $wpdb->get_var("SELECT zone FROM $stable WHERE id='$qcode' limit 1");
					echo $qstate."<br />";
				}else{
					echo $drow->ship_state."<br />";
				}
				echo $drow->ship_postcode."<br />\n";
				$qcode=$wpdb->escape($drow->ship_country);
				$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qcode' limit 1");
				$countryzone = $wpdb->get_var("SELECT zone FROM $ctable WHERE code='$qcode' limit 1");
				echo $qcountry."</address>";
				if($eshopoptions['shipping_zone']=='country'){
					$qzone=$countryzone;
				}else{
					$qzone=$statezone;
					if($statezone=='') $qzone=$eshopoptions['unknown_state'];
				}
				echo '<p>'. __('Shipping Zone:','eshop')." <strong>".$qzone."</strong></p></div>\n";
			}
		}else{
			echo '</p></div>';
		}
		echo '<hr class="eshopclear" />';
		if($drow->thememo!=''){
			echo '<div class="paypalmemo"><h4>'.__('Paypal memo:','eshop').'</h4><p>'.nl2br(htmlspecialchars(stripslashes($drow->thememo))).'</p></div>';
		}
		
		if($drow->comments!=''){
			echo '<div class="eshopmemo"><h4>'.__('Order comments:','eshop').'</h4><p>'.nl2br(htmlspecialchars(stripslashes($drow->comments))).'</p></div>';
		}
		if($drow->thememo!='' || $drow->comments!=''){
			echo '<hr class="eshopclear" />';
		}
	}

echo '<br class="clearbr" />&nbsp;</div>';

}else{
displaymyorders();
}
eshop_show_credits();
?>