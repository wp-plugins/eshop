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
include 'eshop-base-functions.php';

global $wpdb,$eshopoptions;
$err='';
//set up submenu here so it can accessed in the code
if (isset($_GET['eshop']) )
	$action_status = esc_attr($_GET['eshop']);
else
	$_GET['eshop']=$action_status = 'General';
$stati=array('General'=>__('General','eshop'),'Merchant' => __('Merchant Gateways','eshop'),'Discounts' => __('Discounts','eshop'),'Downloads' => __('Downloads','eshop'),'Pages' => __('Special Pages','eshop'),'Base'=>__('eShop Base','eshop'));
foreach ( $stati as $status => $label ) {
	$class = '';
	if ( $status == $action_status )
		$class = ' class="current"';
	$status_links[] = "<li><a href=\"options-general.php?page=eshop_settings.php&amp;eshop=$status\"$class>" . $label . '</a>';
}
//end submenu

if(isset($_POST['submit'])){
	include 'cart-functions.php';
	//ensure fresh copy:
	$eshopoptions = get_option('eshop_plugin_settings');

	if (get_magic_quotes_gpc()==0) {
		$_POST = stripslashes_array($_POST);
	}
	$_POST=sanitise_array($_POST);
	switch($action_status){
		case ('Merchant'):
			$eshopoptions['method']=$wpdb->escape($_POST['eshop_method']);
			//these are all for paypal
			$eshopoptions['currency']=$wpdb->escape($_POST['eshop_currency']);
			$eshopoptions['location']=$wpdb->escape($_POST['eshop_location']);
			$eshopoptions['business']=$wpdb->escape(trim($_POST['eshop_business']));
			$eshopoptions['paypal_noemail']=$wpdb->escape($_POST['eshop_paypal_noemail']);
			//these are for other payment options
			//payson
			$paysonpost['email']=$wpdb->escape($_POST['payson']['email']);
			$paysonpost['id']=$wpdb->escape($_POST['payson']['id']);
			$paysonpost['key']=$wpdb->escape($_POST['payson']['key']);
			$paysonpost['description']=$wpdb->escape($_POST['payson']['description']);
			$paysonpost['minimum']=$wpdb->escape($_POST['payson']['minimum']);
			$eshopoptions['payson']=$paysonpost;
			//ideallite
			$ideallitepost['IDEAL_AQUIRER']=$wpdb->escape($_POST['ideallite']['IDEAL_AQUIRER']);
			$ideallitepost['IDEAL_HASH_KEY']=$wpdb->escape($_POST['ideallite']['IDEAL_HASH_KEY']);
			$ideallitepost['IDEAL_MERCHANT_ID']=$wpdb->escape($_POST['ideallite']['IDEAL_MERCHANT_ID']);
			$ideallitepost['IDEAL_SUB_ID']=$wpdb->escape($_POST['ideallite']['IDEAL_SUB_ID']);
			$ideallitepost['IDEAL_TEST_MODE']=$wpdb->escape($_POST['ideallite']['IDEAL_TEST_MODE']);
			//$ideallitepost['IDEAL_URL_CANCEL']=$wpdb->escape($_POST['ideallite']['IDEAL_URL_CANCEL']);
			//$ideallitepost['IDEAL_URL_ERROR']=$wpdb->escape($_POST['ideallite']['IDEAL_URL_ERROR']);
			//$ideallitepost['IDEAL_URL_SUCCESS']=$wpdb->escape($_POST['ideallite']['IDEAL_URL_SUCCESS']);
			$ideallitepost['idealownermail']=$wpdb->escape($_POST['ideallite']['idealownermail']);
			$ideallitepost['idealdescription']=$wpdb->escape($_POST['ideallite']['idealdescription']);
			$eshopoptions['ideallite']=$ideallitepost;
			//authorize.net
			$authorizenetpost['email']=$wpdb->escape($_POST['authorizenet']['email']);
			$authorizenetpost['id']=$wpdb->escape($_POST['authorizenet']['id']);
			$authorizenetpost['key']=$wpdb->escape($_POST['authorizenet']['key']);
			$authorizenetpost['secret']=$wpdb->escape($_POST['authorizenet']['secret']);
			$authorizenetpost['desc']=$wpdb->escape($_POST['authorizenet']['desc']);
			$eshopoptions['authorizenet']=$authorizenetpost;
			//epn
			$epnpost['email']=$wpdb->escape($_POST['epn']['email']);
			$epnpost['id']=$wpdb->escape($_POST['epn']['id']);
			$epnpost['description']=$wpdb->escape($_POST['epn']['description']);
			$eshopoptions['epn']=$epnpost;
			//cash
			$cashpost['email']=$wpdb->escape($_POST['cash']['email']);
			$cashpost['rename']=$wpdb->escape($_POST['cash']['rename']);
			$eshopoptions['cash']=$cashpost;
			//webtopay
			$webtopaypost['id']=$wpdb->escape($_POST['webtopay']['id']);
			$webtopaypost['password']=$wpdb->escape($_POST['webtopay']['password']);
			$webtopaypost['lang']=$wpdb->escape($_POST['webtopay']['lang']);
			$webtopaypost['signature']=$wpdb->escape($_POST['webtopay']['signature']);
			$webtopaypost['projectid']=$wpdb->escape($_POST['webtopay']['projectid']);
			$eshopoptions['webtopay']=$webtopaypost;
			if(!is_array($eshopoptions['method'])){
				$eshopoptions['status']=$wpdb->escape('testing');
				$err.='<li>'.__('No Merchant Gateway selected, eShop has been put in Test Mode','eshop').'</li>';
			}
			break;
	
		case ('Pages'):
			if(is_numeric($_POST['eshop_xtra_privacy'])){
				$ptitle=get_post($_POST['eshop_xtra_privacy']);
				if($ptitle->post_title!=''){
					$eshopoptions['xtra_privacy']=$wpdb->escape($_POST['eshop_xtra_privacy']);
				}else{
					$err.='<li>'.__('Privacy Policy page id chosen','eshop').' ('.$_POST['eshop_xtra_privacy'].') '.__('is invalid.','eshop').'</li>';
					$eshopoptions['xtra_privacy']='';
				}
			}elseif($_POST['eshop_xtra_privacy']!=''){
				$err.='<li>'.__('The Privacy Policy page needs to be a page id number.','eshop').'</li>';
			}else{
				$eshopoptions['xtra_privacy']='';
			}
			if(is_numeric($_POST['eshop_xtra_help'])){
				$ptitle=get_post($_POST['eshop_xtra_help']);
				if($ptitle->post_title!=''){
					$eshopoptions['xtra_help']=$wpdb->escape($_POST['eshop_xtra_help']);
				}else{
					$err.='<li>'.__('Help page id chosen','eshop').' ('.$_POST['eshop_xtra_help'].') '.__('is invalid.','eshop').'</li>';
				}	
			}elseif($_POST['eshop_xtra_help']!=''){
				$err.='<li>'.__('The help page needs to be a page id number.','eshop').'</li>';
			}else{
				$eshopoptions['xtra_help']='';
			}
			if(is_numeric($_POST['eshop_cart_shipping'])){
				$ptitle=get_post($_POST['eshop_cart_shipping']);
				if($ptitle->post_title!=''){
					$eshopoptions['cart_shipping']=$wpdb->escape($_POST['eshop_cart_shipping']);
				}else{
					$err.='<li>'.__('The Shipping rates page id chosen','eshop').' ('.$_POST['eshop_cart_shipping'].') '.__('is invalid.','eshop').'</li>';
				}	
			}elseif(trim($_POST['eshop_cart_shipping'])!=''){
					$err.='<li>'.__('The Shipping rates page needs to be a page id number.','eshop').'</li>';
			}else{
				$eshopoptions['cart_shipping']='';
			}
			
			if(is_numeric($_POST['eshop_shop_page'])){
				$ptitle=get_post($_POST['eshop_shop_page']);
				if($ptitle->post_title!=''){
					$eshopoptions['shop_page']=$wpdb->escape($_POST['eshop_shop_page']);
				}else{
					$err.='<li>'.__('The Main Shop page id chosen','eshop').' ('.$_POST['eshop_shop_page'].') '.__('is invalid.','eshop').'</li>';
				}	
			}elseif(trim($_POST['eshop_shop_page'])!=''){
					$err.='<li>'.__('The Main Shop page needs to be a page id number.','eshop').'</li>';
			}else{
				$eshopoptions['shop_page']='';
			}
			if(is_numeric($_POST['eshop_cart'])){
				$ptitle=get_post($_POST['eshop_cart']);
				if($ptitle->post_title!=''){
					$eshopoptions['cart']=$wpdb->escape($_POST['eshop_cart']);
				}else{
					$err.='<li>'.__('The Cart page id chosen','eshop').' ('.$_POST['eshop_cart'].') '.__('is invalid.','eshop').'</li>';
				}	
			}else{
				$err.='<li>'.__('The Cart page needs to be a page id number.','eshop').'</li>';
			}

			if(is_numeric($_POST['eshop_cart_cancel'])){
				$ptitle=get_post($_POST['eshop_cart_cancel']);
				if($ptitle->post_title!=''){
					$eshopoptions['cart_cancel']=$wpdb->escape($_POST['eshop_cart_cancel']);
				}else{
					$err.='<li>'.__('The Cancelled payment page id chosen','eshop').' ('.$_POST['eshop_cart_cancel'].') '.__('is invalid.','eshop').'</li>';
				}	
			}else{
				$err.='<li>'.__('The Cancelled payment page needs to be a page id number.','eshop').'</li>';
			}

			if(is_numeric($_POST['eshop_checkout'])){
				$ptitle=get_post($_POST['eshop_checkout']);
				if($ptitle->post_title!=''){
				$eshopoptions['checkout']=$wpdb->escape($_POST['eshop_checkout']);
				}else{
				$err.='<li>'.__('The Checkout page id chosen','eshop').' ('.$_POST['eshop_checkout'].') '.__('is invalid.','eshop').'</li>';
				}	
			}else{
				$err.='<li>'.__('The Checkout page needs to be a page id number.','eshop').'</li>';
			}

			if(is_numeric($_POST['eshop_cart_success'])){
				$ptitle=get_post($_POST['eshop_cart_success']);
				if($ptitle->post_title!=''){
					$eshopoptions['cart_success']=$wpdb->escape($_POST['eshop_cart_success']);
				}else{
					$err.='<li>'.__('The Successful payment page id chosen','eshop').' ('.$_POST['eshop_cart_success'].') '.__('is invalid.','eshop').'</li>';
				}	
			}else{
				$err.='<li>'.__('The Successful payment page needs to be a page id number.','eshop').'</li>';
			}

			if(is_numeric($_POST['eshop_show_downloads'])){
				$ptitle=get_post($_POST['eshop_show_downloads']);
				if($ptitle->post_title!=''){
					$eshopoptions['show_downloads']=$wpdb->escape($_POST['eshop_show_downloads']);
				}else{
					$err.='<li>'.__('The Downloads page id chosen','eshop').' ('.$_POST['eshop_show_downloads'].') '.__('is invalid.','eshop').'</li>';
				}	
			}else{
				$err.='<li>'.__('The Downloads page needs to be a page id number.','eshop').'</li>';
			}
			break;	
		case ('Downloads'):
			if(is_numeric($_POST['eshop_downloads_num'])){
				$eshopoptions['downloads_num']=$wpdb->escape($_POST['eshop_downloads_num']);
			}else{
				$err.='<li>'.__('Number of download attempts should be numeric, a default of 3 has been applied.','eshop').'</li>';
				$eshopoptions['downloads_num']='3';
			}
			$eshopoptions['downloads_only']=$wpdb->escape($_POST['eshop_downloads_only']);
			$eshopoptions['downloads_hideall']=$wpdb->escape($_POST['eshop_downloads_hideall']);

			break;	
		case ('Discounts'):
			if(is_numeric($_POST['eshop_discount_shipping'])){
				$eshopoptions['discount_shipping']=$wpdb->escape($_POST['eshop_discount_shipping']);
			}elseif($_POST['eshop_discount_shipping']!=''){
				$err.='<li>'.__('"Spend over to get free shipping" must be numeric!','eshop').'</li>';
				$eshopoptions['discount_shipping']='';
			}else{
				$eshopoptions['discount_shipping']='';
			}

			for ($x=1;$x<=3;$x++){
				if(is_numeric($_POST['eshop_discount_spend'.$x]) && is_numeric($_POST['eshop_discount_value'.$x])){
					$eshopoptions['discount_spend'.$x]=$wpdb->escape($_POST['eshop_discount_spend'.$x]);
					$eshopoptions['discount_value'.$x]=$wpdb->escape($_POST['eshop_discount_value'.$x]);
				}elseif($_POST['eshop_discount_spend'.$x]=='' || $_POST['eshop_discount_value'.$x]=='') {
					$eshopoptions['discount_spend'.$x]='';
					$eshopoptions['discount_value'.$x]='';
					if(($_POST['eshop_discount_spend'.$x]!='' && $_POST['eshop_discount_value'.$x]=='') || ($_POST['eshop_discount_spend'.$x]=='' && $_POST['eshop_discount_value'.$x]!='')){
						$err.='<li>'.__('Discount','eshop').' ' .$x.' '.__('Either "Spend" or "% Discount" was empty so both values were unset!','eshop').'</li>';
					}
				}else{
					$err.='<li>'.__('Discount','eshop').' ' .$x.' '.__('"Spend" and "% Discount" must be numeric!','eshop').'</li>';
				}
				if($_POST['eshop_discount_value'.$x]>=100) {
					$err.='<li>'.__('Discount','eshop').' ' .$x.' '.__('<strong>Warning</strong> % Discount is equal to or over 100%!','eshop').'</li>';
				}
				
			}
			break;	
		case ('General'):
			$eshopoptions['from_email']=$wpdb->escape($_POST['eshop_from_email']);
			$eshopoptions['cron_email']=$wpdb->escape($_POST['eshop_cron_email']);
			$eshopoptions['sysemails']=$wpdb->escape($_POST['eshop_sysemails']);
			$eshopoptions['currency_symbol']=$wpdb->escape($_POST['eshop_currency_symbol']);
			$eshopoptions['cart_nostock']=$wpdb->escape($_POST['eshop_cart_nostock']);
			$eshopoptions['credits']=$wpdb->escape($_POST['eshop_credits']);
			$eshopoptions['fold_menu']=$wpdb->escape($_POST['eshop_fold_menu']);
			$eshopoptions['hide_cartco']=$wpdb->escape($_POST['eshop_hide_cartco']);
			$eshopoptions['stock_control']=$wpdb->escape($_POST['eshop_stock_control']);
			$eshopoptions['show_stock']=$wpdb->escape($_POST['eshop_show_stock']);
			$eshopoptions['search_img']=$wpdb->escape($_POST['eshop_search_img']);
			$eshopoptions['show_forms']=$wpdb->escape($_POST['eshop_show_forms']);
			$eshopoptions['show_sku']=$wpdb->escape($_POST['eshop_show_sku']);
			$eshopoptions['addtocart_image']=$wpdb->escape($_POST['eshop_addtocart_image']);
			$eshopoptions['hide_addinfo']=$wpdb->escape($_POST['eshop_hide_addinfo']);
			$eshopoptions['hide_shipping']=$wpdb->escape($_POST['eshop_hide_shipping']);
			$eshopoptions['tandc']=$wpdb->escape($_POST['eshop_tandc']);
			$eshopoptions['tandc_use']=$wpdb->escape($_POST['eshop_tandc_use']);
			$eshopoptions['tandc_id']=$wpdb->escape($_POST['eshop_tandc_id']);
			$eshopoptions['set_cacheability']=$wpdb->escape($_POST['eshop_set_cacheability']);
			if (eshop_wp_version('3'))
				$eshopoptions['users']=$wpdb->escape($_POST['eshop_users']);

			//error grabbing
			if(is_numeric($_POST['eshop_records'])){
				$eshopoptions['records']=$wpdb->escape($_POST['eshop_records']);
			}else{
				$err.='<li>'.__('Orders per page should be numeric, a default of 10 has been applied.','eshop').'</li>';
				$eshopoptions['records']='10';
			}
			if(is_numeric($_POST['eshop_options_num']) && $_POST['eshop_options_num']>'0'){
				$eshopoptions['options_num']=$wpdb->escape($_POST['eshop_options_num']);
			}else{
				$err.='<li>'.__('Options per product should be numeric and be greater than 0, a default of 3 has been applied.','eshop').'</li>';
				$eshopoptions['options_num']='3';
			}

			if(is_numeric($_POST['eshop_image_in_cart']) || $_POST['eshop_image_in_cart']==''){
				$eshopoptions['image_in_cart']=$wpdb->escape($_POST['eshop_image_in_cart']);
			}else{
				$err.='<li>'.__('The number entered for the image in the cart must be numeric, a default of 75 has been applied.','eshop').'</li>';
				$eshopoptions['image_in_cart']='75';
			}
			if($_POST['eshop_currency_symbol']==''){
				$err.='<li>'.__('Currency Symbol was missing, the default $ has been applied.','eshop').'</li>';
				$eshopoptions['currency_symbol']='$';
			}
			if($_POST['eshop_status']=='live'){
				$statuserr='';
				if(!is_array($eshopoptions['method']))
					$statuserr.='<li>'.__('You must have a Merchant Gateway selected before you can go live!','eshop').'</li>';
				if($eshopoptions['from_email']=='')
					$statuserr.='<li>'.__('You must have set an eShop from email address before you can go live!','eshop').'</li>';
				if($statuserr=='')
					$eshopoptions['status']=$wpdb->escape($_POST['eshop_status']);
				else
					$err.=$statuserr;
			}else
				$eshopoptions['status']=$wpdb->escape($_POST['eshop_status']);
			
			break;
		case ('Base'):
				$eshopoptions['base_brand']=$wpdb->escape($_POST['eshop_base_brand']);
				$eshopoptions['base_condition']=$wpdb->escape($_POST['eshop_base_condition']);
				$eshopoptions['base_expiry']=$wpdb->escape($_POST['eshop_base_expiry']);
				$eshopoptions['base_ptype']=$wpdb->escape($_POST['eshop_base_ptype']);
				$eshopoptions['base_payment']=$wpdb->escape($_POST['eshop_base_payment']);
			break;
	}
 	//send an error: create_error('test me out');
 	//update options
	update_option('eshop_plugin_settings',$eshopoptions);
}
if($err!=''){
	create_eshop_error('<p>'.__('<strong>Error</strong> the following were not valid:','eshop').'</p><ul>'.$err.'</ul>');
}
echo '<div class="wrap">';
echo '<div id="eshopicon" class="icon32"></div><h2>'.__('eShop Settings','eshop').'</h2>'."\n";
//info:
echo '<p class="eshopwarn">';
if('live' == $eshopoptions['status'])
	_e('eShop is currently <span class="live">Live</span>.','eshop');
else
	_e('eShop is currently in <span class="test">Test Mode</span>.','eshop');

if(is_array($eshopoptions['method']))
    echo __(' Merchant Gateways in use:','eshop').' <span class="eshopgate">'.ucwords(implode(', ',(array)$eshopoptions['method'])).'</span>';
else
    _e(' No Merchant Gateway selected.','eshop');
echo '</p>';
//the submenu 
echo '<ul class="subsubsub">';
echo implode(' | </li>', $status_links) . '</li>';
echo '</ul><br class="clear" />';
if(isset($_GET['resetbase']) && $_GET['resetbase']=='yes'){
	$table=$wpdb->prefix.'eshop_base_products';
	$wpdb->query("TRUNCATE TABLE $table"); 
	echo '<div id="message" class="updated fade"><p>'.__('eShop Base product data has been reset.','eshop').'</p></div>'."\n";
}elseif(isset($_POST['submit'])){
	echo'<div id="message" class="updated fade"><p>'.__('eshop Settings have been updated.','eshop').'</p></div>'."\n";
}
/* submenu end */

switch($action_status){
	case ('Base'):
	?>
	<form method="post" action="" id="eshop-settings">
	<?php wp_nonce_field('update-options') ?>
	<fieldset><legend><?php _e('eShop Base Options','eshop'); ?></legend>
	
	<label for="eshop_base_brand"><?php _e('Brand','eshop'); ?></label><input id="eshop_base_brand" name="eshop_base_brand" type="text" value="<?php echo $eshopoptions['base_brand']; ?>" size="30" /><br />
	<label for="eshop_base_condition"><?php _e('Condition','eshop'); ?></label>
		<select name="eshop_base_condition" id="eshop_base_condition">
		<?php
			
		foreach($currentconditions as $code){
			if($code == $eshopoptions['base_condition']){
				$sel=' selected="selected"';
			}else{
				$sel='';
			}
			echo '<option value="'. $code .'"'. $sel .'>'. $code .'</option>';
		}
		
		?>
		</select><br />
	<label for="eshop_base_expiry"><?php _e('Product expiry in days','eshop'); ?></label>
		<select name="eshop_base_expiry" id="eshop_base_expiry">
		<?php
		$currentexpiry=array('1', '7', '28', '180', '365','730');
		foreach($currentexpiry as $code){
			if($code == $eshopoptions['base_expiry']){
				$sel=' selected="selected"';
			}else{
				$sel='';
			}
			echo '<option value="'. $code .'"'. $sel .'>'. $code .'</option>';
		}
		?>
	</select><br />
	
	<label for="eshop_base_ptype"><?php _e('Product type','eshop'); ?></label><input id="eshop_base_ptype" name="eshop_base_ptype" type="text" value="<?php echo $eshopoptions['base_ptype']; ?>" size="30" /><br />
	<label for="eshop_base_payment"><?php _e('Payment Accepted <small> comma delimited list of payment methods available.</small>','eshop'); ?></label><input id="eshop_base_payment" name="eshop_base_payment" type="text" value="<?php echo $eshopoptions['base_payment']; ?>" size="30" /><br />
	
	<input type="hidden" name="page_options" value="eshop_base_brand,eshop_base_condition,
	eshop_base_expiry,eshop_base_ptype,eshop_base_payment" />
	
	</fieldset>
	<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;','eshop') ?>" />
	</p>
	</form>
	
	</div>
	<div class="wrap">
	<h2><?php _e('Reset eShop Base','eshop'); ?></h2>
	<p><?php _e('This resets all product data entered on the <a href="admin.php?page=eshop_base.php">eShop Base Products</a> page.','eshop'); ?></p>
	<p class="ebox"><a class="ebox" href="?page=eshop_settings.php&amp;eshop=Base&amp;resetbase=yes"><?php _e('Reset Now','eshop'); ?></a></p>
	</div>
	<?php
	break;
	case ('Merchant'):
	?>
	<form method="post" action="" id="eshop-settings">
	<input type='hidden' name='option_page' value='eshop_settings' />
	<?php wp_nonce_field('update-options') ?>
	<fieldset><legend><?php _e('General Settings','eshop'); ?></legend>
	<label for="eshop_location"><?php _e('Business Location','eshop'); ?></label>
			<select name="eshop_location" id="eshop_location">
			<?php
			$ctable=$wpdb->prefix.'eshop_countries';
			$currentlocations=$wpdb->get_results("SELECT * from $ctable ORDER BY country");
			//$currentlocations=array('GB', 'US', 'JP', 'CA', 'DE');
			foreach ($currentlocations as $row){
				if($row->code == $eshopoptions['location']){
					$sel=' selected="selected"';
				}else{
					$sel='';
				}
				echo '<option value="'. $row->code .'"'. $sel .'>'. $row->country .'</option>';
			}
			?>
		</select><br />
		<p><?php printf(__('Don\'t forget to set the <a href="%s">State/County/Province</a> on the shipping pages.','eshop'),'admin.php?page=eshop_shipping.php&amp;action=states'); ?></p>
	<?php //' fix my code colours - not needed elswhere ?>
		<label for="eshop_currency"><?php _e('Currency Code','eshop'); ?></label>
			<select name="eshop_currency" id="eshop_currency">
			<?php
			$currencycodes=array(
			'GBP'=>__('Pounds Sterling','eshop'),
			'USD'=>__('U.S. Dollars','eshop'),
			'EUR'=>__('Euros','eshop'),
			'AUD'=>__('Australian Dollars','eshop'),
			'BRL'=>__('Brazilian Real','eshop'),
			'CAD'=>__('Canadian Dollars','eshop'),
			'CHF'=>__('Swiss Franc','eshop'),
			'CZK'=>__('Czech Koruna','eshop'),
			'DKK'=>__('Danish Krone','eshop'),
			'HKD'=>__('Hong Kong Dollar','eshop'),
			'HUF'=>__('Hungarian Forint','eshop'),
			'ILS'=>__('Israeli Shekel','eshop'),
			'JPY'=>__('Japan Yen','eshop'),
			'LTL' =>__('Lithuanian Litas','eshop'),
			'LVL'=>__('Latvijas lats','eshop'),
			'MXN'=>__('Mexican Peso','eshop'),
			'NOK'=>__('Norwegian Krone','eshop'),
			'NZD'=>__('New Zealand Dollar','eshop'),
			'PHP'=>__('Philippine Pesos','eshop'),
			'PLN'=>__('Polish Zloty','eshop'),
			'MYR' => __('Ringgit Malaysia','eshop'),
			'SEK'=>__('Swedish Krona','eshop'),
			'SGD'=>__('Singapore Dollar','eshop'),
			'TL' => __('Turkish Lira','eshop')
			);
			foreach($currencycodes as $code=>$codename){
				if($code == $eshopoptions['currency']){
					$sel=' selected="selected"';
				}else{
					$sel='';
				}
				echo '<option value="'. $code .'"'. $sel .'>'. $codename.' ('.$code.')' .'</option>';
			}
			?>
		</select><br />
	</fieldset>
	<fieldset><legend><?php _e('Merchant Gateways','eshop'); ?></legend>
	<fieldset><legend><?php _e('Paypal','eshop'); ?></legend>
		<p class="cbox"><input id="eshop_method" name="eshop_method[]" type="checkbox" value="paypal"<?php if(in_array('paypal',(array)$eshopoptions['method'])) echo ' checked="checked"'; ?> /><label for="eshop_method"><?php _e('Accept payment by Paypal','eshop'); ?></label></p>
		<label for="eshop_business"><?php _e('Email address','eshop'); ?></label><input id="eshop_business" name="eshop_business" type="text" value="<?php echo $eshopoptions['business']; ?>" size="30" /><br />
		<label for="eshop_paypal_noemail"><?php _e('Send buyers email address to paypal?','eshop'); ?></label>
				<select name="eshop_paypal_noemail" id="eshop_paypal_noemail">
				<?php
				if('no' == $eshopoptions['paypal_noemail']){
					echo '<option value="yes">'.__('Yes','eshop').'</option>';
					echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
				}else{
					echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
					echo '<option value="no">'.__('No','eshop').'</option>';
				}
				?>
			</select><br />
	</fieldset>
	<fieldset><legend><?php _e('Payson','eshop'); ?></legend>
	<p><?php _e('<strong>Warning:</strong> Payson has a minimum purchase value of 4 SEK (when last checked). All payments to Payson are in SEK, irrespective of settings above.','eshop'); ?></p>
	<?php $payson = $eshopoptions['payson']; ?>

		<p class="cbox"><input id="eshop_methodb" name="eshop_method[]" type="checkbox" value="payson"<?php if(in_array('payson',(array)$eshopoptions['method'])) echo ' checked="checked"'; ?> /><label for="eshop_methodb"><?php _e('Accept payment by Payson','eshop'); ?></label></p>
		<label for="eshop_paysonemail"><?php _e('Email address','eshop'); ?></label><input id="eshop_paysonemail" name="payson[email]" type="text" value="<?php echo $payson['email']; ?>" size="30" maxlength="50" /><br />
		<label for="eshop_paysonid"><?php _e('Agent ID','eshop'); ?></label><input id="eshop_paysonid" name="payson[id]" type="text" value="<?php echo $payson['id']; ?>" size="20" /><br />
		<label for="eshop_paysonkey"><?php _e('Secret Key','eshop'); ?></label><input id="eshop_paysonkey" name="payson[key]" type="text" value="<?php echo $payson['key']; ?>" size="40" /><br />
		<label for="eshop_paysondesc"><?php _e('Cart Description','eshop'); ?></label><input id="eshop_paysondesc" name="payson[description]" type="text" value="<?php echo $payson['description']; ?>" size="50" maxlength="200" /><br />
		<label for="eshop_paysonmin"><?php _e('Min. Cart value','eshop'); ?></label>
		<select name="payson[minimum]" id="eshop_paysonmin">
			<?php
			for($i=1;$i<=20;$i++){
			?>
				<option value="<?php echo $i; ?>"<?php if($payson['minimum']==$i) echo ' selected="selected"'; ?>><?php echo $i; ?></option>
			<?php
			}
			?>
	</select><br />
	</fieldset>
	<fieldset><legend><?php _e('iDeal Lite','eshop'); ?></legend>
	<?php $ideallite = $eshopoptions['ideallite']; ?>
		<p class="cbox"><input id="eshop_methodc" name="eshop_method[]" type="checkbox" value="ideallite"<?php if(in_array('ideallite',(array)$eshopoptions['method'])) echo ' checked="checked"'; ?> /><label for="eshop_methodc"><?php _e('Accept payment by iDeal Lite','eshop'); ?></label></p>
		<label for="eshop_IDEAL_AQUIRER"><?php _e('Aquirer','eshop'); ?></label><input id="eshop_IDEAL_AQUIRER" name="ideallite[IDEAL_AQUIRER]" type="text" value="<?php echo $ideallite['IDEAL_AQUIRER']; ?>" size="40" maxlength="50" /><em><?php _e('Use Rabobank, ING Bank or Simulator','eshop'); ?></em><br />
		<label for="eshop_IDEAL_HASH_KEY"><?php _e('Hash Key','eshop'); ?></label><input id="eshop_IDEAL_HASH_KEY" name="ideallite[IDEAL_HASH_KEY]" type="text" value="<?php echo $ideallite['IDEAL_HASH_KEY']; ?>" size="20" /><em><?php _e('For Simulator use "Password"','eshop'); ?></em><br />
		<label for="eshop_IDEAL_MERCHANT_ID"><?php _e('Merchant ID','eshop'); ?></label><input id="eshop_IDEAL_MERCHANT_ID" name="ideallite[IDEAL_MERCHANT_ID]" type="text" value="<?php echo $ideallite['IDEAL_MERCHANT_ID']; ?>" size="40" /><em><?php _e('For Simulator use "123456789"','eshop'); ?></em><br />
		<label for="eshop_IDEAL_SUB_ID"><?php _e('Sub ID','eshop'); ?></label><input id="eshop_IDEAL_SUB_ID" name="ideallite[IDEAL_SUB_ID]" type="text" value="<?php echo $ideallite['IDEAL_SUB_ID']; ?>" size="40" /><em><?php _e('Unless you know what you\'re doing. Leave this to "0"','eshop'); ?></em><br />
		<label for="eshop_IDEAL_TEST_MODE"><?php _e('Test Mode','eshop'); ?></label><input id="eshop_IDEAL_TEST_MODE" name="ideallite[IDEAL_TEST_MODE]" type="text" value="<?php echo $ideallite['IDEAL_TEST_MODE']; ?>" size="20" maxlength="20" /><em><?php _e('Use "true" or "false"','eshop'); ?></em><br />
		<br />
		<label for="eshop_idealownermail"><?php _e('Email address','eshop'); ?></label><input id="eshop_idealownermail" name="ideallite[idealownermail]" type="text" value="<?php echo $ideallite['idealownermail']; ?>" size="40" maxlength="30" /><em><?php _e('Order notifications are sent to this address.','eshop'); ?></em><br />
		<label for="eshop_idealdescription"><?php _e('Description','eshop'); ?></label><input id="eshop_idealdescription" name="ideallite[idealdescription]" type="text" value="<?php echo $ideallite['idealdescription']; ?>" size="40" maxlength="30" /><em><?php _e('Description for the iDEAL payment','eshop'); ?></em><br />
		<br />
	</fieldset>
	<fieldset><legend><?php _e('eProcessingNetwork','eshop'); ?></legend>
		<p><?php _e('<strong>Warning:</strong> All payments to eProcessingNetwork are in USD, irrespective of settings above. In test mode totals ending in a single cent are always failed.','eshop'); ?></p>
		<?php $epn = $eshopoptions['epn']; ?>
		<p class="cbox"><input id="eshop_methodd" name="eshop_method[]" type="checkbox" value="epn"<?php if(in_array('epn',(array)$eshopoptions['method'])) echo ' checked="checked"'; ?> /><label for="eshop_methodd"><?php _e('Accept payment by eProcessingNetwork','eshop'); ?></label></p>
		<label for="eshop_epnemail"><?php _e('Email address','eshop'); ?></label><input id="eshop_epnemail" name="epn[email]" type="text" value="<?php echo $epn['email']; ?>" size="30" /><br />
		<label for="eshop_epnid"><?php _e('User ID','eshop'); ?></label><input id="eshop_epnid" name="epn[id]" type="text" value="<?php echo $epn['id']; ?>" size="20" /><br />
		<label for="eshop_epndesc"><?php _e('Cart Description','eshop'); ?></label><input id="eshop_epndesc" name="epn[description]" type="text" value="<?php echo $epn['description']; ?>" size="50" maxlength="200" /><br />
	</fieldset>
	<fieldset><legend><?php _e('Cash','eshop'); ?></legend>
		<p><?php _e('<strong>Note:</strong> payment by other means, usually used for offline payments.','eshop'); ?></p>
		<?php $eshopcash = $eshopoptions['cash']; ?>
		<p class="cbox"><input id="eshop_methode" name="eshop_method[]" type="checkbox" value="cash"<?php if(in_array('cash',(array)$eshopoptions['method'])) echo ' checked="checked"'; ?> /><label for="eshop_methode"><?php _e('Accept cash payments','eshop'); ?></label></p>
		<label for="eshop_cashemail"><?php _e('Email address','eshop'); ?></label><input id="eshop_cashemail" name="cash[email]" type="text" value="<?php echo $eshopcash['email']; ?>" size="30" maxlength="50" /><br />
		<label for="eshop_cashrename"><?php _e('Change Cash name to','eshop'); ?></label><input id="eshop_cashrename" name="cash[rename]" type="text" value="<?php echo $eshopcash['rename']; ?>" size="30" maxlength="50" /><br />

		</fieldset>
		
	<fieldset><legend><?php _e('Webtopay','eshop'); ?></legend>
	<p><?php _e('<strong>Note:</strong> payment by other means, usually used for offline payments.','eshop'); ?></p>
	<?php $eshopwebtopay = $eshopoptions['webtopay']; ?>
	<p class="cbox"><input id="eshop_methodf" name="eshop_method[]" type="checkbox" value="webtopay"<?php if(in_array('webtopay',(array)$eshopoptions['method'])) echo ' checked="checked"'; ?> /><label for="eshop_methodf"><?php _e('Accept webtopay payments','eshop'); ?></label></p>
			
	<label for="eshop_webtopayid"><?php _e('Webtopay user ID','eshop'); ?></label>
	<input id="eshop_webtopayid" name="webtopay[id]" type="text" value="<?php echo $eshopwebtopay['id']; ?>" size="30" maxlength="50" /><br />
			
	<label for="eshop_webtopaypassword"><?php _e('Webtopay password','eshop'); ?></label>
	<input id="eshop_webtopaypassword" name="webtopay[password]" type="password" value="<?php echo $eshopwebtopay['password']; ?>" size="30" maxlength="50" /><br />
			
	<label for="eshop_webtopaylang"><?php _e('Webtopay language (ENG ESP EST FIN FRE GEO GER ITA LAV LIT NOR POL ROU RUS SPA SWE)','eshop'); ?></label>
	<input id="eshop_webtopaylang" name="webtopay[lang]" type="text" value="<?php echo $eshopwebtopay['lang']; ?>" size="30" maxlength="50" /><br />
	
	<label for="eshop_webtopayprojectid"><?php _e('Webtopay project ID','eshop'); ?></label>
	<input id="eshop_webtopayprojectid" name="webtopay[projectid]" type="text" value="<?php echo $eshopwebtopay['projectid']; ?>" size="30" maxlength="50" /><br />
	                        
	<label for="eshop_webtopaysignature"><?php _e('Webtopay signature password','eshop'); ?></label>
	<input id="eshop_webtopaysignature" name="webtopay[signature]" type="text" value="<?php echo $eshopwebtopay['signature']; ?>" size="30" maxlength="50" /><br />
	
	</fieldset>
	
	<fieldset><legend><?php _e('Authorize.net','eshop'); ?></legend>
		<?php $authorizenet = $eshopoptions['authorizenet']; ?>
		<p class="cbox"><input id="eshop_methodg" name="eshop_method[]" type="checkbox" value="authorize.net"<?php if(in_array('authorize.net',(array)$eshopoptions['method'])) echo ' checked="checked"'; ?> /><label for="eshop_methodg"><?php _e('Accept payment by Authorize.net','eshop'); ?></label></p>
		<label for="eshop_authorizenetemail"><?php _e('Email address','eshop'); ?></label><input id="eshop_authorizenetemail" name="authorizenet[email]" type="text" value="<?php echo $authorizenet['email']; ?>" size="30" maxlength="50" /><br />
		<label for="eshop_authorizenetid"><?php _e('API Login ID','eshop'); ?></label><input id="eshop_authorizenetid" name="authorizenet[id]" type="text" value="<?php echo $authorizenet['id']; ?>" size="20" /><br />
		<label for="eshop_authorizenetkey"><?php _e('Transaction Key','eshop'); ?></label><input id="eshop_authorizenetkey" name="authorizenet[key]" type="text" value="<?php echo $authorizenet['key']; ?>" size="40" /><br />
		<label for="eshop_authorizenetsecret"><?php _e('MD5-Hash Phrase(was Secret Answer)','eshop'); ?></label><input id="eshop_authorizenetsecret" name="authorizenet[secret]" type="text" value="<?php echo $authorizenet['secret']; ?>" size="40" /><br />
		<label for="eshop_authorizenetdesc"><?php _e('Cart description','eshop'); ?></label><input id="eshop_authorizenetdesc" name="authorizenet[desc]" type="text" value="<?php echo $authorizenet['desc']; ?>" size="40" /><br />
	</fieldset>
	
	
	</fieldset>
	<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;','eshop') ?>" />
	</p>
	</form>
	
	</div>
<?php
	break;
	case ('Discounts'):
		?>
		<form method="post" action="" id="eshop-settings">
		<input type='hidden' name='option_page' value='eshop_settings' />
		<?php wp_nonce_field('update-options') ?>

<fieldset><legend><?php _e('Discounts','eshop'); ?></legend>
<p><?php _e('In all cases deleting the entry will disable the discount.', 'eshop'); ?></p>
<table class="hidealllabels widefat eshopdisc" summary="<?php _e('Discount for amount sold','eshop'); ?>">
	<caption><?php _e('Discount for amount sold','eshop'); ?></caption>
	<thead>
	<tr>
	<th id="elevel"><?php _e('Discounts','eshop'); ?></th>
	<th id="espend"><?php _e('Spend','eshop'); ?></th>
	<th id="ediscount"><?php _e('% Discount','eshop'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	for ($x=1;$x<=3;$x++){
	?>
	<tr>
	<th headers="elevel"  id="row<?php echo $x ?>"><?php echo $x ?></th>
	<td headers="elevel espend row<?php echo $x ?>"><label for="eshop_discount_spend<?php echo $x ?>"><?php _e('Spend','eshop'); ?></label><input id="eshop_discount_spend<?php echo $x ?>" name="eshop_discount_spend<?php echo $x ?>" type="text" value="<?php echo $eshopoptions['discount_spend'.$x]; ?>" size="5" /></td>
	<td headers="elevel ediscount row<?php echo $x ?>"><label for="eshop_discount_value<?php echo $x ?>"><?php _e('Discount','eshop'); ?></label><input id="eshop_discount_value<?php echo $x ?>" name="eshop_discount_value<?php echo $x ?>" type="text" value="<?php echo $eshopoptions['discount_value'.$x]; ?>" size="5" maxlength="5" /></td>
	</tr>
	<?php
	}
	?>
	</tbody>
	</table>
<p><label for="eshop_discount_shipping"><?php _e('Spend over to get free shipping','eshop'); ?></label><input id="eshop_discount_shipping" name="eshop_discount_shipping" type="text" value="<?php echo $eshopoptions['discount_shipping']; ?>" size="5" /></p>
</fieldset>
		

		<p class="submit">
		<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;','eshop') ?>" />
		</p>
		</form>

		</div>
	<?php
	break;
	case ('Downloads'):
		?>
		<form method="post" action="" id="eshop-settings">
		<input type='hidden' name='option_page' value='eshop_settings' />
		<?php wp_nonce_field('update-options') ?>
	
		<fieldset><legend><?php _e('Downloadables','eshop'); ?></legend>
		<label for="eshop_downloads_num"><?php _e('Download attempts','eshop'); ?></label><input id="eshop_downloads_num" name="eshop_downloads_num" type="text" value="<?php echo $eshopoptions['downloads_num']; ?>" size="5" /><br />
		</fieldset>
		<fieldset><legend><?php _e('Downloads Only','eshop'); ?></legend>
		<p><?php _e('Change this setting only if you are using eShop for downloadable sales only.','eshop'); ?></p>
		<label for="eshop_downloads_only"><?php _e('Downloads Only','eshop'); ?></label>
			<select name="eshop_downloads_only" id="eshop_downloads_only">
			<?php
			if('yes' == $eshopoptions['downloads_only']){
				echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
				echo '<option value="no">'.__('No','eshop').'</option>';
			}else{
				echo '<option value="yes">'.__('Yes','eshop').'</option>';
				echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
			}
			?>
			</select><br />
		</fieldset>
			<fieldset><legend><?php _e('Download All','eshop'); ?></legend>
			<p><?php _e('As some downloads can be quite large, people may experience errors if they try and download all files in one go.','eshop'); ?></p>
			<label for="eshop_downloads_hideall"><?php _e('Hide download all form','eshop'); ?></label>
				<select name="eshop_downloads_hideall" id="eshop_downloads_hideall">
				<?php
				if('yes' == $eshopoptions['downloads_hideall']){
					echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
					echo '<option value="no">'.__('No','eshop').'</option>';
				}else{
					echo '<option value="yes">'.__('Yes','eshop').'</option>';
					echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
				}
				?>
				</select><br />
		</fieldset>
		<p class="submit">
		<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;','eshop') ?>" />
		</p>
		</form>
		
		</div>
<?php
	break;
	case ('Pages'):
		?>
		<form method="post" action="" id="eshop-settings">
		<input type='hidden' name='option_page' value='eshop_settings' />
		<?php wp_nonce_field('update-options') ?>
		<fieldset><legend><?php _e('Continue Shopping Link','eshop'); ?></legend>
			<p><?php _e('If you enter the page id of your main Shop page, then eShop will use that for the <strong>Continue Shopping</strong> link. Leave this blank and eShop will either link to the last product, or to the main page of your site automatically.','eshop'); ?></p>
			<label for="eshop_shop_page"><?php _e('Shop Page - page id number','eshop'); ?></label><input id="eshop_shop_page" name="eshop_shop_page" type="text" value="<?php echo $eshopoptions['shop_page']; ?>" size="5" /><br />
		</fieldset>
		<fieldset><legend><?php _e('Link to extra pages','eshop'); ?></legend>
		<p><?php _e('These links automatically appear on the checkout page.','eshop'); ?></p>
		<label for="eshop_cart_shipping"><?php _e('Shipping rates - page id number','eshop'); ?></label><input id="eshop_cart_shipping" name="eshop_cart_shipping" type="text" value="<?php echo $eshopoptions['cart_shipping']; ?>" size="5" /><br />
		<label for="eshop_xtra_privacy"><?php _e('Privacy Policy - page id number','eshop'); ?></label><input id="eshop_xtra_privacy" name="eshop_xtra_privacy" type="text" value="<?php echo $eshopoptions['xtra_privacy']; ?>" size="5" /><br />
		<label for="eshop_xtra_help"><?php _e('Help - page id number','eshop'); ?></label><input id="eshop_xtra_help" name="eshop_xtra_help" type="text" value="<?php echo $eshopoptions['xtra_help']; ?>" size="5" /><br />
		</fieldset>
						
		<fieldset><legend><?php _e('Automatically created pages','eshop'); ?></legend>
		<p class="warn"><?php _e('<strong>Warning:</strong> Changes made here amend the page id of the automatically created pages - change with extreme care.','eshop'); ?></p>
		<label for="eshop_cart"><?php _e('Cart - page id number','eshop'); ?></label><input id="eshop_cart" name="eshop_cart" type="text" value="<?php echo $eshopoptions['cart']; ?>" size="5" /><br />
		<label for="eshop_checkout"><?php _e('Checkout - page id number','eshop'); ?></label><input id="eshop_checkout" name="eshop_checkout" type="text" value="<?php echo $eshopoptions['checkout']; ?>" size="5" /><br />
		<label for="eshop_cart_success"><?php _e('Successful payment  - page id number','eshop'); ?></label><input id="eshop_cart_success" name="eshop_cart_success" type="text" value="<?php echo $eshopoptions['cart_success']; ?>" size="5" /><br />
		<label for="eshop_cart_cancel"><?php _e('Cancelled payment - page id number','eshop'); ?></label><input id="eshop_cart_cancel" name="eshop_cart_cancel" type="text" value="<?php echo $eshopoptions['cart_cancel']; ?>" size="5" /><br />
		<label for="eshop_show_downloads"><?php _e('Downloads - page id number','eshop'); ?></label><input id="eshop_show_downloads" name="eshop_show_downloads" type="text" value="<?php echo $eshopoptions['show_downloads']; ?>" size="5" /><br />
		</fieldset>
						
		<p class="submit">
		<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;','eshop') ?>" />
		</p>
		</form>
		
		</div>
<?php
	break;
	
	
	case('General'):
	
	

?>
<form method="post" action="" id="eshop-settings">
<input type='hidden' name='option_page' value='eshop_settings' />
<?php wp_nonce_field('update-options') ?>
<fieldset><legend><?php _e('eShop Admin','eshop'); ?></legend>
<label for="eshop_status"><?php _e('eShop status','eshop'); ?></label>
	<select name="eshop_status" id="eshop_status">
	<?php
	if('live' == $eshopoptions['status']){
		echo '<option value="live" selected="selected">'.__('Live','eshop').'</option>';
		echo '<option value="testing">'.__('Testing','eshop').'</option>';
	}else{
		echo '<option value="live">'.__('Live','eshop').'</option>';
		echo '<option value="testing" selected="selected">'.__('Testing','eshop').'</option>';
	}
	?>
	</select><br />
<label for="eshop_records"><?php _e('Orders per page','eshop'); ?></label><input id="eshop_records" name="eshop_records" type="text" value="<?php echo $eshopoptions['records']; ?>" size="5" /><br />
</fieldset>

<fieldset><legend><?php _e('Business Details','eshop'); ?></legend>
<label for="eshop_from_email"><?php _e('eShop From email address','eshop'); ?></label><input id="eshop_from_email" name="eshop_from_email" type="text" value="<?php echo $eshopoptions['from_email']; ?>" size="30" /><br />
<label for="eshop_sysemails"><?php _e('Available business email addresses','eshop'); ?></label>
<textarea id="eshop_sysemails" name="eshop_sysemails" rows="5" cols="50">
<?php echo $eshopoptions['sysemails']; ?>
</textarea>
<br />
</fieldset>

<fieldset><legend><?php _e('Product Options','eshop'); ?></legend>
<label for="eshop_options_num"><?php _e('Options per product','eshop'); ?></label><input id="eshop_options_num" name="eshop_options_num" type="text" value="<?php echo $eshopoptions['options_num']; ?>" size="5" /><br />
<label for="eshop_cart_nostock"><?php _e('Out of Stock message','eshop'); ?></label><input id="eshop_cart_nostock" name="eshop_cart_nostock" type="text" value="<?php echo $eshopoptions['cart_nostock']; ?>" size="30" /><br />
<label for="eshop_stock_control"><?php _e('Stock Control','eshop')._e(' <small>(Warning: setting this will make all products have zero stock, each one will have to be set manually.</small>)','eshop'); ?></label>
	<select name="eshop_stock_control" id="eshop_stock_control">
	<?php
	if('yes' == $eshopoptions['stock_control']){
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
	if('yes' == $eshopoptions['show_stock']){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />
<label for="eshop_show_sku"><?php _e('Show product sku','eshop'); ?></label>
	<select name="eshop_show_sku" id="eshop_show_sku">
	<?php
	if('yes' == $eshopoptions['show_sku']){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />


</fieldset>




<fieldset><legend><?php _e('Currency','eshop'); ?></legend>

<label for="eshop_currency_symbol"><?php _e('Symbol','eshop'); ?></label><input id="eshop_currency_symbol" name="eshop_currency_symbol" type="text" value="<?php echo $eshopoptions['currency_symbol']; ?>" size="10" /><br />

</fieldset>
<fieldset><legend><?php _e('Product Listings','eshop'); ?></legend>
	<label for="eshop_show_forms"><?php _e('Show add to cart forms on WordPress post listings. <span class="warn"><span>Warning</span> this can invalidate your html!</span>','eshop'); ?></label>
	<select name="eshop_show_forms" id="eshop_show_forms">
	<?php
	if('yes' == $eshopoptions['show_forms']){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />
</fieldset>

<fieldset><legend><?php _e('Cart Options','eshop'); ?></legend>
	<label for="eshop_image_in_cart"><?php _e('Percentage size of thumbnail image shown in cart - leave blank to not show the image.','eshop'); ?></label><input id="eshop_image_in_cart" name="eshop_image_in_cart" type="text" value="<?php echo $eshopoptions['image_in_cart']; ?>" size="5" /><br />
<label for="eshop_addtocart_image"><?php _e('Use an add to cart image or button?','eshop'); ?></label>
	<select name="eshop_addtocart_image" id="eshop_addtocart_image">
	<?php
	if('img' == $eshopoptions['addtocart_image']){
		echo '<option value="img" selected="selected">'.__('Image','eshop').'</option>';
		echo '<option value="">'.__('Button','eshop').'</option>';
	}else{
		echo '<option value="img">'.__('Image','eshop').'</option>';
		echo '<option value="" selected="selected">'.__('Button','eshop').'</option>';
	}
	?>
	</select><br />
	
</fieldset>
<fieldset><legend><?php _e('Checkout Options','eshop'); ?></legend>
<label for="eshop_hide_addinfo"><?php _e('Hide the Additional information form fields.','eshop'); ?></label>
	<select name="eshop_hide_addinfo" id="eshop_hide_addinfo">
	<?php
	if('yes' == $eshopoptions['hide_addinfo']){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />
<label for="eshop_hide_shipping"><?php _e('Hide the shipping address form fields.','eshop'); ?></label>
	<select name="eshop_hide_shipping" id="eshop_hide_shipping">
	<?php
	if('yes' == $eshopoptions['hide_shipping']){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br /><br />
	<label for="eshop_tandc_use"><?php _e('Add a required checkbox to the checkout.','eshop'); ?></label>
	<select name="eshop_tandc_use" id="eshop_tandc_use">
		<?php
		if('yes' == $eshopoptions['tandc_use']){
			echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
			echo '<option value="">'.__('No','eshop').'</option>';
		}else{
			echo '<option value="yes">'.__('Yes','eshop').'</option>';
			echo '<option value="" selected="selected">'.__('No','eshop').'</option>';
		}
		?>
	</select><br />
	<label for="eshop_tandc"><?php _e('Text for the required checkbox.','eshop'); ?></label><input id="eshop_tandc" name="eshop_tandc" type="text" value="<?php echo $eshopoptions['tandc']; ?>" size="60" /><br />
	<label for="eshop_tandc_id"><?php _e('Page id (transforms text above into a link).','eshop'); ?></label><input id="eshop_tandc_id" name="eshop_tandc_id" type="text" value="<?php echo $eshopoptions['tandc_id']; ?>" size="6" /><br />
<?php if (eshop_wp_version('3')){ ?>
<label for="eshop_users"><?php _e('Allow users to sign up. <small>(You should ensure that {LOGIN_DETAILS} is in your email templates.)</small>','eshop'); ?></label>
	<select name="eshop_users" id="eshop_users">
		<?php
		if('yes' == $eshopoptions['users']){
			echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
			echo '<option value="">'.__('No','eshop').'</option>';
		}else{
			echo '<option value="yes">'.__('Yes','eshop').'</option>';
			echo '<option value="" selected="selected">'.__('No','eshop').'</option>';
		}
		?>
	</select><br />
<?php } ?>
</fieldset>
<fieldset><legend><?php _e('Sub pages','eshop'); ?></legend>
<label for="eshop_fold_menu"><?php _e('Hide sub pages from menu until top level page is visited.','eshop'); ?></label>
	<select name="eshop_fold_menu" id="eshop_fold_menu">
	<?php
	if('yes' == $eshopoptions['fold_menu']){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />
	<label for="eshop_hide_cartco"><?php _e('Hide cart and checkout pages until items are in cart.','eshop'); ?></label>
		<select name="eshop_hide_cartco" id="eshop_hide_cartco">
		<?php
		if('yes' == $eshopoptions['hide_cartco']){
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
	if('yes' == $eshopoptions['search_img']){
		echo '<option value="no">'.__('No','eshop').'</option>';
		echo '<option value="all">'.__('All pages and posts','eshop').'</option>';
		echo '<option value="yes" selected="selected">'.__('eShop products pages and posts only','eshop').'</option>';
	}elseif('all' == $eshopoptions['search_img']){
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
	if('yes' == $eshopoptions['credits']){
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
<label for="eshop_cron_email"><?php _e('Cron Email address','eshop'); ?></label><input id="eshop_cron_email" name="eshop_cron_email" type="text" value="<?php echo $eshopoptions['cron_email']; ?>" size="30" /><br />
</fieldset>
<fieldset><legend><?php _e('Cacheability','eshop'); ?></legend>
<label for="eshop_set_cacheability"><?php _e('Disable WP Supercache for eShop pages including cart, checkout and pages using shortcodes.','eshop'); ?></label>
	<select name="eshop_set_cacheability" id="eshop_set_cacheability">
	<?php
	if('yes' == $eshopoptions['set_cacheability']){
		echo '<option value="yes" selected="selected">'.__('Yes','eshop').'</option>';
		echo '<option value="no">'.__('No','eshop').'</option>';
	}else{
		echo '<option value="yes">'.__('Yes','eshop').'</option>';
		echo '<option value="no" selected="selected">'.__('No','eshop').'</option>';
	}
	?>
	</select><br />
</fieldset>
<p class="submit">
<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;','eshop') ?>" />
</p>
</form>

</div>
<?php
	break;
}
?>
<?php eshop_show_credits(); ?>