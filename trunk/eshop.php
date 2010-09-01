<?php
if ('eshop.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');
if(!defined('ESHOP_VERSION'))
	define('ESHOP_VERSION', '5.6.4');
/*
Plugin Name: eShop for Wordpress
Plugin URI: http://wordpress.org/extend/plugins/eshop/
Description: The accessible shopping cart for WordPress 3.0 and above.
Version: 5.6.4
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

load_plugin_textdomain('eshop', false, dirname( plugin_basename( __FILE__ ) ) );
//grab all options here in one go
$eshopoptions = get_option('eshop_plugin_settings');

/* eShop general (or not sure where they are utilised! */
add_action('init','eshopsession',1);
if (!function_exists('eshopsession')) {
	function eshopsession(){
	 	if(!session_id()){
	    	session_start();
    	}
    }
}
/* cron */
add_action('eshop_event', 'eshop_cron');
if (!function_exists('eshop_cron')) {
	function eshop_cron(){
		global $wpdb,$eshopoptions;
		if($eshopoptions['cron_email']!=''){
			$dtable=$wpdb->prefix.'eshop_orders';
			$max = $wpdb->get_var("SELECT COUNT(id) FROM $dtable WHERE status='Completed' OR status='Waiting'");
			if($max>0){
				$to = $eshopoptions['cron_email'];    //  your email
				$body =  __("You may have some outstanding orders to process\n\nregards\n\nYour eShop plugin",'eshop');
				$body .="\n\n".get_bloginfo('url').'/wp-admin/admin.php?page=eshop_orders.php&action=Dispatch'."\n";
				$headers=eshop_from_address();
				$subject=get_bloginfo('name').__(": outstanding orders");
				wp_mail($to, $subject, $body, $headers);
			}
		}
	}
}
include_once 'cart-functions.php';
/* the widget */
include_once 'eshop_widget.php';
//make sure theme thumbnail support is on, even for those themes that don't use it.
add_theme_support('post-thumbnails');

if(is_admin()){
	/* eShop ADMIN SPECIFIC HERE */
	include_once 'admin_functions.php';
	/* activations */
	register_activation_hook(__FILE__,'eshop_install');
	/*deactivation*/
	register_deactivation_hook( __FILE__, 'eshop_deactivate' );
	include_once 'eshop_settings.php';
	//add eshop product entry onto the post and page edit pages.
	include_once( 'eshop-product-entry.php' );
	include_once( 'eshop-eshortcodes.php');
	add_action('admin_init', 'eshop_admin_init');
	add_action('admin_menu', 'eshop_admin');
	add_action( 'admin_notices', 'eshop_update_nag');
}else{
	include_once 'public_functions.php';
	/* eShop Public facing only */
	include_once( 'eshop-shortcodes.php' );
	//add credits
	add_action('wp_footer', 'eshop_visible_credits');
	//process cart
	add_action ('init','eshop_cart_process');
	//displays the add to cart form
	include_once( 'eshop-add-cart.php' );
	add_filter('the_content', 'eshop_boing');
}
?>