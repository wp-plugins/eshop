<?php
if ('eshop.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');
define('ESHOP_VERSION', '5.4.2');

/*
Plugin Name: eShop for Wordpress
Plugin URI: http://wordpress.org/extend/plugins/eshop/
Description: The accessible shopping cart for WordPress 3.0 and above.
Version: 5.4.2
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
/* eShop ADMIN STUFF HERE */
add_action('admin_init', 'eshop_admin_init');
add_action('admin_menu', 'eshop_admin');
if (!function_exists('eshop_admin')) {
    /**
     * used by the admin panel hook
     */
    function eshop_admin() {    
		global $wp_version;
		//goto stats page
		$page[]=add_menu_page(__('eShop','eshop'), __('eShop','eshop'), 'eShop', 'eshop.php', 'eshop_admin_orders_stats',WP_PLUGIN_URL.'/eshop/eshop.png');
		$page[]=add_submenu_page('eshop.php',__('eShop Stats','eshop'), __('Stats','eshop'),'eShop', 'eshop.php','eshop_admin_orders_stats');
		$page[]=add_submenu_page('eshop.php',__('eShop Orders','eshop'), __('Orders','eshop'),'eShop_admin', basename('eshop_orders.php'),'eshop_admin_orders');
		$page[]=add_submenu_page('eshop.php',__('eShop Shipping','eshop'), __('Shipping','eshop'),'eShop_admin', basename('eshop_shipping.php'),'eshop_admin_shipping');
		$page[]=add_submenu_page('eshop.php',__('eShop Products','eshop'),__('Products','eshop'), 'eShop', basename('eshop_products.php'), 'eshop_admin_products');
		$page[]=add_submenu_page('eshop.php',__('eShop Options','eshop'),__('Option Sets','eshop'), 'eShop', basename('eshop_options.php'), 'eshop_admin_options');
		$page[]=add_submenu_page('eshop.php',__('eShop Downloads','eshop'),__('Downloads','eshop'), 'eShop_admin', basename('eshop_downloads.php'), 'eshop_admin_downloads');
		$page[]=add_submenu_page('eshop.php',__('eShop Discount Codes','eshop'),__('Discount Codes','eshop'), 'eShop_admin', basename('eshop_discount_codes.php'), 'eshop_discount_codes');
		$page[]=add_submenu_page('eshop.php',__('eShop Base','eshop'),__('Base','eshop'), 'eShop_admin', basename('eshop_base.php'), 'eshop_admin_base');
		$page[]=add_submenu_page('eshop.php',__('eShop Email Templates','eshop'), __('Emails','eshop'),'eShop_admin', basename('eshop_templates.php'),'eshop_admin_templates');
		$page[]=add_submenu_page('eshop.php',__('eShop About','eshop'),__('About','eshop'), 'eShop', basename('eshop_about.php'), 'eshop_admin_about');
		$page[]=add_submenu_page('eshop.php',__('eShop Help','eshop'),__('Help','eshop'), 'eShop', basename('eshop_help.php'), 'eshop_admin_help');
		if (eshop_wp_version('3'))
			$page[]=add_users_page(__('eShop Orders','eshop'), __('My Orders','eshop'),'read', basename('my_orders.php'),'eshop_user_orders');

		$page[]=add_theme_page(__('eShop Style','eshop'), __('eShop','eshop'),'eShop_admin', basename('eshop_style.php'),'eshop_admin_style');
		$page[]=add_options_page(__('eShop Settings','eshop'), __('eShop','eshop'),'eShop_admin', basename('eshop_settings.php'),'eshop_admin_settings');
		$page[]=add_submenu_page( 'plugins.php', __('eShop Uninstall','eshop'), __('eShop Uninstall','eshop'),'eShop_admin', basename('eshop_uninstall.php'),'eshop_admin_uninstall');
		$help='
		<p><strong>' . __('Extra eShop help:') . '</strong><br />
		'.__('<a href="http://wordpress.org/tags/eshop">Wordpress forums</a>','eshop').'<br />
		'.__('<a href="http://quirm.net/forum/forum.php?id=14">Quirm.net</a>','eshop').'</p>
		';
		foreach ($page as $paged){
			add_action('admin_print_styles-' . $paged, 'eshop_admin_styles');
			if($paged!='users_page_my_orders')
				add_contextual_help($paged,$help); 
		}
		if(is_admin())
			include 'user.php';
    
    }
}
if (!function_exists('eshop_admin_init')) {
	function eshop_admin_init(){
		/* Register our stylesheet. */
		wp_register_style('eShopAdminStyles', WP_PLUGIN_URL . '/eshop/eshop.css');
		wp_register_style('eShopAdminPrint', WP_PLUGIN_URL . '/eshop/eshop-print.css','','','print');
		wp_register_script('eShopCheckAll', WP_PLUGIN_URL . '/eshop/eshopcheckall.js', array('jquery'));
		wp_enqueue_style('eShopAdminStyles');

	}
}

if (!function_exists('eshop_admin_styles')) {
	function eshop_admin_styles(){
		/*
		 * It will be called only on your plugin pages, enqueue our stylesheet here
		 */
		wp_enqueue_style('eShopAdminPrint');
		wp_enqueue_script('eShopCheckAll');

	}
}
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
     	global $eshopoptions;
     	//redirect to install instructions on first visit only
		 if('no'==$eshopoptions['first_time']){
			$_GET['action']='Stats';
			include 'eshop_orders.php';
		 }else{
			include 'eshop_about.php';
		 }
	}
}

if (!function_exists('eshop_user_orders')) {
    /**
     * display the pending orders.
     */
     function eshop_user_orders() {
		include 'eshop_user_orders.php';
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
if (!function_exists('eshop_admin_options')) {
    /**
     * display the pending orders.
     */
     function eshop_admin_options() {
		include 'eshop_options.php';
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
if (!function_exists('eshop_discount_codes')) {
    /**
     * discount codes.
     */
     function eshop_discount_codes() {
         include 'eshop_discount_codes.php';
         eshop_discounts_manager();
     }
}
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
/* INSTALL UNINSTALL */
/* activations */
register_activation_hook(__FILE__,'eshop_install');
/*deactivation*/
register_deactivation_hook( __FILE__, 'eshop_deactivate' );

if (!function_exists('eshop_install')) {
    /**
     * installation routine to set up tables
     */
    function eshop_install() {
        global $wpdb, $user_level, $wp_rewrite, $wp_version;
        include_once ('cart-functions.php');
        include 'eshop_install.php';
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
				$body =  __("You may have some outstanding orders to process\n\nregards\n\nYour eShop plugin");
				$body .="\n\n".get_bloginfo('url').'/wp-admin/admin.php?page=eshop_orders.php&action=Dispatch'."\n";
				$headers=eshop_from_address();
				$subject=get_bloginfo('name').__(": outstanding orders");
				wp_mail($to, $subject, $body, $headers);
			}
		}
	}
}
/* includes */
include_once 'cart-functions.php';
include_once( 'eshop-shortcodes.php' );
// shortcodes now defined in that file
/* the widget */
include_once 'eshop_widget.php';

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

if (!function_exists('eshop_pre_wp_head')) {
    function eshop_pre_wp_head() {
    	global $wp_query;
		if(isset($wp_query->query_vars['eshopaction'])) {
   	 		$eshopaction = urldecode($wp_query->query_vars['eshopaction']);
		   	if($eshopaction=='success'){
		   		//destroy cart
				$_SESSION = array();
				//session_destroy();
			}
			//we need to buffer output on a few pages
			if($eshopaction=='redirect'){
				ob_start();
			}
			if($eshopaction=='webtopayipn'){
				include_once 'webtopay.php';
				exit;
			}
			if($eshopaction=='paypalipn'){
				include_once 'paypal.php';
				exit;
			}
			if($eshopaction=='paysonipn'){
				include_once 'payson.php';
				//exit;
			}
			if($eshopaction=='authorizenetipn'){
				include_once 'authorizenet.php';
				//exit;
			}
			if($eshopaction=='idealliteipn'){
				include_once 'ideallite.php';
				//exit;
			}
		}
		if(isset($_POST['eshopident_1'])){
			ob_start();
		}
		
    }
}
if (!function_exists('eshop_wp_head_add')) {
    /**
     * javascript functions
     */
    function eshop_wp_head_add() {
    	global $wp_query,$eshopoptions,$wpdb;
    	$eshopurl=eshop_files_directory();
		if(isset($wp_query->query_vars['eshopaction'])) {
   	 		$eshopaction = urldecode($wp_query->query_vars['eshopaction']);
		   	if($eshopaction=='redirect'){
				//this automatically submits the redirect form
				if($eshopoptions['status']=='live'){
					wp_register_script('eShopSubmit', $eshopurl['1'].'eshop-onload.js', array('jquery'));
					wp_enqueue_script('eShopSubmit');
				}
			}
		}
		
    }
}
/**
 * eshop wordpress actions
 */
if (!function_exists('add_eshop_query_vars')) {
	function add_eshop_query_vars($aVars) {
		$aVars[] = "eshopaction";    // represents the name of the product category as shown in the URL
		$aVars[] = "eshopaz";
		$aVars[] = "eshopall";
		$aVars[] = "_p";
		return $aVars;
	}
}
add_filter('query_vars', 'add_eshop_query_vars');
add_action('wp', 'eshop_pre_wp_head');
add_action('wp_print_scripts', 'eshop_wp_head_add');

add_action('wp_print_styles', 'eshop_stylesheet');
if (!function_exists('eshop_stylesheet')) {
	function eshop_stylesheet() {
		global $eshopoptions;
		$eshopurl=eshop_files_directory();
		if(@file_exists(STYLESHEETPATH.'/eshop.css')) {
			$myStyleUrl = get_stylesheet_directory_uri().'/eshop.css';
			$myStyleFile=STYLESHEETPATH.'/eshop.css';
		}elseif($eshopoptions['style']=='yes'){
			$myStyleUrl = $eshopurl['1'] . 'eshop.css';
			$myStyleFile=$eshopurl['0'] . 'eshop.css';
		}
		if ( file_exists($myStyleFile) ) {
			wp_register_style('myStyleSheets', $myStyleUrl);
			wp_enqueue_style( 'myStyleSheets');
		}
	}
}
add_filter('style_loader_src','eshop_unversion');
//removes version number from css, needed for multisite
function eshop_unversion($src) {
    if( strpos($src,'eshop.css') )
        $src=remove_query_arg('ver', $src);
    return $src;
}
//this automatically hides the relevant pages
add_filter('wp_list_pages_excludes', 'eshop_add_excludes');
//fold the page menu as it is likely to get long...
//this can be removed in a theme by using remove_filter...
//add option to make it settable
if($eshopoptions['fold_menu'] == 'yes'){
	add_filter('wp_list_pages_excludes', 'eshop_fold_menus');
}

/**
* eshop download products - need to process afore page is rendered
* so this has to be called like this - unless anyone can come up with a better idea!
*/
if (isset($_POST['eshoplongdownloadname'])){
//long silly name to ensure it isn't used elsewhere!
	eshop_download_the_product($_POST); 
}

//add eshop product entry onto the post and page edit pages.
include_once( 'eshop-product-entry.php' );
include_once( 'eshop-eshortcodes.php');

//displays the add to cart form
include_once( 'eshop-add-cart.php' );
add_filter('the_content', 'eshop_boing');

add_action('init','eshopdata',1);
if (!function_exists('eshopdata')) {
	function eshopdata(){
	 	if(!session_id()){
	    	session_start();
    	}
		global $current_user, $wp_roles, $post;
		get_currentuserinfo() ;
		if(current_user_can('eShop')){
			//this block is used solely for back end downloads *ONLY*
			if(isset($_GET['eshopdl'])){
				include 'eshop-all-data.php';
			}
			if(isset($_GET['eshopbasedl'])){
				include 'eshop_base_feed.php';
			}
		}
		
	}
}

//add images to the search page if set
if('no' != $eshopoptions['search_img']){
	add_filter('the_excerpt','eshop_excerpt_img');
	add_filter('the_content','eshop_excerpt_img');
}

add_action( 'admin_notices', 'eshop_update_nag');
add_theme_support('post-thumbnails');
?>