<?php 
add_shortcode('eshop_list_featured', 'eshop_list_featured');
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

function eshop_list_subpages($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('class'=>'eshopsubpages','panels'=>'no','form'=>'no'), $atts));

	switch (get_option('eshop_sudo_cat')){
		case '1'://newest
			$orderby='date';
			$order= 'DESC';
			break;
		case '2'://oldest
			$orderby='date';
			$order= 'ASC';
			break;
		case '3'://alphabetically
		default:
			$orderby='title';
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
		}else{
			$echo .= '<br class="pagfoot" />';
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
			$echo .= eshop_listpages($pages,$class,$form);
		}else{
			if($class='eshopsubpages') $class='eshoppanels';
			$echo .= eshop_listpanels($pages,$class,$form);
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

	$pages=$wpdb->get_results("SELECT p.* from $wpdb->postmeta as pm,$wpdb->posts as p WHERE pm.meta_key='_Featured Product' AND pm.meta_value='Yes' AND post_status='publish' AND p.ID=pm.post_id ORDER BY $orderby $order");
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
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' order by rand() limit $elimit");
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
			$thispage=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND $wpdb->posts.ID='$thisid'");
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
	$echo ='<ul class="eshop '.$eshopclass.'">';
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

function eshop_listpanels($subpages,$eshopclass,$form){
	global $wpdb, $post;
	$paged=$post;
	$eshopprodimg='_eshop_prod_img';
	$echo ='<ul class="eshop '.$eshopclass.'">';
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
		$echo .= $post->post_title.'</a>'."\n";
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
			if(isset($_POST) && $_POST['country']!=''){
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
			<label for="submitit"><input type="submit" class="button" id="submitit" name="submit" value="'.__('Submit','eshop').'" /></label>
			</fieldset></form>';
			if(isset($_POST) && $_POST['country']!=''){
				$qccode=$wpdb->escape($_POST['country']);
				$qcountry = $wpdb->get_row("SELECT country,zone FROM $tablec WHERE code='$qccode' limit 1",ARRAY_A);
				$echo .='<p id="customzone">'.sprintf(__('%1$s is in Zone %2$s','eshop'),$qcountry['country'],$qcountry['zone']).'.</p>';
			}

		}else{
			//each time re-request from the database
			$dtable=$wpdb->prefix.'eshop_states';
			$List=$wpdb->get_results("SELECT code, stateName from $dtable ORDER BY stateName",ARRAY_A);
			foreach($List as $key=>$value){
				$k=$value['code'];
				$v=$value['stateName'];
				$stateList[$k]=$v;
			}
			if(isset($_POST) && $_POST['state']!=''){
				$state=$_POST['state'];
			}
			$echo ='<form action="#customzone" method="post" class="eshopzones"><fieldset>
			<legend>'.__('Check your shipping zone','eshop').'</legend>
			<label for="state">'.__('State','eshop').'<select class="med" name="state" id="state">';
			$echo .='<option value="" selected="selected">'.__('Select your State','eshop').'</option>';
			foreach($stateList as $code => $label)	{
				if (isset($state) && $state == $code){
					$echo.= "<option value=\"$code\" selected=\"selected\">$label</option>\n";
				}else{
					$echo.="<option value=\"$code\">$label</option>\n";
				}
			}
			$echo.= '</select></label>
			<label for="submitit"><input type="submit" class="button" id="submitit" name="submit" value="'.__('Submit','eshop').'" /></label>
			</fieldset></form>';
			if(isset($_POST) && $_POST['state']!=''){
				$qccode=$wpdb->escape($_POST['state']);
				$qstate = $wpdb->get_row("SELECT stateName,zone FROM $dtable WHERE code='$qccode' limit 1",ARRAY_A);
				$echo .='<p id="customzone">'.sprintf(__('%1$s is in Zone %2$s','eshop'),$qstate['stateName'],$qstate['zone']).'.</p>';
			}
		}
		if(get_bloginfo('version')<'2.5.1')
			remove_filter('the_content', 'wpautop');

		return $echo;
	}
}
?>