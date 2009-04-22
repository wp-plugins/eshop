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

global $wpdb;
$err='';
//set up submenu here so it can accessed in the code
if (isset($_GET['eshop']) )
	$action_status = attribute_escape($_GET['eshop']);
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
	if (get_magic_quotes_gpc()==0) {
		$_POST = stripslashes_array($_POST);
	}
	$_POST=sanitise_array($_POST);
	switch($action_status){
		case ('Merchant'):
			update_option('eshop_method',$_POST['eshop_method']);
			//these are all for paypal
			update_option('eshop_currency',$wpdb->escape($_POST['eshop_currency']));
			update_option('eshop_location',$wpdb->escape($_POST['eshop_location']));
			update_option('eshop_business',$wpdb->escape(trim($_POST['eshop_business'])));
			//these are for other payment options
			//payson
			$paysonpost['email']=$wpdb->escape($_POST['payson']['email']);
			$paysonpost['id']=$wpdb->escape($_POST['payson']['id']);
			$paysonpost['key']=$wpdb->escape($_POST['payson']['key']);
			$paysonpost['description']=$wpdb->escape($_POST['payson']['description']);
			$paysonpost['minimum']=$wpdb->escape($_POST['payson']['minimum']);
			update_option('eshop_payson',$paysonpost);
			//cash
			$cashpost['email']=$wpdb->escape($_POST['cash']['email']);
			update_option('eshop_cash',$cashpost);

			if(!is_array(get_option('eshop_method'))){
				update_option('eshop_status',$wpdb->escape('testing'));
				$err.='<li>'.__('No Merchant Gateway selected, eShop has been put in Test Mode','eshop').'</li>';
			}
			
			break;
	
		case ('Pages'):
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
			
			if(is_numeric($_POST['eshop_shop_page'])){
				$ptitle=get_post($_POST['eshop_shop_page']);
				if($ptitle->post_title!=''){
					update_option('eshop_shop_page',$wpdb->escape($_POST['eshop_shop_page']));
				}else{
					$err.='<li>'.__('The Main Shop page id chosen','eshop').' ('.$_POST['eshop_shop_page'].') '.__('is invalid.','eshop').'</li>';
				}	
			}elseif(trim($_POST['eshop_shop_page'])!=''){
					$err.='<li>'.__('The Main Shop page needs to be a page id number.','eshop').'</li>';
			}else{
				update_option('eshop_shop_page','');
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
			break;	
		case ('Downloads'):
			if(is_numeric($_POST['eshop_downloads_num'])){
				update_option('eshop_downloads_num',$wpdb->escape($_POST['eshop_downloads_num']));
			}else{
				$err.='<li>'.__('Number of download attempts should be numeric, a default of 3 has been applied.','eshop').'</li>';
				update_option('eshop_downloads_num','3');
			}
			update_option('eshop_downloads_only',$wpdb->escape($_POST['eshop_downloads_only']));
			update_option('eshop_downloads_hideall',$wpdb->escape($_POST['eshop_downloads_hideall']));

			break;	
		case ('Discounts'):
			if(is_numeric($_POST['eshop_discount_shipping'])){
				update_option('eshop_discount_shipping',$wpdb->escape($_POST['eshop_discount_shipping']));
			}elseif($_POST['eshop_discount_shipping']!=''){
				$err.='<li>'.__('"Spend over to get free shipping" must be numeric!','eshop').'</li>';
				update_option('eshop_discount_shipping','');
			}else{
				update_option('eshop_discount_shipping','');
			}

			for ($x=1;$x<=3;$x++){
				if(is_numeric($_POST['eshop_discount_spend'.$x]) && is_numeric($_POST['eshop_discount_value'.$x])){
					update_option('eshop_discount_spend'.$x,$wpdb->escape($_POST['eshop_discount_spend'.$x]));
					update_option('eshop_discount_value'.$x,$wpdb->escape($_POST['eshop_discount_value'.$x]));
				}elseif($_POST['eshop_discount_spend'.$x]=='' || $_POST['eshop_discount_value'.$x]=='') {
					update_option('eshop_discount_spend'.$x,'');
					update_option('eshop_discount_value'.$x,'');
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
			update_option('eshop_from_email',$wpdb->escape($_POST['eshop_from_email']));
			update_option('eshop_cron_email',$wpdb->escape($_POST['eshop_cron_email']));
			update_option('eshop_sysemails',$wpdb->escape($_POST['eshop_sysemails']));
			update_option('eshop_currency_symbol',$wpdb->escape($_POST['eshop_currency_symbol']));
			update_option('eshop_cart_nostock',$wpdb->escape($_POST['eshop_cart_nostock']));
			update_option('eshop_credits',$wpdb->escape($_POST['eshop_credits']));
			update_option('eshop_fold_menu',$wpdb->escape($_POST['eshop_fold_menu']));
			update_option('eshop_stock_control',$wpdb->escape($_POST['eshop_stock_control']));
			update_option('eshop_show_stock',$wpdb->escape($_POST['eshop_show_stock']));
			update_option('eshop_search_img',$wpdb->escape($_POST['eshop_search_img']));
			update_option('eshop_show_forms',$wpdb->escape($_POST['eshop_show_forms']));
			update_option('eshop_show_sku',$wpdb->escape($_POST['eshop_show_sku']));

			//error grabbing
			if(is_numeric($_POST['eshop_records'])){
				update_option('eshop_records',$wpdb->escape($_POST['eshop_records']));
			}else{
				$err.='<li>'.__('Orders per page should be numeric, a default of 10 has been applied.','eshop').'</li>';
				update_option('eshop_records','10');
			}
			if(is_numeric($_POST['eshop_options_num']) && $_POST['eshop_options_num']>'0'){
				update_option('eshop_options_num',$wpdb->escape($_POST['eshop_options_num']));
			}else{
				$err.='<li>'.__('Options per product should be numeric and be greater than 0, a default of 3 has been applied.','eshop').'</li>';
				update_option('eshop_options_num','3');
			}

			if(is_numeric($_POST['eshop_image_in_cart']) || $_POST['eshop_image_in_cart']==''){
				update_option('eshop_image_in_cart',$wpdb->escape($_POST['eshop_image_in_cart']));
			}else{
				$err.='<li>'.__('The number entered for the image in the cart must be numeric, a default of 75 has been applied.','eshop').'</li>';
				update_option('eshop_image_in_cart','75');
			}
			if($_POST['eshop_currency_symbol']==''){
				$err.='<li>'.__('Currency Symbol was missing, the default $ has been applied.','eshop').'</li>';
				update_option('eshop_currency_symbol','$');
			}
			if($_POST['eshop_status']=='live'){
				$statuserr='';
				if(!is_array(get_option('eshop_method')))
					$statuserr.='<li>'.__('You must have a Merchant Gateway selected before you can go live!','eshop').'</li>';
				if(get_option('eshop_from_email')=='')
					$statuserr.='<li>'.__('You must have set an eShop from email address before you can go live!','eshop').'</li>';
				if($statuserr=='')
					update_option('eshop_status',$wpdb->escape($_POST['eshop_status']));
				else
					$err.=$statuserr;
			}else
				update_option('eshop_status',$wpdb->escape($_POST['eshop_status']));
			
			break;
		case ('Base'):
				update_option('eshop_base_brand',$wpdb->escape($_POST['eshop_base_brand']));
				update_option('eshop_base_condition',$wpdb->escape($_POST['eshop_base_condition']));
				update_option('eshop_base_expiry',$wpdb->escape($_POST['eshop_base_expiry']));
				update_option('eshop_base_ptype',$wpdb->escape($_POST['eshop_base_ptype']));
				update_option('eshop_base_payment',$wpdb->escape($_POST['eshop_base_payment']));
			break;
	}
}

echo '<div class="wrap">';
echo '<h2>'.__('eShop Settings','eshop').'</h2>'."\n";
//info:
echo '<p class="eshopwarn">';
if('live' == get_option('eshop_status'))
	_e('eShop is currently <span class="live">Live</span>.','eshop');
else
	_e('eShop is currently in <span class="test">Test Mode</span>.','eshop');

if(is_array(get_option('eshop_method')))
	echo ' Merchant Gateways in use: <span class="eshopgate">'.ucwords(implode(', ',(array)get_option('eshop_method'))).'</span>';
else
	echo ' No Merchant Gateway selected.';
echo '</p>';
//the submenu 
echo '<ul class="subsubsub">';
echo implode(' | </li>', $status_links) . '</li>';
echo '</ul><br class="clear" />';
if($err!=''){
	echo'<div id="message" class="error fade"><p>'.__('<strong>Error</strong> the following were not valid:','eshop').'</p><ul>'.$err.'</ul></div>'."\n";
	
}elseif(isset($_GET['resetbase']) && $_GET['resetbase']=='yes'){
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
	
	<label for="eshop_base_brand"><?php _e('Brand','eshop'); ?></label><input id="eshop_base_brand" name="eshop_base_brand" type="text" value="<?php echo get_option('eshop_base_brand'); ?>" size="30" /><br />
	<label for="eshop_base_condition"><?php _e('Condition','eshop'); ?></label>
		<select name="eshop_base_condition" id="eshop_base_condition">
		<?php
			
		foreach($currentconditions as $code){
			if($code == get_option('eshop_base_condition')){
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
			if($code == get_option('eshop_base_expiry')){
				$sel=' selected="selected"';
			}else{
				$sel='';
			}
			echo '<option value="'. $code .'"'. $sel .'>'. $code .'</option>';
		}
		?>
	</select><br />
	
	<label for="eshop_base_ptype"><?php _e('Product type','eshop'); ?></label><input id="eshop_base_ptype" name="eshop_base_ptype" type="text" value="<?php echo get_option('eshop_base_ptype'); ?>" size="30" /><br />
	<label for="eshop_base_payment"><?php _e('Payment Accepted <small> comma delimited list of payment methods available.</small>','eshop'); ?></label><input id="eshop_base_payment" name="eshop_base_payment" type="text" value="<?php echo get_option('eshop_base_payment'); ?>" size="30" /><br />
	
	<input type="hidden" name="page_options" value="eshop_base_brand,eshop_base_condition,
	eshop_base_expiry,eshop_base_ptype,eshop_base_payment" />
	
	</fieldset>
	<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;') ?>" />
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
				if($row->code == get_option('eshop_location')){
					$sel=' selected="selected"';
				}else{
					$sel='';
				}
				echo '<option value="'. $row->code .'"'. $sel .'>'. $row->country .'</option>';
			}
			?>
		</select><br />
		<p>Don't forget to set the <a href="admin.php?page=eshop_shipping.php&action=states">State/County/Province</a> on the shipping pages.</p>
	<?php //' fix my code colours - not needed elswhere ?>
		<label for="eshop_currency"><?php _e('Currency Code','eshop'); ?></label>
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
	<fieldset><legend><?php _e('Merchant Gateways','eshop'); ?></legend>
	<fieldset><legend><?php _e('Paypal','eshop'); ?></legend>
		<p class="cbox"><input id="eshop_method" name="eshop_method[]" type="checkbox" value="paypal"<?php if(in_array('paypal',(array)get_option('eshop_method'))) echo ' checked="checked"'; ?> /><label for="eshop_method"><?php _e('Accept payment by Paypal','eshop'); ?></label></p>
		<label for="eshop_business"><?php _e('Email address','eshop'); ?></label><input id="eshop_business" name="eshop_business" type="text" value="<?php echo get_option('eshop_business'); ?>" size="30" /><br />
	
	</fieldset>
	<fieldset><legend><?php _e('Payson','eshop'); ?></legend>
	<p><?php _e('<strong>Warning:</strong> Payson has a minimum purchase value of 4 SEK (when last checked). All payments to Payson are in SEK, irrespective of settings above.','eshop'); ?>
	<?php $payson = get_option('eshop_payson'); ?>

		<p class="cbox"><input id="eshop_methodb" name="eshop_method[]" type="checkbox" value="payson"<?php if(in_array('payson',(array)get_option('eshop_method'))) echo ' checked="checked"'; ?> /><label for="eshop_methodb"><?php _e('Accept payment by Payson','eshop'); ?></label></p>
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
		<fieldset><legend><?php _e('Cash','eshop'); ?></legend>
		<p><?php _e('<strong>Note:</strong> payment by other means, usually used for offline payments.','eshop'); ?>
		<?php $eshopcash = get_option('eshop_cash'); ?>
		<p class="cbox"><input id="eshop_methodc" name="eshop_method[]" type="checkbox" value="cash"<?php if(in_array('cash',(array)get_option('eshop_method'))) echo ' checked="checked"'; ?> /><label for="eshop_methodc"><?php _e('Accept cash payments','eshop'); ?></label></p>
		<label for="eshop_cashemail"><?php _e('Email address','eshop'); ?></label><input id="eshop_cashemail" name="cash[email]" type="text" value="<?php echo $eshopcash['email']; ?>" size="30" maxlength="50" /><br />
		</fieldset>
	</fieldset>
	<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;') ?>" />
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
<p>In all cases deleting the entry will disable the discount.</p>
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
	<td headers="elevel espend row<?php echo $x ?>"><label for="eshop_discount_spend<?php echo $x ?>"><?php _e('Spend','eshop'); ?></label><input id="eshop_discount_spend<?php echo $x ?>" name="eshop_discount_spend<?php echo $x ?>" type="text" value="<?php echo get_option("eshop_discount_spend$x"); ?>" size="5" /></td>
	<td headers="elevel ediscount row<?php echo $x ?>"><label for="eshop_discount_value<?php echo $x ?>"><?php _e('Discount','eshop'); ?></label><input id="eshop_discount_value<?php echo $x ?>" name="eshop_discount_value<?php echo $x ?>" type="text" value="<?php echo get_option("eshop_discount_value$x"); ?>" size="5" maxlength="4" /></td>
	</tr>
	<?php
	}
	?>
	</tbody>
	</table>
<p><label for="eshop_discount_shipping"><?php _e('Spend over to get free shipping','eshop'); ?></label><input id="eshop_discount_shipping" name="eshop_discount_shipping" type="text" value="<?php echo get_option('eshop_discount_shipping'); ?>" size="5" /></p>
</fieldset>
		

		<p class="submit">
		<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;') ?>" />
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
		<label for="eshop_downloads_num"><?php _e('Download attempts','eshop'); ?></label><input id="eshop_downloads_num" name="eshop_downloads_num" type="text" value="<?php echo get_option('eshop_downloads_num'); ?>" size="5" /><br />
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
			<fieldset><legend><?php _e('Download All','eshop'); ?></legend>
			<p><?php _e('As some downloads can be quite large, people may experience errors if they try and download all files in one go.','eshop'); ?></p>
			<label for="eshop_downloads_hideall"><?php _e('Hide download all form','eshop'); ?></label>
				<select name="eshop_downloads_hideall" id="eshop_downloads_hideall">
				<?php
				if('yes' == get_option('eshop_downloads_hideall')){
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
		<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;') ?>" />
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
			<label for="eshop_shop_page"><?php _e('Shop Page - page id number','eshop'); ?></label><input id="eshop_shop_page" name="eshop_shop_page" type="text" value="<?php echo get_option('eshop_shop_page'); ?>" size="5" /><br />
		</fieldset>
		<fieldset><legend><?php _e('Link to extra pages','eshop'); ?></legend>
		<p><?php _e('These links automatically appear on the checkout page.','eshop'); ?></p>
		<label for="eshop_cart_shipping"><?php _e('Shipping rates - page id number','eshop'); ?></label><input id="eshop_cart_shipping" name="eshop_cart_shipping" type="text" value="<?php echo get_option('eshop_cart_shipping'); ?>" size="5" /><br />
		<label for="eshop_xtra_privacy"><?php _e('Privacy Policy - page id number','eshop'); ?></label><input id="eshop_xtra_privacy" name="eshop_xtra_privacy" type="text" value="<?php echo get_option('eshop_xtra_privacy'); ?>" size="5" /><br />
		<label for="eshop_xtra_help"><?php _e('Help - page id number','eshop'); ?></label><input id="eshop_xtra_help" name="eshop_xtra_help" type="text" value="<?php echo get_option('eshop_xtra_help'); ?>" size="5" /><br />
		</fieldset>
						
		<fieldset><legend><?php _e('Automatically created pages','eshop'); ?></legend>
		<p class="warn"><?php _e('<strong>Warning:</strong> Changes made here amend the page id of the automatically created pages - change with extreme care.','eshop'); ?></p>
		<label for="eshop_cart"><?php _e('Cart - page id number','eshop'); ?></label><input id="eshop_cart" name="eshop_cart" type="text" value="<?php echo get_option('eshop_cart'); ?>" size="5" /><br />
		<label for="eshop_checkout"><?php _e('Checkout - page id number','eshop'); ?></label><input id="eshop_checkout" name="eshop_checkout" type="text" value="<?php echo get_option('eshop_checkout'); ?>" size="5" /><br />
		<label for="eshop_cart_success"><?php _e('Successful payment  - page id number','eshop'); ?></label><input id="eshop_cart_success" name="eshop_cart_success" type="text" value="<?php echo get_option('eshop_cart_success'); ?>" size="5" /><br />
		<label for="eshop_cart_cancel"><?php _e('Cancelled payment - page id number','eshop'); ?></label><input id="eshop_cart_cancel" name="eshop_cart_cancel" type="text" value="<?php echo get_option('eshop_cart_cancel'); ?>" size="5" /><br />
		<label for="eshop_show_downloads"><?php _e('Downloads - page id number','eshop'); ?></label><input id="eshop_show_downloads" name="eshop_show_downloads" type="text" value="<?php echo get_option('eshop_show_downloads'); ?>" size="5" /><br />
		</fieldset>
						
		<p class="submit">
		<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;') ?>" />
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

<fieldset><legend><?php _e('Business Details','eshop'); ?></legend>
<label for="eshop_from_email"><?php _e('eShop from email address','eshop'); ?></label><input id="eshop_from_email" name="eshop_from_email" type="text" value="<?php echo get_option('eshop_from_email'); ?>" size="30" /><br />
<label for="eshop_sysemails"><?php _e('Available business email addresses','eshop'); ?></label>
<textarea id="eshop_sysemails" name="eshop_sysemails" rows="5" cols="50">
<?php echo get_option('eshop_sysemails'); ?>
</textarea>
<br />
</fieldset>

<fieldset><legend><?php _e('Product Options','eshop'); ?></legend>
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
<label for="eshop_show_sku"><?php _e('Show product sku','eshop'); ?></label>
	<select name="eshop_show_sku" id="eshop_show_sku">
	<?php
	if('yes' == get_option('eshop_show_sku')){
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

<label for="eshop_currency_symbol"><?php _e('Symbol','eshop'); ?></label><input id="eshop_currency_symbol" name="eshop_currency_symbol" type="text" value="<?php echo get_option('eshop_currency_symbol'); ?>" size="10" /><br />

</fieldset>
<fieldset><legend><?php _e('Product Listings','eshop'); ?></legend>
	<label for="eshop_show_forms"><?php _e('Show add to cart forms on WordPress post listings. <span class="warn"><span>Warning</span> this can invalidate your site!</span>','eshop'); ?></label>
	<select name="eshop_show_forms" id="eshop_show_forms">
	<?php
	if('yes' == get_option('eshop_show_forms')){
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
	<label for="eshop_image_in_cart"><?php _e('Percentage size of thumbnail image shown in cart - leave blank to not show the image.','eshop'); ?></label><input id="eshop_image_in_cart" name="eshop_image_in_cart" type="text" value="<?php echo get_option('eshop_image_in_cart'); ?>" size="5" /><br />
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


<fieldset><legend><?php _e('Cron','eshop'); ?></legend>
<label for="eshop_cron_email"><?php _e('Cron Email address','eshop'); ?></label><input id="eshop_cron_email" name="eshop_cron_email" type="text" value="<?php echo get_option('eshop_cron_email'); ?>" size="30" /><br />
</fieldset>


<p class="submit">
<input type="submit" name="submit" class="button-primary" value="<?php _e('Update Options &#187;') ?>" />
</p>
</form>

</div>
<?php
	break;
}
?>
<?php eshop_show_credits(); ?>