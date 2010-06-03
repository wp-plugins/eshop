<?php 
add_shortcode('eshop_list_featured', 'eshop_list_featured');
add_shortcode('eshop_best_sellers', 'eshop_best_sellers');
add_shortcode('eshop_list_new', 'eshop_list_new');
add_shortcode('eshop_list_subpages', 'eshop_list_subpages');
add_shortcode('eshop_list_cat_tags', 'eshop_list_cat_tags');
add_shortcode('eshop_random_products', 'eshop_list_random');
add_shortcode('eshop_show_cancel', 'eshop_show_cancel');
add_shortcode('eshop_show_cart', 'eshop_show_cart');
add_shortcode('eshop_show_checkout', 'eshop_show_checkout');
add_shortcode('eshop_show_discounts','eshop_show_discounts');
add_shortcode('eshop_show_downloads', 'eshop_show_downloads');
add_shortcode('eshop_show_payments','eshop_show_payments');
add_shortcode('eshop_show_product','eshop_show_product');
add_shortcode('eshop_show_shipping', 'eshop_show_shipping');
add_shortcode('eshop_show_success', 'eshop_show_success');
add_shortcode('eshop_empty_cart', 'eshop_empty_cart');
add_shortcode('eshop_list_alpha', 'eshop_list_alpha');
add_shortcode('eshop_cart_items','eshop_cart_items');
add_shortcode('eshop_addtocart','eshop_addtocart');
add_shortcode('eshop_welcome','eshop_welcome');

function eshop_cart_items($atts){
	global $blog_id;
	extract(shortcode_atts(array('before'=>'','after'=>'','hide'=>'no','showwhat'=>'both'), $atts));
	$eecho='';
	if($before!='')
		$eecho.=$before.' ';
	if(isset($_SESSION['eshopcart'.$blog_id]) || $hide=='no'){
		$eshopsize=0;
		$eshopqty=0;
		if(isset($_SESSION['eshopcart'.$blog_id])){
			$eshopsize=sizeof($_SESSION['eshopcart'.$blog_id]);
			foreach($_SESSION['eshopcart'.$blog_id] as $eshopdo=>$eshopwop){
				$eshopqty+=$eshopwop['qty'];
			}
		}
		
		if($showwhat=='items' || $showwhat=='both'){
			$eecho .='<span>'.$eshopsize.'</span> '.eshop_plural($eshopsize, __('product','eshop'), __('products','eshop') ).' '.__('in cart','eshop').'.';
		}
		if($showwhat=='qty' || $showwhat=='both'){
			if($showwhat=='both') $eecho.= '<br />';
			$eecho .='<span>'.$eshopqty.'</span> '.eshop_plural($eshopqty, __('item','eshop'), __('items','eshop') ).' '.__('in cart','eshop').'.';
		}
		$eecho.= '<br /><a href="'.get_permalink($eshopoptions['cart']).'">'.__('View Cart','eshop').'</a>';
		$eecho .='<br /><a href="'.get_permalink($eshopoptions['checkout']).'">'.__('Checkout','eshop').'</a>';
		
	}
	
	if($after!='')
		$eecho.=' '.$after;
	
	return $eecho;
}

function eshop_empty_cart($atts, $content = '') {
	global $blog_id;
	if(isset($_SESSION['eshopcart'.$blog_id])){
		$content='';
	}
	return $content;
}
function eshop_list_alpha($atts){
	global $wpdb, $post,$wp_rewrite,$wp_query;
	eshop_cache();
	extract(shortcode_atts(array('class'=>'eshopalpha','panels'=>'no','form'=>'no','records'=>'25','imgsize'=>'','links'=>'yes'), $atts));
	//a-z listing
	$letter_array = range('A','Z');
	$fullarray=$letter_array;
	$fullarray[]='num';
	$usedaz=$wpdb->get_results("SELECT DISTINCT UPPER(LEFT(post_title,1)) as letters FROM $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' ORDER BY letters");
	$usednum=$wpdb->get_var("SELECT COUNT(DISTINCT UPPER(LEFT(post_title,1)) BETWEEN '0' AND '9') FROM $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish'");
	foreach($usedaz as $usethis){
		$used[]=$usethis->letters;
	}
	if(isset($wp_query->query_vars['eshopaz'])) {
   	 	$eshopaz = urldecode($wp_query->query_vars['eshopaz']);
   	}
	if(!isset($eshopaz) || !in_array($eshopaz,$fullarray)){
		$eshopaz='a';
		$dbletter='A';
	}
	$econtain='<ul class="eshop eshopaz">';
	$thisispage=get_permalink($post->ID);
	foreach ($letter_array as $letter) {
		if (in_array($letter, $used)){
			if(isset($eshopaz) && strtoupper($eshopaz)==$letter){
				$addclass=' class="current"';
				$dbletter=$letter;
			}else{
				$addclass='';
			}
			$thispage=add_query_arg('eshopaz',$letter,$thisispage);
			$econtain.= '<li'.$addclass.'><a href="'.$thispage.'">'.$letter . "</a></li>\n";
		}else{
			$econtain.= '<li><span>'.$letter."</span></li>\n";
		}
	}
	if(isset($eshopaz) && $eshopaz=='num' && $usednum>0 ){
		$thispage=add_query_arg('eshopaz','num',$thisispage);
		$econtain.= '<li class="current"><a href="'.$thispage.'">0-9</a></li>'."\n";
	}elseif ($usednum>0){
		$thispage=add_query_arg('eshopaz','num',$thisispage);
		$econtain.= '<li><a href="'.$thispage.'">0-9</a></li>'."\n";
	}else{
	 	$econtain.= '<li><span>0-9</span></li>'."\n";
	}
	$econtain.="</ul>\n";
	if(in_array($dbletter,$letter_array))
		$qbuild=" AND UPPER(LEFT(post_title,1))='$dbletter'";
	elseif(isset($eshopaz) && $eshopaz=='num')
		$qbuild=" AND UPPER(LEFT(post_title,1)) BETWEEN '0' AND '9'";
	$max=$wpdb->get_var("SELECT count($wpdb->posts.ID) from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' $qbuild");
	if($max>0){
		if(isset($wp_query->query_vars['_p']))$epage=$wp_query->query_vars['_p'];
		else $epage='1';
		if(!isset($wp_query->query_vars['eshopall'])){
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array'
				));
			$offset=($epage*$records)-$records;
		}else{
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array',
				'show_all' => true,
			));
			$offset='0';
			$records=$max;
		}
	}
	
	
	if(!isset($offset)) $offset='0';
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' $qbuild order by post_title ASC limit $offset,$records");
	if($pages) {
		//paginate
		$echo = '<div class="paginate">';
		if($records!=$max){
			$eecho = $page_links;
		}
		$echo .= sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>',
			number_format_i18n( ( $epage - 1 ) * $records + 1 ),
			number_format_i18n( min( $epage * $records, $max ) ),
			number_format_i18n( $max)
		);
			
		$echo .= '</div>';
		//end
		if($panels=='no'){
			$echo .= eshop_listpages($pages,$class,$form,$imgsize,$links);
		}else{
			if($class=='eshopalpha') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form,$imgsize,$links);
		}

		if(isset($eecho)){
			$thispage=add_query_arg('eshopall','yes',$thisispage);
			$eeecho="<ul class='page-numbers'>\n\t<li>".join("</li>\n\t<li>", $eecho)."</li>\n<li>".'<a href="'.$thispage.'">View All</a>'."</li>\n</ul>\n";
			$echo .= '<div class="paginate pagfoot">'.$eeecho.'</div>';
		}else{
			$echo .= '<br class="pagfoot" />';
		}
		return $econtain.$echo;
	} 
	
	return $econtain .'<p>'. __('No products found for that letter or number.','eshop').'</p>';
} 
function eshop_list_subpages($atts){
	global $wpdb, $post,$wp_query;
	eshop_cache();
	extract(shortcode_atts(array('class'=>'eshopsubpages','panels'=>'no','form'=>'no','show'=>'100','records'=>'10','sortby'=>'post_title','order'=>'ASC','imgsize'=>'','id'=>'','links'=>'yes'), $atts));
	$echo='';
	if($id!='')
		$eshopid=$id;
	else
		$eshopid=$post->ID;
		
	$allowedsort=array('post_date','post_title','menu_order');
	$allowedorder=array('ASC','DESC');
	if(!in_array($sortby,$allowedsort)) 
		$sortby='post_title';
	switch($sortby){
		case ('post_date'):
			$orderby='date';
			break;
		case ('menu_order'):
			$orderby='menu_order';
			break;
		case ('post_title'):
		default:
			$orderby='title';
			break;
	}
	if(!in_array($order,$allowedorder)) 
		$order='ASC';
	
	$thisispage=get_permalink($post->ID);
	$max = $wpdb->get_var("SELECT count(ID) from $wpdb->posts WHERE post_type='page' AND post_parent='$eshopid' AND post_status='publish'");
	if($max>$show)
		$max=$show;
	if($max>0){
		if(isset($wp_query->query_vars['_p']))$epage=$wp_query->query_vars['_p'];
		else $epage='1';
		if(!isset($wp_query->query_vars['eshopall'])){
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array'
				));
			$offset=($epage*$records)-$records;
		}else{
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array',
				'show_all' => true,
			));
			$offset='0';
			$records=$max;
		}
	}
	if(!isset($offset)) $offset='0';
	$args = array(
	'post_type' => 'page',
	'post_status' => null,
	'post_parent' => $eshopid, // any parent
	'orderby'=> $orderby,
	'order'=> $order,
	'numberposts' => $records, 
	'offset' => $offset,
	); 
	$pages = get_posts($args);
	wp_reset_query();

	if($pages) {
		//paginate
		$echo .= '<div class="paginate">';
		if($records!=$max){
			$eecho = $page_links;
		}
		$echo .= sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>',
			number_format_i18n( ( $epage - 1 ) * $records + 1 ),
			number_format_i18n( min( $epage * $records, $max ) ),
			number_format_i18n( $max)
		);
		$echo .= '</div>';
		//end
		if($panels=='no'){
			$echo .= eshop_listpages($pages,$class,$form,$imgsize,$links);
		}else{
			if($class=='eshopsubpages') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form,$imgsize,$links);
		}
		
		if(isset($eecho)){
			$thispage=add_query_arg('eshopall','yes',$thisispage);
			$eeecho="<ul class='page-numbers'>\n\t<li>".join("</li>\n\t<li>", $eecho)."</li>\n<li>".'<a href="'.$thispage.'">View All</a>'."</li>\n</ul>\n";
			$echo .= '<div class="paginate pagfoot">'.$eeecho.'</div>';
		}else{
			$echo .= '<br class="pagfoot" />';
		}
		return $echo;
	} 
	return;
}
function eshop_list_cat_tags($atts){
	global $wpdb, $post,$wp_query;
	eshop_cache();
	extract(shortcode_atts(array('class'=>'eshopcats','panels'=>'no','form'=>'no','show'=>'100','records'=>'10','sortby'=>'post_title','order'=>'ASC','imgsize'=>'','find'=>'','type'=>'tag','links'=>'yes'), $atts));
	$echo='';
	$allowedtype=array('cat','category_name','tag','tag_id');
	if(!in_array($type,$allowedtype))  $type='tag';
	
	$allowedsort=array('post_date','post_title','menu_order');
	$allowedorder=array('ASC','DESC');
	if(!in_array($sortby,$allowedsort)) 
		$sortby='post_title';
	switch($sortby){
		case ('post_date'):
			$orderby='date';
			break;
		case ('menu_order'):
			$orderby='menu_order';
			break;
		case ('post_title'):
		default:
			$orderby='title';
			break;
	}
	if(!in_array($order,$allowedorder)) 
		$order='ASC';
	
	$thisispage=get_permalink($post->ID);
	$args = array(
	'post_type' => 'post',
	'post_status' => null,
	$type => $find
	); 
	$max = sizeof(query_posts($args));
	if($max>$show)
		$max=$show;
	if($max>0){
		if(isset($wp_query->query_vars['_p']))$epage=$wp_query->query_vars['_p'];
		else $epage='1';
		if(!isset($wp_query->query_vars['eshopall'])){
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array'
				));
			$offset=($epage*$records)-$records;
		}else{
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array',
				'show_all' => true,
			));
			$offset='0';
			$records=$max;
		}
	}
	if(!isset($offset)) $offset='0';
	$args = array(
	'post_type' => 'post',
	'post_status' => null,
	$type => $find, 
	'meta_key'=>'_eshop_product',
	'orderby'=> $orderby,
	'order'=> $order,
	'numberposts' => $records, 
	'offset' => $offset,
	); 
	$pages = get_posts($args);
	wp_reset_query();
	if($pages) {
		//paginate
		$echo .= '<div class="paginate">';
		if($records!=$max){
			$eecho = $page_links;
		}
		$echo .= sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>',
			number_format_i18n( ( $epage - 1 ) * $records + 1 ),
			number_format_i18n( min( $epage * $records, $max ) ),
			number_format_i18n( $max)
		);
		$echo .= '</div>';
		//end
		if($panels=='no'){
			$echo .= eshop_listpages($pages,$class,$form,$imgsize,$links);
		}else{
			if($class=='eshopsubpages') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form,$imgsize,$links);
		}
		
		if(isset($eecho)){
			$thispage=add_query_arg('eshopall','yes',$thisispage);
			$eeecho="<ul class='page-numbers'>\n\t<li>".join("</li>\n\t<li>", $eecho)."</li>\n<li>".'<a href="'.$thispage.'">View All</a>'."</li>\n</ul>\n";
			$echo .= '<div class="paginate pagfoot">'.$eeecho.'</div>';
		}else{
			$echo .= '<br class="pagfoot" />';
		}
		return $echo;
	} 
	return;
}
function eshop_list_new($atts){
	global $wpdb, $post,$wp_query;
	eshop_cache();
	extract(shortcode_atts(array('class'=>'eshopsubpages','panels'=>'no','form'=>'no','show'=>'100','records'=>'10','imgsize'=>'','links'=>'yes'), $atts));
	$echo='';
	$max=$wpdb->get_var("SELECT count($wpdb->posts.ID) from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish'");
	if($max>$show)
		$max=$show;
	if($max>0){
		if(isset($wp_query->query_vars['_p']))$epage=$wp_query->query_vars['_p'];
		else $epage='1';
		if(!isset($wp_query->query_vars['eshopall'])){
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array'
				));
			$offset=($epage*$records)-$records;
		}else{
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array',
				'show_all' => true,
			));
			$offset='0';
			$records=$max;
		}
	}
	if(!isset($offset)) $offset='0';
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' order by post_date DESC limit $offset,$records");
	$thisispage=get_permalink($post->ID);
	if($pages) {
		//paginate
		$echo = '<div class="paginate">';
		if($records!=$max){
			$eecho = $page_links;
		}
		$echo .= sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>',
			number_format_i18n( ( $epage - 1 ) * $records + 1 ),
			number_format_i18n( min( $epage * $records, $max ) ),
			number_format_i18n( $max)
		);
		$echo .= '</div>';
		//end
		if($panels=='no'){
			$echo .= eshop_listpages($pages,$class,$form,$imgsize,$links);
		}else{
			if($class=='eshopsubpages') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form,$imgsize,$links);
		}

		if(isset($eecho)){
			$thispage=add_query_arg('eshopall','yes',$thisispage);
			$eeecho="<ul class='page-numbers'>\n\t<li>".join("</li>\n\t<li>", $eecho)."</li>\n<li>".'<a href="'.$thispage.'">View All</a>'."</li>\n</ul>\n";
			$echo .= '<div class="paginate pagfoot">'.$eeecho.'</div>';
		}else{
			$echo .= '<br class="pagfoot" />';
		}
		return $echo;
	} 
	return;
} 
function eshop_best_sellers($atts){
	global $wpdb, $post,$wp_query;
	eshop_cache();
	extract(shortcode_atts(array('class'=>'eshopbestsellers','panels'=>'no','form'=>'no','show'=>'100','records'=>'10','imgsize'=>'','links'=>'yes'), $atts));
	$echo='';
	$stktable=$wpdb->prefix.'eshop_stock';
	$max=$wpdb->get_var("SELECT COUNT($wpdb->postmeta.post_id)
		from $wpdb->postmeta,$wpdb->posts, $stktable as stk
		WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' 
	AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND stk.post_id=$wpdb->posts.ID");
	if($max>$show)
		$max=$show;
	if($max>0){
		if(isset($wp_query->query_vars['_p']))$epage=$wp_query->query_vars['_p'];
		else $epage='1';
		if(!isset($wp_query->query_vars['eshopall'])){
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array'
				));
			$offset=($epage*$records)-$records;
		}else{
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array',
				'show_all' => true,
			));
			$offset='0';
			$records=$max;
		}
	}
	if(!isset($offset)) $offset='0';
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title 
	from $wpdb->postmeta,$wpdb->posts, $stktable as stk
	WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' 
	AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND stk.post_id=$wpdb->posts.ID
	order by stk.purchases DESC limit $offset,$records");
	$thisispage=get_permalink($post->ID);
	if($pages) {
		//paginate
		$echo = '<div class="paginate">';
		if($records!=$max){
			$eecho = $page_links;
		}
		$echo .= sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>',
			number_format_i18n( ( $epage - 1 ) * $records + 1 ),
			number_format_i18n( min( $epage * $records, $max ) ),
			number_format_i18n( $max)
		);
		$echo .= '</div>';
		//end
		if($panels=='no'){
			$echo .= eshop_listpages($pages,$class,$form,$imgsize,$links);
		}else{
			if($class=='eshopbestsellers') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form,$imgsize,$links);
		}

		if(isset($eecho)){
			$thispage=add_query_arg('eshopall','yes',$thisispage);
			$eeecho="<ul class='page-numbers'>\n\t<li>".join("</li>\n\t<li>", $eecho)."</li>\n<li>".'<a href="'.$thispage.'">View All</a>'."</li>\n</ul>\n";
			$echo .= '<div class="paginate pagfoot">'.$eeecho.'</div>';
		}else{
			$echo .= '<br class="pagfoot" />';
		}
		return $echo;
	} 
	return;
} 
function eshop_list_featured($atts){
	global $wpdb, $post;
	eshop_cache();
	$paged=$post;
	extract(shortcode_atts(array('class'=>'eshopfeatured','panels'=>'no','form'=>'no','sortby'=>'post_title','order'=>'ASC','imgsize'=>'','links'=>'yes'), $atts));
	$allowedsort=array('post_date','post_title','menu_order');
	$allowedorder=array('ASC','DESC');
	if(!in_array($sortby,$allowedsort)) 
		$sortby='post_title';
	if(!in_array($order,$allowedorder)) 
		$order='ASC';
	$pages=$wpdb->get_results("SELECT p.* from $wpdb->postmeta as pm,$wpdb->posts as p WHERE pm.meta_key='_eshop_featured' AND pm.meta_value='Yes' AND p.post_status='publish' AND p.ID=pm.post_id ORDER BY $sortby $order");
	if($pages) {
		if($panels=='no'){
			$echo = eshop_listpages($pages,$class,$form,$imgsize,$links);
		}else{
			if($class=='eshopfeatured') $class='eshoppanels';
			$echo = eshop_listpanels($pages,$class,$form,$imgsize,$links);
		}
		$echo .= '<br class="pagfoot" />';
		$post=$paged;
		return $echo;
	} 
	$post=$paged;
	return;
}
function eshop_list_random($atts){
	global $wpdb, $post;
	//cache
	eshop_cache();
	$paged=$post;
	extract(shortcode_atts(array('list' => 'yes','class'=>'eshoprandomlist','panels'=>'no','form'=>'no','show'=>'6','records'=>'6','imgsize'=>'','excludes'=>'0','links'=>'yes'), $atts));
	if($list!='yes' && $class='eshoprandomlist'){
		$class='eshoprandomproduct';
	}
	if($list=='yes'){
		$elimit=$show;
	}else{
		$elimit=1;
	}
	$subquery='';
	if($excludes!=0){
		$exclude= explode(",", $excludes);
		foreach($exclude as $exid){
			if(is_numeric($exid) && $exid>0){
				$subq[]= "$wpdb->posts.ID!=$exid";
			}
		}
		$subquery= ' AND '.implode(' AND ',$subq);
	}
	
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish'$subquery order by rand() limit $elimit");

	if($pages) {
		if($panels=='no'){
			$echo = eshop_listpages($pages,$class,$form,$imgsize,$links);
		}else{
			if($class=='eshoprandomlist') $class='eshoppanels';
				$echo = eshop_listpanels($pages,$class,$form,$imgsize,$links);
		}
		$post=$paged;
		return $echo;
	}
	$post=$paged;
	return;
}
function eshop_show_product($atts){
	global $wpdb, $post;
	eshop_cache();
	$paged=$post;
	extract(shortcode_atts(array('id'=>'0','class'=>'eshopshowproduct','panels'=>'no','form'=>'no','imgsize'=>'','links'=>'yes'), $atts));
	if($id!=0){
		$pages=array();
		$theids = explode(",", $id);
		foreach($theids as $thisid){
			$thispage=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND $wpdb->posts.ID='$thisid'");
			if(sizeof($thispage)>0)//only add if it exists
				array_push($pages,$thispage['0']);
		}
		if(sizeof($pages)>0){//if nothing found - don't do this
			if($panels=='no'){
				$echo = eshop_listpages($pages,$class,$form,$imgsize,$links);
			}else{
				if($class=='eshopshowproduct') $class='eshoppanels';
				$echo = eshop_listpanels($pages,$class,$form,$imgsize,$links);
			}
			$post=$paged;
			return $echo;
		}
		$post=$paged;
	}
	return;
}
function eshop_listpages($subpages,$eshopclass,$form,$imgsize,$links){
	global $wpdb, $post;
	$paged=$post;
	$eshopprodimg='_eshop_prod_img';
	$echo ='<ul class="eshop '.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);
		if($links=='yes')
			$echo .= '<li><a class="itemref" href="'.get_permalink($post->ID).'">'.apply_filters("the_title",$post->post_title).'</a>';
		else
			$echo .= '<li>'.apply_filters("the_title",$post->post_title);

		$w=get_option('thumbnail_size_w');
		$h=get_option('thumbnail_size_h');
		if($imgsize!=''){
			$w=round(($w*$imgsize)/100);
			$h=round(($h*$imgsize)/100);
		}
		if (has_post_thumbnail( $post->ID ) ) {
			if($links=='yes')
				$echo .='<a class="itemref" href="'.get_permalink($post->ID).'">'.get_the_post_thumbnail( $post->ID, array($w, $h)).'</a>'."\n";
			else
				$echo .=get_the_post_thumbnail( $post->ID, array($w, $h))."\n";

		}else{
			$eimage=eshop_files_directory();
			if($links=='yes')
				$echo .='<a class="itemref" href="'.get_permalink($post->ID).'"><img src="'.$eimage['1'].'noimage.png" height="'.$h.'" width="'.$w.'" alt="" /></a>'."\n";
			else
				$echo .='<img src="'.$eimage['1'].'noimage.png" height="'.$h.'" width="'.$w.'" alt="" />'."\n";

		}
		//this line stops the addtocart form appearing.
		remove_filter('the_content', 'eshop_boing');
		$echo .= apply_filters('the_excerpt', get_the_excerpt());
		if($form=='yes'){
			$short='yes';
			$echo =eshop_boing($echo,$short);
		}else
			$short='no';
		$echo .= '</li>'."\n";
		//and then we re-add it
		add_filter('the_content', 'eshop_boing');
	}
	$echo .= '</ul>';
	$post=$paged;
	return $echo;
}

function eshop_listpanels($subpages,$eshopclass,$form,$imgsize,$links){
	global $wpdb, $post;
	$paged=$post;
	$eshopprodimg='_eshop_prod_img';
	$echo ='<ul class="eshop '.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);
		$echo .= '<li>';
		$w=get_option('thumbnail_size_w');
		$h=get_option('thumbnail_size_h');
		if($imgsize!=''){
			$w=round(($w*$imgsize)/100);
			$h=round(($h*$imgsize)/100);
		}
		if (has_post_thumbnail( $post->ID ) ) {
			if($links=='yes')
				$echo .='<a class="itemref" href="'.get_permalink($post->ID).'">'.get_the_post_thumbnail( $post->ID, array($w, $h)).'</a>'."\n";
			else
				$echo .=get_the_post_thumbnail( $post->ID, array($w, $h))."\n";
		}else{
			$eimage=eshop_files_directory();
			if($links=='yes')
				$echo .='<a class="itemref" href="'.get_permalink($post->ID).'"><img src="'.$eimage['1'].'noimage.png" height="'.$h.'" width="'.$w.'" alt="" /></a>'."\n";
			else
				$echo .='<img src="'.$eimage['1'].'noimage.png" height="'.$h.'" width="'.$w.'" alt="" />'."\n";
		}
		if($links=='yes')
			$echo .= '<a href="'.get_permalink($post->ID).'"><span>'.apply_filters("the_title",$post->post_title).'</span></a>'."\n";
		else
			$echo .= '<span>'.apply_filters("the_title",$post->post_title).'</span>'."\n";

		include_once( 'eshop-get-custom.php' );
		if($form=='yes'){
			$short='yes';
			$echo =eshop_boing($echo,$short);
		}else
			$short='no';
		$echo .= '</li>'."\n";
	}
	$echo .= '</ul>';
	$post=$paged;
	return $echo;
}
function eshop_show_discounts(){
	global $eshopoptions;
	$edisc=array();
	eshop_cache();
	$currsymbol=$eshopoptions['currency_symbol'];
	$shipdisc=$eshopoptions['discount_shipping'];
	for ($x=1;$x<=3;$x++){
		if($eshopoptions['discount_spend'.$x]!='')
			$edisc[$eshopoptions['discount_spend'.$x]]=$eshopoptions['discount_value'.$x];
	}
	$echo ='';
	$discarray=sizeof($edisc);
	if($discarray>0){
		ksort($edisc);
		$echo='
		<table class="eshop eshopdiscounts" summary="'.__('Discount for amount sold','eshop').'">
		<caption>'.__('Discount for amount sold','eshop').'</caption>
		<thead>
		<tr>
		<th id="elevel">'.__('Discounts','eshop').'</th>
		<th id="espend">'.__('Spend','eshop').'</th>
		<th id="ediscount">'.__('% Discount','eshop').'</th>
		</tr>
		</thead>
		<tbody>';
		$x=0;
		foreach ($edisc as $amt => $percent) {
			$x++;
			$echo .='
			<tr>
			<th headers="elevel"  id="row'.$x.'">'.$x.'</th>
			<td headers="elevel espend row'.$x.'" class="amts">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($amt,2)).'</td>
			<td headers="elevel ediscount row'.$x.'" class="disc">'.$percent.'</td>
			</tr>';
		}
		$echo .='</table>';
	}
	if($shipdisc>0){
		$echo .='
		<p class="shipdiscount">'.__('Free Shipping if you spend over','eshop').' <span>'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($eshopoptions['discount_shipping'],2)).'</span></p>';
	}
	return $echo;
}
function eshop_show_payments(){
	global $eshopoptions;
	$echo='';
	eshop_cache();
	if(is_array($eshopoptions['method'])){
		$i=1;
		$eshopfiles=eshop_files_directory();
		$echo.= "\n".'<ul class="eshop eshoppayoptions">'."\n";
		foreach($eshopoptions['method'] as $k=>$eshoppayment){
			$eshoppayment_text=$eshoppayment;
			if($eshoppayment_text=='cash'){
				$eshopcash = $eshopoptions['cash'];
				if($eshopcash['rename']!='')
					$eshoppayment_text=$eshopcash['rename'];
			}

			$echo.= '<li><img src="'.$eshopfiles['1'].$eshoppayment.'.png" height="44" width="142" alt="'.__('Pay via','eshop').' '.$eshoppayment.'" title="'.__('Pay via','eshop').' '.$eshoppayment.'" /></li>'."\n";
			$i++;
		}
		$echo.= "</ul>\n";
	}
	return $echo;
}

function eshop_show_shipping($atts) { 
	global $wpdb, $eshopoptions;
	eshop_cache();
	if($eshopoptions['shipping']!='4'){
		extract(shortcode_atts(array('shipclass'=>'A,B,C,D,E,F'), $atts));
		$shipclasses = explode(",", $shipclass);
		$dtable=$wpdb->prefix.'eshop_shipping_rates';
		$query=$wpdb->get_results("SELECT * from $dtable");
		$currsymbol=$eshopoptions['currency_symbol'];

		$eshopshiptable='<table id="eshopshiprates" summary="'.__('This is a table of our online order shipping rates','eshop').'" class="eshopshiprates eshop">';
		$eshopshiptable.='<caption><span>'.__('Shipping rates by class and zone <small>(subject to change)</small>','eshop').'</span></caption>'."\n";
		$eshopshiptable.='<thead><tr><th id="class">'.__('Ship Class','eshop').'</th><th id="zone1">'.__('Zone 1','eshop').'</th><th id="zone2">'.__('Zone 2','eshop').'</th><th id="zone3">'.__('Zone 3','eshop').'</th><th id="zone4">'.__('Zone 4','eshop').'</th><th id="zone5">'.__('Zone 5','eshop').'</th></tr></thead>'."\n";
		$eshopshiptable.='<tbody>'."\n";
		$x=1;
		$calt=0;
		switch ($eshopoptions['shipping']){
			case '1':// ( per quantity of 1, prices reduced for additional items )

				$query=$wpdb->get_results("SELECT * from $dtable ORDER BY class ASC, items ASC");

				foreach ($query as $row){
					if(in_array($row->class,$shipclasses)){
						$calt++;
						$alt = ($calt % 2) ? ' class="eshoprow'.$x.'"' : ' class="alt eshoprow'.$x.'"';
						$eshopshiptable.= '<tr'.$alt.'>';
						if($row->items==1){
							$eshopshiptable.= '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>'.__('(First Item)','eshop').'</small></th>'."\n";
						}else{
							$eshopshiptable.= '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>'.__('(Additional Items)','eshop').'</small></th>'."\n";
						}
						$eshopshiptable.= '<td headers="zone1 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone1).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone2 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone2).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone3 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone3).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone4 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone4).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone5 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone5).'</td>'."\n";
						$eshopshiptable.= '</tr>';
						$x++;
					}
				}
				break;
			case '2'://( once per shipping class no matter what quantity is ordered )
				$query=$wpdb->get_results("SELECT * from $dtable where items='1' ORDER BY 'class'  ASC");
				foreach ($query as $row){
					if(in_array($row->class,$shipclasses)){
						$calt++;
						$alt = ($calt % 2) ? ' class="eshoprow'.$x.'"' : ' class="alt eshoprow'.$x.'"';
						$eshopshiptable.= '<tr'.$alt.'>';
						$eshopshiptable.= '<th id="cname'.$x.'" headers="class">'.$row->class.'</th>'."\n";
						$eshopshiptable.= '<td headers="zone1 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone1).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone2 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone2).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone3 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone3).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone4 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone4).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone5 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone5).'</td>'."\n";	
						$eshopshiptable.= '</tr>';
						$x++;
					}
				}
				break;
			case '3'://( one overall charge no matter how many are ordered )

				$query=$wpdb->get_results("SELECT * from $dtable where items='1' and class='A' ORDER BY 'class'  ASC");

				foreach ($query as $row){
					if(in_array($row->class,$shipclasses)){
						$calt++;
						$alt = ($calt % 2) ? ' class="eshoprow'.$x.'"' : ' class="alt eshoprow'.$x.'"';
						$eshopshiptable.= '<tr'.$alt.'>';
						$eshopshiptable.= '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>'.__('(Overall charge)','eshop').'</small></th>'."\n";
						$eshopshiptable.= '<td headers="zone1 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone1).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone2 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone2).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone3 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone3).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone4 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone4).'</td>'."\n";
						$eshopshiptable.= '<td headers="zone5 cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone5).'</td>'."\n";
						$eshopshiptable.= '</tr>';
						$x++;
					}
				}
				break;
		}
		if(in_array('F',$shipclasses)){
			$calt++;
			$alt = ($calt % 2) ? ' class="eshoprowf"' : ' class="alt eshoprowf"';
			$eshopshiptable.= '<tr'.$alt.'>';
			$eshopshiptable.= '<th id="cname'.$x.'" headers="class">F <small>'.__('(Free)','eshop').'</small></th>'."\n";
			$eshopshiptable.= '<td headers="zone1 zone2 zone3 zone4 zone5 cname'.$x.'" colspan="5" class="center">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format('0',2)).'</td>'."\n";
			$eshopshiptable.= '</tr>';
		}
		$eshopshiptable.='</tbody>'."\n";
		$eshopshiptable.='</table>'."\n";
	}else{
		if(isset($eshopoptions['ship_types'])){
			$dtable=$wpdb->prefix.'eshop_shipping_rates';
			$eshopshiptable='';
			$typearr=explode("\n", $eshopoptions['ship_types']);
			$eshopletter = "A";
			$weightsymbol=$eshopoptions['weight_unit'];
			$currsymbol=$eshopoptions['currency_symbol'];
			foreach ($typearr as $k=>$type){
				$k++;
				$eshopshiptable.='
				<table class="eshopshiprates eshop" summary="'.__('Shipping rates per mode','eshop').'">
				<caption>'.stripslashes(esc_attr($type)).'</caption>
				<thead>
				<tr>
				<th id="'.$eshopletter.'weight">'. __('Starting weight','eshop').'</th>
				<th id="'.$eshopletter.'zone1">'. __('Zone 1','eshop').'</th>
				<th id="'.$eshopletter.'zone2">'. __('Zone 2','eshop').'</th>
				<th id="'.$eshopletter.'zone3">'. __('Zone 3','eshop').'</th>
				<th id="'.$eshopletter.'zone4">'. __('Zone 4','eshop').'</th>
				<th id="'.$eshopletter.'zone5">'. __('Zone 5','eshop').'</th>
				</tr>
				</thead>
				<tbody>';
				$x=1;
				$query=$wpdb->get_results("SELECT * from $dtable where ship_type='$k' ORDER BY weight ASC");
				foreach ($query as $row){
					$alt = ($x % 2) ? '' : ' class="alt"';
					$eshopshiptable.='
					<tr'.$alt.'>
					<td id="'.$eshopletter.'cname'.$x.'" headers="'.$eshopletter.'weight">'.sprintf( _x('%1$s %2$s','1 - weight 2-weight symbol','eshop'), number_format($row->weight,2),$weightsymbol).'</td>
					<td headers="'.$eshopletter.'zone1 '.$eshopletter.'cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone1).'</td>
					<td headers="'.$eshopletter.'zone2 '.$eshopletter.'cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone2).'</td>
					<td headers="'.$eshopletter.'zone3 '.$eshopletter.'cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone3).'</td>
					<td headers="'.$eshopletter.'zone4 '.$eshopletter.'cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone4).'</td>
					<td headers="'.$eshopletter.'zone5 '.$eshopletter.'cname'.$x.'">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone5).'</td>
					</tr>';
					$x++;
				}
				$eshopletter++;
				$eshopshiptable.='</tbody></table>'."\n";
			}
		}else{
			$eshopshiptable='';
		}
	}

	if('yes' == $eshopoptions['show_zones']){
		$eshopshiptable.=eshop_show_zones();
	}
	return $eshopshiptable;

}

if (!function_exists('eshop_show_zones')) {
    /**
     * returns a table of the ones, state or country depending on what is chosen.
     */
    function eshop_show_zones() { 
		global $wpdb,$eshopoptions;
		eshop_cache();
		if('country' == $eshopoptions['shipping_zone']){
			//countries
			$tablec=$wpdb->prefix.'eshop_countries';
			$List=$wpdb->get_results("SELECT code,country FROM $tablec GROUP BY list,country",ARRAY_A);
			foreach($List as $key=>$value){
				$k=$value['code'];
				$v=$value['country'];
				$countryList[$k]=$v;
			}
			if(isset($_POST['country']) && $_POST['country']!=''){
				$country=$_POST['country'];
			}
			$echo ='<form action="#customzone" method="post" class="eshop eshopzones"><fieldset>
			<legend>'.__('Check your shipping zone','eshop').'</legend>
			 <label for="country">'.__('Country','eshop').' <select class="med" name="country" id="country">';
			$echo .='<option value="" selected="selected">'.__('Select your Country','eshop').'</option>';
			foreach($countryList as $code => $label)	{
				if (isset($country) && $country == $code){
					$echo.= "<option value=\"$code\" selected=\"selected\">$label</option>\n";
				}else{
					$echo.="<option value=\"$code\">$label</option>\n";
				}
			}
			$echo.= '</select></label> 
			<span class="buttonwrap"><input type="submit" class="button" id="submitit" name="submit" value="'.__('Submit','eshop').'" /></span>
			</fieldset></form>';
			if(isset($_POST) && $_POST['country']!=''){
				$qccode=$wpdb->escape($_POST['country']);
				$qcountry = $wpdb->get_row("SELECT country,zone FROM $tablec WHERE code='$qccode' limit 1",ARRAY_A);
				$echo .='<p id="customzone">'.sprintf(__('%1$s is in Zone %2$s','eshop'),$qcountry['country'],$qcountry['zone']).'.</p>';
			}

		}else{
			//each time re-request from the database
			// state list from db
			$table=$wpdb->prefix.'eshop_states';
			$getstate=$eshopoptions['shipping_state'];
			if($eshopoptions['show_allstates'] != '1'){
				$stateList=$wpdb->get_results("SELECT code,stateName FROM $table WHERE list='$getstate' ORDER BY stateName",ARRAY_A);
			}else{
				$stateList=$wpdb->get_results("SELECT code,stateName,list FROM $table ORDER BY list,stateName",ARRAY_A);
			}
			
			if(isset($_POST['state']) && $_POST['state']!=''){
				$state=$_POST['state'];
			}
			$echo ='<form action="#customzone" method="post" class="eshopzones"><fieldset>
			<legend>'.__('Check your shipping zone','eshop').'</legend>
			<label for="state">'.__('State','eshop').'<select class="med" name="state" id="state">';
			$echo .='<option value="" selected="selected">'.__('Select your State','eshop').'</option>';

			foreach($stateList as $code => $value){
				if(isset($value['list'])) $li=$value['list'];
				else $li='1';
				$eshopstatelist[$li][$value['code']]=$value['stateName'];
			}
			$tablec=$wpdb->prefix.'eshop_countries';
			foreach($eshopstatelist as $egroup =>$value){
				$eshopcname=$wpdb->get_var("SELECT country FROM $tablec where code='$egroup' limit 1");

				$echo .='<optgroup label="'.$eshopcname.'">'."\n";
				foreach($value as $code =>$stateName){
					$stateName=htmlspecialchars($stateName);
					if (isset($state) && $state == $code){
						$echo.= '<option value="'.$code.'" selected="selected">'.$stateName."</option>\n";
					}else{
						$echo.='<option value="'.$code.'">'.$stateName."</option>\n";
					}
				}
				$echo .="</optgroup>\n";
			}
			$echo.= "</select></label>\n".'
			<span class="buttonwrap"><input type="submit" class="button" id="submitit" name="submit" value="'.__('Submit','eshop').'" /></span>
			</fieldset></form>';
			if(isset($_POST['state']) && $_POST['state']!=''){
				$qccode=$wpdb->escape($_POST['state']);
				$qstate = $wpdb->get_row("SELECT stateName,zone FROM $table WHERE code='$qccode' limit 1",ARRAY_A);
				$echo .='<p id="customzone">'.sprintf(__('%1$s is in Zone %2$s','eshop'),$qstate['stateName'],$qstate['zone']).'.</p>';
			}
		}
		if(get_bloginfo('version')<'2.5.1')
			remove_filter('the_content', 'wpautop');

		return $echo;
	}
}
function eshop_addtocart(){
	global $wpdb, $post;
	eshop_cache();
	include_once( 'eshop-get-custom.php' );
	return eshop_boing('');
}
function eshop_welcome($atts, $content = ''){
	global $blog_id;
	extract(shortcode_atts(array('before'=>'','returning'=>'','guest'=>'','after'=>''), $atts));
	$echo='';
	if($before!='')
		$echo.=$before.' ';
	if(isset($_COOKIE["eshopcart"])){
		if($returning!='')
			$echo.=$returning.' ';
		$crumbs=eshop_break_cookie($_COOKIE["eshopcart"]);
		$echo .=$crumbs['first_name'].' '.$crumbs['last_name'];
	}else{
		$echo.=$guest.' ';
	}
	if($content!='')
		$echo.=$content.' ';	
	if($after!='')
		$echo.=' '.$after;
	return $echo;
}

function eshop_show_cancel(){
	global $wp_query;
	if(isset($wp_query->query_vars['eshopaction'])) {
		$eshopaction = urldecode($wp_query->query_vars['eshopaction']);
		if($eshopaction=='cancel'){
			$echo ='<h3 class="error">'.__('The order was cancelled at PayPal.','eshop')."</h3>";
			$echo.='<p>'.__('We have not emptied your shopping cart in case you want to make changes.','eshop').'</p>';
			return $echo;
		}
	}
	return;
}


function eshop_show_success(){
	global $wpdb,$eshopoptions;
	//cache
	eshop_cache();
	$echo='';
	global $wp_query;
	if(isset($wp_query->query_vars['eshopaction'])) {
		$eshopaction = urldecode($wp_query->query_vars['eshopaction']);
		if($eshopaction=='success' && isset($_POST['txn_id'])){
			$detailstable=$wpdb->prefix.'eshop_orders';
			$dltable=$wpdb->prefix.'eshop_download_orders';
			if($eshopoptions['status']=='live'){
				$txn_id = $wpdb->escape($_POST['txn_id']);
			}else{
				$txn_id = __('TEST-','eshop').$wpdb->escape($_POST['txn_id']);
			}
			$checkid=$wpdb->get_var("select checkid from $detailstable where transid='$txn_id' && downloads='yes' limit 1");
			$checkstatus=$wpdb->get_var("select status from $detailstable where transid='$txn_id' && downloads='yes' limit 1");

			if(($checkstatus=='Sent' || $checkstatus=='Completed') && $checkid!=''){
				$row=$wpdb->get_row("select email,code from $dltable where checkid='$checkid' and downloads>0 limit 1");
				if($row->email!='' && $row->code!=''){
					//display form only if there are downloads!
						$echo = '<form method="post" class="dform" action="'.get_permalink($eshopoptions['show_downloads']).'">
					<p class="submit"><input name="email" type="hidden" value="'.$row->email.'" /> 
					<input name="code" type="hidden" value="'.$row->code.'" /> 
					<span class="buttonwrap"><input type="submit" id="submit" class="button" name="Submit" value="'.__('View your downloads','eshop').'" /></span></p>
					</form>';
				}
			}
		}elseif($eshopaction=='success' && isset($_GET['epn'])){
			include_once (WP_PLUGIN_DIR.'/eshop/epn/process.php');
			if(isset($_GET['epn']) && $_GET['epn']=='ok' && isset($_POST['transid'])){
				$detailstable=$wpdb->prefix.'eshop_orders';
				$dltable=$wpdb->prefix.'eshop_download_orders';
				if($eshopoptions['status']=='live'){
					$txn_id = $wpdb->escape($_POST['transid']);
				}else{
					$txn_id = __('TEST-','eshop').$wpdb->escape($_POST['transid']);
				}
				$checkid=$wpdb->get_var("select checkid from $detailstable where transid='$txn_id' && downloads='yes' limit 1");
				$checkstatus=$wpdb->get_var("select status from $detailstable where transid='$txn_id' && downloads='yes' limit 1");

				if(($checkstatus=='Sent' || $checkstatus=='Completed') && $checkid!=''){
					$row=$wpdb->get_row("select email,code from $dltable where checkid='$checkid' and downloads>0 limit 1");
					if($row->email!='' && $row->code!=''){
						//display form only if there are downloads!
							$echo = '<form method="post" class="dform" action="'.get_permalink($eshopoptions['show_downloads']).'">
						<p class="submit"><input name="email" type="hidden" value="'.$row->email.'" /> 
						<input name="code" type="hidden" value="'.$row->code.'" /> 
						<span class="buttonwrap"><input type="submit" id="submit" class="button" name="Submit" value="'.__('View your downloads','eshop').'" /></span></p>
						</form>';
					}
				}
			}
		}
		elseif($eshopaction=='authorizenetipn'){
			// because authorize.net handles things differently... have to add this in here
			$detailstable=$wpdb->prefix.'eshop_orders';
			$dltable=$wpdb->prefix.'eshop_download_orders';
			if($eshopoptions['status']=='live'){
				$txn_id = $wpdb->escape($_POST['x_trans_id']);
			}else{
				$txn_id = __('TEST-','eshop').$wpdb->escape($_POST['x_trans_id']);
			}
			$checked=$wpdb->get_var("select checkid from $detailstable where transid='$txn_id' && downloads='yes' order by id DESC limit 1");

			$checkstatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' && downloads='yes' limit 1");
			if(($checkstatus=='Sent' || $checkstatus=='Completed') && $checked!=''){
				$row=$wpdb->get_row("select email,code from $dltable where checkid='$checked' and downloads>0 limit 1");
				if($row->email!='' && $row->code!=''){
					//display form only if there are downloads!
						$echo = '<form method="post" class="dform" action="'.get_permalink($eshopoptions['show_downloads']).'">
					<p class="submit"><input name="email" type="hidden" value="'.$row->email.'" /> 
					<input name="code" type="hidden" value="'.$row->code.'" /> 
					<span class="buttonwrap"><input type="submit" id="submit" class="button" name="Submit" value="'.__('View your downloads','eshop').'" /></span></p>
					</form>';
				}
			}
			// Start of TEMCEDIT-20091117-a
		}elseif($eshopaction=='idealliteipn' && isset($_GET['ideal']['status']) ){
			if($_GET['ideal']['status'] == md5("SUCCESS") ) {
					$echo ='<h3 class="success">'.__('Thank you for your order','eshop')." !</h3>";
					$echo.= '<p>'.__('Your iDEAL payment has been succesfully recieved.','eshop').'<br />';
					$echo.= __('We will get on it as soon as possible.','eshop').'</p>';
			}elseif($_GET['ideal']['status'] == md5("ERROR") ) {
					$echo ='<h3 class="error">'.__('The payment failed at iDEAL.','eshop')."</h3>";
					$echo.= '<p>'.__('Your iDEAL payment has not been revieced yet, and currently has status "ERROR".','eshop').'<br />';
					$echo.= __('Please try checkout your order again.','eshop').'</p>';
					$echo.='<p>'.__('We have not emptied your shopping cart in case you want to make changes.','eshop').'</p>';
			}elseif($_GET['ideal']['status'] == md5("CANCEL") ) {

					$echo ='<h3 class="error">'.__('The payment was cancelled at iDEAL.','eshop')."</h3>";
					$echo.= '<p>'.__('Your iDEAL payment has not been revieced yet, and currently has status "CANCEL".','eshop').'<br />';
					$echo.= __('Please try checkout your order again.','eshop').'</p>';
			}else{
					$echo ='<h3 class="error">'.__('The payment failed at iDEAL.','eshop')."</h3>";
					$echo.= '<p>'.__('Please try checkout your order again.','eshop').'</p>';
			}
			// End of TEMCEDIT-20091117-a
		}
		return $echo;
	}
}


function eshop_show_cart() {
	include_once 'cart.php';
	return eshop_cart($_POST);
}


function eshop_show_checkout(){
	include_once 'checkout.php';
	return eshop_checkout($_POST);
}


function eshop_show_downloads(){
	include_once 'purchase-downloads.php';
	return eshop_downloads($_POST);
}
?>
