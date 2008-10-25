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
		<h2>Here be Feeds</h2>
		<p><a href="../wp-content/plugins/eshop/eshop_base_feed.php?d=1">Download</a> the xml file, or <a href="../wp-content/plugins/eshop/eshop_base_feed.php">view</a> it online.</p>
	</div>
	
	<?php
	eshop_show_credits();

}
?>