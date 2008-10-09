<?php
function eshop_widget($args) {
	extract($args);
	$options = get_option("eshop_widget");
	if (!is_array( $options )){
		$options = array('title' => __('eShop Cart','eshop'));
	}      
	echo $before_widget;
	echo $before_title.$options['title'].$after_title;
	$eshopsize=0;
	if(isset($_SESSION['shopcart'])){
		$eshopsize=sizeof($_SESSION['shopcart']);
	}
	echo '<p class="eshopwidget"><span>'.$eshopsize.'</span> ',plural($eshopsize, __('item','eshop'), __('items','eshop') ).' '.__('in cart','eshop').'.';
	if(isset($_SESSION['shopcart'])){
		echo '<br /><a href="'.get_permalink(get_option('eshop_cart')).'">'.__('View Cart','eshop').'</a>';
		echo '<br /><a href="'.get_permalink(get_option('eshop_checkout')).'">'.__('Checkout','eshop').'</a>';
	}
	echo '</p>'.$after_widget;
}
function eshop_control(){
  $options = get_option("eshop_widget");
  if (!is_array( $options )){
		$options = array('title' => __('eShop Cart','eshop'));
  }
  if (isset($_POST['eshop-Submit']) && $_POST['eshop-Submit']=='1') {
    $options['title'] = htmlspecialchars($_POST['eshop-WidgetTitle']);
    update_option("eshop_widget", $options);
  }
?>
  <p>
    <label for="eshop-WidgetTitle">eShop Widget Title:</label>
    <input type="text" id="eshop-WidgetTitle" name="eshop-WidgetTitle" value="<?php echo $options['title'];?>" />
    <input type="hidden" id="eshop-Submit" name="eshop-Submit" value="1" />
  </p>
<?php
}

function eshopwidget_init(){
  	register_sidebar_widget(__('eShop Cart','eshop'), 'eshop_widget');
    register_widget_control(__('eShop Cart','eshop'), 'eshop_control');
}
/* used in widget only - only move if used elsewhere */
function plural( $quantity, $singular, $plural ){
  if( intval( $quantity ) == 1 )
    return $singular;
  return $plural;
}
add_action("plugins_loaded", "eshopwidget_init");
?>