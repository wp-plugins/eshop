<?php
function eshopwidgets_init(){
	$widget_ops = array('title'=>'','classname' => 'eshopcart_widget', 'description' => __('Displays a simplified cart','eshop'));
  	wp_register_sidebar_widget('eshopcart',__('eShop Cart','eshop'), 'eshop_widget',$widget_ops);
    wp_register_widget_control('eshopcart',__('eShop Cart','eshop'), 'eshop_control');
    $widget_ops = array('title'=>'','classname' => 'eshop_payments_widget', 'description' => __('Displays accepted payment logos','eshop'));
    wp_register_sidebar_widget('eshop_payments',__('eShop Payments','eshop'), 'eshop_pay_widget',$widget_ops);
  	wp_register_widget_control('eshop_payments',__('eshop_payments','eshop'), 'eshop_pay_control');
	eshop_products_register();
}
add_action("widgets_init", "eshopwidgets_init");
//add_action("plugins_loaded", "eshopwidgets_init");

/* *************************
** Main eShop cart widget **
************************** */

function eshop_widget($args) {
	global $blog_id;
	extract($args);
	$options = get_option("eshop_widget");
	$title = empty($options['title']) ? __('eShop Cart','eshop') : apply_filters('widget_title', $options['title']);
	if($options['show']!='no'){
		echo $before_widget;
		echo $before_title.$title.$after_title;
		$eshopsize=0;
		if(isset($_SESSION['shopcart'.$blog_id])){
			$eshopsize=sizeof($_SESSION['shopcart'.$blog_id]);
		}
		echo '<p class="eshopwidget"><span>'.$eshopsize.'</span> ',plural($eshopsize, __('item','eshop'), __('items','eshop') ).' '.__('in cart','eshop').'.';
		if(isset($_SESSION['shopcart'.$blog_id])){
			echo '<br /><a href="'.get_permalink(get_option('eshop_cart')).'">'.__('View Cart','eshop').'</a>';
			echo '<br /><a href="'.get_permalink(get_option('eshop_checkout')).'">'.__('Checkout','eshop').'</a>';
		}
		echo '</p>'.$after_widget;
	}
}
function eshop_control(){
  $options = get_option("eshop_widget");

  if (isset($_POST['eshop-Submit']) && $_POST['eshop-Submit']=='1') {
    $options['title'] = strip_tags(stripslashes($_POST['eshop-Widget']['title']));
    $options['show'] = strip_tags(stripslashes($_POST['eshop-Widget']['show']));

    update_option("eshop_widget", $options);
  }
  
?>
  <p>
    <label for="eshop-WidgetTitle"><?php _e('Title:'); ?></label>
    <input type="text" id="eshop-Widgettitle" name="eshop-Widget[title]" value="<?php echo $options['title'];?>" />
    <input type="hidden" id="eshop-Submit" name="eshop-Submit" value="1" />
  </p>
  <p><label for="eshop-WidgetShow"><?php _e('Show when empty','eshop'); ?></label>
  		<select id="eshop-WidgetShow" name="eshop-Widget[show]">
  		<option value="yes"<?php selected( $options['show'], 'yes' ); ?>><?php _e('Yes','eshop'); ?></option>
  		<option value="no"<?php selected( $options['show'], 'no' ); ?>><?php _e('No','eshop'); ?></option>
	</select></p>
<?php
}
/* *******************************
** eShop payment options widget **
******************************** */
function eshop_pay_widget($args) {
	global $blog_id;
	extract($args);
	$options = get_option("eshop_pay_widget");
	if (!is_array( $options )){
		$options = array('title' => __('eShop Payments Accepted','eshop'));
	}   
	$title = empty($options['title']) ? __('eShop Payments Accepted','eshop') : apply_filters('widget_title', $options['title']);

	echo $before_widget;
	echo $before_title.$title.$after_title;
	if(is_array(get_option('eshop_method'))){
		$i=1;
		$eshopfiles=eshop_files_directory();
		echo "\n".'<ul class="eshoppaywidget">'."\n";
		foreach(get_option('eshop_method') as $k=>$eshoppayment){
			$eshoppayment_text=$eshoppayment;
			if($eshoppayment_text=='cash'){
				$eshopcash = get_option('eshop_cash');
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
function eshop_pay_control(){
  $options = get_option("eshop_pay_widget");
  if (!is_array( $options )){
		$options = array('title' => __('eShop Payments Accepted','eshop'));
  }
  if (isset($_POST['eshop-pay-Submit']) && $_POST['eshop-pay-Submit']=='1') {
    $options['title'] = strip_tags(stripslashes($_POST['eshop-pay-WidgetTitle']));
    update_option("eshop_pay_widget", $options);
  }
?>
  <p>
    <label for="eshop-pay-WidgetTitle"><?php _e('eShop Payments Widget Title:','eshop'); ?></label>
    <input type="text" id="eshop-pay-WidgetTitle" name="eshop-pay-WidgetTitle" value="<?php echo $options['title'];?>" />
    <input type="hidden" id="eshop-Submit" name="eshop-pay-Submit" value="1" />
  </p>
<?php
}
/* **************************
**  eShop products widget  **
*************************** */
/**
 * Displays widget.
 *
 * Supports multiple widgets.
 *
 * @param array $args Widget arguments.
 * @param array|int $widget_args Widget number. Which of the several widgets of this type do we mean.
 */
function eshop_products_widgets( $args, $widget_args = 1 ) {
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	// Data should be stored as array:  array( number => data for that instance of the widget, ... )
	$options = get_option('eshop_products_widgets');
	if ( !isset($options[$number]) )
		return;

	echo $before_widget;
	// Do stuff for this widget, drawing data from $options[$number]
	$show_title = attribute_escape($options[$number]['show_title']);
	$show_size = attribute_escape($options[$number]['show_size']);
	$show_id = attribute_escape($options[$number]['show_id']);
	$show_type = attribute_escape($options[$number]['show_type']);
	$show_what = attribute_escape($options[$number]['show_what']);
	$order_by = attribute_escape($options[$number]['order_by']);
	$show_amts = attribute_escape($options[$number]['show_amts']);
	if(!is_numeric($show_size)) $show_size='';
	echo $before_title.$show_title.$after_title;
	if($show_type==1) $stype='yes';
	else $stype='no';
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
		case '5'://show specific products
			echo eshopw_best_sellers(array('images'=>$stype,'show'=>$show_amts,'size'=>$show_size));
			break;
	}
	echo $after_widget;
}

/**
 * Displays form for a particular instance of the widget.
 *
 * Also updates the data after a POST submit.
 *
 * @param array|int $widget_args Widget number. Which of the several widgets of this type do we mean.
 */
function eshop_products_control( $widget_args = 1 ) {
	global $wp_registered_widgets;
	static $updated = false; // Whether or not we have already updated the data after a POST submit

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	// Data should be stored as array:  array( number => data for that instance of the widget, ... )
	$options = get_option('eshop_products_widgets');
	if ( !is_array($options) )
		$options = array();

	// We need to update the data
	if ( !$updated && !empty($_POST['sidebar']) ) {
		// Tells us what sidebar to put the data in
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			// Remove all widgets of this type from the sidebar.  We'll add the new data in a second.  This makes sure we don't get any duplicate data
			// since widget ids aren't necessarily persistent across multiple updates
			if ( 'eshop_products_widgets' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "eshop-prod-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed. "many-$widget_number" is "{id_base}-{widget_number}
					unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['eshop_products_widget'] as $widget_number => $eshop_products_widgets_instance ) {
			// compile data from $eshop_products_widgets_instance
			if ( !isset($eshop_products_widgets_instance['show_what']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
			$show_id = wp_specialchars( $eshop_products_widgets_instance['show_id'] );
			$show_title = wp_specialchars( $eshop_products_widgets_instance['show_title'] );
			$show_size = wp_specialchars( $eshop_products_widgets_instance['show_size'] );
			$show_type = wp_specialchars( $eshop_products_widgets_instance['show_type'] );
			$show_what = wp_specialchars( $eshop_products_widgets_instance['show_what'] );
			$order_by = wp_specialchars( $eshop_products_widgets_instance['order_by'] );
			$show_amts = wp_specialchars( $eshop_products_widgets_instance['show_amts'] );
			$options[$widget_number] = array( 'show_id' => $show_id,'show_size' => $show_size,'show_what' => $show_what,'order_by' => $order_by,'show_amts' => $show_amts,'show_type' => $show_type,'show_title' => $show_title);  // Even simple widgets should store stuff in array, rather than in scalar
		}

		update_option('eshop_products_widgets', $options);

		$updated = true; // So that we don't go through this more than once
	}


	// Here we echo out the form
	if ( -1 == $number ) { // We echo out a template for a form which can be converted to a specific form later via JS
		$show_size = $show_id = $show_title = $show_what = $order_by = $show_amts = $show_type='';
		$number = '%i%';
	} else {
		$show_size = attribute_escape($options[$number]['show_size']);
		$show_id = attribute_escape($options[$number]['show_id']);
		$show_title = attribute_escape($options[$number]['show_title']);
		$show_type = attribute_escape($options[$number]['show_type']);
		$show_what = attribute_escape($options[$number]['show_what']);
		$order_by = attribute_escape($options[$number]['order_by']);
		$show_amts = attribute_escape($options[$number]['show_amts']);
	}

	// The form has inputs with names like eshop_products_widget[$number][something] so that all data for that instance of
	// the widget are stored in one $_POST variable: $_POST['eshop_products_widget'][$number]
?>
	<p><label for="eshop_show_title-<?php echo $number; ?>"><?php _e('Title','eshop'); ?></label>
	<input class="widefat" id="eshop_show_title-<?php echo $number; ?>" name="eshop_products_widget[<?php echo $number; ?>][show_title]" type="text" value="<?php echo $show_title; ?>" />
	</p>
	<p><label for="eshop_show_type-<?php echo $number; ?>"><?php _e('Images or text','eshop'); ?></label>
		<select id="eshop_show_type-<?php echo $number; ?>" name="eshop_products_widget[<?php echo $number; ?>][show_type]">
		<option value="1"<?php selected( $show_type, '1' ); ?>><?php _e('Images','eshop'); ?></option>
		<option value="2"<?php selected( $show_type, '2' ); ?>><?php _e('Text','eshop'); ?></option>
	</select></p>
	<p><label for="eshop_show_size-<?php echo $number; ?>"><?php _e('&#37; size image to display','eshop'); ?></label>
		<input size="3" maxlength="3" id="eshop_show_size-<?php echo $number; ?>" name="eshop_products_widget[<?php echo $number; ?>][show_size]" type="text" value="<?php echo $show_size; ?>" />
	</p>
	<p><label for="eshop_show_what-<?php echo $number; ?>"><?php _e('What to show','eshop'); ?></label>
		<select id="eshop_show_what-<?php echo $number; ?>" name="eshop_products_widget[<?php echo $number; ?>][show_what]">
		<option value="1"<?php selected( $show_what, '1' ); ?>><?php _e('Featured','eshop'); ?></option>
		<option value="2"<?php selected( $show_what, '2' ); ?>><?php _e('New','eshop'); ?></option>
		<option value="3"<?php selected( $show_what, '3' ); ?>><?php _e('Random','eshop'); ?></option>
		<option value="4"<?php selected( $show_what, '4' ); ?>><?php _e('Specific products','eshop'); ?></option>
		<option value="5"<?php selected( $show_what, '5' ); ?>><?php _e('Best Sellers','eshop'); ?></option>
	</select></p>
	<p><label for="eshop_order_by-<?php echo $number; ?>"><?php _e('Featured Order by','eshop'); ?></label>
			<select id="eshop_order_by-<?php echo $number; ?>" name="eshop_products_widget[<?php echo $number; ?>][order_by]">
			<option value="1"<?php selected( $order_by, '1' ); ?>><?php _e('Title','eshop'); ?></option>
			<option value="2"<?php selected( $order_by, '2' ); ?>><?php _e('Menu Order','eshop'); ?></option>
			<option value="3"<?php selected( $order_by, '3' ); ?>><?php _e('Date Ascending','eshop'); ?></option>
			<option value="4"<?php selected( $order_by, '4' ); ?>><?php _e('Date Descending','eshop'); ?></option>
	</select></p>
	<p><label for="eshop_show_amts-<?php echo $number; ?>"><?php _e('How many to show','eshop'); ?></label>
		<select id="eshop_show_amts-<?php echo $number; ?>" name="eshop_products_widget[<?php echo $number; ?>][show_amts]">
		<?php
		for($i=1;$i<=10;$i++){
		?>
			<option value="<?php echo $i; ?>"<?php selected( $show_amts, $i ); ?>><?php echo $i; ?></option>
		<?php
		}
		?>
	</select></p>
	<p><label for="eshop_show_id-<?php echo $number; ?>"><?php _e('Page/Post IDs - comma separated','eshop'); ?></label>
		<input class="widefat" id="eshop_show_id-<?php echo $number; ?>" name="eshop_products_widget[<?php echo $number; ?>][show_id]" type="text" value="<?php echo $show_id; ?>" />
	</p>
	<p>
		<input type="hidden" id="eshop_products_widget-submit-<?php echo $number; ?>" name="eshop_products_widget[<?php echo $number; ?>][submit]" value="1" />
	</p>
<?php
}

/**
 * Registers each instance of our widget on startup.
 */
function eshop_products_register() {
	if ( !$options = get_option('eshop_products_widgets') )
		$options = array();

	$widget_ops = array('classname' => 'eshop_products_widgets', 'description' => __('eShop Widget for displaying products','eshop'));
	$control_ops = array('id_base' => 'eshop-prod');
	$name = __('eShop Products','eshop');

	$registered = false;
	foreach ( array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['show_what']) ) // we used 'something' above in our exampple.  Replace with with whatever your real data are.
			continue;

		// $id should look like {$id_base}-{$o}
		$id = "eshop-prod-$o"; // Never never never translate an id
		$registered = true;
		wp_register_sidebar_widget( $id, $name, 'eshop_products_widgets', $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, 'eshop_products_control', $control_ops, array( 'number' => $o ) );
	}

	// If there are none, we register the widget's existance with a generic template
	if ( !$registered ) {
		wp_register_sidebar_widget( 'eshop-prod-1', $name, 'eshop_products_widgets', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'eshop-prod-1', $name, 'eshop_products_control', $control_ops, array( 'number' => -1 ) );
	}
}


/* ************************************************* */
/* used in widget only - only move if used elsewhere */
/* ************************************************* */

function plural( $quantity, $singular, $plural ){
  if( intval( $quantity ) == 1 )
    return $singular;
  return $plural;
}
/********************************************************************** */
/* functions for widgets above - similar but not the same as shortcodes */
/* ******************************************************************** */
function eshopw_list_new($atts){
	global $wpdb, $post;
	extract(shortcode_atts(array('class'=>'eshopw_new','images'=>'no','show'=>'6','size'=>''), $atts));
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' order by post_date DESC limit $show");
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
	WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' 
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

	$pages=$wpdb->get_results("SELECT p.* from $wpdb->postmeta as pm,$wpdb->posts as p WHERE pm.meta_key='_Featured Product' AND pm.meta_value='Yes' AND post_status='publish' AND p.ID=pm.post_id ORDER BY $orderby $order LIMIT $show");
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
	$pages=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' order by rand() limit $show");
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
			$thispage=$wpdb->get_results("SELECT $wpdb->postmeta.post_id, $wpdb->posts.post_content,$wpdb->posts.ID,$wpdb->posts.post_title from $wpdb->postmeta,$wpdb->posts WHERE $wpdb->postmeta.meta_key='_Stock Available' AND $wpdb->postmeta.meta_value='Yes' AND $wpdb->posts.ID=$wpdb->postmeta.post_id AND $wpdb->posts.post_status='publish' AND $wpdb->posts.ID='$thisid'");
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
function eshopw_listpages($subpages,$eshopclass){
	global $wpdb, $post;
	$paged=$post;
	$eshopprodimg='_eshop_prod_img';
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
	$eshopprodimg='_eshop_prod_img';
	$echo='';
	$echo .='<ul class="'.$eshopclass.'">';
	foreach ($subpages as $post) {
		setup_postdata($post);
		//grab image  or choose first image uploaded for that page
		$proddataimg=get_post_meta($post->ID,$eshopprodimg,true);
		$imgs= eshop_get_images($post->ID,$size);
		$x=1;
		if(is_array($imgs)){
			if($proddataimg==''){
				foreach($imgs as $k=>$v){
					$x++;
					$echo .='<li><a href="'.get_permalink($post->ID).'"><img src="'.$v['url'].'" '.$v['size'].' alt="'.apply_filters("the_title",$post->post_title).'" /></a></li>'."\n";;
					break;
				}
			}else{
				foreach($imgs as $k=>$v){
					if($proddataimg==$v['url']){
						$x++;
						$echo .='<li><a href="'.get_permalink($post->ID).'"><img src="'.$v['url'].'" '.$v['size'].' alt="'.apply_filters("the_title",$post->post_title).'" /></a></li>'."\n";;
						break;
					}
				}
			}
		}
	}
	$echo .= '</ul>';
	$post=$paged;
	return $echo;
}
?>