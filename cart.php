<?php
if ('cart.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');

if (!function_exists('eshop_cart')) {
	function eshop_cart($_POST){
		global $wpdb, $blog_id,$wp_query,$eshopoptions;
		$echo='';
		include "cart-functions.php";
		$error='';
		//cache
		eshop_cache();
	
		//delete the session, empties the cart
		if(isset($_POST['unset']) || (calculate_items()==0 && isset($_SESSION['eshopcart'.$blog_id]))){
			unset($_SESSION['eshopcart'.$blog_id]);
			unset($_SESSION['final_price'.$blog_id]);
			unset($_SESSION['items'.$blog_id]);
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
		if(isset($_POST['qty']) && !isset($_POST['save']) && (!is_numeric(trim($_POST['qty']))|| strlen($_POST['qty'])>3)){
			$qty=$_POST['qty']=1;
			$error='<p><strong class="error">'.__('Error: The quantity must contain numbers only, with a 999 maximum.','eshop').'</strong></p>';
		}
		if(isset($_POST['postid'])){
			$stkav=get_post_meta( $_POST['postid'], '_eshop_stock',true );
    		$eshop_product=get_post_meta( $_POST['postid'], '_eshop_product',true );
    	}
		if(isset($_POST['option']) && !isset($_POST['save'])){
			$edown=$getprice=$option=$_POST['option'];
			$qty=$_POST['qty'];
			$pclas=$_POST['pclas'];
			$productid=$pid=$_POST['pid'];
			$pname=$_POST['pname'];
			/* if download option then it must be free shipping */
			$postid=$wpdb->escape($_POST['postid']);
			$eshop_product=get_post_meta( $postid, '_eshop_product',true );

			$dlchk=$eshop_product['products'][$option]['download'];
			if($dlchk!='')	$pclas='F';
			$iprice= $eshop_product['products'][$option]['price'];
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
		if(isset($_POST['optset']))
			$optset='os'.implode('os',$_POST['optset']);
		else
			$optset='';
		if(!isset($pid)) $pid='';
		if(!isset($option)) $option='';
		if(!isset($postid)) $postid='';
		$identifier=$pid.$option.$postid.$optset;
		$needle=array(" ",".","-","_");
		$identifier=str_replace($needle,"",$identifier);
		
		if(isset($_SESSION['eshopcart'.$blog_id][$identifier])){
			$testqty=$_SESSION['eshopcart'.$blog_id][$identifier]['qty']+$qty;
			$eshopid=$_SESSION['eshopcart'.$blog_id][$identifier]['postid'];
			$eshop_product=get_post_meta( $postid, '_eshop_product',true );
			$stkqty = $eshop_product['qty'];
			//recheck stkqty
			$stocktable=$wpdb->prefix ."eshop_stock";
			$stktableqty=$wpdb->get_var("SELECT available FROM $stocktable where post_id=$eshopid");
			if(isset($stktableqty) && is_numeric($stktableqty)) $stkqty=$stktableqty;
			if(!ctype_digit(trim($testqty))|| strlen($testqty)>3){
				$error='<p><strong class="error">'.__('Error: The quantity must contain numbers only, with a 999 maximum.','eshop').'</strong></p>';
			}elseif('yes' == $eshopoptions['stock_control'] && ($stkav!='1' || $stkqty<$testqty)){
				$error='<p><strong class="error">'.__('Error: That quantity is not available for that product.','eshop').'</strong></p>';
			}else{
				$_SESSION['eshopcart'.$blog_id][$identifier]['qty']+=$qty;
			}

		}elseif($identifier!=''){
			$weight=0;
			$postid=$wpdb->escape($_POST['postid']);
			$eshop_product=get_post_meta( $postid, '_eshop_product',true );
			$item=$eshop_product['products'][$option]['option'];

			$_SESSION['eshopcart'.$blog_id][$identifier]['postid']=$postid;
			$testqty=$qty;
			$stkqty = $eshop_product['qty'];
			//recheck stkqty
			$stocktable=$wpdb->prefix ."eshop_stock";
			$stktableqty=$wpdb->get_var("SELECT available FROM $stocktable where post_id=$postid");
			if(isset($stktableqty) && is_numeric($stktableqty)) $stkqty=$stktableqty;
			if(!ctype_digit(trim($testqty))|| strlen($testqty)>3){
				$error='<p><strong class="error">'.__('Error: The quantity must contain numbers only, with a 999 maximum.','eshop').'</strong></p>';
			}elseif('yes' == $eshopoptions['stock_control'] && ($stkav!='1' || $stkqty<$testqty)){
				$error='<p><strong class="error">'.__('Error: That quantity is not available for that product.','eshop').'</strong></p>';
				$_SESSION['eshopcart'.$blog_id][$identifier]['qty']=$stkqty;
			}else{
				$_SESSION['eshopcart'.$blog_id][$identifier]['qty']=$qty;
			}
			
			$_SESSION['eshopcart'.$blog_id][$identifier]['item']=$item;
			$_SESSION['eshopcart'.$blog_id][$identifier]['option']=stripslashes($option);
			$_SESSION['eshopcart'.$blog_id][$identifier]['pclas']=stripslashes($pclas);
			$_SESSION['eshopcart'.$blog_id][$identifier]['pid']=$pid;
			$_SESSION['eshopcart'.$blog_id][$identifier]['pname']=stripslashes($pname);
			$_SESSION['eshopcart'.$blog_id][$identifier]['price']=$iprice;
			if(isset($_POST['optset'])){
				$_SESSION['eshopcart'.$blog_id][$identifier]['optset']=serialize($_POST['optset']);

				$oset=$qb=array();
				$optings=$_POST['optset'];
				$opttable=$wpdb->prefix.'eshop_option_sets';
				foreach($optings as $foo=>$opst){
					$qb[]="id=$opst";
				}
				$qbs = implode(" OR ", $qb);
				$otable=$wpdb->prefix.'eshop_option_sets';
				$orowres=$wpdb->get_results("select weight from $otable where $qbs ORDER BY id ASC");
				$x=0;
				foreach($orowres as $orow){
					$weight+=$orow->weight;
					$x++;
				}
				
			}
			//weights?
			if(isset($eshop_product['products'][$option]['weight']))
				$weight+=$eshop_product['products'][$option]['weight'];
			$_SESSION['eshopcart'.$blog_id][$identifier]['weight']=$weight;
			
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
		if(isset($_POST['save']) && $_POST['save']=='true' && isset($_SESSION['eshopcart'.$blog_id])){
			foreach ($_SESSION['eshopcart'.$blog_id] as $productid => $opt){
				$needle=array(" ",".");
				$sessproductid=str_replace($needle,"_",$productid);
				foreach ($_POST as $key => $value){
					if($key==$sessproductid){
						foreach ($value as $notused => $qty){
							if($qty=="0"){							
								unset($_SESSION['eshopcart'.$blog_id][$productid]);
							}else{
								$eshopid=$_SESSION['eshopcart'.$blog_id][$productid]['postid'];
								$eshop_product=get_post_meta( $postid, '_eshop_product',true );
								$stkqty = $eshop_product['qty'];
								//recheck stkqty
								$stocktable=$wpdb->prefix ."eshop_stock";
								$stktableqty=$wpdb->get_var("SELECT available FROM $stocktable where post_id=$eshopid");
    							if(isset($stktableqty) && is_numeric($stktableqty)) $stkqty=$stktableqty;
								if(!ctype_digit(trim($qty))|| strlen($qty)>3){
									$error='<p><strong class="error">'.__('Error: The quantity must contain numbers only, with a 999 maximum.','eshop').'</strong></p>';
								}elseif('yes' == $eshopoptions['stock_control'] &&  $stkqty<$qty){
									$error='<p><strong class="error">'.$qty.' - '.$stkqty.__('Error: That quantity is not available for that product.','eshop').'</strong></p>';
								}else{
									$_SESSION['eshopcart'.$blog_id][$productid]['qty'] =$qty;
								}
							}
						}
					}
				}
			}
			$_SESSION['final_price'.$blog_id] = calculate_price();
			//$_SESSION['items'.$blog_id] = calculate_items();
		}
		//any errors will print here.
		if($error!='') $echo.= $error;
		if(sizeof(isset($_SESSION['eshopcart'.$blog_id]) && $_SESSION['eshopcart'.$blog_id])=='0'){
			unset($_SESSION['eshopcart'.$blog_id]);
		}
		if(isset($_SESSION['eshopcart'.$blog_id])){
			if((isset($wp_query->query_vars['eshopaction']) && urldecode($wp_query->query_vars['eshopaction'])=='cancel') && !isset($_POST['save'])){
				$echo.= "<h3>".__('The order was cancelled at','eshop')." ".$eshopoptions['method'].".</h3>"; 
				$echo.= '<p>'.__('We have not deleted the contents of your shopping cart in case you may want to edit its content.','eshop').'</p>';
			}
			if($eshopoptions['shop_page']!=''){
				$return=get_permalink($eshopoptions['shop_page']);
			}else{
				$return=esc_attr( stripslashes( wp_get_referer() ) );
			}
			$echo.= display_cart($_SESSION['eshopcart'.$blog_id],'true', $eshopoptions['checkout']);
			$echo.='<ul class="continue-proceed"><li><a href="'.$return.'">'.__('&laquo; Continue Shopping','eshop').'</a></li><li><a href="'.get_permalink($eshopoptions['checkout']).'">'.__('Proceed to Checkout &raquo;','eshop').'</a></li></ul>';
		}else{
			//can be altered as desired.
			$echo.= '<p><strong class="error">'.__('Your shopping cart is currently empty.','eshop').'</strong></p>';
		}
		return $echo;
	}
}
?>