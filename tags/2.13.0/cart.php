<?php
if ('cart.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');

if (!function_exists('eshop_cart')) {
	function eshop_cart($_POST){
		global $wpdb, $blog_id;

		$echo='';
		include "cart-functions.php";
		$error='';
		//delete the session, empties the cart
		if(isset($_POST['unset']) || (calculate_items()==0 && isset($_SESSION['shopcart'.$blog_id]))){
			$_SESSION = array();
			//session_destroy();
			$_POST['save']='false';
		}
		//on windows this check isn't working correctly, so I've added ==0 
		if (get_magic_quotes_gpc()) {
			$_COOKIE = stripslashes_array($_COOKIE);
			$_FILES = stripslashes_array($_FILES);
			$_GET = stripslashes_array($_GET);
			$_POST = stripslashes_array($_POST);
			$_REQUEST = stripslashes_array($_REQUEST);
		}
		$_POST=sanitise_array($_POST);
		//if adding a product to the cart
		if(isset($_POST['qty']) && !isset($_POST['save']) && (!ctype_digit(trim($_POST['qty']))|| strlen($_POST['qty'])>3)){
			$qty=$_POST['qty']=1;
			$error='<p><strong class="error">'.__('Error: The quantity must contain numbers only, with a 999 maximum.','eshop').'</strong></p>';
		}
		if(isset($_POST['option']) && !isset($_POST['save'])){
			$option='_'.$_POST['option'];
			$qty=$_POST['qty'];
			$pclas=$_POST['pclas'];
			$productid=$pid=$_POST['pid'];
			$pname=$_POST['pname'];
			$getprice='_Price '.ltrim($option,'_Option ');
			/* if download option then it must be free shipping */
			$postid=$wpdb->escape($_POST['postid']);
			$edown='_Download '.ltrim($option,'_Option ');
			$dlchk=get_post_meta($postid,$edown, true);
			if($dlchk!='')	$pclas='F';
			//////////////////////////////
			$table=$wpdb->prefix.'postmeta';
			$iprice= $wpdb->get_var("SELECT meta_value FROM $table WHERE meta_key='$getprice' AND post_id='$postid'");
			if($iprice==''){
				$error='<p><strong class="error">'.__('Error: That product is currently not available.','eshop').'</strong></p>';
				$option=$_POST['option']='';
				$qty=$_POST['qty']='';
				$pclas=$_POST['pclas']='';
				$productid=$pid=$_POST['pid']='';
				$pname=$_POST['pname']='';
				$iprice='';
			}
		}


		//unique identifier
		if(!isset($pid)) $pid='';
		if(!isset($option)) $option='';
		if(!isset($postid)) $postid='';
		$identifier=$pid.$option.$postid;
		$needle=array(" ",".","-","_");
		$identifier=str_replace($needle,"",$identifier);
		
		if(isset($_SESSION['shopcart'.$blog_id][$identifier])){
			$_SESSION['shopcart'.$blog_id][$identifier]['qty']+=$qty;

		}elseif($identifier!=''){
			$postid=$wpdb->escape($_POST['postid']);
			$table=$wpdb->prefix.'postmeta';
			$item= $wpdb->get_var("SELECT meta_value FROM $table WHERE meta_key='$option' AND post_id='$postid'");
			$_SESSION['shopcart'.$blog_id][$identifier]['item']=$item;
			$_SESSION['shopcart'.$blog_id][$identifier]['option']=stripslashes($option);
			$_SESSION['shopcart'.$blog_id][$identifier]['qty']=$qty;
			$_SESSION['shopcart'.$blog_id][$identifier]['pclas']=stripslashes($pclas);
			$_SESSION['shopcart'.$blog_id][$identifier]['pid']=$pid;
			$_SESSION['shopcart'.$blog_id][$identifier]['pname']=stripslashes($pname);
			$_SESSION['shopcart'.$blog_id][$identifier]['price']=$iprice;
			$_SESSION['shopcart'.$blog_id][$identifier]['postid']=$postid;
		}
		//save? not sure why I used that, but its working so why make trouble for myself.
		if(isset($_POST['save'])){
			$save=$_POST['save'];
		}
		//this bit is possibly not required
		if(isset($productid)){
			//new item selected ******* may need checking
			$_SESSION['final_price'.$blog_id] = calculate_price();
			$_SESSION['items'.$blog_id] = calculate_items();
		}
		
		//update products in the cart
		if(isset($_POST['save']) && $_POST['save']=='true'){
			foreach ($_SESSION['shopcart'.$blog_id] as $productid => $opt){
				$needle=array(" ",".");
				$sessproductid=str_replace($needle,"_",$productid);
				foreach ($_POST as $key => $value){
					if($key==$sessproductid){
						foreach ($value as $notused => $qty){
							if($qty=="0"){
								unset($_SESSION['shopcart'.$blog_id][$productid]);
							}else{
								if(!ctype_digit(trim($qty))|| strlen($qty)>3){
									$error='<p><strong class="error">'.__('Error: The quantity must contain numbers only, with a 999 maximum.','eshop').'</strong></p>';
								}else{
									$_SESSION['shopcart'.$blog_id][$productid]['qty'] =$qty;
								}
							}
						}
					}
				}
			}
			$_SESSION['final_price'.$blog_id] = calculate_price();
			$_SESSION['items'.$blog_id] = calculate_items();
		}
		//any errors will print here.
		if($error!='') $echo.= $error;

		if(isset($_SESSION['shopcart'.$blog_id])){
			if((isset($_GET['action']) && $_GET['action']=='cancel') && !isset($_POST['save'])){
				$echo.= "<h3>".__('The order was cancelled at','eshop')." ".get_option('eshop_method').".</h3>"; 
				$echo.= '<p>'.__('We have not deleted the contents of your shopping cart in case you may want to edit its content.','eshop').'</p>';
			}
			if(isset($_POST['purl'])){
				$return=wp_specialchars($_POST['purl']);
			}else{
				$return=get_option('siteurl');
			}
			$echo.= display_cart($_SESSION['shopcart'.$blog_id],'true', get_option('eshop_checkout'));
			$echo.='<ul class="continue-proceed"><li><a href="'.$return.'">'.__('&laquo; Continue Shopping','eshop').'</a></li><li><a href="'.get_permalink(get_option('eshop_checkout')).'">'.__('Proceed to Checkout &raquo;','eshop').'</a></li></ul>';
		}else{
			//can be altered as desired.
			$echo.= '<p><strong class="error">'.__('Your shopping cart is currently empty.','eshop').'</strong></p>';
		}
		return $echo;
	}
}
?>