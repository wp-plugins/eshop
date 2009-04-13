<?php
if ('eshop_shipping.php' == basename($_SERVER['SCRIPT_FILENAME']))
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
global $wpdb;
include_once(ABSPATH.'wp-content/plugins/eshop/cart-functions.php');

// Back to our regularly scheduled script :)

echo '<div class="wrap">';
echo '<h2>'.__('eShop Customer Contact','eshop').'</h2><p>'.__('Use this form to notify the selected customer at any time, for any reason.','eshop').'</p>';

if(isset($_POST['thisemail']) && isset($_GET['viewemail'])){
	if (get_magic_quotes_gpc()) {
		$_POST['thisemail'] = stripslashes($_POST['thisemail']);
		$_POST['subject'] = stripslashes($_POST['subject']);

	}
	$body=wordwrap($_POST['thisemail'],75,"\n");
	$from=$_POST['from'];
	$subject=$_POST['subject'];
	$to=$_POST['email'];
	if(isset($from) && $from!=''){
		$headers='From: '.get_bloginfo('name').' <'.$from.">\n";
	}else{
		$headers=eshop_from_address();
	}
	wp_mail($to, $subject, $body, $headers);
	$page='?page='.$_GET['page'].'&amp;view='.$_POST['id'];
	echo '<p class="success">'.__('Email sent successfully.','eshop').'</p>';
	echo '<p><a class="return" href="'.$page.'">'.__('&laquo; Return to Order Detail','eshop').'</a></p>';
	
}elseif(isset($_GET['viewemail'])){
	$view=$wpdb->escape($_GET['viewemail']);
	$dtable=$wpdb->prefix.'eshop_orders';
	$checked=$wpdb->get_var("Select checkid From $dtable where id='$view'");
	$email=$wpdb->get_var("Select email From $dtable where id='$view'");
	$array=eshop_rtn_order_details($checked);
	//grab the template
	$eshopurl=eshop_files_directory();
	$templateFile = $eshopurl['0'];
	$this_email = stripslashes(file_get_contents($eshopurl['0'].'customer-response-email.tpl'));
	// START SUBST
	$subject=get_bloginfo('name').__(' Notification','eshop');
	$this_email = str_replace('{STATUS}', $array['status'], $this_email);
	$this_email = str_replace('{FIRSTNAME}', $array['firstname'], $this_email);
	$this_email = str_replace('{NAME}', $array['ename'], $this_email);
	$this_email = str_replace('{EMAIL}', $array['eemail'], $this_email);
	$this_email = str_replace('{CART}', $array['cart'], $this_email);
	$this_email = str_replace('{DOWNLOADS}', $array['downloads'], $this_email);
	$this_email = str_replace('{ADDRESS}', $array['address'], $this_email);
	$this_email = str_replace('{REFCOMM}', $array['extras'], $this_email);
	$this_email = str_replace('{CONTACT}', $array['contact'], $this_email);
	$this_email = str_replace('&#8230;', '...', $this_email);
// For system email - get_option('eshop_business') is called for below, twice
	if(get_option('eshop_business')!=''){
		$from=get_option('eshop_business');
	}else{
		//nicked from wp_mail function!
		$from="system@" . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
	}
	?>
	<div id="eshopemailform"><form id="emailer" action="<?php echo wp_specialchars($_SERVER['REQUEST_URI']);?>" method="post">
	<fieldset><legend><?php _e('Send a notification to:','eshop'); ?> <strong><?php echo $email; ?></strong></legend>
	<label for="from"><?php _e('Select a reply-to address:','eshop'); ?><br />
    <select class="pointer" name="from" id="from">
    <option value="<?php echo get_option('eshop_business'); ?>" selected="selected"><?php echo get_option('eshop_business'); ?></option>
	<?php
	if(get_option('eshop_from_email')!=''){
	?>
	<option value="<?php echo get_option('eshop_from_email'); ?>"><?php echo get_option('eshop_from_email'); ?></option>
	<?php
	}
    if(get_option('eshop_sysemails')!=''){
		$sysmailex=explode("\n",get_option('eshop_sysemails'));
		while (list(, $sysMail) = each($sysmailex)) {	
			echo '<option value="'.$sysMail.'">'.$sysMail.'</option>'."\n";  
		} 
	}
	?></select></label><br />
	<label for="subject"><?php _e('Enter your subject line:','eshop'); ?><br /><input type="text" id="subject" name="subject" size="60" value="<?php echo $subject; ?>" /></label><br />
	<label for="thisemail"><?php _e('Enter your custom message:','eshop'); ?><br /><textarea name="thisemail" id="thisemail" cols="70" rows="20"><?php echo $this_email; ?></textarea></label>
	<input type="hidden" id="email" name="email" value="<?php echo $email; ?>" />
	<input type="hidden" id="id" name="id" value="<?php echo $view; ?>" />

	<p class="submit eshop"><input type="submit" id="submit" value="<?php _e('Send Email','eshop'); ?>" /></p>
	</fieldset></form></div>
<?php
}else{

	echo '<p>'.__('Nothing here yet.','eshop').'</p>';
}
echo '</div>';

?>