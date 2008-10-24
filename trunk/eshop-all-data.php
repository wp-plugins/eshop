<?php
session_start();
global $wpdb;
//tables used
$dtable=$wpdb->prefix.'eshop_orders';
$itable=$wpdb->prefix.'eshop_order_items';
$stable=$wpdb->prefix.'eshop_states';
$ctable=$wpdb->prefix.'eshop_countries';

//bits for csv
$cr='"'."\n";
$delim='","';
$data ='"';
//columns:
$data .= __('ID','eshop').$delim.
__('Order Status','eshop').$delim.
__('Transaction ID:','eshop').$delim.
__('Product Name','eshop').$delim.
__('Item or Unit Data','eshop').$delim.
__('Download?','eshop').$delim.
__('Quantity','eshop').$delim.
__('Price','eshop').$delim.
__('Total','eshop').$delim.
__('Order placed on','eshop').$delim.
__("Name: ",'eshop').$delim.
__("Company: ",'eshop').$delim.
__("Phone: ",'eshop').$delim.
__('Email:','eshop').$delim.
__("Address: ",'eshop').$delim.
__('Shipping Zone: ','eshop').$delim.
__("Ship To Name: ",'eshop').$delim.
__("Ship To Company: ",'eshop').$delim.
__("Ship To Phone: ",'eshop').$delim.
__('Ship To Address','eshop').$delim.
__('Ship To Shipping Zone:','eshop').$delim.
__('Customer paypal memo:','eshop').$delim.
__('Customer reference:','eshop').$delim.
__('Customer order comments:','eshop').$cr;

$dquery=$wpdb->get_results("Select * From $dtable");
foreach($dquery as $drow){
	$checkid=$drow->checkid;
	$custom=$drow->custom_field;
	$result=$wpdb->get_results("Select * From $itable where checkid='$checkid' ORDER BY id ASC");
	$currsymbol=get_option('eshop_currency_symbol');
	$total=0;

	foreach($result as $myrow){
		$value=$myrow->item_qty * $myrow->item_amt;
		$total=$total+$value;
		$itemid=$myrow->item_id;
		//check if downloadable product
		$post_id=$myrow->post_id;
		$mtable=$wpdb->prefix.'postmeta';
		$dlchk= $wpdb->get_var("SELECT meta_value FROM $mtable WHERE meta_key='_Product Download' AND post_id='$post_id'");
		if($dlchk!='' && $dlchk!='0'){
			//item is a download
			$downloadable=__('Yes','eshop');
		}else{
			$downloadable=__('No','eshop');
		}
		// add in a check if postage here as well as a link to the product
		if($itemid=='postage'){
			$showit=__('Shipping','eshop');
			$thetotal=$total;
		}else{
			$showit=$myrow->optname;
			$thetotal="";
		}
		$data.='"'.$drow->id.$delim.eshopcleanit($drow->status).$delim.eshopcleanit($drow->transid).$delim;



		$data .=eshopcleanit($showit).$delim.eshopcleanit($itemid).$delim.eshopcleanit($downloadable).$delim.eshopcleanit($myrow->item_qty).$delim.eshopcleanit(number_format($value, 2)).$delim.eshopcleanit(number_format($thetotal, 2)).$delim;

		//date
		$cyear=substr($custom, 0, 4);
		$cmonth=substr($custom, 4, 2);
		$cday=substr($custom, 6, 2);
		$chours=substr($custom, 8, 2);
		$cminutes=substr($custom, 10, 2);
		$thisdate=$cyear."-".$cmonth."-".$cday.' at '.$chours.':'.$cminutes;
		$data.=eshopcleanit($thisdate).$delim;

		$data.=eshopcleanit($drow->first_name)." ".eshopcleanit($drow->last_name).$delim.eshopcleanit($drow->company).$delim.eshopcleanit($drow->phone).$delim.eshopcleanit($drow->email).$delim;

		//address
		$theaddress=$drow->address1."\n".$drow->address2."\n".$drow->city."\n".$drow->zip."\n";
		if($drow->country=='US'){
			$qcode=$wpdb->escape($drow->state);
			$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
			$statezone = $wpdb->get_var("SELECT zone FROM $stable WHERE code='$qcode' limit 1");
			$theaddress.=$qstate."\n";
		}
		$qcode=$wpdb->escape($drow->country);
		$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qcode' limit 1");
		$countryzone = $wpdb->get_var("SELECT zone FROM $ctable WHERE code='$qcode' limit 1");
		$theaddress.=$qcountry;

		if(get_option('eshop_shipping_zone')=='country'){
			$qzone=$countryzone;
		}else{
			$qzone=$statezone;
		}
		$data .=eshopcleanit($theaddress).$delim.eshopcleanit($qzone).$delim;

		$data.=	eshopcleanit($drow->ship_name).$delim.eshopcleanit($drow->ship_company).$delim.eshopcleanit($drow->ship_phone).$delim;

		$shipaddress=$drow->ship_address."\n".$drow->ship_city."\n".$drow->ship_postcode."\n";
		if($drow->ship_country=='US'){
			$qcode=$wpdb->escape($drow->ship_state);
			$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
			$statezone = $wpdb->get_var("SELECT zone FROM $stable WHERE code='$qcode' limit 1");
			$shipaddress.=$qstate."\n";
		}
		$qcode=$wpdb->escape($drow->ship_country);
		$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qcode' limit 1");
		$countryzone = $wpdb->get_var("SELECT zone FROM $ctable WHERE code='$qcode' limit 1");
		$shipaddress.=$qcountry;
		if(get_option('eshop_shipping_zone')=='country'){
			$qzone=$countryzone;
		}else{
			$qzone=$statezone;
		}
		$data .=eshopcleanit($shipaddress).$delim.eshopcleanit($qzone).$delim;

		$data.=eshopcleanit($drow->memo).$delim.eshopcleanit($drow->reference).$delim.eshopcleanit($drow->comments).$cr;

	}

}
$downloadFilename='eshop-data.csv';
if(isset($_GET['os']) && $_GET['os']=='mac'){
	$data=utf8_encode($data);
	$data=iconv('UTF-8', 'macintosh', $data);
}	
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=$downloadFilename");
header("Pragma: no-cache");
header("Expires: 0");		
echo $data;
exit;
function eshopcleanit($data){
	$toreps='"';
	$repswith='""';
	$order   = array("\r\n", "\n", "\r");
	$replace = "\n";
	$data = str_replace($toreps, $repswith, $data);
	$data = wordwrap($data, 75, "\n", 1);
	$data = str_replace($order, $replace, $data);
	return $data;
}
?>