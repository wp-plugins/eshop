<?php
if ('eshop_settings.php' == basename($_SERVER['SCRIPT_FILENAME']))
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
$result='';
global $wpdb;
	$err='';

if(isset($_POST['submit'])){
	include 'cart-functions.php';
	if (get_magic_quotes_gpc()==0) {
		$_POST = stripslashes_array($_POST);
	}
	$_POST=sanitise_array($_POST);
	update_option('eshop_method',$wpdb->escape($_POST['eshop_method']));
	update_option('eshop_status',$wpdb->escape($_POST['eshop_status']));
	update_option('eshop_currency',$wpdb->escape($_POST['eshop_currency']));
	update_option('eshop_location',$wpdb->escape($_POST['eshop_location']));
	update_option('eshop_business',$wpdb->escape($_POST['eshop_business']));
	update_option('eshop_cron_email',$wpdb->escape($_POST['eshop_cron_email']));
	update_option('eshop_sysemails',$wpdb->escape($_POST['eshop_sysemails']));
	update_option('eshop_currency_symbol',$wpdb->escape($_POST['eshop_currency_symbol']));
	update_option('eshop_cart_nostock',$wpdb->escape($_POST['eshop_cart_nostock']));
	update_option('eshop_sudo_cat',$wpdb->escape($_POST['eshop_sudo_cat']));
	update_option('eshop_credits',$wpdb->escape($_POST['eshop_credits']));
	update_option('eshop_fold_menu',$wpdb->escape($_POST['eshop_fold_menu']));
	update_option('eshop_stock_control',$wpdb->escape($_POST['eshop_stock_control']));
	update_option('eshop_show_stock',$wpdb->escape($_POST['eshop_show_stock']));
	update_option('eshop_downloads_only',$wpdb->escape($_POST['eshop_downloads_only']));
	update_option('eshop_search_img',$wpdb->escape($_POST['eshop_search_img']));
	//error grabbing
	if(is_numeric($_POST['eshop_records'])){
		update_option('eshop_records',$wpdb->escape($_POST['eshop_records']));
	}else{
		$err.='<li>'.__('Orders per page should be numeric, a default of 10 has been applied.','eshop').'</li>';
		update_option('eshop_records','10');
	}
	if(is_numeric($_POST['eshop_options_num'])){
		update_option('eshop_options_num',$wpdb->escape($_POST['eshop_options_num']));
	}else{
		$err.='<li>'.__('Options per product should be numeric, a default of 3 has been applied.','eshop').'</li>';
		update_option('eshop_options_num','3');
	}
	if(is_numeric($_POST['eshop_random_num'])){
		update_option('eshop_random_num',$wpdb->escape($_POST['eshop_random_num']));
	}else{
		$err.='<li>'.__('Number of random products to display should be numeric, a default of 5 has been applied.','eshop').'</li>';
		update_option('eshop_random_num','5');
	}
	if(is_numeric($_POST['eshop_pagelist_num'])){
		update_option('eshop_pagelist_num',$wpdb->escape($_POST['eshop_pagelist_num']));
	}else{
		$err.='<li>'.__('Number of products to display on department pages should be numeric, a default of 5 has been applied.','eshop').'</li>';
		update_option('eshop_pagelist_num','5');
	}
	if(is_numeric($_POST['eshop_downloads_num'])){
		update_option('eshop_downloads_num',$wpdb->escape($_POST['eshop_downloads_num']));
	}else{
		$err.='<li>'.__('Number of download attempts should be numeric, a default of 3 has been applied.','eshop').'</li>';
		update_option('eshop_downloads_num','3');
	}

	if(is_numeric($_POST['eshop_xtra_privacy'])){
		$ptitle=get_post($_POST['eshop_xtra_privacy']);
		if($ptitle->post_title!=''){
			update_option('eshop_xtra_privacy',$wpdb->escape($_POST['eshop_xtra_privacy']));
		}else{
			$err.='<li>'.__('Privacy Policy page id chosen','eshop').' ('.$_POST['eshop_xtra_privacy'].') '.__('is invalid.','eshop').'</li>';
			update_option('eshop_xtra_privacy','');
		}
	}elseif($_POST['eshop_xtra_privacy']!=''){
		$err.='<li>'.__('The Privacy Policy page needs to be a page id number.','eshop').'</li>';
	}else{
		update_option('eshop_xtra_privacy','');
	}
	if(is_numeric($_POST['eshop_xtra_help'])){
		$ptitle=get_post($_POST['eshop_xtra_help']);
		if($ptitle->post_title!=''){
			update_option('eshop_xtra_help',$wpdb->escape($_POST['eshop_xtra_help']));
		}else{
			$err.='<li>'.__('Help page id chosen','eshop').' ('.$_POST['eshop_xtra_help'].') '.__('is invalid.','eshop').'</li>';
		}	
	}elseif($_POST['eshop_xtra_help']!=''){
		$err.='<li>'.__('The help page needs to be a page id number.','eshop').'</li>';
	}else{
		update_option('eshop_xtra_help','');
	}
	if(is_numeric($_POST['eshop_cart_shipping'])){
		$ptitle=get_post($_POST['eshop_cart_shipping']);
		if($ptitle->post_title!=''){
			update_option('eshop_cart_shipping',$wpdb->escape($_POST['eshop_cart_shipping']));
		}else{
			$err.='<li>'.__('The Shipping rates page id chosen','eshop').' ('.$_POST['eshop_cart_shipping'].') '.__('is invalid.','eshop').'</li>';
		}	
	}elseif(trim($_POST['eshop_cart_shipping'])!=''){
			$err.='<li>'.__('The Shipping rates page needs to be a page id number.','eshop').'</li>';
	}else{
		update_option('eshop_cart_shipping','');
	}
	if(is_numeric($_POST['eshop_cart'])){
		$ptitle=get_post($_POST['eshop_cart']);
		if($ptitle->post_title!=''){
			update_option('eshop_cart',$wpdb->escape($_POST['eshop_cart']));
		}else{
			$err.='<li>'.__('The Cart page id chosen','eshop').' ('.$_POST['eshop_cart'].') '.__('is invalid.','eshop').'</li>';
		}	
	}else{
		$err.='<li>'.__('The Cart page needs to be a page id number.','eshop').'</li>';
	}

	if(is_numeric($_POST['eshop_cart_cancel'])){
		$ptitle=get_post($_POST['eshop_cart_cancel']);
		if($ptitle->post_title!=''){
			update_option('eshop_cart_cancel',$wpdb->escape($_POST['eshop_cart_cancel']));
		}else{
			$err.='<li>'.__('The Cancelled payment page id chosen','eshop').' ('.$_POST['eshop_cart_cancel'].') '.__('is invalid.','eshop').'</li>';
		}	
	}else{
		$err.='<li>'.__('The Cancelled payment page needs to be a page id number.','eshop').'</li>';
	}

	if(is_numeric($_POST['eshop_checkout'])){
		$ptitle=get_post($_POST['eshop_checkout']);
		if($ptitle->post_title!=''){
		update_option('eshop_checkout',$wpdb->escape($_POST['eshop_checkout']));
		}else{
		$err.='<li>'.__('The Checkout page id chosen','eshop').' ('.$_POST['eshop_checkout'].') '.__('is invalid.','eshop').'</li>';
		}	
	}else{
		$err.='<li>'.__('The Checkout page needs to be a page id number.','eshop').'</li>';
	}

	if(is_numeric($_POST['eshop_cart_success'])){
		$ptitle=get_post($_POST['eshop_cart_success']);
		if($ptitle->post_title!=''){
			update_option('eshop_cart_success',$wpdb->escape($_POST['eshop_cart_success']));
		}else{
			$err.='<li>'.__('The Successful payment page id chosen','eshop').' ('.$_POST['eshop_cart_success'].') '.__('is invalid.','eshop').'</li>';
		}	
	}else{
		$err.='<li>'.__('The Successful payment page needs to be a page id number.','eshop').'</li>';
	}


	if(is_numeric($_POST['eshop_show_downloads'])){
		$ptitle=get_post($_POST['eshop_show_downloads']);
		if($ptitle->post_title!=''){
			update_option('eshop_show_downloads',$wpdb->escape($_POST['eshop_show_downloads']));
		}else{
			$err.='<li>'.__('The Downloads page id chosen','eshop').' ('.$_POST['eshop_show_downloads'].') '.__('is invalid.','eshop').'</li>';
		}	
	}else{
		$err.='<li>'.__('The Downloads page needs to be a page id number.','eshop').'</li>';
	}

	if($_POST['eshop_currency_symbol']==''){
		$err.='<li>'.__('Currency Symbol was missing, the default $ has been applied.','eshop').'</li>';
		update_option('eshop_currency_symbol','$');
	}

}
if($err!=''){
	echo'<div id="message" class="error fade"><p>'.__('<strong>Error</strong> the following were not valid:','eshop').'</p><ul>'.$err.'</ul></div>'."\n";
}elseif(isset($_POST['submit'])){
	echo'<div id="message" class="updated fade"><p>'.__('eshop Settings have been updated.','eshop').'</p></div>'."\n";
}
echo '<div class="wrap">';
echo '<h2>'.__('eShop Settings','eshop').'</h2>'."\n";
/* defaults will need to be created */
echo $result;
?>
<form method="post" action="" id="eshop-settings">
<?php wp_nonce_field('update-options') ?>
<fieldset><legend><?php _e('eShop Admin','eshop'); ?></legend>
<label for="eshop_status"><?php _e('eShop status','eshop'); ?></label>
	<select name="eshop_status" id="eshop_status">
	<?php
	if('live' == get_option('eshop_status')){
		echo '<option value="live" selected="selected">'.__('Live','eshop').'</option>';
		echo '<option value="testing">'.__('Testing','eshop').'</option>';
	}else{
		echo '<option value="live">'.__('Live','eshop').'</option>';
		echo '<option value="testing" selected="selected">'.__('Testing','eshop').'</option>';
	}
	?>
	</select><br />
<label for="eshop_records"><?php _e('Orders per page','eshop'); ?></label><input id="eshop_records" name="eshop_records" type="text" value="<?php echo get_option('eshop_records'); ?>" size="5" /><br />
</fieldset>
<fieldset><legend><?php _e('Merchant Gateway','eshop'); ?></legend>
<label for="eshop_method"><?php _e('Payment method','eshop'); ?></label>
	<select name="eshop_method" id="eshop_method">
	<?php
	//for future use
	if('paypal' == get_option('eshop_method')){
		echo '<option value="paypal" selected="selected">'.__('Paypal','eshop').'</option>';
	}else{
		echo '<option value="paypal">'.__('Paypal','eshop').'</option>';
	}
	?>
	</select><br />
<label for="eshop_business"><?php _e('Email address','eshop'); ?></label><input id="eshop_business" name="eshop_business" type="text" value="<?php echo get_option('eshop_business'); ?>" size="30" /><br />
</fieldset>
<fieldset><legend><?php _e('Business Details','eshop'); ?></legend>
<label for="eshop_sysemails"><?php _e('Available business email addresses','eshop'); ?></label>
<textarea id="eshop_sysemails" name="eshop_sysemails" rows="10" cols="50">
<?php echo get_option('eshop_sysemails'); ?>
</textarea>
<br />
<label for="eshop_location"><?php _e('Business Location','eshop'); ?></label>
	<select name="eshop_location" id="eshop_location">
	<?php
	$ctable=$wpdb->prefix.'eshop_countries';
	$currentlocations=$wpdb->get_results("SELECT * from $ctable ORDER BY country");
	//$currentlocations=array('GB', 'US', 'JP', 'CA', 'DE');
	foreach ($currentlocations as $row){
		if($row->code == get_option('eshop_location')){
			$sel=' selected="selected"';
		}else{
			$sel='';
		}
		echo '<option value="'. $row->code .'"'. $sel .'>'. $row->country .'</option>';
	}
	?>
</select><br />
</fieldset>

<fieldset><legend><?php _e('Product options','eshop'); ?></legend>
<label for="eshop_options_num"><?php _e('Options per product','eshop'); ?></label><input id="eshop_options_num" name="eshop_options_num" type="text" value="<?php echo get_option('eshop_options_num'); ?>" size="5" /><br />
<label for="eshop_cart_nostock"><?php _e('Out of Stock message','eshop'); ?></label><input id="eshop_cart_nostock" name="eshop_cart_nostock" type="text" value="<?php echo get_option('eshop_cart_nostock'); ?>" size="30" /><br />
<label for="eshop_stock_control"><?php _e('Stock Control','eshop'); ?></label>
	<select name="eshop_stock_control" id="eshop_stock_control">
	<?php
	if('yes' == get_option('eshop_stock_control')){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />

<label for="eshop_show_stock"><?php _e('Show stock available','eshop'); ?></label>
	<select name="eshop_show_stock" id="eshop_show_stock">
	<?php
	if('yes' == get_option('eshop_show_stock')){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />


<fieldset><legend><?php _e('Downloadables','eshop'); ?></legend>
<label for="eshop_downloads_num"><?php _e('Download attempts','eshop'); ?></label><input id="eshop_downloads_num" name="eshop_downloads_num" type="text" value="<?php echo get_option('eshop_downloads_num'); ?>" size="5" /><br />
</fieldset>
</fieldset>
<fieldset><legend><?php _e('Currency','eshop'); ?></legend>

<label for="eshop_currency_symbol"><?php _e('Symbol','eshop'); ?></label><input id="eshop_currency_symbol" name="eshop_currency_symbol" type="text" value="<?php echo get_option('eshop_currency_symbol'); ?>" size="10" /><br />

<label for="eshop_currency"><?php _e('Code','eshop'); ?></label>
	<select name="eshop_currency" id="eshop_currency">
	<?php
	$currencycodes=array('AUD'=>'Australian Dollars','CAD'=>'Canadian Dollars','EUR'=>'Euros','GBP'=>'Pounds Sterling ','JPY'=>'Yen ','USD'=>'U.S. Dollars','NZD'=>'New Zealand Dollar','CHF'=>'Swiss Franc','HKD'=>'Hong Kong Dollar ','SGD'=>'Singapore Dollar ','SEK'=>'Swedish Krona','DKK'=>'Danish Krone','PLN'=>'Polish Zloty','NOK'=>'Norwegian Krone','HUF'=>'Hungarian Forint','CZK'=>'Czech Koruna','ILS'=>'Israeli Shekel','MXN'=>'Mexican Peso');
	foreach($currencycodes as $code=>$codename){
		if($code == get_option('eshop_currency')){
			$sel=' selected="selected"';
		}else{
			$sel='';
		}
		echo '<option value="'. $code .'"'. $sel .'>'. $codename.' ('.$code.')' .'</option>';
	}
	?>
	</select><br />

</fieldset>
<fieldset><legend><?php _e('Product Listings','eshop'); ?></legend>
<label for="eshop_sudo_cat"><?php _e('Featured and department product sort order','eshop'); ?></label>
	<select name="eshop_sudo_cat" id="eshop_sudo_cat">
	<?php
	switch (get_option('eshop_sudo_cat')){
		case '1'://newest
			$sudo1=' selected="selected"';
			$sudo2=$sudo3='';
			break;
		case '2'://oldest
			$sudo2=' selected="selected"';
			$sudo1=$sudo3='';
			break;
		case '3'://alphabetically
			$sudo3=' selected="selected"';
			$sudo1=$sudo2='';
			break;
		
	}
	echo '<option value="1"'.$sudo1.'>'.__('Newest','eshop').'</option>';
	echo '<option value="2"'.$sudo2.'>'.__('Oldest','eshop').'</option>';
	echo '<option value="3"'.$sudo3.'>'.__('Alphabetically','eshop').'</option>';

	?>
	</select><br />
	<label for="eshop_random_num"><?php _e('Random products to display','eshop'); ?></label><input id="eshop_random_num" name="eshop_random_num" type="text" value="<?php echo get_option('eshop_random_num'); ?>" size="5" /><br />
	<label for="eshop_pagelist_num"><?php _e('Department Products to display','eshop'); ?></label><input id="eshop_pagelist_num" name="eshop_pagelist_num" type="text" value="<?php echo get_option('eshop_pagelist_num'); ?>" size="5" /><br />
</fieldset>

<fieldset><legend><?php _e('Sub pages','eshop'); ?></legend>
<label for="eshop_fold_menu"><?php _e('Hide sub pages from menu until top level page is visited.','eshop'); ?></label>
	<select name="eshop_fold_menu" id="eshop_fold_menu">
	<?php
	if('yes' == get_option('eshop_fold_menu')){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />
</fieldset>

<fieldset><legend><?php _e('Search Results','eshop'); ?></legend>
<label for="eshop_search_img"><?php _e('Add image to search results','eshop'); ?></label>
	<select name="eshop_search_img" id="eshop_search_img">
	<?php
	if('yes' == get_option('eshop_search_img')){
		echo '<option value="no">'.__('No','eshop').'</option>';
		echo '<option value="all">'.__('All pages and posts','eshop').'</option>';
		echo '<option value="yes" selected="selected">'.__('eShop products pages and posts only','eshop').'</option>';
	}elseif('all' == get_option('eshop_search_img')){
		echo '<option value="no">'.__('No','eshop').'</option>';
		echo '<option value="all" selected="selected">'.__('All pages and posts','eshop').'</option>';
		echo '<option value="yes">'.__('eShop products pages and posts only','eshop').'</option>';
	}else{
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
		echo '<option value="all">'.__('All pages and posts','eshop').'</option>';
		echo '<option value="yes">'.__('eShop products pages and posts only','eshop').'</option>';
	}
	?>
	</select><br />
</fieldset>

<fieldset><legend><?php _e('Credits','eshop'); ?></legend>
<label for="eshop_credits"><?php _e('Display eShop credits','eshop'); ?></label>
	<select name="eshop_credits" id="eshop_credits">
	<?php
	if('yes' == get_option('eshop_credits')){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />
</fieldset>

<fieldset><legend><?php _e('Link to extra pages','eshop'); ?></legend>
<p><?php _e('These links automatically appear on the checkout page.','eshop'); ?></p>
<label for="eshop_cart_shipping"><?php _e('Shipping rates - page id number','eshop'); ?></label><input id="eshop_cart_shipping" name="eshop_cart_shipping" type="text" value="<?php echo get_option('eshop_cart_shipping'); ?>" size="5" /><br />
<label for="eshop_xtra_privacy"><?php _e('Privacy Policy - page id number','eshop'); ?></label><input id="eshop_xtra_privacy" name="eshop_xtra_privacy" type="text" value="<?php echo get_option('eshop_xtra_privacy'); ?>" size="5" /><br />
<label for="eshop_xtra_help"><?php _e('Help - page id number','eshop'); ?></label><input id="eshop_xtra_help" name="eshop_xtra_help" type="text" value="<?php echo get_option('eshop_xtra_help'); ?>" size="5" /><br />
</fieldset>

<fieldset><legend><?php _e('Downloads Only','eshop'); ?></legend>
<p><?php _e('Change this setting only if you are using eShop for downloadable sales only.','eshop'); ?></p>
<label for="eshop_downloads_only"><?php _e('Downloads Only','eshop'); ?></label>
	<select name="eshop_downloads_only" id="eshop_downloads_only">
	<?php
	if('yes' == get_option('eshop_downloads_only')){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />
</fieldset>

<fieldset><legend><?php _e('Cron','eshop'); ?></legend>
<label for="eshop_cron_email"><?php _e('Cron Email address','eshop'); ?></label><input id="eshop_cron_email" name="eshop_cron_email" type="text" value="<?php echo get_option('eshop_cron_email'); ?>" size="30" /><br />
</fieldset>

<fieldset><legend><?php _e('Automatically created pages','eshop'); ?></legend>
<p class="warn"><?php _e('<strong>Warning:</strong> Changes made here amend the page id of the automatically created pages - change with extreme care.','eshop'); ?></p>
<label for="eshop_cart"><?php _e('Cart - page id number','eshop'); ?></label><input id="eshop_cart" name="eshop_cart" type="text" value="<?php echo get_option('eshop_cart'); ?>" size="5" /><br />
<label for="eshop_checkout"><?php _e('Checkout - page id number','eshop'); ?></label><input id="eshop_checkout" name="eshop_checkout" type="text" value="<?php echo get_option('eshop_checkout'); ?>" size="5" /><br />
<label for="eshop_cart_success"><?php _e('Successful payment  - page id number','eshop'); ?></label><input id="eshop_cart_success" name="eshop_cart_success" type="text" value="<?php echo get_option('eshop_cart_success'); ?>" size="5" /><br />
<label for="eshop_cart_cancel"><?php _e('Cancelled payment - page id number','eshop'); ?></label><input id="eshop_cart_cancel" name="eshop_cart_cancel" type="text" value="<?php echo get_option('eshop_cart_cancel'); ?>" size="5" /><br />
<label for="eshop_show_downloads"><?php _e('Downloads - page id number','eshop'); ?></label><input id="eshop_show_downloads" name="eshop_show_downloads" type="text" value="<?php echo get_option('eshop_show_downloads'); ?>" size="5" /><br />
</fieldset>



<input type="hidden" name="page_options" value="eshop_method,
eshop_status,eshop_currency,eshop_location,eshop_business,
eshop_sysemails,eshop_records,eshop_options_num,eshop_currency_symbol,
eshop_cart_nostock,eshop_sudo_cat,eshop_random_num,eshop_downloads_num, eshop_credits,eshop_fold_menu,
eshop_xtra_help,eshop_xtra_privacy,eshop_stock_control,eshop_show_stock,eshop_cron_email,eshop_cart,eshop_search_img,
eshop_cart_cancel,eshop_cart_shipping,eshop_cart_success,eshop_checkout,eshop_show_downloads,eshop_downloads_only" />

<p class="submit">
<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;') ?>" />
</p>
</form>

</div>
<?php eshop_show_credits(); ?>