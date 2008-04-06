<?php
if ('eshop_settings.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');
     
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

if(isset($_POST['submit'])){
	include 'cart-functions.php';
	if (get_magic_quotes_gpc()==0) {
		$_POST = stripslashes_array($_POST);
	}
	$_POST=sanitise_array($_POST);
	$err='';
	update_option('eshop_method',$wpdb->escape($_POST['eshop_method']));
	update_option('eshop_status',$wpdb->escape($_POST['eshop_status']));
	update_option('eshop_currency',$wpdb->escape($_POST['eshop_currency']));
	update_option('eshop_location',$wpdb->escape($_POST['eshop_location']));
	update_option('eshop_business',$wpdb->escape($_POST['eshop_business']));
	update_option('eshop_sysemails',$wpdb->escape($_POST['eshop_sysemails']));
	update_option('eshop_currency_symbol',$wpdb->escape($_POST['eshop_currency_symbol']));
	update_option('eshop_cart_nostock',$wpdb->escape($_POST['eshop_cart_nostock']));
	update_option('eshop_sudo_cat',$wpdb->escape($_POST['eshop_sudo_cat']));
	update_option('eshop_credits',$wpdb->escape($_POST['eshop_credits']));
	
	update_option('eshop_stock_control',$wpdb->escape($_POST['eshop_stock_control']));
	update_option('eshop_show_stock',$wpdb->escape($_POST['eshop_show_stock']));

	//error grabbing
	if(is_numeric($_POST['eshop_records'])){
		update_option('eshop_records',$wpdb->escape($_POST['eshop_records']));
	}else{
		$err.='<li>Orders per page should be numeric, a default of 10 has been applied.</li>';
		update_option('eshop_records','10');
	}
	if(is_numeric($_POST['eshop_options_num'])){
		update_option('eshop_options_num',$wpdb->escape($_POST['eshop_options_num']));
	}else{
		$err.='<li>Options per product should be numeric, a default of 3 has been applied.</li>';
		update_option('eshop_options_num','3');
	}
	if(is_numeric($_POST['eshop_random_num'])){
		update_option('eshop_random_num',$wpdb->escape($_POST['eshop_random_num']));
	}else{
		$err.='<li>Number of random products to display should be numeric, a default of 5 has been applied.</li>';
		update_option('eshop_random_num','5');
	}
	if(is_numeric($_POST['eshop_pagelist_num'])){
		update_option('eshop_pagelist_num',$wpdb->escape($_POST['eshop_pagelist_num']));
	}else{
		$err.='<li>Number of products to display on department pages should be numeric, a default of 5 has been applied.</li>';
		update_option('eshop_pagelist_num','5');
	}
	if(is_numeric($_POST['eshop_downloads_num'])){
		update_option('eshop_downloads_num',$wpdb->escape($_POST['eshop_downloads_num']));
	}else{
		$err.='<li>Number of download attempts should be numeric, a default of 3 has been applied.</li>';
		update_option('eshop_downloads_num','3');
	}

	if(is_numeric($_POST['eshop_xtra_privacy'])){
		$ptitle=get_post($_POST['eshop_xtra_privacy']);
		if($ptitle->post_title!=''){
			update_option('eshop_xtra_privacy',$wpdb->escape($_POST['eshop_xtra_privacy']));
		}else{
			$err.='<li>Privacy Policy page id chosen ('.$_POST['eshop_xtra_privacy'].') is invalid.</li>';
			update_option('eshop_xtra_privacy','');
		}
	}elseif($_POST['eshop_xtra_privacy']!=''){
		$err.='<li>The Privacy Policy page needs to be a page id number.</li>';
		update_option('eshop_xtra_privacy','');
	}
	if(is_numeric($_POST['eshop_xtra_help'])){
		$ptitle=get_post($_POST['eshop_xtra_help']);
		if($ptitle->post_title!=''){
			update_option('eshop_xtra_help',$wpdb->escape($_POST['eshop_xtra_help']));
		}else{
			$err.='<li>Help page id chosen ('.$_POST['eshop_xtra_help'].') is invalid.</li>';
			update_option('eshop_xtra_help','');
		}	
	}elseif($_POST['eshop_xtra_help']!=''){
		$err.='<li>The help page needs to be a page id number.</li>';
		update_option('eshop_xtra_help','');
	}


	if($_POST['eshop_currency_symbol']==''){
		$err.='<li>Currency Symbol was missing, the default $ has been applied.</li>';
		update_option('eshop_currency_symbol','$');
	}
	
}
if($err!=''){
	echo'<div id="message" class="error fade"><p><strong>Error</strong> the following were not valid:</p><ul>'.$err.'</ul></div>'."\n";
}elseif(isset($_POST['submit'])){
	echo'<div id="message" class="updated fade"><p>eshop Settings have been updated.</p></div>'."\n";
}
echo '<div class="wrap">';
echo '<h2>eShop Settings</h2>'."\n";
/* defaults will need to be created */
echo $result;
?>
<form method="post" action="" id="eshop-settings">
<?php wp_nonce_field('update-options') ?>
<fieldset><legend>eShop Admin</legend>
<label for="eshop_status">eShop status</label>
	<select name="eshop_status" id="eshop_status">
	<?php
	if('live' == get_option('eshop_status')){
		echo '<option value="live" selected="selected">Live</option>';
		echo '<option value="testing">Testing</option>';
	}else{
		echo '<option value="live">Live</option>';
		echo '<option value="testing" selected="selected">Testing</option>';
	}
	?>
	</select><br />
<label for="eshop_records">Orders per page</label><input id="eshop_records" name="eshop_records" type="text" value="<?php echo get_option('eshop_records'); ?>" size="5" /><br />
</fieldset>
<fieldset><legend>Merchant Gateway</legend>
<label for="eshop_method">Payment method</label>
	<select name="eshop_method" id="eshop_method">
	<?php
	//for future use
	if('paypal' == get_option('eshop_method')){
		echo '<option value="paypal" selected="selected">Paypal</option>';
	}else{
		echo '<option value="paypal">Paypal</option>';
	}
	?>
	</select><br />
<label for="eshop_business">Email address</label><input id="eshop_business" name="eshop_business" type="text" value="<?php echo get_option('eshop_business'); ?>" size="30" /><br />
</fieldset>
<fieldset><legend>Business Details</legend>
<label for="eshop_sysemails">Available business email addresses</label>
<textarea id="eshop_sysemails" name="eshop_sysemails" rows="10" cols="50">
<?php echo get_option('eshop_sysemails'); ?>
</textarea>
<br />
<label for="eshop_location">Business Location</label>
	<select name="eshop_location" id="eshop_location">
	<?php
	$currentlocations=array('GB', 'US', 'JP', 'CA', 'DE');
	foreach($currentlocations as $code){
		if($code == get_option('eshop_location')){
			$sel=' selected="selected"';
		}else{
			$sel='';
		}
		echo '<option value="'. $code .'"'. $sel .'>'. $code .'</option>';
	}
	?>
</select><br />
</fieldset>

<fieldset><legend>Product options</legend>
<label for="eshop_options_num">Options per product</label><input id="eshop_options_num" name="eshop_options_num" type="text" value="<?php echo get_option('eshop_options_num'); ?>" size="5" /><br />
<label for="eshop_cart_nostock">Out of Stock message</label><input id="eshop_cart_nostock" name="eshop_cart_nostock" type="text" value="<?php echo get_option('eshop_cart_nostock'); ?>" size="30" /><br />
<label for="eshop_stock_control">Stock Control</label>
	<select name="eshop_stock_control" id="eshop_stock_control">
	<?php
	if('yes' == get_option('eshop_stock_control')){
		echo '<option value="yes" selected="selected">Yes</option>';
		echo '<option value="no">No</option>';
	}else{
		echo '<option value="yes">Yes</option>';
		echo '<option value="no" selected="selected">No</option>';
	}
	?>
	</select><br />

<label for="eshop_show_stock">Show stock available</label>
	<select name="eshop_show_stock" id="eshop_show_stock">
	<?php
	if('yes' == get_option('eshop_show_stock')){
		echo '<option value="yes" selected="selected">Yes</option>';
		echo '<option value="no">No</option>';
	}else{
		echo '<option value="yes">Yes</option>';
		echo '<option value="no" selected="selected">No</option>';
	}
	?>
	</select><br />

<fieldset><legend>Downloadables</legend>
<label for="eshop_downloads_num">Download attempts</label><input id="eshop_downloads_num" name="eshop_downloads_num" type="text" value="<?php echo get_option('eshop_downloads_num'); ?>" size="5" /><br />
</fieldset>
</fieldset>
<fieldset><legend>Currency</legend>

<label for="eshop_currency_symbol">Symbol</label><input id="eshop_currency_symbol" name="eshop_currency_symbol" type="text" value="<?php echo get_option('eshop_currency_symbol'); ?>" size="10" /><br />

<label for="eshop_currency">Code</label>
	<select name="eshop_currency" id="eshop_currency">
	<?php
	$currencycodes=array('GBP','USD','JPY', 'CAD', 'EUR');
	foreach($currencycodes as $code){
		if($code == get_option('eshop_currency')){
			$sel=' selected="selected"';
		}else{
			$sel='';
		}
		echo '<option value="'. $code .'"'. $sel .'>'. $code .'</option>';
	}
	?>
	</select><br />

</fieldset>
<fieldset><legend>Product Listings</legend>
<label for="eshop_sudo_cat">Featured and department product sort order</label>
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
	echo '<option value="1"'.$sudo1.'>Newest</option>';
	echo '<option value="2"'.$sudo2.'>Oldest</option>';
	echo '<option value="3"'.$sudo3.'>Alphabetically</option>';

	?>
	</select><br />
	<label for="eshop_random_num">Random products to display</label><input id="eshop_random_num" name="eshop_random_num" type="text" value="<?php echo get_option('eshop_random_num'); ?>" size="5" /><br />
	<label for="eshop_pagelist_num">Department Products to display</label><input id="eshop_pagelist_num" name="eshop_pagelist_num" type="text" value="<?php echo get_option('eshop_pagelist_num'); ?>" size="5" /><br />

</fieldset>
<fieldset><legend>Credits</legend>
<label for="eshop_credits">Display eShop credits</label>
	<select name="eshop_credits" id="eshop_credits">
	<?php
	if('yes' == get_option('eshop_credits')){
		echo '<option value="yes" selected="selected">Yes</option>';
		echo '<option value="no">No</option>';
	}else{
		echo '<option value="yes">Yes</option>';
		echo '<option value="no" selected="selected">No</option>';
	}
	?>
	</select><br />
</fieldset>

<fieldset><legend>Link to extra pages</legend>
<label for="eshop_xtra_privacy">Privacy Policy - page id number</label><input id="eshop_xtra_privacy" name="eshop_xtra_privacy" type="text" value="<?php echo get_option('eshop_xtra_privacy'); ?>" size="5" /><br />
<label for="eshop_xtra_help">Help - page id number</label><input id="eshop_xtra_help" name="eshop_xtra_help" type="text" value="<?php echo get_option('eshop_xtra_help'); ?>" size="5" /><br />
</fieldset>

<input type="hidden" name="page_options" value="eshop_method,
eshop_status,eshop_currency,eshop_location,eshop_business,
eshop_sysemails,eshop_records,eshop_options_num,eshop_currency_symbol,
eshop_cart_nostock,eshop_sudo_cat,eshop_random_num,eshop_downloads_num, eshop_credits,
eshop_xtra_help,eshop_xtra_privacy,eshop_stock_control,eshop_show_stock" />

<p class="submit">
<input type="submit" name="submit" value="<?php _e('Update Options &#187;') ?>" />
</p>
</form>

</div>
<?php eshop_show_credits(); ?>