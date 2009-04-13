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
include 'eshop-base-functions.php';
$result='';
global $wpdb;
if(isset($_GET['reset']) && $_GET['reset']=='yes'){
	$table=$wpdb->prefix.'eshop_base_products';
	$wpdb->query("TRUNCATE TABLE $table"); 
	echo'<div id="message" class="updated fade"><p>eShop Base product data has been reset.</p></div>'."\n";
}
if(isset($_POST['submit'])){
	include 'cart-functions.php';
	if (get_magic_quotes_gpc()==0) {
		$_POST = stripslashes_array($_POST);
	}
	$_POST=sanitise_array($_POST);
	$err='';
	update_option('eshop_base_brand',$wpdb->escape($_POST['eshop_base_brand']));
	update_option('eshop_base_condition',$wpdb->escape($_POST['eshop_base_condition']));
	update_option('eshop_base_expiry',$wpdb->escape($_POST['eshop_base_expiry']));
	update_option('eshop_base_ptype',$wpdb->escape($_POST['eshop_base_ptype']));
	update_option('eshop_base_payment',$wpdb->escape($_POST['eshop_base_payment']));
}
if($err!=''){
	echo'<div id="message" class="error fade"><p><strong>Error</strong> the following were not valid:</p><ul>'.$err.'</ul></div>'."\n";
}elseif(isset($_POST['submit'])){
	echo'<div id="message" class="updated fade"><p>eShop Base Settings have been updated.</p></div>'."\n";
}
echo '<div class="wrap">';
echo '<h2>eShop Base Settings</h2>'."\n";


/* defaults will need to be created */
echo $result;
?>
<form method="post" action="" id="eshop-settings">
<?php wp_nonce_field('update-options') ?>
<fieldset><legend>eShop Base Options</legend>

<label for="eshop_base_brand">Brand</label><input id="eshop_base_brand" name="eshop_base_brand" type="text" value="<?php echo get_option('eshop_base_brand'); ?>" size="30" /><br />
<label for="eshop_base_condition">Condition</label>
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
<label for="eshop_base_expiry">Product expiry in days</label>
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

<label for="eshop_base_ptype">Product type</label><input id="eshop_base_ptype" name="eshop_base_ptype" type="text" value="<?php echo get_option('eshop_base_ptype'); ?>" size="30" /><br />
<label for="eshop_base_payment">Payment Accepted <small> comma delimited list of payment methods available in addition to paypal.</small></label><input id="eshop_base_payment" name="eshop_base_payment" type="text" value="<?php echo get_option('eshop_base_payment'); ?>" size="30" /><br />


<input type="hidden" name="page_options" value="eshop_base_brand,eshop_base_condition,
eshop_base_expiry,eshop_base_ptype,eshop_base_payment" />

</fieldset>
<p class="submit">
<input type="submit" name="submit" value="<?php _e('Update Options &#187;') ?>" />
</p>
</form>

</div>
<div class="wrap">
<h2>Reset eShop Base</h2>
<p>This resets all product data entered on the <a href="?page=eshop_base.php">eShop Base Products</a> page.</p>
<p class="ebox"><a class="ebox" href="?page=eshop_base_settings.php&amp;reset=yes">Reset Now</a></p>
</div>

<?php eshop_show_credits(); ?>