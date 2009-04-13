<?php
if ('eshop_base_create_feed.php' == basename($_SERVER['SCRIPT_FILENAME']))
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
function eshop_base_create_feed(){
	?>
	<div class="wrap">
		<h2><?php _e('Here be Feeds','eshop'); ?></h2>
		<ul>
		<?php
		$dlpage='?page='.$_GET['page'].'&amp;eshopbasedl=yes';
		?>
		<li><a href="<?php echo $dlpage; ?>&amp;d=1"><?php _e('Download the xml file','eshop'); ?></a></li>
		<li><a href="<?php echo $dlpage; ?>&amp;d=1&amp;os=mac"><?php _e('Mac - Download the xml file','eshop'); ?></a></li>
		<li><a href="<?php echo $dlpage; ?>"><?php _e('View xml file','eshop'); ?></a></li>
		</ul>
	</div>
	<?php
	eshop_show_credits();
}
?>