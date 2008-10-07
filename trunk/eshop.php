<?php
if ('eshop.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');
define('ESHOP_VERSION', '2.7.1');

/*
Plugin Name: eShop for Wordpress
Plugin URI: http://wordpress.org/extend/plugins/eshop/
Description: The accessible PayPal shopping cart for WordPress 2.5 and above.
Version: 2.7.1
Author: Rich Pedley 
Author URI: http://quirm.net/

    Copyright 2007  R PEDLEY  (email : rich@quirm.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

load_plugin_textdomain('eshop', 'wp-content/plugins/eshop');
ob_start();
if(!isset($_SESSION['shopcart'])) {
  session_start();
  $setsession=$_SESSION['shopcart'];

}
//if(!session_is_registered('shopcart')){
//	$set=$_SESSION['shopcart'];
//}
/*access level : default of 7 is for editors, change to 8 for administrators.
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
+ DO NOT UPDATE TO NEW SETTINGS UNLESS YOU ALSO UPDATE ESHOPDATE FUNCTION +
+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
*/
$eshoplevel=7;
if (!function_exists('eshop_admin')) {
    /**
     * used by the admin panel hook
     */
    function eshop_admin() {    
        if (function_exists('add_menu_page')) {
        	global $eshoplevel;
        	//goto stats page
            add_menu_page(__('eShop','eshop'), __('eShop','eshop'), $eshoplevel, __FILE__, 'eshop_admin_orders_stats');
            add_submenu_page(__FILE__,__('eShop Orders','eshop'), __('Orders','eshop'),$eshoplevel, basename('eshop_orders.php'),'eshop_admin_orders');
      	    add_submenu_page(__FILE__,__('eShop Shipping','eshop'), __('Shipping','eshop'),$eshoplevel, basename('eshop_shipping.php'),'eshop_admin_shipping');
      	    add_submenu_page(__FILE__,__('eShop Products','eshop'),__('Products','eshop'), $eshoplevel, basename('eshop_products.php'), 'eshop_admin_products');
      	    add_submenu_page(__FILE__,__('eShop Downloads','eshop'),__('Downloads','eshop'), $eshoplevel, basename('eshop_downloads.php'), 'eshop_admin_downloads');
      	    add_submenu_page(__FILE__,__('eShop Base','eshop'),__('Base','eshop'), $eshoplevel, basename('eshop_base.php'), 'eshop_admin_base');
      	    add_submenu_page(__FILE__,__('eShop Style','eshop'), __('Style','eshop'),$eshoplevel, basename('eshop_style.php'),'eshop_admin_style');
			add_submenu_page(__FILE__,__('eShop Email Templates','eshop'), __('Templates','eshop'),$eshoplevel, basename('eshop_templates.php'),'eshop_admin_templates');
      	    add_submenu_page(__FILE__,__('eShop About','eshop'),__('About','eshop'), $eshoplevel, basename('eshop_about.php'), 'eshop_admin_about');
      	    add_submenu_page(__FILE__,__('eShop Help','eshop'),__('Help','eshop'), $eshoplevel, basename('eshop_help.php'), 'eshop_admin_help');
			add_options_page(__('eShop Base Settings','eshop'), __('eShop Base','eshop'),$eshoplevel, basename('eshop_base_settings.php'),'eshop_admin_base_settings');
			add_management_page(__('eShop Base Feed','eshop'), __('eShop Base Feed','eshop'),$eshoplevel, basename('eshop_base_create_feed.php'),'eshop_admin_base_create_feed');
			add_options_page(__('eShop Settings','eshop'), __('eShop','eshop'),$eshoplevel, basename('eshop_settings.php'),'eshop_admin_settings');
      	//test
      		add_submenu_page( 'plugins.php', __('eShop Uninstall','eshop'), __('eShop Uninstall','eshop'),$eshoplevel, basename('eshop_uninstall.php'),'eshop_admin_uninstall');
		//
      	}        
    }
}
//test
	if (!function_exists('eshop_admin_uninstall')) {
		/**
		 * display the uninstall page.
		 */
		 function eshop_admin_uninstall() {
			 include 'eshop_uninstall.php';
		 }
	}
//
if (!function_exists('eshop_admin_help')) {
    /**
     * display the help page.
     */
     function eshop_admin_help() {
         include 'eshop_help.php';
     }
}
if (!function_exists('eshop_admin_about')) {
    /**
     * display the about page.
     */
     function eshop_admin_about() {
         include 'eshop_about.php';
     }
}
if (!function_exists('eshop_admin_settings')) {
    /**
     * display the settings page.
     */
     function eshop_admin_settings() {
         include 'eshop_settings.php';
     }
}
if (!function_exists('eshop_admin_orders_stats')) {
    /**
     * display the order stats.
     */
     function eshop_admin_orders_stats() {
     	//redirect to install instructions on first visit only
		 if('no'==get_option('eshop_first_time')){
			$_GET['action']='Stats';
			include 'eshop_orders.php';
		 }else{
			include 'eshop_about.php';
		 }
		}
}

if (!function_exists('eshop_admin_orders')) {
    /**
     * display the pending orders.
     */
     function eshop_admin_orders() {
		include 'eshop_orders.php';
     }
}
if (!function_exists('eshop_admin_shipping')) {
    /**
     * display the shipping.
     */
     function eshop_admin_shipping() {
         include 'eshop_shipping.php';
     }
}
if (!function_exists('eshop_admin_shipping')) {
    /**
     * display the shipping.
     */
     function eshop_admin_shipping() {
         include 'eshop_shipping.php';
     }
}
if (!function_exists('eshop_admin_states')) {
    /**
     * display the states.
     */
     function eshop_admin_states() {
         include 'eshop_states.php';
     }
}
if (!function_exists('eshop_admin_countries')) {
    /**
     * display the countries.
     */
     function eshop_admin_countries() {
         include 'eshop_countries.php';
     }
}
if (!function_exists('eshop_admin_style')) {
    /**
     * display the CSS.
     */
     function eshop_admin_style() {
         include 'eshop_style.php';
         eshop_form_admin_style();
     }
}
if (!function_exists('eshop_admin_templates')) {
    /**
     * display the email templates.
     */
     function eshop_admin_templates() {
         include 'eshop_templates.php';
         eshop_template_email();
     }
}
if (!function_exists('eshop_admin_downloads')) {
    /**
     * display upload/downloads.
     */
     function eshop_admin_downloads() {
         include 'eshop_downloads.php';
         eshop_downloads_manager();
     }
}
if (!function_exists('eshop_admin_products')) {
    /**
     * display products.
     */
     function eshop_admin_products() {
         include 'eshop_products.php';
         eshop_products_manager();
     }
}
////////////////eshop base test////////////
if (!function_exists('eshop_admin_base')) {
    /**
     * display products.
     */
     function eshop_admin_base() {
         include 'eshop_base.php';
         eshop_base_manager();
     }
}

if (!function_exists('eshop_admin_base_settings')) {
    /**
     * display products.
     */
     function eshop_admin_base_settings() {
         include 'eshop_base_settings.php';
     }
}
if (!function_exists('eshop_admin_base_create_feed')) {
    /**
     * display products.
     */
     function eshop_admin_base_create_feed() {
         include 'eshop_base_create_feed.php';
         eshop_base_create_feed();
     }
}
////////////////////////////////////////////



if (!function_exists('eshop_install')) {
    /**
     * installation routine to set up tables
     */
    function eshop_install() {
        global $wpdb, $user_level, $wp_rewrite, $wp_version;
        include_once ('cart-functions.php');
        if( eshop_files_directory()!=0 ){
       		eshop_download_directory();
       		eshop_files_directory();
       		//mdy
       		$eshoptime=mktime(0, 0, 0, date('n'), date('j'), date('Y'));
       		wp_schedule_event($eshoptime, 'daily', 'eshop_event');
       		include 'eshop_install.php';
       	}else{
       		deactivate_plugins('eshop/eshop.php'); //Deactivate ourself
			wp_die(__('ERROR! This plugin requires that the wp_content directory is writable.','eshop')); //add a more descriptive message of course.
		}
    }
}

if (!function_exists('eshop_deactivate')) {
    /**
     * mostly handled by uninstall - this just resets the cron
     */
    function eshop_deactivate() {
    	wp_clear_scheduled_hook('eshop_event');
    }
}
//cron
add_action('eshop_event', 'eshop_cron');
if (!function_exists('eshop_cron')) {
	function eshop_cron(){
		global $wpdb;
		if(get_option('eshop_cron_email')!=''){
			$dtable=$wpdb->prefix.'eshop_orders';
			$max = $wpdb->get_var("SELECT COUNT(id) FROM $dtable WHERE status='Pending'");
			if($max>0){
				$to = get_option('eshop_cron_email');    //  your email
				$body =  __("You may have some outstanding orders to process\n\nregards\n\nYour eShop plugin");
				$body .="\n\n".get_bloginfo('url').'/wp-admin/admin.php?page=eshop_orders.php'."\n";
				if(get_option('eshop_business')!=''){
					$headers='From: '.get_bloginfo('name').' <'.get_option('eshop_business').">\n";
				}else{
					$headers='';
				}
				$subject=get_bloginfo('name').__(": outstanding orders");
				wp_mail($to, $subject, $body, $headers);
			}
		}
	}
}
if (!function_exists('eshop_show_cancel')) {
	function eshop_show_cancel(){
		if(isset($_GET['action']) && $_GET['action']=='cancel'){
			$echo ="<h3 class=\"error\">".__('The order was canceled at PayPal.','eshop')."</h3>";
			$echo.='<p>'.__('We have not emptied your shopping cart in case you want to make changes.','eshop').'</p>';
		}
		return $echo;
	}
}
if (!function_exists('eshop_show_success')) {
	function eshop_show_success(){
		global $wpdb;
		if(isset($_GET['action']) && $_GET['action']=='success'){
			$detailstable=$wpdb->prefix.'eshop_orders';
			$dltable=$wpdb->prefix.'eshop_download_orders';
			if(get_option('eshop_status')=='live'){
				$txn_id = $wpdb->escape($_POST['txn_id']);
			}else{
				$txn_id = __('TEST-','eshop').$wpdb->escape($_POST['txn_id']);
			}
			$checkid=$wpdb->get_var("select checkid from $detailstable where transid='$txn_id' && downloads='yes' limit 1");
			$checkstatus=$wpdb->get_var("select status from $detailstable where transid='$txn_id' && downloads='yes' limit 1");

			if(($checkstatus=='Sent' || $checkstatus=='Completed') && $checkid!=''){
				$row=$wpdb->get_row("select email,code from $dltable where checkid='$checkid' and downloads>0 limit 1");
				if($row->email!='' && $row->code!=''){
					//display form only if there are downloads!
						$echo = '<form method="post" class="dform" action="'.get_permalink(get_option('eshop_show_downloads')).'">
					<p class="submit"><input name="email" type="hidden" value="'.$row->email.'" /> 
					<input name="code" type="hidden" value="'.$row->code.'" /> 
					<input type="submit" id="submit" class="button" name="Submit" value="'.__('View your downloads','eshop').'" /></p>
					</form>';
				}
			}
			return $echo;  
		}
	}
}
if (!function_exists('eshop_show_cart')) {
	function eshop_show_cart() {
		include_once 'cart.php';
		return eshop_cart($_POST);
	}
}
if (!function_exists('eshop_show_checkout')) {
	function eshop_show_checkout(){
		include_once 'checkout.php';
		return eshop_checkout($_POST);
	}
}
if (!function_exists('eshop_show_downloads')) {
	function eshop_show_downloads(){
		include_once 'purchase-downloads.php';
		return eshop_downloads($_POST);
	}
}
include_once 'cart-functions.php';
include_once( 'eshop-shortcodes.php' );
add_shortcode('eshop_show_downloads', 'eshop_show_downloads');
add_shortcode('eshop_random_products', 'eshop_list_random');
add_shortcode('eshop_list_featured', 'eshop_list_featured');
add_shortcode('eshop_list_subpages', 'eshop_list_subpages');
add_shortcode('eshop_show_checkout', 'eshop_show_checkout');
add_shortcode('eshop_show_cart', 'eshop_show_cart');
add_shortcode('eshop_show_shipping', 'eshop_get_shipping');
add_shortcode('eshop_show_cancel', 'eshop_show_cancel');
add_shortcode('eshop_show_success', 'eshop_show_success');
add_shortcode('eshop_list_new', 'eshop_list_new');

//add credits
add_action('wp_footer', 'eshop_visible_credits');

if (!function_exists('eshop_contains')) {
    /**
     * Return true if one string can be found in another
     * as used above
     * @param $haystack the string to search *in*
     * @param $needle the string to search *for*
     */
    function eshop_contains($haystack, $needle){
        $pos = strpos($haystack, $needle);
        
        if ($pos === false) {
            return false;
        }
        else {
            return true;
        }
    }   
}
if (!function_exists('eshop_admin_head')) {
    /**
     * javascript functions & stylesheet to be included in the ADMIN WP head
     */
    function eshop_admin_head() {
        echo '   <link title="eShop Admin Styles" rel="stylesheet" href="' . get_bloginfo('url') . '/wp-content/plugins/eshop/eshop.css" type="text/css" media="screen" />'."\n";
        echo '   <link title="eShop Print Styles" rel="stylesheet" href="' . get_bloginfo('url') . '/wp-content/plugins/eshop/eshop-print.css" type="text/css" media="print" />'."\n";
		echo '   <script type="text/javascript">
     //<![CDATA[
      function checkedAll (id, checked) {
	  var el = document.getElementById(id);
	  for (var i = 0; i < el.elements.length; i++) {
	  el.elements[i].checked = checked;
        }
      }
    //]]> 
    </script>'."\n";
    }
}
if (!function_exists('eshop_wp_head')) {
    /**
     * javascript functions & stylesheet to be included in the FRONT END WP head
     */
    function eshop_wp_head() {
    	$eshopurl=eshop_files_directory();
    	
    	if(get_option('eshop_style')=='yes'){
        	echo '<link rel="stylesheet" href="' . $eshopurl['1'] . 'eshop.css" type="text/css" media="screen" />'."\n";
        }
       /*
       if(isset($_GET['action']) && $_GET['action']=='redirect'){
        	//only add necessary javascript if on the correct page
        	//this automatically submit the redirect form
        	//wish it was a bit quicker, but that is paypals fault.
        	if(get_option('eshop_status')!='live'){
        		echo '<script src="'.$eshopurl['1'].'eshop-onload.js" type="text/javascript"></script>';
        	}
		}
		*/
		if(isset($_GET['action']) && $_GET['action']=='success'){
			$_SESSION = array();
			session_destroy();
		}
    }
}

//this automatically hides the relevant pages
include_once ('cart-functions.php');
add_filter('wp_list_pages_excludes', 'eshop_add_excludes');
//fold the page menu as it is likely to get long...
//this can be removed in a theme by using remove_filter...
//add option to make it settable
add_option('eshop_fold_menu', 'yes');

if(get_option('eshop_fold_menu') == 'yes'){
	add_filter('wp_list_pages_excludes', 'eshop_fold_menus');
}



/**
 * eshop wordpress actions
 */
add_action('admin_head', 'eshop_admin_head');
add_action('wp_head', 'eshop_wp_head');
add_action('admin_menu', 'eshop_admin');

/* activations */
register_activation_hook(__FILE__,'eshop_install');

/*deactivation*/
register_deactivation_hook( __FILE__, 'eshop_deactivate' );

/**
* eshop download products - need to process afore page is rendered
* so this has to be called like this - unless anyone can come up with a better idea!
*/
if (isset($_POST['eshoplongdownloadname'])){
//long silly name to ensure it isn't used elsewhere!
	ob_start();
	eshop_download_the_product($_POST); 
	ob_flush();
}

/***
* default options(mainly for settings) go here
*/
add_option('eshop_style', 'yes');
add_option('eshop_method','paypal');
add_option('eshop_records','10');
add_option('eshop_options_num','3');
add_option('eshop_downloads_num','3');
add_option('eshop_random_num','5');
add_option('eshop_pagelist_num','5');
add_option('eshop_cart_nostock','Out of Stock');
add_option('eshop_status', 'testing');
add_option('eshop_currency_symbol','&pound;');
add_option('eshop_currency','GBP');
add_option('eshop_location','GB');
add_option('eshop_sudo_cat','1');
add_option('eshop_shipping', '1');
add_option('eshop_shipping_zone', 'country');
add_option('eshop_show_zones','no');
add_option('eshop_credits', 'yes');
add_option('eshop_stock_control','no');
add_option('eshop_show_stock','no');
add_option('eshop_first_time', 'yes');
add_option('eshop_downloads_only', 'no');

//add eshop product entry onto the post and page edit pages.
include_once( 'eshop-custom-fields.php' );

//displays the add to cart form
include_once( 'eshop-get-custom.php' );
add_filter('the_content', 'eshop_boing');


add_action('init','eshopdata');
function eshopdata(){
	global $current_user, $wp_roles;
	//when using this code block use if($role
	if( $current_user->id )  {
		foreach($wp_roles->role_names as $role => $Role) {
			if (array_key_exists($role, $current_user->caps))
				break;
		}
	}
	get_currentuserinfo() ;
	global $user_level;
	global $eshoplevel;
	//for now use this - will need to check what will break on updating older plugins before implementing change
	if ($user_level >= $eshoplevel) {
	//if($role=='administrator' || $role=='editor'){
	//when we change $eshoplevel to tie it to an permission use this one
	//if(current_user_can('manage_options')){
		//this block is used solely for back end downloads *ONLY*
		if(isset($_GET['eshopdl'])){
			include 'eshop-all-data.php';
		}
		if(isset($_GET['eshopbasedl'])){
			include 'eshop_base_feed.php';
		}
	}
	if(isset($_GET['action']) && $_GET['action']=='ipn'){
		include_once 'paypal.php';
		exit;
	}
}
/* the widget */
include 'eshop_widget.php';
?>