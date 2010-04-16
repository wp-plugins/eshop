<?php
function eshopwidgets_init(){
	register_widget('eshop_widget');
	register_widget('eshop_pay_widget');
	register_widget('eshop_products_widget');
}
add_action("widgets_init", "eshopwidgets_init");

/* *************************
** Main eShop cart widget **
************************** */
class eshop_widget extends WP_Widget {

	function eshop_widget() {
		$widget_ops = array('classname' => 'eshopcart_widget', 'description' => __('Displays a simplified cart','eshop'));
		$this->WP_Widget('eshopw_cart', __('eShop Cart','eshop'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );
		global $blog_id,$eshopoptions;
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$show = apply_filters( 'widget_text', $instance['show'], $instance );
		$showwhat = apply_filters( 'widget_text', $instance['showwhat'], $instance );

		if(isset($_SESSION['eshopcart'.$blog_id])){
			$eshopsize=0;
			$eshopqty=0;
			if(isset($_SESSION['eshopcart'.$blog_id])){
				
				$eshopsize=sizeof($_SESSION['eshopcart'.$blog_id]);
				
				foreach($_SESSION['eshopcart'.$blog_id] as $eshopdo=>$eshopwop){
					$eshopqty+=$eshopwop['qty'];
				}
				$eecho='<p class="eshopwidget">';
				if($showwhat=='items' || $showwhat=='both'){
					$eecho .='<span>'.$eshopsize.'</span> '.eshop_plural($eshopsize, __('product','eshop'), __('products','eshop') ).' '.__('in cart','eshop').'.';
				}
				if($showwhat=='qty' || $showwhat=='both'){
					if($showwhat=='both') $eecho.= '<br />';
					$eecho .='<span>'.$eshopqty.'</span> '.eshop_plural($eshopqty, __('item','eshop'), __('items','eshop') ).' '.__('in cart','eshop').'.';
				}
				$eecho.= '<br /><a href="'.get_permalink($eshopoptions['cart']).'">'.__('View Cart','eshop').'</a>';
				$eecho .='<br /><a href="'.get_permalink($eshopoptions['checkout']).'">'.__('Checkout','eshop').'</a>';
				$eecho .='</p>';
				echo $before_widget;
				echo $before_title.$title.$after_title;
				echo $eecho;
				echo $after_widget;
			}			
		}elseif($show!='no'){
			$eecho= '<p><a href="'.get_permalink($eshopoptions['cart']).'">'.__('View Cart','eshop').'</a>';
			$eecho .='<br /><a href="'.get_permalink($eshopoptions['checkout']).'">'.__('Checkout','eshop').'</a></p>';
			echo $before_widget;
			echo $before_title.$title.$after_title;
			echo $eecho;
			echo $after_widget;
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show'] = strip_tags( $new_instance['show'] );
		$instance['showwhat'] = strip_tags( $new_instance['showwhat'] );
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','show'=>'no','showwhat'=>'items' ) );
		$title = strip_tags($instance['title']);
		$show = $instance['show'];
		$showwhat = $instance['showwhat'];
		?>
		 <p>
		    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title);?>" />
		 </p>
		 <p>
		  	<label for="<?php echo $this->get_field_id('show'); ?>"><?php _e('Show when empty','eshop'); ?></label>
		  	<select id="<?php echo $this->get_field_id('show'); ?>" name="<?php echo $this->get_field_name('show'); ?>">
		  	<option value="yes"<?php selected( $show, 'yes' ); ?>><?php _e('Yes','eshop'); ?></option>
		  	<option value="no"<?php selected( $show, 'no' ); ?>><?php _e('No','eshop'); ?></option>
			</select><br />
			<label for="<?php echo $this->get_field_id('showwhat'); ?>"><?php _e('What to show','eshop'); ?></label>
			<select id="<?php echo $this->get_field_id('showwhat'); ?>" name="<?php echo $this->get_field_name('showwhat'); ?>">
			<option value="items"<?php selected( $showwhat, 'items' ); ?>><?php _e('Total number of different products','eshop'); ?></option>
			<option value="qty"<?php selected( $showwhat, 'qty' ); ?>><?php _e('Total number of different items','eshop'); ?></option>
			<option value="both"<?php selected( $showwhat, 'both' ); ?>><?php _e('Both','eshop'); ?></option>
			</select>
		</p>
	<?php
	}
}
/* *******************************
** eShop payment options widget **
******************************** */
class eshop_pay_widget extends WP_Widget {

	function eshop_pay_widget() {
		$widget_ops = array('classname' => 'eshoppay_widget', 'description' => __('Displays accepted payment logos','eshop'));
		$this->WP_Widget('eshopw_pay', __('eShop Payments Accepted','eshop'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );
		global $blog_id,$eshopoptions;
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		echo $before_widget;
		echo $before_title.$title.$after_title;
		if(is_array($eshopoptions['method'])){
			$i=1;
			$replace = array(".");
			$eshopfiles=eshop_files_directory();
			echo "\n".'<ul class="eshoppaywidget">'."\n";
			foreach($eshopoptions['method'] as $k=>$eshoppayment){
				$eshoppayment_text=$eshoppayment;
				$eshoppayment = str_replace($replace, "", $eshoppayment);
				if($eshoppayment_text=='cash'){
					$eshopcash = $eshopoptions['cash'];
					if($eshopcash['rename']!='')
						$eshoppayment_text=$eshopcash['rename'];
				}
				echo '<li><img src="'.$eshopfiles['1'].$eshoppayment.'.png" height="44" width="142" alt="'.__('Pay via','eshop').' '.$eshoppayment_text.'" title="'.__('Pay via','eshop').' '.$eshoppayment_text.'" /></li>'."\n";
				$i++;
			}
			echo "</ul>\n";
		}
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = strip_tags($instance['title']);
		?>
		 <p>
		    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title);?>" />
		 </p>
		 
	<?php
	}
}

/* **************************
**  eShop products widget  **
*************************** */
class eshop_products_widget extends WP_Widget {

	function eshop_products_widget() {
		$widget_ops = array('classname' => 'eshopproducts_widget', 'description' => __('Displays products','eshop'));
		$this->WP_Widget('eshopw_prod', __('eShop Products','eshop'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );
		global $blog_id;
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$show_size = $instance['show_size'];
		$show_id = $instance['show_id'];
		$show_type = $instance['show_type'];
		$show_what = $instance['show_what'];
		$order_by = $instance['order_by'];
		$show_amts = $instance['show_amts'];
		if($show_type==1) $stype='yes';
		else $stype='no';
		echo $before_widget;
		echo $before_title.$title.$after_title;
		switch($show_what){
			case '1'://featured
				echo eshopw_list_featured(array('images'=>$stype,'show'=>$show_amts,'size'=>$show_size,'sortby'=>$order_by));
				break;
			case '2'://new
				echo eshopw_list_new(array('images'=>$stype,'show'=>$show_amts,'size'=>$show_size));
				break;
			case '3'://random
				echo eshopw_list_random(array('images'=>$stype,'show'=>$show_amts,'size'=>$show_size));
				break;
			case '4'://show specific products
				echo eshopw_show_product(array('id'=>$show_id,'images'=>$stype,'size'=>$show_size));
				break;
			case '5'://show best sellers
				echo eshopw_best_sellers(array('images'=>$stype,'show'=>$show_amts,'size'=>$show_size));
				break;
			case '6'://show best sellers
				echo eshopw_list_cat_tags(array('images'=>$stype,'show'=>$show_amts,'size'=>$show_size,'type'=>'category_name','id'=>$show_id,'sortby'=>$order_by));
				break;
			case '7'://show best sellers
				echo eshopw_list_cat_tags(array('images'=>$stype,'show'=>$show_amts,'size'=>$show_size,'type'=>'cat','id'=>$show_id,'sortby'=>$order_by));
				break;
			case '8'://show best sellers
				echo eshopw_list_cat_tags(array('images'=>$stype,'show'=>$show_amts,'size'=>$show_size,'type'=>'tag','id'=>$show_id,'sortby'=>$order_by));
				break;
			case '9'://show best sellers
				echo eshopw_list_cat_tags(array('images'=>$stype,'show'=>$show_amts,'size'=>$show_size,'type'=>'tag_id','id'=>$show_id,'sortby'=>$order_by));
				break;
		}
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['show_size'] = strip_tags($new_instance['show_size']);
		$instance['show_id'] = $new_instance['show_id'];
		$instance['show_type'] = strip_tags($new_instance['show_type']);
		$instance['show_what'] = strip_tags($new_instance['show_what']);
		$instance['order_by'] = strip_tags($new_instance['order_by']);
		$instance['show_amts'] = strip_tags($new_instance['show_amts']);
		if(!is_numeric($instance['show_size'])) $instance['show_size']='';
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '','show_size'=>'','show_id'=>'','show_type'=>'','show_what'=>'','order_by'=>'','show_amts'=>'',) );
		$title = strip_tags($instance['title']);
		$show_size=$instance['show_size'];
		$show_id=$instance['show_id'];
		$show_type=$instance['show_type'];
		$show_what=$instance['show_what'];
		$order_by=$instance['order_by'];
		$show_amts=$instance['show_amts'];
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','eshop'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id('show_type'); ?>"><?php _e('Images or text','eshop'); ?></label>
			<select id="<?php echo $this->get_field_id('show_type'); ?>" name="<?php echo $this->get_field_name('show_type'); ?>">
			<option value="1"<?php selected( $show_type, '1' ); ?>><?php _e('Images','eshop'); ?></option>
			<option value="2"<?php selected( $show_type, '2' ); ?>><?php _e('Text','eshop'); ?></option>
		</select></p>
		<p><label for="<?php echo $this->get_field_id('show_size'); ?>"><?php _e('&#37; size image to display','eshop'); ?></label>
			<input size="3" maxlength="3" id="<?php echo $this->get_field_id('show_size'); ?>" name="<?php echo $this->get_field_name('show_size'); ?>" type="text" value="<?php echo $show_size; ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id('show_what'); ?>"><?php _e('What to show','eshop'); ?></label>
			<select id="<?php echo $this->get_field_id('show_what'); ?>" name="<?php echo $this->get_field_name('show_what'); ?>">
			<option value="1"<?php selected( $show_what, '1' ); ?>><?php _e('Featured','eshop'); ?></option>
			<option value="2"<?php selected( $show_what, '2' ); ?>><?php _e('New','eshop'); ?></option>
			<option value="3"<?php selected( $show_what, '3' ); ?>><?php _e('Random','eshop'); ?></option>
			<option value="4"<?php selected( $show_what, '4' ); ?>><?php _e('Specific products','eshop'); ?></option>
			<option value="5"<?php selected( $show_what, '5' ); ?>><?php _e('Best Sellers','eshop'); ?></option>
			<option value="6"<?php selected( $show_what, '6' ); ?>><?php _e('Category names','eshop'); ?></option>
			<option value="7"<?php selected( $show_what, '7' ); ?>><?php _e('Category ID','eshop'); ?></option>
			<option value="8"<?php selected( $show_what, '8' ); ?>><?php _e('Tags','eshop'); ?></option>
			<option value="9"<?php selected( $show_what, '9' ); ?>><?php _e('Tag ID','eshop'); ?></option>

		</select></p>
		<p><label for="<?php echo $this->get_field_id('order_by'); ?>"><?php _e('Featured Order by','eshop'); ?></label>
				<select id="<?php echo $this->get_field_id('order_by'); ?>" name="<?php echo $this->get_field_name('order_by'); ?>">
				<option value="1"<?php selected( $order_by, '1' ); ?>><?php _e('Title','eshop'); ?></option>
				<option value="2"<?php selected( $order_by, '2' ); ?>><?php _e('Menu Order','eshop'); ?></option>
				<option value="3"<?php selected( $order_by, '3' ); ?>><?php _e('Date Ascending','eshop'); ?></option>
				<option value="4"<?php selected( $order_by, '4' ); ?>><?php _e('Date Descending','eshop'); ?></option>
		</select></p>
		<p><label for="<?php echo $this->get_field_id('show_amts'); ?>"><?php _e('How many to show','eshop'); ?></label>
			<select id="<?php echo $this->get_field_id('show_amts'); ?>" name="<?php echo $this->get_field_name('show_amts'); ?>">
			<?php
			for($i=1;$i<=10;$i++){
			?>
				<option value="<?php echo $i; ?>"<?php selected( $show_amts, $i ); ?>><?php echo $i; ?></option>
			<?php
			}
			?>
		</select></p>
		<p><label for="<?php echo $this->get_field_id('show_id'); ?>"><?php _e('Page, Post, Tag &amp; Category IDs or Tag/Category names - comma separated','eshop'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('show_id'); ?>" name="<?php echo $this->get_field_name('show_id'); ?>" type="text" value="<?php echo $show_id; ?>" />
		</p>
	<?php
	}
}

/********************************************************************** */
/* functions for widgets above - similar but not the same as shortcodes */
/* ******************************************************************** */
function eshopw_list_new($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('class'=>'eshopw_new','images'=>'no','show'=>'6','size'=>''), $atts));
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' order by post_date DESC limit $show");
	if($pages) {
		if($images=='no'){
			$echo = eshopw_listpages($pages,$class);
		}else{
			$class='eshopw_panels';
			$echo = eshopw_listpanels($pages,$class,$size);
		}
		return $echo;
	} 
	return;
} 
function eshopw_best_sellers($atts){
	global $wpdb, $post;
	$stktable=$wpdb->prefix.'eshop_stock';
	extract(shortcode_atts(array('class'=>'eshopw_best','images'=>'no','show'=>'6','size'=>''), $atts));
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title 
	from $wpdb->postmeta,$wpdb->posts, $stktable as stk
	WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' 
	AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND stk.post_id=$wpdb->posts.ID
	order by stk.purchases DESC limit $show");
	if($pages) {
		if($images=='no'){
			$echo = eshopw_listpages($pages,$class);
		}else{
			$class='eshopw_panels';
			$echo = eshopw_listpanels($pages,$class,$size);
		}
		return $echo;
	} 
	return;
} 
function eshopw_list_featured($atts){
	global $wpdb, $post;
	$paged=$post;
	extract(shortcode_atts(array('class'=>'eshopw_featured','images'=>'no','show'=>'6','size'=>'','sortby'=>'1'), $atts));

	switch ($sortby){
		case '2'://menu order
			$orderby='p.menu_order';
			$order= 'ASC';
			break;
		case '3'://date asc
			$orderby='p.post_date';
			$order= 'ASC';
			break;
		case '4'://date desc
			$orderby='p.post_date';
			$order= 'DESC';
			break;
		case '1'://title
		default:
			$orderby='p.post_title';
			$order= 'ASC';
			break;
	}

	$pages=$wpdb->get_results("SELECT p.* from $wpdb->postmeta as pm,$wpdb->posts as p WHERE pm.meta_key='_eshop_featured' AND pm.meta_value='Yes' AND post_status='publish' AND p.ID=pm.post_id ORDER BY $orderby $order LIMIT $show");
	if($pages) {
		if($images=='no'){
			$echo = eshopw_listpages($pages,$class);
		}else{
			$class='eshopw_panels';
			$echo = eshopw_listpanels($pages,$class,$size);
		}
		$post=$paged;
		return $echo;
	} 
	$post=$paged;
	return;
}
function eshopw_list_random($atts){
	global $wpdb, $post;
	$paged=$post;
	extract(shortcode_atts(array('class'=>'eshopw_random','images'=>'no','show'=>'6','size'=>''), $atts));
	if($list!='yes' && $class='eshoprandomlist'){
		$class='eshoprandomproduct';
	}
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' order by rand() limit $show");
	if($pages) {
		if($images=='no'){
			$echo = eshopw_listpages($pages,$class);
		}else{
			$class='eshopw_panels';
			$echo = eshopw_listpanels($pages,$class,$size);
		}
		$post=$paged;
		return $echo;
	}
	$post=$paged;
	return;
}
function eshopw_show_product($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('id'=>'0','class'=>'eshopw_prod','images'=>'no','size'=>''), $atts));
	if($id!=0){
		$epages=array();
		$theids = explode(",", $id);
		foreach($theids as $thisid){
			$thispage=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_eshop_stock' AND $wpdb->postmeta.meta_value='1' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND $wpdb->posts.ID='$thisid'");
			if(sizeof($thispage)>0)//only add if it exists
				array_push($epages,$thispage['0']);
		}
		if(sizeof($epages)>0){//if nothing found - don't do this
			if($images=='no'){
				$echo = eshopw_listpages($epages,$class);
			}else{
				$echo = eshopw_listpanels($epages,$class,$size);
			}
			return $echo;
		}
	}
	return;
}
function eshopw_list_cat_tags($atts){
	global $wpdb, $post,$wp_query;
	eshop_cache();
	extract(shortcode_atts(array('class'=>'eshopwcats','images'=>'no','sortby'=>'1','show'=>'6','order'=>'ASC','size'=>'','id'=>'','type'=>''), $atts));
	$allowedsort=array('post_date','post_title','menu_order');
	$allowedorder=array('ASC','DESC');
	if(!in_array($sortby,$allowedsort)) 
		$sortby='post_title';
	switch ($sortby){
		case '2'://menu order
			$orderby='menu_order';
			$order= 'ASC';
			break;
		case '3'://date asc
			$orderby='post_date';
			$order= 'ASC';
			break;
		case '4'://date desc
			$orderby='post_date';
			$order= 'DESC';
			break;
		case '1'://title
		default:
			$orderby='post_title';
			$order= 'ASC';
			break;
	}
	if(!in_array($order,$allowedorder)) 
		$order='ASC';
	
	$args = array(
	'post_type' => 'post',
	'post_status' => null,
	$type => $id, 
	'meta_key'=>'_eshop_product',
	'orderby'=> $orderby,
	'LIMIT' => $show,
	); 
	$pages = query_posts($args);
	wp_reset_query();
	if($pages) {
		if($images=='no'){
			$echo = eshopw_listpages($pages,$class);
		}else{
			if($class=='eshopsubpages') $class='eshopwpanels';
			$echo = eshopw_listpanels($pages,$class,$size);
		}
		return $echo;
	} 
	return;
}
function eshopw_listpages($subpages,$eshopclass){
	global $wpdb, $post;
	$paged=$post;
	$echo='';
	$echo .='<ul class="'.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);
		$echo .= '<li><a class="itemref" href="'.get_permalink($post->ID).'">'.apply_filters("the_title",$post->post_title).'</a></li>';
	}
	$echo .= '</ul>';
	$post=$paged;
	return $echo;
}

function eshopw_listpanels($subpages,$eshopclass,$size){
	global $wpdb, $post;
	$paged=$post;
	$echo='';
	$echo .='<ul class="'.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);
		$w=get_option('thumbnail_size_w');
		$h=get_option('thumbnail_size_h');
		if($size!=''){
			$w=round(($w*$size)/100);
			$h=round(($h*$size)/100);
		}
		if (has_post_thumbnail( $post->ID ) ) {
			$echo .='<li><a class="itemref" href="'.get_permalink($post->ID).'">'.get_the_post_thumbnail( $post->ID, array($w, $h)).'</a></li>'."\n";
		}else{
			$eimage=eshop_files_directory();
			$echo .='<li><a class="itemref" href="'.get_permalink($post->ID).'"><img src="'.$eimage['1'].'noimage.png" height="'.$h.'" width="'.$w.'" alt="" /></a></li>'."\n";
		}
	}
	$echo .= '</ul>';
	$post=$paged;
	return $echo;
}
?>