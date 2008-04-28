<?php 
function eshop_list_subpages($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('class'=>'eshopsubpages','panels'=>'no'), $atts));

	switch (get_option('eshop_sudo_cat')){
		case '1'://newest
			$orderby='post_date';
			$order= 'DESC';
			break;
		case '2'://oldest
			$orderby='post_date';
			$order= 'ASC';
			break;
		case '3'://alphabetically
		default:
			$orderby='post_title';
			$order= 'ASC';
			break;
	}
	
	//my pager
	include_once ("pager-class.php");
	$range=10;
	$max = $wpdb->get_var("SELECT count(ID) from $wpdb->posts WHERE post_type='page' AND post_parent=$post->ID AND post_status='publish'");
	if($max>0){
		if(get_option('eshop_pagelist_num')!='' && is_numeric(get_option('eshop_pagelist_num'))){
			$records=get_option('eshop_pagelist_num');
		}else{
			$records='6';
		}
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
	$args = array(
	'post_type' => 'page',
	'numberposts' => null,
	'post_status' => null,
	'post_parent' => $post->ID, // any parent
	'orderby'=> $orderby,
	'order'=> $order,
	'numberposts' => $records, 
	'offset' => $offset,
	); 
	
	$pages = get_posts($args);
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
			$echo .= eshop_listpages($pages,$class);
		}else{
			if($class='eshopsubpages') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class);
		}
		
		if(isset($eecho)){
			$echo .= '<div class="paginate pagfoot">'.$eecho.'</div>';
		}
		return $echo;
	} 
	return;
} 
function eshop_list_featured($atts){
	global $wpdb, $post;
	$paged=$post;
	extract(shortcode_atts(array('class'=>'eshopfeatured','panels'=>'no'), $atts));

	switch (get_option('eshop_sudo_cat')){
		case '1'://newest
			$orderby='p.post_date';
			$order= 'DESC';
			break;
		case '2'://oldest
			$orderby='p.post_date';
			$order= 'ASC';
			break;
		case '3'://alphabetically
		default:
			$orderby='p.post_title';
			$order= 'ASC';
			break;
	}

	$pages=$wpdb->get_results("SELECT p.* from $wpdb->postmeta as pm,$wpdb->posts as p WHERE pm.meta_key='Featured Product' AND pm.meta_value='Yes' AND post_status='publish' AND p.ID=pm.post_id ORDER BY $orderby $order");
	if($pages) {
		if($panels=='no'){
			$echo = eshop_listpages($pages,$class);
		}else{
			if($class='eshopfeatured') $class='eshoppanels';
			$echo = eshop_listpanels($pages,$class);
		}
		$post=$paged;
		return $echo;
	} 
	$post=$paged;
	return;
}
function eshop_list_random($atts){
	global $wpdb, $post;
	$paged=$post;
	extract(shortcode_atts(array('list' => 'yes','class'=>'eshoprandomlist','panels'=>'no'), $atts));
	if($list!='yes' && $class='eshoprandomlist'){
		$class='eshoprandomproduct';
	}
	if($list=='yes'){
		$elimit=get_option('eshop_random_num');
	}else{
		$elimit=1;
	}
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' order by rand() limit $elimit");
	if($pages) {
		if($panels=='no'){
			$echo = eshop_listpages($pages,$class);
		}else{
			if($class='eshoprandomlist') $class='eshoppanels';
				$echo = eshop_listpanels($pages,$class);
		}
		$post=$paged;
		return $echo;
	}
	$post=$paged;
	return;
}

function eshop_listpages($subpages,$eshopclass){
	global $wpdb, $post;
	$paged=$post;
	
	$echo ='<ul class="'.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);

		$echo .= '<li><a class="itemref" href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
		//grab image used in the base feed, or choose first image uploaded for that page
		$basetable=$wpdb->prefix ."eshop_base_products";
		$basedimg=$wpdb->get_var("SELECT img FROM $basetable WHERE post_id = $post->ID");
				
		$imgs= eshop_get_images($post->ID);
		$x=1;
		if(is_array($imgs)){
			if($basedimg==''){
				foreach($imgs as $k=>$v){
					$x++;
					$echo .='<a class="itemref" href="'.get_permalink($post->ID).'"><img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /></a>';
					break;
				}
			}else{
				foreach($imgs as $k=>$v){
					if($basedimg==$v['url']){
						$x++;
						$echo .='<a class="itemref" href="'.get_permalink($post->ID).'"><img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /></a>';
						break;
					}
				}
			}
		}
		//this line stops the addtocart form appearing.
		remove_filter('the_content', 'eshop_boing');
		$echo .= apply_filters('the_excerpt', get_the_excerpt());
		$echo .= '</li>';
	}
	$echo .= '</ul>';
	$post=$paged;
	return $echo;
}

function eshop_listpanels($subpages,$eshopclass){
	global $wpdb, $post;

	$echo ='<ul class="'.$eshopclass.'">';
	foreach ($subpages as $paged) {
		setup_postdata($paged);

		$echo .= '<li><a href="'.get_permalink($paged->ID).'">';
		//grab image used in the base feed, or choose first image uploaded for that page
		$basetable=$wpdb->prefix ."eshop_base_products";
		$basedimg=$wpdb->get_var("SELECT img FROM $basetable WHERE post_id = $paged->ID");
				
		$imgs= eshop_get_images($paged->ID);
		$x=1;
		if(is_array($imgs)){
			if($basedimg==''){
				foreach($imgs as $k=>$v){
					$x++;
					$echo .='<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /><br />';
					break;
				}
			}else{
				foreach($imgs as $k=>$v){
					if($basedimg==$v['url']){
						$x++;
						$echo .='<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /><br />';
						break;
					}
				}
			}
		}
		$echo .= $paged->post_title.'</a></li>';
		//this line stops the addtocart form appearing, but is not used- very very weird.
		apply_filters('the_excerpt', get_the_excerpt());
	}
	$echo .= '</ul>';
	return $echo;
}
?>