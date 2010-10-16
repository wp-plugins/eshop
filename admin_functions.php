<?php
if ('admin-functions.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');
     
if (!function_exists('eshopdata')) {
	function eshopdata(){
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
     
if (!function_exists('eshop_admin')) {
    /**
     * used by the admin panel hook
     */
    function eshop_admin() {    
		global $wp_version;
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
		if (eshop_wp_version('3'))
			$page[]=add_users_page(__('eShop Orders','eshop'), __('My Orders','eshop'),'read', basename('my_orders.php'),'eshop_user_orders');

		$page[]=add_theme_page(__('eShop Style','eshop'), __('eShop','eshop'),'eShop_admin', basename('eshop_style.php'),'eshop_admin_style');
		//$page[]=add_options_page(__('eShop Settings','eshop'), __('eShop','eshop'),'eShop_admin', basename('eshop_settings.php'),'eshop_admin_settings');
		$page[]=add_submenu_page( 'plugins.php', __('eShop Uninstall','eshop'), __('eShop Uninstall','eshop'),'eShop_admin', basename('eshop_uninstall.php'),'eshop_admin_uninstall');
		$help='
		<p><strong>' . __('eShop help:') . '</strong></p>
		<ul>
		<li>'.__('<a href="http://quirm.net/wiki/eshop/">eShop Wiki</a>','eshop').'</li>
		<li>'.__('<a href="http://wordpress.org/tags/eshop">Wordpress forums</a>','eshop').'</li>
		<li>'.__('<a href="http://quirm.net/forum/forum.php?id=14">Quirm.net</a>','eshop').'</li>
		</ul>';
		foreach ($page as $paged){
			add_action('admin_print_styles-' . $paged, 'eshop_admin_styles');
			if($paged!='users_page_my_orders' && $paged!='')
				add_contextual_help($paged,$help); 
		}
		if(is_admin())
			include WP_PLUGIN_DIR.'/eshop/user.php';
    
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

if (!function_exists('eshop_show_credits')) {
	function eshop_show_credits(){
	//for admin
	$version = explode(".", ESHOP_VERSION);
	?>
	<p class="creditline"><?php _e('Powered by','eshop'); ?> <a href="http://www.quirm.net/" title="<?php _e('Created by','eshop'); ?> Rich Pedley">eShop</a>
	<dfn title="<?php echo ESHOP_VERSION; ?>">v.<?php echo $version[0]; ?></dfn></p> 
	<?php 
	}
}
if (!function_exists('eshop_update_nag')) {
	function eshop_update_nag() {
		global $eshopoptions;
		if ( $eshopoptions['version']!='' && $eshopoptions['version'] >= ESHOP_VERSION )
			return false;

		if ( current_user_can('manage_options') )
			$msg = sprintf( __('<strong>eShop %1$s</strong> is now ready to use. <strong>You must now <a href="%2$s">deactivate and re-activate the plugin</a></strong>.','eshop'), ESHOP_VERSION, 'plugins.php#active-plugins-table' );
		else
			$msg = sprintf( __('<strong>eShop %1$s<strong> needs updating! Please notify the site administrator.','eshop'), ESHOP_VERSION );

		echo "<div id='update-nag'>$msg</div>";
	}
}
if (!function_exists('eShopPluginUpdateMessage')) {
	function eShopPluginUpdateMessage (){
		define('PLUGIN_README_URL',  'http://svn.wp-plugins.org/eshop/trunk/readme.txt');
		$response = wp_remote_get( PLUGIN_README_URL, array ('user-agent' => 'WordPress/eShop ' . ESHOP_VERSION . '; ' . get_bloginfo( 'url' ) ) );
		if ( ! is_wp_error( $response ) || is_array( $response ) ) {
			$data = $response['body'];
			$bits=explode('== Changelog ==',$data);
			$pieces=explode('Version '.ESHOP_VERSION,$bits['1']);
			echo '<div id="eshop-upgrade"><p>'.nl2br(trim($pieces [0])).'</p></div>';
		}else{
			printf(__('<br /><strong style="color:#800;">Note:</strong> Please review the <a class="thickbox" href="%1$s">changelog</a> before upgrading.','eshop'),'plugin-install.php?tab=plugin-information&amp;plugin=eshop&amp;TB_iframe=true&amp;width=640&amp;height=594');
		}
	}
}
?>