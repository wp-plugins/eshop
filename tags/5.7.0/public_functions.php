<?php
if ('public-functions.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');

if (!function_exists('eshop_pre_wp_head')) {
    function eshop_pre_wp_head() {
    	global $wp_query,$blog_id;
		if(isset($wp_query->query_vars['eshopaction'])) {
   	 		$eshopaction = urldecode($wp_query->query_vars['eshopaction']);
		   	if($eshopaction=='success'){
		   		//destroy cart
				$_SESSION = array();
				//session_destroy();
			}
			//we need to buffer output on a few pages
			if($eshopaction=='redirect'){
				global $eshopoptions;
				ob_start();
				if(isset($eshopoptions['zero']) && $eshopoptions['zero']=='1'){
					if($_POST['amount']=='0' && $_SESSION['final_price'.$blog_id]== '0')
						$_POST['eshop_payment']=$_SESSION['eshop_payment'.$blog_id]='cash';
				}
			}
			if($eshopaction=='webtopayipn'){
				include_once 'webtopay.php';
				exit;
			}
			if($eshopaction=='paypalipn'){
				include_once 'paypal.php';
				exit;
			}
			if($eshopaction=='paysonipn'){
				include_once 'payson.php';
				//exit;
			}
			if($eshopaction=='authorizenetipn'){
				include_once 'authorizenet.php';
				//exit;
			}
			if($eshopaction=='idealliteipn'){
				include_once 'ideallite.php';
				//exit;
			}
			if($eshopaction=='ogoneipn'){
				include_once 'ogone.php';
				//exit;
			}
		}
		if(isset($_POST['eshopident_1'])){
			ob_start();
		}
		
    }
}
if (!function_exists('eshop_wp_head_add')) {
    /**
     * javascript functions
     */
    function eshop_wp_head_add() {
    	global $wp_query,$eshopoptions,$wpdb;
    	$eshopurl=eshop_files_directory();
		if(isset($wp_query->query_vars['eshopaction'])) {
   	 		$eshopaction = urldecode($wp_query->query_vars['eshopaction']);
		   	if($eshopaction=='redirect'){
				//this automatically submits the redirect form
				if($eshopoptions['status']=='live'){
					wp_register_script('eShopSubmit', $eshopurl['1'].'eshop-onload.js', array('jquery'));
					wp_enqueue_script('eShopSubmit');
				}
			}
		}
    }
}


if (!function_exists('add_eshop_query_vars')) {
	function add_eshop_query_vars($aVars) {
		$aVars[] = "eshopaction";    // represents the name of the product category as shown in the URL
		$aVars[] = "eshopaz";
		$aVars[] = "eshopall";
		$aVars[] = "_p";
		return $aVars;
	}
}

if (!function_exists('eshop_stylesheet')) {
	function eshop_stylesheet() {
		global $eshopoptions;
		$eshopurl=eshop_files_directory();
		if(@file_exists(STYLESHEETPATH.'/eshop.css')) {
			$myStyleUrl = get_stylesheet_directory_uri().'/eshop.css';
			$myStyleFile=STYLESHEETPATH.'/eshop.css';
		}elseif($eshopoptions['style']=='yes'){
			$myStyleUrl = $eshopurl['1'] . 'eshop.css';
			$myStyleFile=$eshopurl['0'] . 'eshop.css';
		}
		if ( file_exists($myStyleFile) ) {
			wp_register_style('myStyleSheets', $myStyleUrl);
			wp_enqueue_style( 'myStyleSheets');
		}
	}
}

if (!function_exists('eshop_unversion')) {
	function eshop_unversion($src) {
		if( strpos($src,'eshop.css'))
			$src=remove_query_arg('ver', $src);
		return $src;
	}
}
function eshop_bits_and_bobs(){
	global $eshopoptions;
	/**
	* eshop download products - need to process afore page is rendered
	* so this has to be called like this - unless anyone can come up with a better idea!
	*/
	if (isset($_POST['eshoplongdownloadname'])){
	//long silly name to ensure it isn't used elsewhere!
		eshop_download_the_product($_POST); 
	}
	if($eshopoptions['status']=='testing'){
		//require_once( ABSPATH . WPINC . '/pluggable.php' );
		if(is_user_logged_in() && current_user_can('eShop_admin')){
			add_action('wp_head','eshop_test_mode');
			add_action('wp_footer','eshop_test_mode_text');
		}
	}
	//add images to the search page if set
	if('no' != $eshopoptions['search_img']){
		add_filter('the_excerpt','eshop_excerpt_img');
		add_filter('the_content','eshop_excerpt_img');
	}
	if($eshopoptions['fold_menu'] == 'yes'){
		add_filter('wp_list_pages_excludes', 'eshop_fold_menus');
	}
}
if (!function_exists('eshop_test_mode_text')) {
	function eshop_test_mode_text(){
		echo '<div id="eshoptestmode" title="'.__("This note is only visible to eShop Admins",'eshop').'">'.__('Admin note: eShop is currently in test mode, and only admins can place orders.','eshop').'</div>';
		return;
	}
}

if (!function_exists('eshop_test_mode')) {
	function eshop_test_mode(){
		echo '<style type="text/css">
			#eshoptestmode{
				padding:5px 0;
				text-align:center;
				width:100%;
				display:block;
				color:#FFFFFF;
				position:absolute;
				top:0;
				left:0;
				background-color:#800;
				filter:alpha(opacity=80);
				-moz-opacity:0.8;
				-khtml-opacity: 0.8;
				opacity: 0.8;
				font-weight:bold;
			}
			</style>';
	return;
	}
}
/* ajax */
if (!function_exists('eshop_ajax_inc')) {
	function eshop_ajax_inc(){
		wp_enqueue_script('jquery');
	}
}

if (!function_exists('eshop_action_javascript')) {
	function eshop_action_javascript() {
	?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($){
	$('.addtocart').submit(function(){
		var Id =$(this).attr("id");
		var data = {action: 'eshop_special_action',post:$('#'+Id).serialize() };  
		$.post("<?php echo admin_url('admin-ajax.php'); ?>", data,
			function(response){
			$('#'+Id +" .eshopajax").insertAfter(this).fadeIn(100).html(response).fadeOut(3000);
			setTimeout (cleareshopCart,500); 
			setTimeout (doeshopRequest,2750);  
			setTimeout (cleareshopRequest,3000);  
		});
		function doeshopRequest(){
			var tdata = {action: 'eshop_cart'};
			$.post("<?php echo admin_url('admin-ajax.php'); ?>", tdata,
			function(response){
				$(".ajaxcart").insertAfter(this).fadeOut(50).html(response).fadeIn(700);
			});
		}
		function cleareshopRequest(){
			$(".eshopajax").empty();
		}
		function cleareshopCart(){
			$(".ajaxcart").insert();
		}
		return false;
	});

});
//]]>
</script>
	<?php
	}
}

if (!function_exists('eshop_cart_callback')) {
	function eshop_cart_callback($array) {
		global $eshopoptions, $blog_id;
		if(isset($_SESSION['eshopcart'.$blog_id]))
			echo display_cart($_SESSION['eshopcart'.$blog_id],false, $eshopoptions['checkout'],'widget');
		die();

	}
}

if (!function_exists('eshop_special_action_callback')) {
	function eshop_special_action_callback($array) {
		global $_POST, $blog_id; 
		// extract the data
		$jdata=$_POST['post'];
		$q = explode("&",$jdata);
		foreach ($q as $qi){
			if ($qi != ""){
				$qa = explode("=",$qi);
				list ($key, $val) = $qa;
				if(substr(urldecode($key),0,6)=='optset' && $val){
					$arr2[urldecode($key)] = urldecode($val);
				}elseif ($val){
					$data[urldecode($key)] = urldecode($val);
				}
			}
		} 
		if(isset($arr2)){
			foreach ($arr2 as $arr => $v){
				$off=substr($arr,6);
				$off=$off.'[val]['.$v.']';
				$on[]=explode('][',trim($off,'[]'));

			}
			foreach($on as $c){
				//change string array into proper array
				//0 = arraynum
				//1=arraykey
				//3=value
				$data['optset'][$c[0]][$c[1]]=$c[3];
			}
		}
		//quick qunatity check
		if(!isset($data['qty']) || isset($data['qty']) && !ctype_digit($data['qty'])){
			$msg=apply_filters('eshopCartQtyError','<p><strong class="error">'.__('Warning: you must supply a valid quantity.','eshop').'</strong></p>');
		}
		if(!isset($msg)){
			eshop_cart_process($data);
			if(isset($_SESSION['eshopcart'.$blog_id]['error'])){
				$msg=apply_filters('eshopCartError',$_SESSION['eshopcart'.$blog_id]['error']);
				unset($_SESSION['eshopcart'.$blog_id]['error']);
			}elseif(isset($_SESSION['eshopcart'.$blog_id]['enote'])){
				$msg=apply_filters('eshopCartNote',$_SESSION['eshopcart'.$blog_id]['enote']);
				unset($_SESSION['eshopcart'.$blog_id]['enote']);
			}else{
				$msg=apply_filters('eshopCartSuccess',__('<p>Added</p>','eshop'));
			}
		}
		echo $msg;
		die();
	}
}
?>