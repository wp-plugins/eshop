<?php
if ('eshop_statistics.php' == basename($_SERVER['SCRIPT_FILENAME']))
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
function eshop_small_stats($stock,$limit=5){
	global $wpdb;
	$rand=eshop_random_code('3');
	$table = $wpdb->prefix ."eshop_downloads";
	$stktable=$wpdb->prefix.'eshop_stock';

	switch($stock){
		case 'dloads':
			$mypages=$wpdb->get_results("Select id,title,purchases,downloads From $table order by purchases DESC LIMIT $limit");
			echo '<table class="widefat"><caption>'.__('Top Download Purchases','eshop').'</caption>';
			echo '<thead><tr><th id="edtitle'.$rand.'">'.__('Download','eshop').'</th><th id="eddown'.$rand.'">'.__('Downloads','eshop').'</th><th id="edpurch'.$rand.'">'.__('Purchases','eshop').'</th></tr></thead><tbody>';
			$calt=0;
			foreach($mypages as $row){
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alternate"';
				echo '<tr'.$alt.'>';
				echo '<td id="redid'.$row->id.'" headers="edtitle'.$rand.'"><a href="?page=eshop_orders.php&amp;action=Stats&amp;dlid='.$row->id.'" title="edit details for '.$row->title.'">'.$row->title."</a></td>\n";
				echo '<td headers="eddown'.$rand.' redid'.$row->id.'">'.$row->downloads."</td>\n";
				echo '<td headers="edpurch'.$rand.' redid'.$row->id.'">'.$row->purchases."</td>\n";
				echo '</tr>'."\n";
			}
			echo '</tbody></table>';
		break;
		case 'stock':
		default:
			$mypages=$wpdb->get_results("SELECT $wpdb->posts.ID,$wpdb->posts.post_title, stk.purchases
			from $wpdb->postmeta,$wpdb->posts, $stktable as stk
			WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' 
			AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND stk.post_id=$wpdb->posts.ID
			order by stk.purchases DESC LIMIT $limit");
			echo '<table class="widefat"><caption>'.__('Top Sellers','eshop').'</caption>';
			echo '<thead><tr><th id="edprod'.$rand.'">'.__('Product','eshop').'</th><th id="edpurch'.$rand.'">'.__('Purchases','eshop').'</th></tr></thead><tbody>';
			$calt=0;
			foreach($mypages as $page){
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alternate"';
				echo '<tr'.$alt.'><td id="repid'.$page->ID.'" headers="edprod'.$rand.'"><a href="page.php?action=edit&amp;post='.$page->ID.'">'.$page->post_title.'</a></td><td headers="edpurch'.$rand.' repid'.$page->ID.'">'.$page->purchases.'</td></tr>'."\n";
			}
			echo '</tbody></table>';
		break;
	}
}
?>