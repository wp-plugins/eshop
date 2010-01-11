<?php 
add_shortcode('eshop_list_featured', 'eshop_list_featured');
add_shortcode('eshop_best_sellers', 'eshop_best_sellers');
add_shortcode('eshop_list_new', 'eshop_list_new');
add_shortcode('eshop_list_subpages', 'eshop_list_subpages');
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

function eshop_cart_items($atts){
	global $blog_id;
	extract(shortcode_atts(array('before'=>'','after'=>'','hide'=>'no'), $atts));
	$echo='';
	if($before!='')
		$echo.=$before.' ';
		
	if(isset($_SESSION['shopcart'.$blog_id]))
		$echo.=sizeof($_SESSION['shopcart'.$blog_id]);
	elseif($hide=='no')
		$echo.='0';
	else
		$eshopsize='';
		
	if($after!='')
		$echo.=' '.$after;
		
	if(isset($eshopsize))
		return;
	else 
		return $echo;
}

function eshop_empty_cart($atts, $content = '') {
	global $blog_id;
	if(isset($_SESSION['shopcart'.$blog_id])){
		$content='';
	}
	return $content;
}
function eshop_list_alpha($atts){
	global $wpdb, $post,$wp_rewrite;
	extract(shortcode_atts(array('class'=>'eshopalpha','panels'=>'no','form'=>'no','records'=>'25','imgsize'=>''), $atts));
	//a-z listing
	$letter_array = range('A','Z');
	$fullarray=$letter_array;
	$fullarray[]='num';
	$usedaz=$wpdb->get_results("SELECT DISTINCT UPPER(LEFT(post_title,1)) as letters FROM $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' ORDER BY letters");
	$usednum=$wpdb->get_var("SELECT COUNT(DISTINCT UPPER(LEFT(post_title,1)) BETWEEN '0' AND '9') FROM $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish'");
	foreach($usedaz as $usethis){
		$used[]=$usethis->letters;
	}
	if(!isset($_GET['eshopaz']) || !in_array($_GET['eshopaz'],$fullarray)){
		$_GET['eshopaz']='a';
		$dbletter='A';
	}
	$econtain='<ul class="eshop eshopaz">';
	$thispage=get_permalink($post->ID);
	if( $wp_rewrite->using_permalinks()){
		$thispage=get_permalink($post->ID).'?eshopaz=';
	}else{
		$thispage=get_permalink($post->ID).'&amp;eshopaz=';
	}

	foreach ($letter_array as $letter) {
		if (in_array($letter, $used)){
			if(isset($_GET['eshopaz']) && strtoupper($_GET['eshopaz'])==$letter){
				$addclass=' class="current"';
				$dbletter=$letter;
			}else
				$addclass='';
			$econtain.= '<li'.$addclass.'><a href="'.$thispage.$letter.'">'.$letter . "</a></li>\n";
		}else $econtain.= '<li><span>'.$letter."</span></li>\n";
	}
	if(isset($_GET['eshopaz']) && $_GET['eshopaz']=='num' && $usednum>0 )
		$econtain.= '<li class="current"><a href="'.$thispage.'num">0-9</a></li>'."\n";
	elseif ($usednum>0)
		$econtain.= '<li><a href="'.$thispage.'num">0-9</a></li>'."\n";
	else
	 	$econtain.= '<li><span>0-9</span></li>'."\n";

	$econtain.="</ul>\n";
	if(in_array($dbletter,$letter_array))
		$qbuild=" AND UPPER(LEFT(post_title,1))='$dbletter'";
	elseif(isset($_GET['eshopaz']) && $_GET['eshopaz']=='num')
		$qbuild=" AND UPPER(LEFT(post_title,1)) BETWEEN '0' AND '9'";
	//my pager
	include_once ("pager-class.php");
	$range=10;
	$max=$wpdb->get_var("SELECT count($wpdb->posts.ID) from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' $qbuild");
	if($max>0){

		if(isset($_GET['viewall']))$records=$max;

		$phpself=explode('?',get_permalink($post->ID));
		$pager = new eshopPager( 
			$max ,          //see above
			$records,            // how many records to display at one time
			@$_GET['_p'],	//this is the current page no. carried via _GET
			array('php_self'=>$phpself[0])
		);

		$pager->set_range($range);
		$offset=$pager->get_limit_offset();
	}
	if(!isset($offset)) $offset='0';
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' $qbuild order by post_title ASC limit $offset,$records");
	if($pages) {
		//paginate
		$echo = '<div class="paginate"><p>';
		if($pager->_pages > 1){
			$echo .= $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>','eshop')). '<br />';
		}else{
			$echo .= $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>','eshop')). '<br />';
		}
		$echo .= '</p>';
		//set up correct link
		$permalink = get_option('permalink_structure');
		if('' != $permalink)
			$bits='?';
		else
			$bits='&amp;';
		if($pager->_pages > 1){
			$eecho =  '<ul>';
			$eecho .=  $pager->get_prev('<li><a href="{LINK_HREF}">'.__('Prev','eshop').'</a></li>');
			$eecho .=  '<li>'.$pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>','</li><li>').'</li>';
			$eecho .=  $pager->get_next('<li><a href="{LINK_HREF}">'.__('Next','eshop').'</a></li>');  		
			if($pager->_pages >= 2){
				$eecho .= '<li><a class="viewall" href="'.get_permalink($post->ID).$bits.'_p=1&amp;viewall=yes">'.__('View All','eshop').'</a></li>';
			}
			$eecho .= '</ul>';
			//$echo .= $eecho;
		}
		$echo .= '</div>';
		//end
		if($panels=='no'){
			$echo .= eshop_listpages($pages,$class,$form,$imgsize);
		}else{
			if($class=='eshopalpha') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form,$imgsize);
		}

		if(isset($eecho)){
			$echo .= '<div class="paginate pagfoot">'.$eecho.'</div>';
		}else{
			$echo .= '<br class="pagfoot" />';
		}
		return $econtain.$echo;
	} 
	return $econtain .'<p>'. __('No products found for that letter or number.','eshop').'</p>';
} 
function eshop_list_subpages($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('class'=>'eshopsubpages','panels'=>'no','form'=>'no','show'=>'100','records'=>'10','sortby'=>'post_title','order'=>'ASC','imgsize'=>'','id'=>''), $atts));
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
	
	
	//my pager
	include_once ("pager-class.php");
	$range=10;
	$max = $wpdb->get_var("SELECT count(ID) from $wpdb->posts WHERE post_type='page' AND post_parent=$eshopid AND post_status='publish'");
	if($max>$show)
		$max=$show;
	if($max>0){
		if(isset($_GET['viewall']))$records=$max;
		$phpself=explode('?',get_permalink($eshopid));
		$pager = new eshopPager( 
			$max ,          //see above
			$records,            // how many records to display at one time
			@$_GET['_p'],	//this is the current page no. carried via _GET
			array('php_self'=>$phpself[0])
		);

		$pager->set_range($range);
		$offset=$pager->get_limit_offset();
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

	if($pages) {
		//paginate
		$echo .= '<div class="paginate"><p>';
		if($pager->_pages > 1){
			$echo .= $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>','eshop')). '<br />';
		}else{
			$echo .= $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>','eshop')). '<br />';
		}
		$echo .= '</p>';
		//set up correct link
		$permalink = get_option('permalink_structure');
		if('' != $permalink)
			$bits='?';
		else
			$bits='&amp;';
		if($pager->_pages > 1){
			$eecho =  '<ul>';
			$eecho .=  $pager->get_prev('<li><a href="{LINK_HREF}">'.__('Prev','eshop').'</a></li>');
			$eecho .=  '<li>'.$pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>','</li><li>').'</li>';
			$eecho .=  $pager->get_next('<li><a href="{LINK_HREF}">'.__('Next','eshop').'</a></li>');  		
			if($pager->_pages >= 2){
				$eecho .= '<li><a class="viewall" href="'.get_permalink($eshopid).$bits.'_p=1&amp;viewall=yes">'.__('View All','eshop').'</a></li>';
			}
			$eecho .= '</ul>';
			//$echo .= $eecho;
		}
		$echo .= '</div>';
		//end
		if($panels=='no'){
			$echo .= eshop_listpages($pages,$class,$form,$imgsize);
		}else{
			if($class=='eshopsubpages') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form,$imgsize);
		}
		
		if(isset($eecho)){
			$echo .= '<div class="paginate pagfoot">'.$eecho.'</div>';
		}else{
			$echo .= '<br class="pagfoot" />';
		}
		return $echo;
	} 
	return;
}
function eshop_list_new($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('class'=>'eshopsubpages','panels'=>'no','form'=>'no','show'=>'100','records'=>'10','imgsize'=>''), $atts));
	$echo='';
	//my pager
	include_once ("pager-class.php");
	$range=10;
	//$max = $wpdb->get_var("SELECT count(ID) from $wpdb->posts WHERE post_type='page' AND post_status='publish' limit 0,$show");
	$max=$wpdb->get_var("SELECT count($wpdb->posts.ID) from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish'");
	if($max>$show)
		$max=$show;
	if($max>0){

		if(isset($_GET['viewall']))$records=$max;

		$phpself=explode('?',get_permalink($post->ID));
		$pager = new eshopPager( 
			$max ,          //see above
			$records,            // how many records to display at one time
			@$_GET['_p'],	//this is the current page no. carried via _GET
			array('php_self'=>$phpself[0])
		);

		$pager->set_range($range);
		$offset=$pager->get_limit_offset();
		if($records>$show)
			$records=$show;
		if($pager->curr > 1 && $show % $records > 0 && $show % $records < $records)
			$records=$show % $records;

	}
	if(!isset($offset)) $offset='0';
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' order by post_date DESC limit $offset,$records");

	if($pages) {
		//paginate
		$echo = '<div class="paginate"><p>';
		if($pager->_pages > 1){
			$echo .= $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>','eshop')). '<br />';
		}else{
			$echo .= $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>','eshop')). '<br />';
		}
		$echo .= '</p>';
		//set up correct link
		$permalink = get_option('permalink_structure');
		if('' != $permalink)
			$bits='?';
		else
			$bits='&amp;';
		if($pager->_pages > 1){
			$eecho =  '<ul>';
			$eecho .=  $pager->get_prev('<li><a href="{LINK_HREF}">'.__('Prev','eshop').'</a></li>');
			$eecho .=  '<li>'.$pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>','</li><li>').'</li>';
			$eecho .=  $pager->get_next('<li><a href="{LINK_HREF}">'.__('Next','eshop').'</a></li>');  		
			if($pager->_pages >= 2){
				$eecho .= '<li><a class="viewall" href="'.get_permalink($post->ID).$bits.'_p=1&amp;viewall=yes">'.__('View All','eshop').'</a></li>';
			}
			$eecho .= '</ul>';
			//$echo .= $eecho;
		}
		$echo .= '</div>';
		//end
		if($panels=='no'){
			$echo .= eshop_listpages($pages,$class,$form,$imgsize);
		}else{
			if($class=='eshopsubpages') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form,$imgsize);
		}

		if(isset($eecho)){
			$echo .= '<div class="paginate pagfoot">'.$eecho.'</div>';
		}else{
			$echo .= '<br class="pagfoot" />';
		}
		return $echo;
	} 
	return;
} 
function eshop_best_sellers($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('class'=>'eshopbestsellers','panels'=>'no','form'=>'no','show'=>'100','records'=>'10','imgsize'=>''), $atts));
	$echo='';
	//my pager
	include_once ("pager-class.php");
	$range=10;
	$stktable=$wpdb->prefix.'eshop_stock';
	$max=$wpdb->get_var("SELECT COUNT($wpdb->postmeta.post_id)
		from $wpdb->postmeta,$wpdb->posts, $stktable as stk
		WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' 
	AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND stk.post_id=$wpdb->posts.ID");
	if($max>$show)
		$max=$show;
	if($max>0){

		if(isset($_GET['viewall']))$records=$max;

		$phpself=explode('?',get_permalink($post->ID));
		$pager = new eshopPager( 
			$max ,          //see above
			$records,            // how many records to display at one time
			@$_GET['_p'],	//this is the current page no. carried via _GET
			array('php_self'=>$phpself[0])
		);

		$pager->set_range($range);
		$offset=$pager->get_limit_offset();
		if($records>$show)
			$records=$show;
		if($pager->curr > 1 && $show % $records > 0 && $show % $records < $records)
			$records=$show % $records;

	}
	if(!isset($offset)) $offset='0';
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title 
	from $wpdb->postmeta,$wpdb->posts, $stktable as stk
	WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' 
	AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND stk.post_id=$wpdb->posts.ID
	order by stk.purchases DESC limit $offset,$records");

	if($pages) {
		//paginate
		$echo = '<div class="paginate"><p>';
		if($pager->_pages > 1){
			$echo .= $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>','eshop')). '<br />';
		}else{
			$echo .= $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>','eshop')). '<br />';
		}
		$echo .= '</p>';
		//set up correct link
		$permalink = get_option('permalink_structure');
		if('' != $permalink)
			$bits='?';
		else
			$bits='&amp;';
		if($pager->_pages > 1){
			$eecho =  '<ul>';
			$eecho .=  $pager->get_prev('<li><a href="{LINK_HREF}">'.__('Prev','eshop').'</a></li>');
			$eecho .=  '<li>'.$pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>','</li><li>').'</li>';
			$eecho .=  $pager->get_next('<li><a href="{LINK_HREF}">'.__('Next','eshop').'</a></li>');  		
			if($pager->_pages >= 2){
				$eecho .= '<li><a class="viewall" href="'.get_permalink($post->ID).$bits.'_p=1&amp;viewall=yes">'.__('View All','eshop').'</a></li>';
			}
			$eecho .= '</ul>';
			//$echo .= $eecho;
		}
		$echo .= '</div>';
		//end
		if($panels=='no'){
			$echo .= eshop_listpages($pages,$class,$form,$imgsize);
		}else{
			if($class=='eshopbestsellers') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form,$imgsize);
		}

		if(isset($eecho)){
			$echo .= '<div class="paginate pagfoot">'.$eecho.'</div>';
		}else{
			$echo .= '<br class="pagfoot" />';
		}
		return $echo;
	} 
	return;
} 
function eshop_list_featured($atts){
	global $wpdb, $post;
	$paged=$post;
	extract(shortcode_atts(array('class'=>'eshopfeatured','panels'=>'no','form'=>'no','sortby'=>'post_title','order'=>'ASC','imgsize'=>''), $atts));
	$allowedsort=array('post_date','post_title','menu_order');
	$allowedorder=array('ASC','DESC');
	if(!in_array($sortby,$allowedsort)) 
		$sortby='post_title';
	if(!in_array($order,$allowedorder)) 
		$order='ASC';
	$pages=$wpdb->get_results("SELECT p.* from $wpdb->postmeta as pm,$wpdb->posts as p WHERE pm.meta_key='_Featured Product' AND pm.meta_value='Yes' AND post_status='publish' AND p.ID=pm.post_id ORDER BY $sortby $order");
	if($pages) {
		if($panels=='no'){
			$echo = eshop_listpages($pages,$class,$form,$imgsize);
		}else{
			if($class=='eshopfeatured') $class='eshoppanels';
			$echo = eshop_listpanels($pages,$class,$form,$imgsize);
		}
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
	extract(shortcode_atts(array('list' => 'yes','class'=>'eshoprandomlist','panels'=>'no','form'=>'no','show'=>'6','records'=>'6','imgsize'=>'','excludes'=>'0'), $atts));
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
	
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish'$subquery order by rand() limit $elimit");

	if($pages) {
		if($panels=='no'){
			$echo = eshop_listpages($pages,$class,$form,$imgsize);
		}else{
			if($class=='eshoprandomlist') $class='eshoppanels';
				$echo = eshop_listpanels($pages,$class,$form,$imgsize);
		}
		$post=$paged;
		return $echo;
	}
	$post=$paged;
	return;
}
function eshop_show_product($atts){
	global $wpdb, $post;
	$paged=$post;
	extract(shortcode_atts(array('id'=>'0','class'=>'eshopshowproduct','panels'=>'no','form'=>'no','imgsize'=>''), $atts));
	if($id!=0){
		$pages=array();
		$theids = explode(",", $id);
		foreach($theids as $thisid){
			$thispage=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND $wpdb->posts.ID='$thisid'");
			if(sizeof($thispage)>0)//only add if it exists
				array_push($pages,$thispage['0']);
		}
		if(sizeof($pages)>0){//if nothing found - don't do this
			if($panels=='no'){
				$echo = eshop_listpages($pages,$class,$form,$imgsize);
			}else{
				if($class=='eshopshowproduct') $class='eshoppanels';
				$echo = eshop_listpanels($pages,$class,$form,$imgsize);
			}
			$post=$paged;
			return $echo;
		}
		$post=$paged;
	}
	return;
}
function eshop_listpages($subpages,$eshopclass,$form,$imgsize){
	global $wpdb, $post;
	$paged=$post;
	$eshopprodimg='_eshop_prod_img';
	$echo ='<ul class="eshop '.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);

		$echo .= '<li><a class="itemref" href="'.get_permalink($post->ID).'">'.apply_filters("the_title",$post->post_title).'</a>';
		//grab image or choose first image uploaded for that page
		$proddataimg=get_post_meta($post->ID,$eshopprodimg,true);
		$imgs= eshop_get_images($post->ID,$imgsize);
		$x=1;
		if(is_array($imgs)){
			if($proddataimg==''){
				foreach($imgs as $k=>$v){
					$x++;
					$echo .='<a class="itemref" href="'.get_permalink($post->ID).'"><img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /></a>'."\n";
					break;
				}
			}else{
				foreach($imgs as $k=>$v){
					if($proddataimg==$v['url']){
						$x++;
						$echo .='<a class="itemref" href="'.get_permalink($post->ID).'"><img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /></a>'."\n";
						break;
					}
				}
			}
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

function eshop_listpanels($subpages,$eshopclass,$form,$imgsize){
	global $wpdb, $post;
	$paged=$post;
	$eshopprodimg='_eshop_prod_img';
	$echo ='<ul class="eshop '.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);

		$echo .= '<li><a href="'.get_permalink($post->ID).'">';
		//grab image  or choose first image uploaded for that page
		$proddataimg=get_post_meta($post->ID,$eshopprodimg,true);
			
		$imgs= eshop_get_images($post->ID,$imgsize);
		$x=1;
		if(is_array($imgs)){
			if($proddataimg==''){
				foreach($imgs as $k=>$v){
					$x++;
					$echo .='<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /><br />';
					break;
				}
			}else{
				foreach($imgs as $k=>$v){
					if($proddataimg==$v['url']){
						$x++;
						$echo .='<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /><br />';
						break;
					}
				}
			}
		}
		$echo .= '<span>'.apply_filters("the_title",$post->post_title).'</span></a>'."\n";
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
	$edisc=array();
	$currsymbol=get_option('eshop_currency_symbol');
	$shipdisc=get_option('eshop_discount_shipping');
	for ($x=1;$x<=3;$x++){
		if(get_option('eshop_discount_spend'.$x)!='')
			$edisc[get_option('eshop_discount_spend'.$x)]=get_option('eshop_discount_value'.$x);
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
			<td headers="elevel espend row'.$x.'" class="amts">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($amt,2)).'</td>
			<td headers="elevel ediscount row'.$x.'" class="disc">'.$percent.'</td>
			</tr>';
		}
		$echo .='</table>';
	}
	if($shipdisc>0){
		$echo .='
		<p class="shipdiscount">'.__('Free Shipping if you spend over','eshop').' <span>'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format(get_option('eshop_discount_shipping'),2)).'</span></p>';
	}
	return $echo;
}
function eshop_show_payments(){
	$echo='';
	if(is_array(get_option('eshop_method'))){
		$i=1;
		$eshopfiles=eshop_files_directory();
		$echo.= "\n".'<ul class="eshop eshoppayoptions">'."\n";
		foreach(get_option('eshop_method') as $k=>$eshoppayment){
			$eshoppayment_text=$eshoppayment;
			if($eshoppayment_text=='cash'){
				$eshopcash = get_option('eshop_cash');
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
	global $wpdb;
	extract(shortcode_atts(array('shipclass'=>'A,B,C,D,E,F'), $atts));
	$shipclasses = explode(",", $shipclass);
	$dtable=$wpdb->prefix.'eshop_shipping_rates';
	$query=$wpdb->get_results("SELECT * from $dtable");
	$currsymbol=get_option('eshop_currency_symbol');

	$eshopshiptable='<table id="eshopshiprates" summary="'.__('This is a table of our online order shipping rates','eshop').'" class="eshop">';
	$eshopshiptable.='<caption><span>'.__('Shipping rates by class and zone <small>(subject to change)</small>','eshop').'</span></caption>'."\n";
	$eshopshiptable.='<thead><tr><th id="class">'.__('Ship Class','eshop').'</th><th id="zone1">'.__('Zone 1','eshop').'</th><th id="zone2">'.__('Zone 2','eshop').'</th><th id="zone3">'.__('Zone 3','eshop').'</th><th id="zone4">'.__('Zone 4','eshop').'</th><th id="zone5">'.__('Zone 5','eshop').'</th></tr></thead>'."\n";
	$eshopshiptable.='<tbody>'."\n";
	$x=1;
	$calt=0;
	switch (get_option('eshop_shipping')){
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
					$eshopshiptable.= '<td headers="zone1 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone1).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone2 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone2).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone3 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone3).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone4 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone4).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone5 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone5).'</td>'."\n";
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
					$eshopshiptable.= '<td headers="zone1 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone1).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone2 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone2).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone3 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone3).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone4 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone4).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone5 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone5).'</td>'."\n";	
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
					$eshopshiptable.= '<td headers="zone1 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone1).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone2 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone2).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone3 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone3).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone4 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone4).'</td>'."\n";
					$eshopshiptable.= '<td headers="zone5 cname'.$x.'">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, $row->zone5).'</td>'."\n";
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
		$eshopshiptable.= '<td headers="zone1 zone2 zone3 zone4 zone5 cname'.$x.'" colspan="5" class="center">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format('0',2)).'</td>'."\n";
		$eshopshiptable.= '</tr>';
	}
	$eshopshiptable.='</tbody>'."\n";
	$eshopshiptable.='</table>'."\n";

	if('yes' == get_option('eshop_show_zones')){
		$eshopshiptable.=eshop_show_zones();
	}
	return $eshopshiptable;

}

if (!function_exists('eshop_show_zones')) {
    /**
     * returns a table of the ones, state or country depending on what is chosen.
     */
    function eshop_show_zones() { 
		global $wpdb;
		if('country' == get_option('eshop_shipping_zone')){
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
			$getstate=get_option('eshop_shipping_state');
			if(get_option('eshop_show_allstates') != '1'){
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
	include_once( 'eshop-get-custom.php' );
	return eshop_boing('');
}
?>