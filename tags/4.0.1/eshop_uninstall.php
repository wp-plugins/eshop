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
	$etable[] = $wpdb->prefix ."eshop_discount_codes";
	$etable[] = $wpdb->prefix ."eshop_emails";
	$etable[] = $wpdb->prefix.'eshop_option_names';
	$etable[] = $wpdb->prefix.'eshop_option_sets';

	foreach($etable as $table){
		if ($wpdb->get_var("show tables like '$table'") == $table) {
			//delete it
			$wpdb->query("DROP TABLE IF EXISTS $table");
		}
	}
	echo '<li>'.__('MySQL Tables - deleted','eshop').'</li>';

	//options
	$epages[] = 'eshop_addtocart_image';
	$epages[] = 'eshop_webtopay';
	$epages[] = 'eshop_payson';
	$epages[] = 'eshop_authorizenet';
	$epages[] = 'eshop_business';
	$epages[] = 'eshop_from_email';
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
	$epages[] = 'eshop_records';
	$epages[] = 'eshop_shipping';
	$epages[] = 'eshop_shipping_zone';
	$epages[] = 'eshop_show_downloads';
	$epages[] = 'eshop_show_stock'; 
	$epages[] = 'eshop_show_zones';
	$epages[] = 'eshop_status'; 
	$epages[] = 'eshop_stock_control'; 
	$epages[] = 'eshop_style';
	$epages[] = 'eshop_sysemails'; 
	$epages[] = 'eshop_unknown_state';
	$epages[] = 'eshop_xtra_help'; 
	$epages[] = 'eshop_xtra_privacy'; 
	$epages[] = 'eshop_downloads_only';
	$epages[] = 'eshop_fold_menu';
	$epages[] = 'eshop_widget';
	$epages[] = 'eshop_pay_widget';
	$epages[] = 'eshop_search_img';
	$epages[] = 'eshop_version';
	$epages[] = 'eshop_image_in_cart';
	$epages[] = 'eshop_shipping_state';
	$epages[] = 'eshop_shop_page';
	$epages[] = 'eshop_base_brand';
	$epages[] = 'eshop_base_condition';
	$epages[] = 'eshop_base_expiry';
	$epages[] = 'eshop_base_payment';
	$epages[] = 'eshop_base_ptype';
	$epages[] = 'eshop_cash';
	$epages[] = 'eshop_epn';
	$epages[] = 'eshop_products_widgets';
	$epages[] = 'eshop_show_allstates';
	$epages[] = 'eshop_show_sku';
	$epages[] = 'eshop_hide_addinfo';
	$epages[] = 'eshop_hide_shipping';
	$epages[] = 'eshop_tandc';
	$epages[] = 'eshop_tandc_id';
	$epages[] = 'eshop_tandc_use';
	for ($x=1;$x<=3;$x++){
		$epages[]='eshop_discount_spend'.$x;
		$epages[]='eshop_discount_value'.$x;
	}
	$epages[]='eshop_discount_shipping';
	$epages[]='eshop_show_forms';
	$epages[]='eshop_downloads_hideall';
	$epages[]='eshop_paypal_noemail';
	
	foreach($epages as $epage){
		delete_option($epage);
	}
	echo '<li>'.__('Options - deleted','eshop').'</li>';

	//meta values
	$eshopmetaary[]= '_Sku';
	$eshopmetaary[]= '_Product Description';
	$eshopmetaary[]= '_Shipping Rate';
	$eshopmetaary[]= '_Featured Product';
	$eshopmetaary[]= '_Stock Available';
	$eshopmetaary[]= '_Stock Quantity';
	$eshopmetaary[]= '_eshop_prod_img';
	$eshopmetaary[]= '_eshoposets';

	for($i=1;$i<=$numoptions;$i++){
		$eshopmetaary[]= '_Option '.$i;
		$eshopmetaary[]= '_Price '.$i;
		$eshopmetaary[]= '_Download '.$i;
	}

	foreach( $eshopmetaary as $eshopmeta) {
		delete_post_meta_by_key($eshopmeta);
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
		echo '<li>'.__('eShop template files deleted','eshop').'</li>';
	}
	//unregister widgets
	unregister_sidebar_widget('eshopcart');
	unregister_sidebar_widget('eshop_payments');
	eshop_products_unregister();
	//clear the cron
	wp_clear_scheduled_hook('eshop_event');
	//remove eshop capability
	remove_eshop_caps();
	//and finally deactivate the plugin - might cause the page to go walkabout - may need to redirect to plugins page
	deactivate_plugins('eshop/eshop.php'); //Deactivate ourself
	echo '<li>'.__('Plugin deactivated','eshop').'</li>';
	echo '</ul>';
	echo '<p><strong>'.__('eShop uninstalled.','eshop').'</strong></>';

}else{
	echo '<p><strong>'.__('Uninstalling eShop will result in the following:','eshop').'</strong></p>';
	echo '<ul>';
	echo '<li>'.__('Removal of files generated by the plugin.','eshop').'</li>';
	echo '<li>'.__('Removal of files uploaded via the plugin (downloads).','eshop').'</li>';
	echo '<li>'.__('Removal of the database tables created by the plugin.','eshop').'</li>';
	echo '<li>'.__('Removal of meta data(product information) associated with a product page.','eshop').'</li>';
	echo '<li>'.__('Deactivation and removal of eShop widgets.','eshop').'</li>';
	echo '<li>'.__('Deactivation of the plugin.','eshop').'</li>';
	echo '</ul>';
	echo '<p><strong>'.__('Uninstalling the plugin will not affect the following, and will therefore have to be deleted manually:','eshop').'</strong></p>';
	echo '<ul>';
	echo '<li><strong>'.__('Page content associated with products.','eshop').'</strong></li>';
	echo '<li><strong>'.__('Pages generated by the plugin.','eshop').'</strong></li>';
	echo '<li><strong>'.__('The plugin itself will not be deleted.','eshop').'</strong></li>';
	echo '</ul>';
	echo '<form action="plugins.php?page=eshop_uninstall.php" method="post"><p class="submit"><input type="submit" id="delete" class="button-primary" name="delete" value="'.__('Uninstall','eshop').'" /></p></form>';
}
echo '</div>';
function remove_eshop_caps() {
	global $wpdb, $user_level, $wp_rewrite, $wp_version;
		$role = get_role('administrator');
		if ($role !== NULL){
			$role->remove_cap('eShop');
			$role->remove_cap('eShop_admin');
		}
		$role = get_role('editor');
		if ($role !== NULL)
			$role->remove_cap('eShop');
}
function eshop_products_unregister() {
	if ( !$options = get_option('eshop_products_widgets') )
		$options = array();

	$registered = false;
	foreach ( array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['show_what']) ) // we used 'something' above in our exampple.  Replace with with whatever your real data are.
			continue;
		// $id should look like {$id_base}-{$o}
		$id = "eshop-prod-$o"; // Never never never translate an id
		$registered = true;
		unregister_sidebar_widget( $id );
	}

}
?>