<?php
/*  based on:
 * PHP ePN IPN Integration Class Demonstration File
 *  4.16.2005 - Micah Carrick, email@micahcarrick.com
*/
global $wpdb;
$detailstable=$wpdb->prefix.'eshop_orders';

//sanitise
include_once(WP_PLUGIN_DIR.'/eshop/cart-functions.php');
$_POST=sanitise_array($_POST);

include_once (WP_PLUGIN_DIR.'/eshop/epn/index.php');
// Setup class
require_once(WP_PLUGIN_DIR.'/eshop/epn/epn.class.php');  // include the class file
$p = new epn_class;             // initiate an instance of the class

$p->epn_url = 'https://www.eProcessingNetwork.com/cgi-bin/dbe/order.pl';     // epn url

$this_script = get_option('siteurl');
global $wp_rewrite;
if(get_option('eshop_checkout')!=''){
	if( $wp_rewrite->using_permalinks()){
		$p->autoredirect=get_permalink(get_option('eshop_checkout')).'?eshopaction=redirect';
	}else{
		$p->autoredirect=get_permalink(get_option('eshop_checkout')).'&amp;eshopaction=redirect';
	}
}else{
	$p->autoredirect=get_permalink(get_option('eshop_checkout')).'&amp;eshopaction=redirect';
}

// if there is no action variable, set the default action of 'process'
if (empty($_GET['eshopaction'])) $_GET['eshopaction'] = 'process';  

switch ($_GET['eshopaction']) {
    case 'redirect':
    	//auto-redirect bits
		header('Cache-Control: no-cache, no-store, must-revalidate'); //HTTP/1.1
		header('Expires: Sun, 01 Jul 2005 00:00:00 GMT');
		header('Pragma: no-cache'); //HTTP/1.0

		//enters all the data into the database
		$checkid=md5($_POST['RefNr']);
		//
		orderhandle($_POST,$checkid);
		$_POST['ID']=$checkid;
		$p = new epn_class; 
		$p->epn_url = 'https://www.eProcessingNetwork.com/cgi-bin/dbe/order.pl';     // epn url
		$echoit.=$p->eshop_submit_epn_post($_POST);
		break;
        
   case 'process':      // Process and order...
	
		// There should be no output at this point.  To process the POST data,
		// the submit_epn_post() function will output all the HTML tags which
		// contains a FORM which is submited instantaneously using the BODY onload
		// attribute.  In other words, don't echo or printf anything when you're
		// going to be calling the submit_epn_post() function.
		
		// This is where you would have your form validation  and all that jazz.
		// You would take your POST vars and load them into the class like below,
		// only using the POST values instead of constant string expressions.

		// For example, after ensureing all the POST variables from your custom
		// order form are valid, you might have:
		//
		// $p->add_field('first_name', $_POST['first_name']);
		// $p->add_field('last_name', $_POST['last_name']);
      
      /****** The order has already gone into the database at this point ******/
      
		global $wp_rewrite,$blog_id;

		//goes direct to this script as nothing needs showing on screen.
		if(get_option('eshop_cart_success')!=''){
			if( $wp_rewrite->using_permalinks()){
				$ilink=get_permalink(get_option('eshop_cart_success')).'?eshopaction=success&amp;epn=ok';
				$idlink=get_permalink(get_option('eshop_cart_success')).'?eshopaction=success&amp;epn=fail';
			}else{
				$ilink=get_permalink(get_option('eshop_cart_success')).'&amp;eshopaction=success&amp;epn=ok';
				$idlink=get_permalink(get_option('eshop_cart_success')).'&amp;eshopaction=success&amp;epn=fail';
			}
		}else{
			$ilink=get_permalink(get_option('eshop_checkout')).'&amp;eshopaction=success&amp;epn=ok';
			$idlink=get_permalink(get_option('eshop_checkout')).'&amp;eshopaction=success&amp;epn=fail';
		}
		$p->add_field('ReturnApprovedURL', $ilink);
		$p->add_field('ReturnDeclinedURL', $idlink);

		$p->add_field('shipping_1', number_format($_SESSION['shipping'.$blog_id],2));
		foreach($_POST as $name=>$value){
			//have to do a discount code check here - otherwise things just don't work - but fine for free shipping codes
			if(strstr($name,'amount_')){
				if(isset($_SESSION['eshop_discount'.$blog_id]) && eshop_discount_codes_check()){
					$chkcode=valid_eshop_discount_code($_SESSION['eshop_discount'.$blog_id]);
					if($chkcode && apply_eshop_discount_code('discount')>0){
						$discount=apply_eshop_discount_code('discount')/100;
						$value = number_format(round($value-($value * $discount), 2),2);
						$vset='yes';
					}
				}
				if(is_discountable(calculate_total())!=0 && !isset($vset)){
					$discount=is_discountable(calculate_total())/100;
					$value = number_format(round($value-($value * $discount), 2),2);
				}
			}
			
			$p->add_field($name, $value);
		}

		if(get_option('eshop_status')!='live' && is_user_logged_in()||get_option('eshop_status')=='live'){
			$echoit .= $p->submit_epn_post(); // submit the fields to epn
    	}
      	break;
   
}
?>