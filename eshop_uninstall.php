<?php
if ('eshop_uninstall.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');
     
//See eshop.php for information and license terms

if (file_exists(ABSPATH . 'wp-includes/l10n.php')) {
    require_once(ABSPATH . 'wp-includes/l10n.php');
}
else {
    require_once(ABSPATH . 'wp-includes/wp-l10n.php');
}
global $wpdb;

echo '<div class="wrap"><h2>'.__('eShop Uninstall','eshop').'</h2>';

if(isset($_POST['delete'])){
	echo '<h3>'.__('Confirm uninstall of eShop','eshop').'</h3>';
	echo '<p>'.__('Are you really sure you want to do this? All information will be lost and this action is irreversible.','eshop').'</p>';
	?>
	<form action="plugins.php?page=eshop_uninstall.php" method="post">
	<p class="submit">
	<input type="submit" id="uninstall" name="uninstall" value="<?php _e('Confirm Uninstall','eshop'); ?>" />
	</p>
	</form>
	<?php
}elseif(isset($_POST['uninstall'])){
	//required for deleting meta - grab bfore its deleted
	$numoptions=get_option('eshop_options_num');
	echo '<ul>';

	//tables
	$etable[] = $wpdb->prefix . "eshop_states";
	$etable[] = $wpdb->prefix . "eshop_shipping_rates";
	$etable[] = $wpdb->prefix . "eshop_order_items";
	$etable[] = $wpdb->prefix . "eshop_orders";
	$etable[] = $wpdb->prefix ."eshop_stock";
	$etable[] = $wpdb->prefix ."eshop_downloads";
	$etable[] = $wpdb->prefix ."eshop_download_orders";
	$etable[] = $wpdb->prefix . "eshop_countries";
	$etable[] = $wpdb->prefix ."eshop_base_products";
	foreach($etable as $table){
		if ($wpdb->get_var("show tables like '$table'") == $table) {
			//delete it
			$wpdb->query("DROP TABLE IF EXISTS $table");
		}
	}
	echo '<li>'.__('MySQL Tables - deleted','eshop').'</li>';

	//options
	$epages[] = 'eshop_business';
	$epages[] = 'eshop_cart';
	$epages[] = 'eshop_cart_cancel';
	$epages[] = 'eshop_cart_nostock';
	$epages[] = 'eshop_cart_shipping';
	$epages[] = 'eshop_cart_success';
	$epages[] = 'eshop_checkout';
	$epages[] = 'eshop_credits';
	$epages[] = 'eshop_cron_email';
	$epages[] = 'eshop_currency';
	$epages[] = 'eshop_currency_symbol';
	$epages[] = 'eshop_downloads_num';
	$epages[] = 'eshop_first_time';
	$epages[] = 'eshop_location';
	$epages[] = 'eshop_method';
	$epages[] = 'eshop_options_num';
	$epages[] = 'eshop_pagelist_num';
	$epages[] = 'eshop_random_num';
	$epages[] = 'eshop_records';
	$epages[] = 'eshop_shipping';
	$epages[] = 'eshop_shipping_zone';
	$epages[] = 'eshop_show_downloads';
	$epages[] = 'eshop_show_stock'; 
	$epages[] = 'eshop_show_zones';
	$epages[] = 'eshop_status'; 
	$epages[] = 'eshop_stock_control'; 
	$epages[] = 'eshop_style';
	$epages[] = 'eshop_sudo_cat'; 
	$epages[] = 'eshop_sysemails'; 
	$epages[] = 'eshop_xtra_help'; 
	$epages[] = 'eshop_xtra_privacy'; 
	$epages[] = 'eshop_downloads_only';
	$epages[] = 'eshop_fold_menu';
	$epages[] = 'eshop_widget';
	$epages[] = 'eshop_search_img';
	foreach($epages as $epage){
		delete_option($epage);
	}
	echo '<li>'.__('Options - deleted','eshop').'</li>';

	//meta values
	$allposts = get_pages();
	foreach( $allposts as $postinfo) {
		delete_post_meta($postinfo->ID, 'Sku');
		delete_post_meta($postinfo->ID, 'Product Description');
		delete_post_meta($postinfo->ID, 'Product Download');
		delete_post_meta($postinfo->ID, 'Shipping Rate');
		delete_post_meta($postinfo->ID, 'Featured Product');
		delete_post_meta($postinfo->ID, 'Stock Available');
		delete_post_meta($postinfo->ID, 'Stock Quantity');
		delete_post_meta($postinfo->ID, '_eshop_prod_img');

		for($i=1;$i<=$numoptions;$i++){
			delete_post_meta($postinfo->ID,'Option '.$i);
			delete_post_meta($postinfo->ID,'Price '.$i);
		}
	}
	echo '<li>'.__('Product Information - deleted','eshop').'</li>';

	//delete files
	$dloaddir=eshop_download_directory();
	if ($handle = opendir($dloaddir)) {
		// This is the correct way to loop over the directory. //
		while (false !== ($file = readdir($handle))) {
			if($file!='.' && $file !='..')
				unlink ($dloaddir.$file);
		}
		closedir($handle);
		rmdir($dloaddir);
		echo '<li>'.__('Files uploaded via the plugin -  deleted','eshop').'</li>';
	}

	$filedir=eshop_files_directory();
	if ($handle = opendir($filedir[0])) {
		// This is the correct way to loop over the directory. //
		while (false !== ($file = readdir($handle))) {
			if($file!='.' && $file !='..'){
				unlink ($filedir[0].$file);
			}
		}
		closedir($handle);
		rmdir ($filedir[0]);
		echo '<li>'.__('eShop files deleted','eshop').'</li>';
	}

	//clear the cron
	wp_clear_scheduled_hook('eshop_event');
	//and finally deactivate the plugin - might cause the page to go walkabout - may need to redirect to plugins page
	deactivate_plugins('eshop/eshop.php'); //Deactivate ourself
	echo '<li>'.__('Plugin deactivated','eshop').'</li>';
	echo '</ul>';
	echo '<p><strong>'.__('eShop uninstalled.','eshop').'</strong></>';

}else{
	echo '<p>'.__('Uninstalling eShop will result in the following:','eshop').'</p>';
	echo '<ul>';
	echo '<li>'.__('Removal of files generated by the plugin.','eshop').'</li>';
	echo '<li>'.__('Removal of files uploaded via the plugin (downloads).','eshop').'</li>';
	echo '<li>'.__('Removal of the database tables created by the plugin.','eshop').'</li>';
	echo '<li>'.__('Removal of meta data(product information) associated with a product page.','eshop').'</li>';
	echo '<li>'.__('Deactivation of the plugin.','eshop').'</li>';
	echo '</ul>';
	echo '<p>'.__('Uninstalling the plugin will not affect the following:','eshop').'</p>';
	echo '<ul>';
	echo '<li>'.__('Page content associated with products.','eshop').'</li>';
	echo '<li>'.__('Pages generated by the plugin.','eshop').'</li>';
	echo '<li>'.__('The plugin itself will not be deleted.','eshop').'</li>';
	echo '</ul>';
	echo '<form action="plugins.php?page=eshop_uninstall.php" method="post"><p class="submit"><input type="submit" id="delete" name="delete" value="'.__('Uninstall','eshop').'" /></p></form>';
}
echo '</div>';
?>