<?php 
function eshop_list_subpages($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('class'=>'eshopsubpages','panels'=>'no','form'=>'no'), $atts));

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
			$echo .= eshop_listpages($pages,$class,$form);
		}else{
			if($class='eshopsubpages') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form);
		}
		
		if(isset($eecho)){
			$echo .= '<div class="paginate pagfoot">'.$eecho.'</div>';
		}
		return $echo;
	} 
	return;
}
function eshop_list_new($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('class'=>'eshopsubpages','panels'=>'no','form'=>'no','show'=>'6','records'=>'6'), $atts));

	//my pager
	include_once ("pager-class.php");
	$range=10;
	//$max = $wpdb->get_var("SELECT count(ID) from $wpdb->posts WHERE post_type='page' AND post_status='publish' limit 0,$show");
	$max=$wpdb->get_var("SELECT count($wpdb->posts.ID) from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish'");
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
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' order by post_date DESC limit $offset,$records");

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
			$echo .= eshop_listpages($pages,$class,$form);
		}else{
			if($class='eshopsubpages') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form);
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
	extract(shortcode_atts(array('class'=>'eshopfeatured','panels'=>'no','form'=>'no'), $atts));

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
			$echo = eshop_listpages($pages,$class,$form);
		}else{
			if($class='eshopfeatured') $class='eshoppanels';
			$echo = eshop_listpanels($pages,$class,$form);
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
	extract(shortcode_atts(array('list' => 'yes','class'=>'eshoprandomlist','panels'=>'no','form'=>'no'), $atts));
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
			$echo = eshop_listpages($pages,$class,$form);
		}else{
			if($class='eshoprandomlist') $class='eshoppanels';
				$echo = eshop_listpanels($pages,$class,$form);
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
	extract(shortcode_atts(array('id'=>'0','class'=>'eshopshowproduct','panels'=>'no','form'=>'no'), $atts));
	if($id!=0){
		$pages=array();
		$theids = explode(",", $id);
		foreach($theids as $thisid){
			$thispage=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND $wpdb->posts.ID='$thisid'");
			if(sizeof($thispage)>0)//only add if it exists
				array_push($pages,$thispage['0']);
		}
		if(sizeof($pages)>0){//if nothing found - don't do this
			if($panels=='no'){
				$echo = eshop_listpages($pages,$class,$form);
			}else{
				$echo = eshop_listpanels($pages,$class,$form);
			}
			$post=$paged;
			return $echo;
		}
		$post=$paged;
	}
	return;
}
function eshop_listpages($subpages,$eshopclass,$form){
	global $wpdb, $post;
	$paged=$post;
	$eshopprodimg='_eshop_prod_img';
	$echo ='<ul class="'.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);

		$echo .= '<li><a class="itemref" href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
		//grab image or choose first image uploaded for that page
		$proddataimg=get_post_meta($post->ID,$eshopprodimg,true);
		$imgs= eshop_get_images($post->ID);
		$x=1;
		if(is_array($imgs)){
			if($proddataimg==''){
				foreach($imgs as $k=>$v){
					$x++;
					$echo .='<a class="itemref" href="'.get_permalink($post->ID).'"><img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /></a>';
					break;
				}
			}else{
				foreach($imgs as $k=>$v){
					if($proddataimg==$v['url']){
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
		if($form=='yes'){
			$short='yes';
			$echo =eshop_boing($echo,$short);
		}else
			$short='no';
		$echo .= '</li>';
		//and then we re-add it
		add_filter('the_content', 'eshop_boing');
	}
	$echo .= '</ul>';
	$post=$paged;
	return $echo;
}

function eshop_listpanels($subpages,$eshopclass,$form){
	global $wpdb, $post;
	$paged=$post;
	$eshopprodimg='_eshop_prod_img';
	$echo ='<ul class="'.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);

		$echo .= '<li><a href="'.get_permalink($post->ID).'">';
		//grab image  or choose first image uploaded for that page
		$proddataimg=get_post_meta($post->ID,$eshopprodimg,true);
			
		$imgs= eshop_get_images($post->ID);
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
		$echo .= $post->post_title.'</a>';
		
		//		$echo .= apply_filters('the_excerpt', get_the_excerpt());
		include_once( 'eshop-get-custom.php' );
		if($form=='yes'){
			$short='yes';
			$echo =eshop_boing($echo,$short);
		}else
			$short='no';
		$echo .= '</li>';
	}
	$echo .= '</ul>';
	$post=$paged;
	return $echo;
}
?>