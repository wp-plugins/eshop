<?php
if ('cart-functions.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');

if (!function_exists('display_cart')) {
	function display_cart($shopcart, $change, $eshopcheckout,$pzone='',$shiparray=''){
		//The cart display.
		global $wpdb, $blog_id,$eshopoptions;
		if($pzone=='widget'){
			$pzone='';
			$iswidget='w';
		}else{
			$iswidget='';
		}
		$echo ='';
		$check=0;
		$sub_total=0;
		$tempshiparray=array();
		//this checks for an empty cart, may not be required but leaving in just in case.
		$eshopcartarray=$_SESSION['eshopcart'.$blog_id];
		foreach ($eshopcartarray as $productid => $opt){
			if(is_array($opt)){
				foreach($opt as $qty){
					$check=$check+$qty;
				}
			}
		}
		//therefore if cart exists and has products
		if($check > 0){
			//global $final_price, $sub_total;
			// no fieldset/legend added - do we need it?
			if ($change == 'true'){
				$echo.= '<form action="'.get_permalink($eshopoptions['cart']).'" method="post" class="eshop eshopcart">';
			}
			$echo.= '<table class="eshop cart" summary="'.__('Shopping cart contents overview','eshop').'">
			<caption>'.__('Shopping Cart','eshop').'</caption>
			<thead>
			<tr class="thead">';
			$echo .='<th id="cartItem'.$iswidget.'" class="nb">'.__('Item Description','eshop').'</th>
			<th id="cartQty'.$iswidget.'" class="bt">'.__('<dfn title="Quantity">Qty</dfn>','eshop').'</th>
			<th id="cartTotal'.$iswidget.'" class="btbr">'.__('Total','eshop').'</th>';
			if($iswidget=='' && $change == 'true'){
				$eshopdeleteheaderimage=apply_filters('eshop_delete_header_image',WP_PLUGIN_URL.'/eshop/no.png');
				$echo.= '<th id="cartDelete" class="btbr"><img src="'.$eshopdeleteheaderimage.'" alt="'.__('Delete','eshop').'" title="'.__('Delete','eshop').'" /></th>';
			}
			$echo .= '</tr></thead><tbody>';
			//display each item as a table row
			$calt=0;
			$shipping=0;
			$totalweight=0;
			$currsymbol=$eshopoptions['currency_symbol'];
			$eshopcartarray=$_SESSION['eshopcart'.$blog_id];
			foreach ($eshopcartarray as $productid => $opt){
				$addoprice=0;
				if(is_array($opt)){
					$key=$opt['option'];
					$calt++;
					$alt = ($calt % 2) ? '' : ' class="alt"';
					$echo.= "\n<tr".$alt.">";
					//do the math for weight
					$eshop_product=get_post_meta( $opt['postid'], '_eshop_product',true );
					$eimg='';
					/* test image insertion */
					if(is_numeric($eshopoptions['image_in_cart'])){
						$imgsize=$eshopoptions['image_in_cart'];
						$w=get_option('thumbnail_size_w');
						$h=get_option('thumbnail_size_h');
						if($imgsize!=''){
							$w=round(($w*$imgsize)/100);
							$h=round(($h*$imgsize)/100);
						}
						if (has_post_thumbnail( $opt['postid'] ) ) {
							$eimg='<a class="itemref" href="'.get_permalink($opt['postid']).'">'.get_the_post_thumbnail( $opt['postid'], array($w, $h)).'</a>'."\n";
						}else{
							$eimage=eshop_files_directory();
							$eshopnoimage=apply_filters('eshop_no_image',$eimage['1'].'noimage.png');
							$eimg='<a class="itemref" href="'.get_permalink($opt['postid']).'"><img src="'.$eshopnoimage.'" height="'.$h.'" width="'.$w.'" alt="" /></a>'."\n";
						}
					}
					/* end */
					//opsets
					
					if(isset($opt['optset'])){
						$oset=$qb=array();
						$optings=unserialize($opt['optset']);
						$c=0;
						if(isset($newoptings)) unset($newoptings);
						foreach($optings as $foo=>$opst){
							$qb[]="id=$opst[id]";
							if((isset($opst['text']) && $opst['text']!='') || !isset($opst['text'])){
								$newoptings[]=$optings[$c];
							}
							$c++;
						}
						if(isset($newoptings)){
							$qbs = implode(" OR ", $qb);
							$otable=$wpdb->prefix.'eshop_option_sets';
							$otablename=$wpdb->prefix.'eshop_option_names';
							$orowres=$wpdb->get_results("select o.name, o.price, o.id, t.type from $otable as o, $otablename as t where ($qbs) && o.optid=t.optid ORDER BY id ASC");
							$x=0;
							foreach($orowres as $orow){
								//if(($orow->type=='2' || $orow->type=='3') && isset($newoptings[$x]['text']))
								if((isset($newoptings[$x]['type']) && ($newoptings[$x]['type']=='2' || $newoptings[$x]['type']=='3')) && isset($newoptings[$x]['text']))
									$oset[]=$orow->name.": \n".'<span class="eshoptext">'.stripslashes($newoptings[$x]['text']).'</span>';
								elseif(($orow->type=='2' || $orow->type=='3') && !isset($newoptings[$x]['text']))
									$xxxx='';
								else
									$oset[]=$orow->name;
								$addoprice=$addoprice+$orow->price;
								$x++;
							}
							$optset="\n".implode("\n",$oset);
						}else{
							$optset='';
						}
					}else{
						$optset='';
					}
					//$eshop_product['products'][$opt['item']]['option']
					$echo.= '<td id="prod'.$calt.$iswidget.'" headers="cartItem" class="leftb cartitem">'.$eimg.'<a href="'.get_permalink($opt['postid']).'">'.stripslashes($opt["pname"]).' <span class="eshopidetails">('.$opt['pid'].' : '.stripslashes($opt['item']).')</span></a>'.nl2br($optset).'</td>'."\n";
					$echo.= "<td class=\"cqty lb\" headers=\"cartQty prod".$calt.$iswidget."\">";
					// if we allow changes, quantities are in text boxes
					if ($change == true){
						//generate acceptable id
						//$toreplace=array(" ","-","$","\r","\r\n","\n","\\","&","#",";");
						$accid=$productid.$key;
						$accid='c'.md5($accid);//str_replace($toreplace, "", $accid);
						$echo.= '<label for="'.$accid.$iswidget.'"><input class="short" type="text" id="'.$accid.$iswidget.'" name="'.$productid.'['.$key.']" value="'.$opt["qty"].'" size="3" maxlength="3" /></label>';
					}else{
						$echo.= $opt["qty"];
					}
					/* DISCOUNT */
					$opt["price"]+=$addoprice;
					if(is_discountable(calculate_total())>0){
						$discount=is_discountable(calculate_total())/100;
						$disc_line= round($opt["price"]-($opt["price"] * $discount), 2);
					}
					$line_total=$opt["price"]*$opt["qty"];
					$echo.= "</td>\n<td headers=\"cartTotal prod".$calt.$iswidget."\" class=\"amts\">".sprintf( __('%1$s%2$s','eshop'), $currsymbol, number_format_i18n($line_total,2))."</td>\n";
					if($iswidget=='' && $change == 'true'){
						$eshopdeleteimage=apply_filters('eshop_delete_image',WP_PLUGIN_URL.'/eshop/no.png');
						$echo .='<td headers="cartDelete" class="deletecartitem"><label for="delete'.$productid.$iswidget.'" class="hide">'.__('Delete this item','eshop').'</label><input type="image" src="'.$eshopdeleteimage.'" id="delete'.$productid.$iswidget.'" name="deleteitem['.$productid.']" value="'.$key.'" title="'.__('Delete this item','eshop').'"/></td>';
					}
					$echo .="</tr>\n";
					if(isset($disc_line))
						$sub_total+=$disc_line*$opt["qty"];
					else		
						$sub_total+=$line_total;
					//weight
					if(isset($opt['weight']))
						$totalweight+=$opt['weight']*$opt['qty'];
				}
			}
			// display subtotal row - total for products only
			$disc_applied='';
			if(is_discountable(calculate_total())>0){
				$discount=is_discountable(calculate_total());
				$disc_applied='<small>('.sprintf(__('Including Discount of <span>%s%%</span>','eshop'),number_format_i18n(round($discount, 2),2)).')</small>';
			}
			if($iswidget==''  && $change == 'true')
				$emptycell='<td headers="cartDelete" class="eshopempty"></td>';
			else
				$emptycell='';
			$echo.= "<tr class=\"stotal\"><th id=\"subtotal$iswidget\" class=\"leftb\">".__('Sub-Total','eshop').' '.$disc_applied."</th><td headers=\"subtotal$iswidget cartTotal\" class=\"amts lb\" colspan=\"2\">".sprintf( __('%1$s%2$s','eshop'), $currsymbol, number_format($sub_total,2))."</td>$emptycell</tr>\n";
			$final_price=$sub_total;
			$_SESSION['final_price'.$blog_id]=$final_price;
			// SHIPPING PRICE HERE
			$shipping=0;
			//$pzone will only be set after the checkout address fields have been filled in
			// we can only work out shipping after that point
			if($pzone!=''){
				//shipping for cart.
				if($eshopoptions['shipping_zone']=='country'){
					$table=$wpdb->prefix.'eshop_countries';
				}else{
					$table=$wpdb->prefix.'eshop_states';
				}
				$table2=$wpdb->prefix.'eshop_shipping_rates';
				switch($eshopoptions['shipping']){
					case '1'://( per quantity of 1, prices reduced for additional items )
						foreach ($shiparray as $nowt => $shipclass){
							//add to temp array for shipping
							if(!in_array($shipclass, $tempshiparray)) {
								if($shipclass!='F'){
									array_push($tempshiparray, $shipclass);
									$shipzone='zone'.$pzone;
									$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='$shipclass' and items='1' limit 1");
									$shipping+=$shipcost;
								}
							}else{
								if($shipclass!='F'){
									$shipzone='zone'.$pzone;
									$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='$shipclass'  and items='2' limit 1");
									$shipping+=$shipcost;
								}
							}
						}
						break;
					case '2'://( once per shipping class no matter what quantity is ordered )
						foreach ($shiparray as $nowt => $shipclass){
							if(!in_array($shipclass, $tempshiparray)) {
								array_push($tempshiparray, $shipclass);
								if($shipclass!='F'){
									$shipzone='zone'.$pzone;
									$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='$shipclass' and items='1' limit 1");
									$shipping+=$shipcost;
								}
							}
						}
						break;
					case '3'://( one overall charge no matter how many are ordered )
						$shiparray=array_unique($shiparray);
						foreach ($shiparray as $nowt => $shipclass){
							if($shipclass!='F'){
								$shipzone='zone'.$pzone;						
								$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='A' and items='1' limit 1");
								$shipping+=$shipcost;
							}
						}
						break;
					case '4'://by weight/zone etc
						//$totalweight
						$shipzone='zone'.$pzone;
						$shipcost=$wpdb->get_var("SELECT $shipzone FROM $table2 where weight<='$totalweight' && ship_type='$shiparray' order by weight DESC limit 1");
						$shipping+=$shipcost;
						$_SESSION['eshopshiptype'.$blog_id]=$shiparray;
				}

				//display shipping cost
				//discount shipping?
				if(is_shipfree(calculate_total())  || eshop_only_downloads()) $shipping=0;
				
				$echo.= '<tr class="alt"><th headers="cartItem" id="scharge" class="leftb">';
				if($eshopoptions['shipping']=='4' && !eshop_only_downloads()){
					$typearr=explode("\n", $eshopoptions['ship_types']);
					//darn, had to add in unique to be able to go back a page
					$echo.=' <a href="'.get_permalink($eshopoptions['checkout']).'?eshoprand='.rand(2,100).'#shiplegend" title="'.__('Change Shipping','eshop').'">'.stripslashes(esc_attr($typearr[$shiparray-1])).'</a>';
				}
				$echo .=__('Shipping','eshop');
				if($eshopoptions['cart_shipping']!=''){
					$ptitle=get_post($eshopoptions['cart_shipping']);
					$echo.=' <small>(<a href="'.get_permalink($eshopoptions['cart_shipping']).'">'.__($ptitle->post_title,'eshop').'</a>)</small>';
				}
	
				$echo.='</th>
				<td headers="cartItem scharge" class="amts lb" colspan="2">'.sprintf( __('%1$s%2$s','eshop'), $currsymbol, number_format_i18n($shipping,2)).'</td>
				</tr>';
				$_SESSION['shipping'.$blog_id]=$shipping;
				$final_price=$sub_total+$shipping;
				$_SESSION['final_price'.$blog_id]=$final_price;
				$echo.= '<tr class="total"><th id="cTotal" class="leftb">'.__('Total Order Charges','eshop')."</th>\n<td headers=\"cTotal cartTotal\"  colspan=\"2\" class = \"amts lb\"><strong>".sprintf( __('%1$s%2$s','eshop'), $currsymbol, number_format_i18n($final_price, 2))."</strong></td></tr>";
			}

			$echo.= "</tbody></table>\n";
			// display unset/update buttons
			if($change == true){
				$echo.= "<div class=\"cartopt\"><input type=\"hidden\" name=\"save\" value=\"true\" />\n<input type=\"hidden\" name=\"eshopnon\" value=\"set\" />\n"; 
				$echo .= wp_nonce_field('eshop_add_product_cart','_wpnonce',true,false);
				$echo.= "<p><label for=\"update\"><input type=\"submit\" class=\"button\" id=\"update\" name=\"update\" value=\"".__('Update Cart','eshop')."\" /></label>";
				$echo.= "<label for=\"unset\"><input type=\"submit\" class=\"button\" id=\"unset\" name=\"unset\" value=\"".__('Empty Cart','eshop')."\" /></label></p>\n";
				$echo.= "</div>\n";
			}
			if ($change == 'true'){
				$echo.= "</form>\n";
			}
		}else{
			//if cart is empty - display a message - this is only a double check and should never be hit
			$echo.= "<p class=\"error\">".__('Your shopping cart is currently empty.','eshop')."</p>\n";
		}
		if($eshopoptions['status']!='live'){
			$echo ="<p class=\"testing\"><strong>".__('Test Mode &#8212; No money will be collected.','eshop')."</strong></p>\n".$echo;
		}
		if(isset($_SESSION['eshop_discount'.$blog_id]) && valid_eshop_discount_code($_SESSION['eshop_discount'.$blog_id])){
			$echo .= '<p class="eshop_dcode">'.sprintf(__('Discount Code <span>%s</span> has been applied to your cart.','eshop'),$_SESSION['eshop_discount'.$blog_id]).'</p>'."\n";
		}
		//test
		if(isset($totalweight))
			$_SESSION['eshop_totalweight'.$blog_id]['totalweight']=$totalweight;
		return $echo;
	}
}
if (!function_exists('calculate_price')) {
	function calculate_price(){
		global $blog_id;
		if(isset($_SESSION['eshopcart'.$blog_id])){
			$thecart=$_SESSION['eshopcart'.$blog_id];
			// sum total price for all items in shopping shopcart
			$price = 0.0;

			if(is_array($thecart)){
				foreach ($thecart as $productid => $opt){
					$price=$price+$opt['price'];
				}
			}
			return number_format($price, 2);
		}
		return '0';
	}
}
if (!function_exists('calculate_total')) {
	function calculate_total(){
		global $blog_id;
		$thecart=$_SESSION['eshopcart'.$blog_id];
		// sum total price for all items in shopping shopcart
		$price = 0;
		if(is_array($thecart)){
			foreach ($thecart as $productid => $opt){
				$price=$price+($opt['price']*$opt['qty']);
			}
		}
		return $price;
	}
}
if (!function_exists('calculate_items')) {
	function calculate_items(){
		global $blog_id;
		if(isset($_SESSION['eshopcart'.$blog_id])){
			$thecart=$_SESSION['eshopcart'.$blog_id];
			// sum total items in shopping shopcart
			$items = 0;
			if(is_array($thecart))	{
				foreach ($thecart as $productid => $opt){
					if(is_array($opt)){
						foreach($opt as $option=>$qty){
							$items = $items+$qty;
						}
					}
				}
			}
			return $items;
		}
		return;
	}
}
if (!function_exists('is_discountable')) {
	function is_discountable($total){
		global $blog_id,$eshopoptions;
		$percent=0;
		//check for 
		if(isset($_SESSION['eshop_discount'.$blog_id]) && eshop_discount_codes_check()){
			$chkcode=valid_eshop_discount_code($_SESSION['eshop_discount'.$blog_id]);
			if($chkcode && apply_eshop_discount_code('discount')>0)
				return apply_eshop_discount_code('discount');
		}
		for ($x=1;$x<=3;$x++){
			if($eshopoptions['discount_spend'.$x]!='')
				$edisc[$eshopoptions['discount_spend'.$x]]=$eshopoptions['discount_value'.$x];
		}
		if(isset($edisc) && is_array($edisc)){
			krsort($edisc);
			foreach ($edisc as $amt => $percent) {
				if($amt <= $total)
					return $percent;	
			}
			$percent=0;
		}
		return $percent;
	}
}

if (!function_exists('is_shipfree')) {
	function is_shipfree($total){
		global $blog_id,$eshopoptions;
		if(isset($_SESSION['eshop_discount'.$blog_id]) && eshop_discount_codes_check()){
			$chkcode=valid_eshop_discount_code($_SESSION['eshop_discount'.$blog_id]);
			if($chkcode && apply_eshop_discount_code('shipping'))
				return true;
		}
		$amt=$eshopoptions['discount_shipping'];
		if($amt!='' && $amt <= $total)
			return true;
		
		return false;

	}
}

// discount/promotional codes
if (!function_exists('apply_eshop_discount_code')) {
	function apply_eshop_discount_code($disc){
		global $wpdb, $blog_id;
		$now=date('Y-m-d');
		$disctable=$wpdb->prefix.'eshop_discount_codes';
		if(eshop_discount_codes_check()){
			$chkcode=valid_eshop_discount_code($_SESSION['eshop_discount'.$blog_id]);
			if(!$chkcode)
				return false;
			$grabthis=$wpdb->escape($_SESSION['eshop_discount'.$blog_id]);
			$row = $wpdb->get_row("SELECT * FROM $disctable WHERE id > 0 && live='yes' && disccode='$grabthis'");
			if($disc=='shipping'){
				switch($row->dtype){
					case '4':
						if($row->remain=='' || $row->remain>0) return true;
						break;
					case '5':
						if($row->enddate>=$now) return true;
						break;
					case '6':
						if(($row->remain=='' || $row->remain>0) && ($row->enddate>=$now)) return true;
						break;
					default:
						return false;
				}
			}

			if($disc=='discount'){
				switch($row->dtype){
					case '1':
						if($row->remain=='' || $row->remain>0) 
							return $row->percent;
						break;
					case '2':
						if($row->enddate>=$now) 
							return $row->percent;
						break;
					case '3':
						if(($row->remain=='' || $row->remain>0) && ($row->enddate>=$now))
							return $row->percent;
						break;
					default:
						return false;
				}
			}
		}
		//and just in case
		return false;

	}
}
if (!function_exists('eshop_discount_codes_check')) {
	function eshop_discount_codes_check(){
		global $wpdb;
		$disctable=$wpdb->prefix.'eshop_discount_codes';
		$max = $wpdb->get_var("SELECT COUNT(id) FROM $disctable WHERE id > 0 && live='yes'");
		if($max>0)
			return true;
		return false;
	}
}
if (!function_exists('valid_eshop_discount_code')) {
	function valid_eshop_discount_code($code){
		global $wpdb;
		$now=date('Y-m-d');
		$code=$wpdb->escape($code);
		$disctable=$wpdb->prefix.'eshop_discount_codes';
		$row = $wpdb->get_row("SELECT * FROM $disctable WHERE id > 0 && live='yes' && binary disccode='$code'");

		switch ($row->dtype){
			case '1':
				if($row->remain=='' || $row->remain>0) 
					return true;
				break;
			case '2':
				if($row->enddate>=$now) 
					return true;
				break;
			case '3':
				if(($row->remain=='' || $row->remain>0) && ($row->enddate>=$now))
					return true;
				break;
			case '4':
				if($row->remain=='' || $row->remain>0) return true;
				break;
			case '5':
				if($row->enddate>=$now) return true;
				break;
			case '6':
				if(($row->remain=='' || $row->remain>0) && ($row->enddate>=$now)) return true;
				break;
			default:
				return false;
		}
		return false;
	}
}

if (!function_exists('checkAlpha')) {
	//check string is alpha only.
	function checkAlpha($text){
		if(trim($text)!='')
			return true;
		else
			return false;
		//was:
		 //return preg_match ("/[A-z-]/", $text);
	}
}
if (!function_exists('checkEmail')) {
	//correctly formed email address?
	function checkEmail($email) {
	  $pattern = "/^[A-z0-9\._-]+"
			 . "@"
			 . "[A-z0-9][A-z0-9-]*"
			 . "(\.[A-z0-9_-]+)*"
			 . "\.([A-z]{2,6})$/";
	 return preg_match ($pattern, $email);
	}
}
if (!function_exists('checkAlphaNum')) {
	//check string is alphanumeric only
	function checkAlphaNum($text){
		 return preg_match ("/^[A-z0-9\._-]/", $text);
	}
}
if (!function_exists('checkPhone')) {
	//check phone number - needs work!
	function checkPhone($text){
		return preg_match ("/[A-z0-9\(\)]/", $text);
	}
}
if (!function_exists('orderhandle')) {
	function orderhandle($_POST,$checkid){
		//This function puts the order into the db.
		global $wpdb, $blog_id,$eshopoptions;

		if (!is_user_logged_in() && isset($eshopoptions['users']) && $eshopoptions['users']=='yes' && isset($_SESSION['eshop_user'.$blog_id])) {
			//set up blank user if in case anything goes phooey
			$user_id=0;
			require_once ( ABSPATH . WPINC . '/registration.php' );
			//auto create a new user if they don't exist - only works if not logged in ;)
			$user_email=$_POST['email'];
			$utable=$wpdb->prefix ."users";
			$names=str_replace(" ","",$_POST['first_name'].$_POST['last_name']);
			$username = strtolower($names);
			$eshopch = $wpdb->get_results("SHOW TABLE STATUS LIKE '$utable'");

			//a unique'ish number
			$altusername=strtolower($names.$eshopch[0]->Auto_increment);
			if(!email_exists($user_email)){
				if(username_exists($username))
					$username=$altusername;

				if(!username_exists($username)){
					$random_password = wp_generate_password( 12, false );
					$user_id = wp_create_user( $username, $random_password, $user_email );
					$eshopuser['company']=$_POST['company'];
					$eshopuser['phone']=$_POST['phone'];
					$eshopuser['address1']=$_POST['address1'];
					$eshopuser['address2']=$_POST['address2'];
					$eshopuser['city']=$_POST['city'];
					$eshopuser['country']=$_POST['country'];
					$eshopuser['state']=$_POST['state'];
					$eshopuser['zip']=$_POST['zip'];
					if(isset($_POST['altstate']) && $_POST['altstate']!='')
						$eshopuser['altstate']=$_POST['altstate'];
					if(!is_numeric($_POST['state'])){
						$statechk=$wpdb->escape($_POST['state']);
						$sttable=$wpdb->prefix.'eshop_states';
						$eshopuser['state']=$wpdb->get_var("SELECT id FROM $sttable where code='$statechk' limit 1");
					}else{
						$eshopuser['state']=$_POST['state'];
					}
					update_user_meta( $user_id, 'eshop', $eshopuser );
					update_user_meta( $user_id, 'first_name', $_POST['first_name'] );
					update_user_meta( $user_id, 'last_name',$_POST['last_name'] );
					update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.
					wp_new_user_notification($user_id, $random_password);
				}
			}
		}else{
			global $current_user;
			 get_currentuserinfo();
			$user_id=$current_user->ID;
		}
		if(!isset($eshopoptions['users'])) $user_id='0';
		
	
		//$wpdb->show_errors();
		if (get_magic_quotes_gpc()) {
			$_POST=stripslashes_array($_POST);
		}
		$custom_field=$wpdb->escape($_POST['custom']);
		$first_name=$wpdb->escape($_POST['first_name']);
		$last_name=$wpdb->escape($_POST['last_name']);
		$email=$wpdb->escape($_POST['email']);
		//set up some defaults
		$phone=$company=$address1=$address2=$city=$zip=$state=$country=$paidvia='';
		if(isset($_POST['phone']))
			$phone=$wpdb->escape($_POST['phone']);
		if(isset($_POST['company']))
			$company=$wpdb->escape($_POST['company']);
		if(isset($_POST['address1']))
			$address1=$wpdb->escape($_POST['address1']);
		if(isset($_POST['address2']))
			$address2=$wpdb->escape($_POST['address2']);
		if(isset($_POST['city']))
			$city=$wpdb->escape($_POST['city']);
		if(isset($_POST['zip']))
			$zip=$wpdb->escape($_POST['zip']);
		if(isset($_POST['state']))
			$state=$wpdb->escape($_POST['state']);
		if(isset($_POST['country']))
			$country=$wpdb->escape($_POST['country']);
		$paidvia=$wpdb->escape($_SESSION['eshop_payment'.$blog_id]);

		if(isset($_POST['state']) && $_POST['state']=='' && isset($_POST['altstate']) && $_POST['altstate']!='')
			$state=$wpdb->escape($_POST['altstate']);

		if(isset($_POST['ship_name'])){
			$ship_name=$wpdb->escape($_POST['ship_name']);
		}else{
			$ship_name=$first_name.' '.$last_name;
		}
		if(isset($_POST['ship_phone'])){
			$ship_phone=$wpdb->escape($_POST['ship_phone']);
		}else{
			$ship_phone=$phone;
		}
		if(isset($_POST['ship_company'])){
			$ship_company=$wpdb->escape($_POST['ship_company']);
		}else{
			$ship_company=$company;
		}
		if(isset($_POST['ship_address'])){
			$ship_address=$wpdb->escape($_POST['ship_address']);
		}else{
			$ship_address=$address1.' '.$address2;
		}
		if(isset($_POST['ship_city'])){
			$ship_city=$wpdb->escape($_POST['ship_city']);
		}else{
			$ship_city=$city;
		}
		if(isset($_POST['ship_postcode'])){
			$ship_postcode=$wpdb->escape($_POST['ship_postcode']);
		}else{
			$ship_postcode=$zip;
		}
		if(isset($_POST['ship_country'])){
			$ship_country=$wpdb->escape($_POST['ship_country']);
		}else{
			$ship_country=$country;
		}
		if(isset($_POST['ship_state'])){
			$ship_state=$wpdb->escape($_POST['ship_state']);
		}else{
			$ship_state=$state;
		}
		
		if(empty($_POST['ship_state']) && !empty($_POST['ship_altstate']))
			$ship_state=$wpdb->escape($_POST['ship_altstate']);
		if(isset($_POST['reference'])){
			$reference=$wpdb->escape($_POST['reference']);
		}else{
			$reference='';
		}
		if(isset($_POST['comments'])){
			$comments=$wpdb->escape($_POST['comments']);
		}else{
			$comments='';
		}
		if(isset($_POST['affiliate']))
			$affiliate=$wpdb->escape($_POST['affiliate']);
		else
			$affiliate='';
		$detailstable=$wpdb->prefix.'eshop_orders';
		$itemstable=$wpdb->prefix.'eshop_order_items';
		$processing=__('Processing&#8230;','eshop');
		//readjust state if needed
		$sttable=$wpdb->prefix.'eshop_states';
		$getstate=$eshopoptions['shipping_state'];
		if($eshopoptions['show_allstates'] != '1'){
			$stateList=$wpdb->get_results("SELECT id,code,stateName FROM $sttable WHERE list='$getstate' ORDER BY stateName",ARRAY_A);
		}else{
			$stateList=$wpdb->get_results("SELECT id,code,stateName,list FROM $sttable ORDER BY list,stateName",ARRAY_A);
		}
		foreach($stateList as $code => $value){
			$eshopstatelist[$value['code']]=$value['id'];
		}
		if(isset($eshopstatelist[$state]))	$state=$eshopstatelist[$state];
		if(isset($eshopstatelist[$ship_state]))	$ship_state=$eshopstatelist[$ship_state];
//if (!is_user_logged_in()) {
		$eshopching=$wpdb->get_var("SELECT checkid from $detailstable where checkid='$checkid' limit 1");
		if($eshopching!=$checkid){
			$query1=$wpdb->query("INSERT INTO $detailstable
				(checkid, first_name, last_name,company,email,phone, address1, address2, city,
				state, zip, country, reference, ship_name,ship_company,ship_phone, 
				ship_address, ship_city, ship_postcode,	ship_state, ship_country, 
				custom_field,transid,edited,comments,thememo,paidvia,affiliate,user_id,admin_note,user_notes)VALUES(
				'$checkid',
				'$first_name',
				'$last_name',
				'$company',
				'$email',
				'$phone',
				'$address1',
				'$address2',
				'$city',
				'$state',
				'$zip',
				'$country',
				'$reference',
				'$ship_name',
				'$ship_company',
				'$ship_phone',
				'$ship_address',
				'$ship_city',
				'$ship_postcode',
				'$ship_state',
				'$ship_country',
				'$custom_field',
				'$processing',
				NOW(),
				'$comments',
				'',
				'$paidvia',
				'$affiliate',
				'$user_id',
				'',''
					);");
					
			do_action('eshoporderhandle',$_POST,$checkid);
					
			$i=1;
			//this is here to generate just one code per order
			$code=eshop_random_code(); 
			while($i<=$_POST['numberofproducts']){
				//test
				$addoprice=0;
				$chk_id='item_number_'.$i;
				$chk_qty='quantity_'.$i;
				$chk_amt='amount_'.$i;
				//$chk_opt=$itemoption.$i;
				$chk_opt='item_name_'.$i;
				$chk_postid='postid_'.$i;
				$chk_weight='weight_'.$i;
				$item_id=$wpdb->escape($_POST[$chk_id]);
				$item_qty=$wpdb->escape($_POST[$chk_qty]);
				$item_amt=$wpdb->escape(str_replace(',', "", $_POST[$chk_amt]));;
				$optname=$wpdb->escape($_POST[$chk_opt]);
				$post_id=$wpdb->escape($_POST[$chk_postid]);
				$weight=$wpdb->escape($_POST[$chk_weight]);
				$dlchking=$_POST['eshopident_'.$i];
				//add opt sets
				if(isset($_SESSION['eshopcart'.$blog_id][$dlchking]['optset'])){
					$oset=$qb=array();
					$optings=unserialize($_SESSION['eshopcart'.$blog_id][$dlchking]['optset']);
					//$opttable=$wpdb->prefix.'eshop_option_sets';
					$c=0;
					if(isset($newoptings)) unset($newoptings);
					foreach($optings as $foo=>$opst){
						if(!isset($opst['type']) || (isset($opst['text']) && $opst['text']!='')){
							$qb[]="id=$opst[id]";
							$newoptings[]=$optings[$c];
						}
						$c++;
					}
					if(isset($newoptings)){
						$qbs = implode(" OR ", $qb);
						$otable=$wpdb->prefix.'eshop_option_sets';
						$otablename=$wpdb->prefix.'eshop_option_names';
						$orowres=$wpdb->get_results("select o.name, o.price, o.id, t.type from $otable as o, $otablename as t where ($qbs) && o.optid=t.optid ORDER BY id ASC");
						$x=0;
						foreach($orowres as $orow){
							if(($orow->type=='2' || $orow->type=='3') && isset($newoptings[$x]['text']))
								$oset[]=$orow->name.": \n".'<span class="eshoptext">'.stripslashes($newoptings[$x]['text']).'</span>';
							elseif(($orow->type=='2' || $orow->type=='3') && !isset($newoptings[$x]['text']))
								$xxxx='';
							else
								$oset[]=$orow->name;
							$addoprice=$addoprice+$orow->price;
							$x++;
						}
						$optset="\n".implode("\n",$oset);
					}else{
						$optset='';
					}
				}else{
					$optset='';
				}
				$optset=$wpdb->escape($optset);
				//end
				$thechk=$_SESSION['eshopcart'.$blog_id][$dlchking]['option'];
				$option_id=$wpdb->escape($thechk);
				if(strpos($thechk,' ')===true){
					$edown=explode(' ',$thechk);
					$edl=$edown[1];
				}else{
					$edl=$thechk;
				}
				$eshop_product=get_post_meta( $post_id, '_eshop_product',true );
				$dlchk='';
				if(isset($eshop_product['products'][$edl]['download']))
					$dlchk=$eshop_product['products'][$edl]['download'];
				if($dlchk!=''){
					//there are downloads.
					$queryitem=$wpdb->query("INSERT INTO $itemstable
					(checkid, item_id,item_qty,item_amt,optname,post_id,option_id,down_id,optsets,weight)values(
					'$checkid','$item_id','$item_qty','$item_amt','$optname','$post_id','$option_id',
					'$dlchk','$optset','$weight');");

					$wpdb->query("UPDATE $detailstable set downloads='yes' where checkid='$checkid'");
					//add to download orders table
					$dloadtable=$wpdb->prefix.'eshop_download_orders';
					//$email,$checkid already set
					$producttable=$wpdb->prefix.'eshop_downloads';
					$grabit=$wpdb->get_row("SELECT id,title, files FROM $producttable where id='$dlchk'");
					$downloads = $eshopoptions['downloads_num'];
					$wpdb->query("INSERT INTO $dloadtable
					(checkid, title,purchased,files,downloads,code,email)values(
					'$checkid',
					'$grabit->title',
					NOW(),
					'$grabit->files',
					'$downloads',
					'$code',
					'$email');"
					);

				}else{
					$queryitem=$wpdb->query("INSERT INTO $itemstable
					(checkid, item_id,item_qty,item_amt,optname,post_id,option_id,optsets,weight)values(
					'$checkid','$item_id','$item_qty','$item_amt','$optname','$post_id','$option_id','$optset','$weight');");
				}
				$i++;

			}
			$postage=$wpdb->escape(str_replace(',', "", $_POST['shipping_1']));
			$postage_name='';
			if(isset($_SESSION['eshopshiptype'.$blog_id])  && !eshop_only_downloads()){
				$st=$_SESSION['eshopshiptype'.$blog_id]-1;
				$typearr=explode("\n", $eshopoptions['ship_types']);
				$postage_name=stripslashes(esc_attr($typearr[$st])).' ';
			}
			$postage_name.=__('Shipping','eshop');
			$querypostage=$wpdb->query("INSERT INTO  $itemstable 
					(checkid, item_id,item_qty,item_amt,optsets)values(
					'$checkid',
					'$postage_name',
					'1',
					'$postage',
					'');");
			//update the discount codes used, and remove from remaining
			$disctable=$wpdb->prefix.'eshop_discount_codes';
			if(eshop_discount_codes_check()){
				if(isset($_SESSION['eshop_discount'.$blog_id]) && valid_eshop_discount_code($_SESSION['eshop_discount'.$blog_id])){
					$discvalid=$wpdb->escape($_SESSION['eshop_discount'.$blog_id]);
					$wpdb->query("UPDATE $disctable SET used=used+1 where disccode='$discvalid' limit 1");

					$remaining=$wpdb->get_var("SELECT remain FROM $disctable where disccode='$discvalid' && dtype!='2' && dtype!='5' limit 1");
					//reduce remaining
					if(is_numeric($remaining) && $remaining!='')			
						$wpdb->query("UPDATE $disctable SET remain=remain-1 where disccode='$discvalid' limit 1");
				}
			}
			if($eshopoptions['status']!='live'){
				echo "<p class=\"testing\"><strong>".__('Test Mode &#8212; No money will be collected. This page will not auto redirect in test mode.','eshop')."</strong></p>\n";
			}
		}
	}
}
if (!function_exists('stripslashes_array')) {
	//only use after magic quote check
	function stripslashes_array($array) {
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}
}
if (!function_exists('sanitise_array')) {
	//sanitises input array!
	function sanitise_array($array) {
		return is_array($array) ? array_map('sanitise_array', $array) : esc_attr($array);
	}
}
if(!function_exists('eshop_build_cookie')) {
	function eshop_build_cookie($var_array) {
		$out='';
	  if (is_array($var_array)) {
		foreach ($var_array as $index => $data) {
		  $out.= ($data!="") ? $index."=".stripslashes($data)."|" : "";
		}
	  }
	  return rtrim($out,"|");
	}
}
if (!function_exists('eshop_break_cookie')) {
	function eshop_break_cookie($cookie_string) {
	  $array=explode("|",$cookie_string);
	  foreach ($array as $i=>$stuff) {
		$stuff=explode("=",$stuff);
		$array[$stuff[0]]=$stuff[1];
		unset($array[$i]);
	  }
	  return $array;
	}
}

if (!function_exists('eshop_only_downloads')) {
	function eshop_only_downloads() {
		global $blog_id;
		$num=0;
		$items=0;
		$eshopcartarray=$_SESSION['eshopcart'.$blog_id];
		foreach ($eshopcartarray as $productid => $opt){
			$post_id=$opt['postid'];
			$option=$opt['option'];
			$eshop_product=get_post_meta( $post_id, '_eshop_product',true );
			if(isset($eshop_product['products'][$option]['download'])){
				$dlchk=$eshop_product['products'][$option]['download'];
				if($dlchk!='')
					$num++;
			}
			$items++;
		}
		if($num==$items)
			return true;
		
		return false;
	}
}


if (!function_exists('eshop_rtn_order_details')) {
	/*
	will return an array consisting of
	status
	name - first/last/company
	cart details
	address
	extras - comments/reference/PO
	contact info
	
	suitable for emailing.
	*/
	function eshop_rtn_order_details($checkid){
		global $wpdb,$eshopoptions;
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		$stable=$wpdb->prefix.'eshop_states';
		$ctable=$wpdb->prefix.'eshop_countries';

		$dquery=$wpdb->get_results("Select * From $dtable where checkid='$checkid' limit 1");
		foreach($dquery as $drow){
			$status=$drow->status;
			$checkid=$drow->checkid;
			$custom=$drow->custom_field;
			$transid=$drow->transid;
			$edited=$drow->edited;
			$affiliate=$drow->affiliate;
			$dbid=$drow->id;
		}
		if($status=='Completed'){$status=__('Order Received','eshop');}
		if($status=='Pending' || $status=='Waiting'){$status=__('Pending Payment','eshop');}
		$contact=$cart=$address=$extras= '';
		$result=$wpdb->get_results("Select * From $itable where checkid='$checkid' ORDER BY id ASC");
		$total=0;
		$currsymbol=$eshopoptions['currency_symbol'];
		$cart.=__('Transaction id:','eshop').' '.$transid."\n";
		$containsdownloads=0;
		foreach($result as $myrow){
			$value=$myrow->item_qty * $myrow->item_amt;
			$total=$total+$value;
			$itemid=$myrow->item_id.' '.$myrow->optsets;
			// add in a check if postage here as well as a link to the product
			if($itemid=='postage'){
				$cart.= __('Shipping Charge:','eshop').' '.sprintf( __('%1$s%2$s','eshop'), $currsymbol, number_format_i18n($value, 2))."\n\n";
			}else{
				$cart.= $myrow->optname." ".strip_tags($itemid)."\n\n".__('Quantity:','eshop')." ".$myrow->item_qty."\n".__('Price:','eshop')." ".sprintf( __('%1$s%2$s','eshop'), $currsymbol, number_format_i18n($value, 2))."\n\n";
			}
		
			//check if downloadable product
			if($myrow->down_id!='0'){
				$containsdownloads++;
			}
		}
		$arrtotal=number_format($total, 2);
		$cart.= __('Total','eshop').' '.sprintf( __('%1$s%2$s','eshop'), $currsymbol, number_format_i18n($total, 2))."\n";
		$cyear=substr($custom, 0, 4);
		$cmonth=substr($custom, 4, 2);
		$cday=substr($custom, 6, 2);
		$thisdate=$cyear."-".$cmonth."-".$cday;
		$cart.= "\n".__('Order placed on','eshop')." ".$thisdate."\n";
		foreach($dquery as $drow){
			$address.= "\n".__('Mailing Address:','eshop')."\n".$drow->address1.", ".$drow->address2."\n";
			$address.= $drow->city."\n";
			$qcode=$wpdb->escape($drow->state);
			$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE id='$qcode' limit 1");
			if($qstate=='') $qstate=$drow->state;
			$address.= $qstate."\n";
			$address.= $drow->zip."\n";
			$qccode=$wpdb->escape($drow->country);
			$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qccode' limit 1");
			$address.= $qcountry."\n";
		
			$contact.= __('Phone:','eshop').' '.$drow->phone."\n";
			$contact.= __('Email:','eshop').' '.$drow->email."\n";

			if($drow->ship_name!='' && $drow->ship_address!='' && $drow->ship_city!='' && $drow->ship_postcode!=''){
				$address.= "\n".__('Shipping Address:','eshop')."\n";
				$address.= $drow->ship_name."\n";
				$address.= $drow->ship_company."\n";
				if(($drow->ship_phone!=$drow->phone) && $drow->ship_phone!=''){
					$contact.= __('Shipping address phone number:','eshop')."\n".$drow->ship_phone."\n";
				}
				$address.= $drow->ship_address."\n";
				$address.= $drow->ship_city."\n";
				$qcode=$wpdb->escape($drow->ship_state);
				$sqstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE id='$qcode' limit 1");
				if($sqstate=='') $sqstate=$drow->ship_state;
				$address.= $sqstate."\n";
				$address.= $drow->ship_postcode."\n";
				$qccode=$wpdb->escape($drow->ship_country);
				$sqcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qccode' limit 1");
				$address.= $sqcountry."\n";
			}
			if($drow->thememo!=''){
				$extras.= __('Paypal memo:','eshop')."\n".$drow->thememo."\n";
			}
			if($drow->reference!=''){
					$extras.= __('Reference/PO:','eshop')."\n".$drow->reference."\n";
			}
			if($drow->comments!=''){
					$extras.= __('Order comments:','eshop')."\n".$drow->comments."\n";
			}
		}
		if($drow->company!=''){
			$ename=$drow->first_name." ".$drow->last_name.' '.__('of','eshop').' '.$drow->company;
		}else{
			$ename=$drow->first_name." ".$drow->last_name;
		}
		$user_id=$drow->user_id;
		$firstname=$drow->first_name;
		$eemail=$drow->email;
		$downloads='';
		if($containsdownloads>0){
			$downtable=$wpdb->prefix.'eshop_download_orders';
			$chkcode= $wpdb->get_var("SELECT code FROM $downtable WHERE checkid='$drow->checkid' && email='$drow->email'");
			$downloads=get_permalink($eshopoptions['show_downloads'])."\n";
			$downloads.=__('Email:','eshop').' '.$drow->email."\n";
			$downloads.=__('Code:','eshop').' '.$chkcode."\n";
		}
		$cart=html_entity_decode($cart);
		$extras=html_entity_decode($extras);
		$firstname=html_entity_decode($firstname);
		$ename=html_entity_decode($ename);
		$address=html_entity_decode($address);
		$array=array("status"=>$status,"firstname"=>$firstname, "ename"=>$ename,"eemail"=>$eemail,"cart"=>$cart,"downloads"=>$downloads,"address"=>$address,"extras"=>$extras, "contact"=>$contact,"date"=>$edited,"affiliate"=>$affiliate,"user_id"=>$user_id,"transid"=>$transid,"total"=>$arrtotal,"dbid"=>$dbid);
		$secarray=apply_filters('eshoprtndetails',$dquery);
		$retarray=array_merge($array,$secarray);
		return $retarray;
	}
}

if (!function_exists('eshop_add_excludes')) {
	function eshop_add_excludes($excludes) {
		global $blog_id,$eshopoptions;
		if(!isset($_SESSION['eshopcart'.$blog_id]) && $eshopoptions['hide_cartco']=='yes'){
			$excludes[]=$eshopoptions['cart'];
			$excludes[]=$eshopoptions['checkout'];
		}
		$excludes[]=$eshopoptions['show_downloads'];
		$excludes[]=$eshopoptions['cart_success'];
		$excludes[]=$eshopoptions['cart_cancel'];
		return $excludes;
	}
}

if (!function_exists('eshop_fold_menus')) {
	function eshop_fold_menus($exclusions = "") {
		global $post, $wpdb,$eshopoptions;
		//code taken from fold page menu plugin and adapted
		if (isset($post->ID))
			$id=$post->ID;
		else
			$id=$eshopoptions['cart'];//fix to hide menus on other pages
		$x = $id;
		$inclusions = "(post_parent <> " . strval($x) . ")";
		do {
			$include = $wpdb->get_results("SELECT post_parent " .
			"FROM $wpdb->posts " .
			"WHERE ID = " . $x . " " .
			"LIMIT 1",ARRAY_N);
			$x = $include[0][0];
			$inclusions .= " AND (post_parent <> " . $x . ")";
		} while ($x <> 0);

		$rows = $wpdb->get_results("SELECT ID " .
		"FROM $wpdb->posts " .
		"WHERE (post_type = 'page') AND " .
		$inclusions, ARRAY_N);
		if ( count($rows) ) {
			foreach ( $rows as $row ) {
				foreach ( $row as $ro ) {
					if ($exclusions <> "")
						//$exclusions .= ",";
						$exclusions[]= strval($ro);
				}
			}
		}
		return $exclusions;
	}
}
if (!function_exists('eshop_random_code')) {
	function eshop_random_code ($length = 10){
		$password = "";
		//characters allowed
		//lower case l, upper case O, number 1 and number 0 have been removed for clarity
		$allowed = __('abcdefghijkmnopqrstuvwxyz23456789ABCDEFGHIJKLMNPQRSTUVWXYZ','eshop');    
		$i = 0; 
		// Loop until password string is the required length
		while ($i < $length){  
		// Select random character allowed string
			$char = substr($allowed, mt_rand(0, strlen($allowed)-1), 1);
		// Add random character to password string
			$password .= $char;
			$i++;
		}

		// Return random password
		return $password;
	}
}
if (!function_exists('eshop_download_the_product')) {
	function eshop_download_the_product($_POST){
		global $wpdb,$eshopoptions;
		$table = $wpdb->prefix ."eshop_downloads";
		$ordertable = $wpdb->prefix ."eshop_download_orders";
		$dir_upload = eshop_download_directory();
		$echo='';
		if (isset($_POST['eshoplongdownloadname'])){
			//check again everything else ok then go ahead
			$id=$wpdb->escape($_POST['id']);
			$code=$wpdb->escape($_POST['code']);
			$email=$wpdb->escape($_POST['email']);
			set_time_limit(1000);
			if($id!='all'){
				//single file handling
				$ordertable = $wpdb->prefix ."eshop_download_orders";
				$chkcount = $wpdb->get_var("SELECT COUNT(id) FROM $ordertable where email='$email' && code='$code' && id='$id' && downloads!=0");
				$chkresult = $wpdb->get_results("Select * from $ordertable where email='$email' && code='$code' && id='$id' && downloads!=0");
				if($chkcount>0){
					foreach($chkresult as $chkrow){
						// make sure output buffering is disabled
					   	ob_end_clean();
						set_time_limit(0);
						$item=$chkrow->files;
						$wpdb->query("UPDATE $ordertable SET downloads=downloads-1 where email='$email' && code='$code' && id='$id' limit 1");
						//update product with number of downloads made
						$wpdb->query("UPDATE $table SET downloads=downloads+1 where title='$chkrow->title' && files='$item' limit 1");
						//force download - should bring up save box, but it doesn't!
						$dload=$dir_upload.$item;
						header("Pragma: public"); // required
						header("Expires: 0");
						header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						header("Cache-Control: private",false); // required for certain browsers 
						header("Content-Type: application/force-download");
						// it even allows spaces in filenames
						header('Content-Disposition: attachment; filename="'.$item.'"');
						header("Content-Transfer-Encoding: binary");
						header("Content-Length: ".filesize($dload));
						//ob_clean();
    					//flush();
						readfile("$dload");
						//alternatives download methods comment above, and uncomment below
						//eshop_readfile($dload);
						//eshop_readfile_temp($dload,$item);
        	   			exit();
					}
				}
			}else{
				//multiple files - need to be zipped.
				include_once("archive-class.php");

				$date=date("Y-m-d");
				$backupfilename=get_bloginfo('name').'-'.$date.'.zip';
				$test = new zip_file($backupfilename);

				// Create archive in memory
				// Do not recurse through subdirectories
				// Do not store file paths in archive
				// Add lib/archive.php to archive
				//$test->add_files("src/archive.php");
				// Add all jpegs and gifs in the images directory to archive


				$test->set_options(array('inmemory' => 1, 'recurse' => 0, 'storepaths' => 0,'prepend' => 'downloads'));
				$chkcount = $wpdb->get_var("SELECT COUNT(id) FROM $ordertable where email='$email' && code='$code' && downloads!='0'");
				$chkresult = $wpdb->get_results("Select * from $ordertable where email='$email' && code='$code' && downloads!='0'");
				if($chkcount>0){
					foreach($chkresult as $drow){
						$item=$drow->files;
						$dload=$dir_upload.$drow->files;
						$test->add_files(array($dload));
						$wpdb->query("UPDATE $ordertable SET downloads=downloads-1 where email='$email' && code='$code' && id='$drow->id'");
						//update product with number of downloads made
						$wpdb->query("UPDATE $table SET downloads=downloads+1 where title='$drow->title' && files='$item' limit 1");
					}
				}	
				// make sure output buffering is disabled
				ob_end_clean();
				// Create archive in memory
				$test->create_archive();
				// Send archive to user for download
				$test->download_file();
			}
		}
		return;
	}
}
if (!function_exists('eshop_readfile')){
  // Read a file and display its content chunk by chunk
  function eshop_readfile($filename, $retbytes = TRUE) {
    $buffer = '';
    $cnt =0;
    $chunksize=1024*1024;// Size (in bytes) of tiles chunk
    //also try this line
    //set_time_limit(300);
    // $handle = fopen($filename, 'rb');
    $handle = fopen($filename, 'rb');
    if ($handle === false) {
      return false;
    }
    while (!feof($handle)) {
      $buffer = fread($handle, $chunksize);
      echo $buffer;
      ob_flush();
      flush();
      if ($retbytes) {
        $cnt += strlen($buffer);
      }
    }
    $status = fclose($handle);
    if ($retbytes && $status) {
      return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
  }
}

//new alt
if (!function_exists('eshop_readfile_temp')){
  function eshop_readfile_temp($fileloc,$filename) {
  	$download_attempt=0;
	do {
        $fs = fopen($fileloc, "rb");
        $uploads = wp_upload_dir();
		$temp_file_name=$uploads['basedir'].'/'.$filename;
        if (!$fs) {
          die (__('Sorry there was an error with the download','eshop'));
        } else {
          $fm = fopen ($temp_file_name, "w");
          stream_set_timeout($fs, 30);

          while(!feof($fs)) {
            $contents = fread($fs, 4096); // Buffered download
            fwrite($fm, $contents);
            $info = stream_get_meta_data($fs);
            if ($info['timed_out']) {
              break;
            }
          }
          fclose($fm);
          fclose($fs);

          if ($info['timed_out']) {
            // Delete temp file if fails
            unlink($temp_file_name);
            $download_attempt++;
          } else {
			wp_redirect($uploads['baseurl'].'/'.$filename, '302');
            unlink($temp_file_name);
            //delete on success.
          }
        }
      } while ($download_attempt < 5 && $info['timed_out']);
	}
}



if (!function_exists('eshop_visible_credits')) {
	function eshop_visible_credits($pee){
		//for front end
		global $eshopoptions;
		$version = explode(".", ESHOP_VERSION);
		if('yes' == $eshopoptions['credits']){
			 echo '<p class="creditline">'.__('Powered by','eshop').' <a href="http://www.quirm.net/" title="'.__('Created by','eshop').' Rich Pedley">eShop</a>
		<dfn title="'.__('Version','eshop').' '.ESHOP_VERSION.'">v.'.$version[0].'</dfn></p> ';
		}else{
			echo '<!--'.__('Powered by','eshop').' eShop v'.ESHOP_VERSION.' by Rich Pedley http://www.quirm.net/-->';
		}
		return;
	}
}
if (!function_exists('eshop_show_extra_links')) {
	function eshop_show_extra_links(){
		global $eshopoptions;
		$linkattr=apply_filters('eShopCheckoutLinksAttr','');
		$xtralinks='';
		if($eshopoptions['cart_shipping']!='' && $eshopoptions['downloads_only']!='yes'){
			$ptitle=get_post($eshopoptions['cart_shipping']);
			$xtralinks.='<a href="'.get_permalink($eshopoptions['cart_shipping']).'"'.$linkattr.'>'.$ptitle->post_title.'</a>, ';
		}
		if($eshopoptions['xtra_privacy']!=''){
			$ptitle=get_post($eshopoptions['xtra_privacy']);
			if($ptitle->post_title!=''){
				$xtralinks.='<a href="'.get_permalink($eshopoptions['xtra_privacy']).'"'.$linkattr.'>'.$ptitle->post_title.'</a>, ';
			}
		}
		if($eshopoptions['xtra_help']!=''){
			$ptitle=get_post($eshopoptions['xtra_help']);
			if($ptitle->post_title!=''){
				$xtralinks.='<a href="'.get_permalink($eshopoptions['xtra_help']).'"'.$linkattr.'>'.$ptitle->post_title.'</a>, ';
			}
		}
		
		if($xtralinks!=''){
			return '('.substr($xtralinks, 0, -2).')';
		}else{
			return;
		}
	}
}
if (!function_exists('eshop_download_directory')) {
    function eshop_download_directory(){
		$dirs=wp_upload_dir();
        $upload_dir=$dirs['basedir'];
        $eshop_goto=$upload_dir.'/../eshop_downloads';
		return $eshop_goto.'/';
    }
}
if (!function_exists('eshop_files_directory')) {
    function eshop_files_directory(){
        $dirs=wp_upload_dir();
        $upload_dir=$dirs['basedir'];
        $url_dir=$dirs['baseurl'];
        if(substr($url_dir, -1)!='/')$url_dir.='/';
       	$eshop_goto=$upload_dir.'/eshop_files';
		$urlpath=$url_dir.'eshop_files/';
		$urlpath=preg_replace('/\/wp-content\/blogs\.dir\/\d+/', '', $urlpath);
		$rtn=array(0=>$eshop_goto.'/',1=>$urlpath);
		return $rtn;
    }
}

if (!function_exists('eshop_from_address')) {
	function eshop_from_address(){
		global $eshopoptions;
		if($eshopoptions['from_email']!=''){
			$headers='From: '.get_bloginfo('name').' <'.$eshopoptions['from_email'].">\n";
		}elseif($eshopoptions['business']!=''){
			$headers='From: '.get_bloginfo('name').' <'.$eshopoptions['business'].">\n";
		}else{
			$headers='';
		}
		return $headers;
	}
}

if (!function_exists('eshop_excerpt_img')) {
	function eshop_excerpt_img($output){
		global $post,$eshopoptions;
		$echo='';
		if(is_search()){
			$isaproduct=get_post_meta($post->ID,'_eshop_product',true);
			$w=get_option('thumbnail_size_w');
			$h=get_option('thumbnail_size_h');
			if (has_post_thumbnail( $post->ID ) ) {
				$eimg =get_the_post_thumbnail( $post->ID, array($w, $h))."\n";
			}else{
				$eimage=eshop_files_directory();
				$eshopnoimage=apply_filters('eshop_no_image',$eimage['1'].'noimage.png');
				$eimg ='<img src="'.$eshopnoimage.'" height="'.$h.'" width="'.$w.'" alt="" />'."\n";
			}
			if($eshopoptions['search_img'] == 'all'){
					$echo .=$eimg;
			}elseif($eshopoptions['search_img'] == 'yes' && $isaproduct!=''){
				$echo .=$eimg;
			}
		}
		return $echo.$output;
	}
}

if (!function_exists('eshop_plural')) {
	function eshop_plural( $quantity, $singular, $plural ){
	  if( intval( $quantity ) == 1 )
		return $singular;
	  return $plural;
	}
}
if (!function_exists('eshop_email_parse')) {
	function eshop_email_parse($this_email,$array, $d='yes'){
		global $eshopoptions;
		require_once ( ABSPATH . WPINC . '/registration.php' );
		$this_email = str_replace('{STATUS}', $array['status'], $this_email);
		$this_email = str_replace('{FIRSTNAME}', $array['firstname'], $this_email);
		$this_email = str_replace('{NAME}', $array['ename'], $this_email);
		$this_email = str_replace('{EMAIL}', $array['eemail'], $this_email);
		$this_email = str_replace('{CART}', $array['cart'], $this_email);
		if(isset($eshopoptions['downloads_email']) && 'yes' == $eshopoptions['downloads_email'] || $d=='yes')
			$this_email = str_replace('{DOWNLOADS}', $array['downloads'], $this_email);
		else
			$this_email = str_replace('{DOWNLOADS}', '', $this_email);
		/*
		if($d=='yes')
			$this_email = str_replace('{DOWNLOADS}', $array['downloads'], $this_email);
		else
			 $this_email = str_replace('{DOWNLOADS}', '', $this_email);
		*/
		$this_email = str_replace('{ADDRESS}', $array['address'], $this_email);
		$this_email = str_replace('{REFCOMM}', $array['extras'], $this_email);
		$this_email = str_replace('{CONTACT}', $array['contact'], $this_email);
		$this_email = str_replace('{ORDERDATE}', $array['date'], $this_email);
		$filterit=array($array,$this_email);
		$temp = apply_filters('eshopemailparse',$filterit);
		if(!is_array($temp)) $this_email=$temp;
		return $this_email;
	}
}
if (!function_exists('eshop_cache')) {
	function eshop_cache(){
		global $eshopoptions;
	  	if(!defined('DONOTCACHEPAGE') && $eshopoptions['set_cacheability']=='yes'){
	  		//wpsupercache
			define("DONOTCACHEPAGE", "true");
		}
	}
}
if (!function_exists('create_eshop_error')) {
	//old method
	function create_eshop_error($error){ ?>
		<div class="error fade"><?php echo $error; ?></div>
	<?php
	}
}
if (!function_exists('eshop_check_error')) {
	function eshop_check_error() {
		if(isset($_GET['eshop_message']))
			return eshop_error_message($_GET['eshop_message']);
	}
}
if (!function_exists('eshop_error')) {
	function eshop_error($loc) {
 		return add_query_arg( 'eshop_message', 1, $loc );
	}
}
if (!function_exists('eshop_price_error')) {
	function eshop_price_error($loc) {
 		return add_query_arg( 'eshop_message', 2, $loc );
	}
}
if (!function_exists('eshop_weight_error')) {
	function eshop_weight_error($loc) {
 		return add_query_arg( 'eshop_message', 3, $loc );
	}
}
if (!function_exists('eshop_stkqty_error')) {
	function eshop_stkqty_error($loc) {
 		return add_query_arg( 'eshop_message', 4, $loc );
	}
}
if (!function_exists('eshop_error_message')) {
	function eshop_error_message($num){ 
		$messages=array(
		'1'=> __('Stock Available not set, as all details were not filled in.','eshop'),
		'2'=> __('Price incorrect, please only enter a numeric value.','eshop'),
		'3'=> __('Weight incorrect, please only enter a numeric value.','eshop'),
		'4'=> __('Stock Quantity is incorrect, please only enter a numeric value.','eshop'),
		'100'=>__('eShop settings updated.','eshop')
		);
		$messages=apply_filters('eshop_error_messages',$messages);
		if($num<100 && array_key_exists($num, $messages)){
		?>
		<div class="error fade"><p><?php echo $messages[$num]; ?></p></div>
		<?php
		}else{
		?>
			<div id="message" class="updated fade"><p><?php echo $messages[$num]; ?></p></div>
		<?php
		}

	}
}
if (!function_exists('eshop_wp_version')) {
	function eshop_wp_version($req){ 
		global $wp_version;
		if (version_compare($wp_version, $req, '>=')) {
			// version x or higher
			return true;
		}
		return false;
	}
}
if (!function_exists('eshop_cart_process')) {
	function eshop_cart_process($data=''){
		global $wpdb, $blog_id,$wp_query,$eshopoptions,$_POST;
		if($data!='')
			$_POST=$data;
		if(!isset($_POST['eshopnon'])){
			return;
		}
		wp_verify_nonce('eshop_add_product_cart');
		
		//setup variables:
		$option=$qty=$pclas=$productid=$pid=$pname=$iprice='';
		$echo='';
		//cache
		eshop_cache();
		//delete the session, empties the cart
		if(isset($_POST['unset']) || (calculate_items()==0 && isset($_SESSION['eshopcart'.$blog_id]) && sizeof($_SESSION['eshopcart'.$blog_id])>0)){
			unset($_SESSION['eshopcart'.$blog_id]);
			unset($_SESSION['final_price'.$blog_id]);
			unset($_SESSION['items'.$blog_id]);
			$_POST['save']='false';
		}
	if(!isset($_POST['save'])){
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
		if(isset($eshopoptions['min_qty']) && $eshopoptions['min_qty']!='') 
			$min=$eshopoptions['min_qty'];
		if(isset($eshopoptions['max_qty']) && $eshopoptions['max_qty']!='') 
			$max=$eshopoptions['max_qty'];
		if(isset($_POST['qty']) && !isset($_POST['save']) && (!is_numeric(trim($_POST['qty']))|| strlen($_POST['qty'])>3)){
			$qty=$_POST['qty']=1;
			$v='999';
			if(isset($max)) $v=$max;
			$error='<p><strong class="error">'.sprintf(__('Error: The quantity must contain numbers only, with a maximum of %s.','eshop'),$v).'</strong></p>';
		}
		
		if(isset($min) && isset($_POST['qty']) && $_POST['qty'] < $min){
			$qty=$_POST['qty']=$min;
			$v='999';
			if(isset($max)) $v=$max;
			$k=$min;
			$enote='<p><strong class="error">'.sprintf(__('Warning: The quantity must be greater than %s, with a maximum of %s.','eshop'),$k,$v).'</strong></p>';
		}
		if(isset($max) && isset($_POST['qty']) && $_POST['qty'] > $max){
			$qty=$_POST['qty']=$max;
			$v=$max;
			$k=1;
			if(isset($min)) $k=$min;
			$enote='<p><strong class="error">'.sprintf(__('Warning: The quantity must be greater than %s, with a maximum of %s.','eshop'),$k,$v).'</strong></p>';
		}
		if(isset($_POST['postid'])){
			$stkav=get_post_meta( $_POST['postid'], '_eshop_stock',true );
    		$eshop_product=get_post_meta( $_POST['postid'], '_eshop_product',true );
    	}
		if(isset($_POST['option']) && !isset($_POST['save'])){
			$edown=$getprice=$option=$_POST['option'];
			if(!isset($_POST['qty'])){
				$enote='<p><strong class="error">'.__('Warning: you must supply a quantity.','eshop').'</strong></p>';
			}
			$qty=$_POST['qty'];
			$plcas='';
			if(isset($_POST['pclas']))
				$pclas=$_POST['pclas'];
			$productid=$pid=$_POST['pid'];
			$pname=$_POST['pname'];
			/* if download option then it must be free shipping */
			$postid=$wpdb->escape($_POST['postid']);
			$eshop_product=get_post_meta( $postid, '_eshop_product',true );
			$dlchk='';
			if(isset($eshop_product['products'][$option]['download']))
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
		$optset='';
		if(isset($_POST['optset'])){
			$xx=0;
			foreach($_POST['optset'] as $opts){
				$optset.='os'.$xx.implode('os'.$xx,$opts);
				$xx++;
			}
		}
		if(!isset($pid)) $pid='';
		if(!isset($option)) $option='';
		if(!isset($postid)) $postid='';
		$identifier=$pid.$option.$postid.$optset;
		//$needle=array(" ","-","$","\r","\r\n","\n","\\","&","#",";");
		$identifier=md5($identifier);//str_replace($needle,"",$identifier);
		$stocktable=$wpdb->prefix ."eshop_stock";
		if(isset($_SESSION['eshopcart'.$blog_id][$identifier])){
			$testqty=$_SESSION['eshopcart'.$blog_id][$identifier]['qty']+$qty;
			$eshopid=$_SESSION['eshopcart'.$blog_id][$identifier]['postid'];
			$eshop_product=get_post_meta( $postid, '_eshop_product',true );
			$optnum=$_SESSION['eshopcart'.$blog_id][$identifier]['option'];
			$item=$eshop_product['products'][$_SESSION['eshopcart'.$blog_id][$identifier]['option']]['option'];
			if('yes' == $eshopoptions['stock_control']){
				$stkqty = $eshop_product['products'][$optnum]['stkqty'];
				//recheck stkqty
				$stktableqty=$wpdb->get_var("SELECT available FROM $stocktable where post_id=$eshopid && option_id=$optnum");
				if(isset($stktableqty) && is_numeric($stktableqty)) $stkqty=$stktableqty;
				if(!ctype_digit(trim($testqty))|| strlen($testqty)>3){
					$error='<p><strong class="error">'.__('Error: The quantity must contain numbers only, with a 999 maximum.','eshop').'</strong></p>';
				}elseif('yes' == $eshopoptions['stock_control'] && ($stkav!='1' || $stkqty<$testqty)){
					$error='<p><strong class="error">'.__('Error: That quantity is not available for that product.','eshop').'</strong></p>';
				}else{
					$_SESSION['eshopcart'.$blog_id][$identifier]['qty']+=$qty;
				}
			}else{
				$_SESSION['eshopcart'.$blog_id][$identifier]['qty']+=$qty;
			}
			$_SESSION['lastproduct'.$blog_id]=$postid;
		}elseif($identifier!=''){
			$weight=0;
			if(isset($_POST['save']) && $_POST['save']=='true'){
				$postid=$_SESSION['eshopcart'.$blog_id][$identifier]['postid'];
				$optid=$_SESSION['eshopcart'.$blog_id][$identifier]['option'];
				$optnum=$optid;
				$testqty=$qty;
			}else{
				$postid=$wpdb->escape($_POST['postid']);
				$optid=$wpdb->escape($_POST['option']);
				$optnum=$optid;
				$_SESSION['eshopcart'.$blog_id][$identifier]['postid']=$postid;
				$testqty=$qty;
			}
			$eshop_product=get_post_meta( $postid, '_eshop_product',true );
			$item=$eshop_product['products'][$optnum]['option'];
			if('yes' == $eshopoptions['stock_control']){
				$stkqty = $eshop_product['products'][$optnum]['stkqty'];

				//recheck stkqty
				$stktableqty=$wpdb->get_var("SELECT available FROM $stocktable where post_id=$postid && option_id=$optid");
				if(isset($stktableqty) && is_numeric($stktableqty)) $stkqty=$stktableqty;
				if(!ctype_digit(trim($testqty))|| strlen($testqty)>3){
					$error='<p><strong class="error">'.__('Error: The quantity must contain numbers only, with a 999 maximum.','eshop').'</strong></p>';
				}elseif('yes' == $eshopoptions['stock_control'] && ($stkav!='1' || $stkqty<$testqty)){
					$error='<p><strong class="error">'.__('Error: That quantity is not available for that product.','eshop').'</strong></p>';
					//$_SESSION['eshopcart'.$blog_id][$identifier]['qty']=$stkqty;
				}else{
					$_SESSION['eshopcart'.$blog_id][$identifier]['qty']=$qty;
				}
			}else{
				$_SESSION['eshopcart'.$blog_id][$identifier]['qty']=$qty;
			}	
			$_SESSION['lastproduct'.$blog_id]=$postid;
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

				//$opttable=$wpdb->prefix.'eshop_option_sets';
				foreach($optings as $foo=>$opst){
					$qb[]="id=$opst[id]";
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
			if(isset($error)){
				unset($_SESSION['eshopcart'.$blog_id][$identifier]);
			}
		}
	}
		if(!isset($error)){

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
			if(isset($_POST['deleteitem'])){
				foreach($_POST['deleteitem'] as $chkey=>$chkval){
					$tochkkey=$chkey;
					$tochkval=$chkval;
				}
			}

			//update products in the cart
			if(isset($_POST['save']) && $_POST['save']=='true' && isset($_SESSION['eshopcart'.$blog_id])){
				$eshopcartarray=$_SESSION['eshopcart'.$blog_id];
				foreach ($eshopcartarray as $productid => $opt){
					$needle=array(" ",".");
					$sessproductid=str_replace($needle,"_",$productid);
					foreach ($_POST as $key => $value){
						if($key==$sessproductid){
							foreach ($value as $notused => $qty){
								if(isset($tochkkey) && $tochkkey==$key) $qty=0;
								if($qty=="0"){							
									unset($_SESSION['eshopcart'.$blog_id][$productid]);
								}else{
									$postid=$eshopid=$_SESSION['eshopcart'.$blog_id][$productid]['postid'];
									$eshop_product=get_post_meta( $postid, '_eshop_product',true );
									$optnum=$_SESSION['eshopcart'.$blog_id][$productid]['option'];
									$stkqty = $eshop_product['products'][$_SESSION['eshopcart'.$blog_id][$productid]['option']]['stkqty'];
									//recheck stkqty
									$stocktable=$wpdb->prefix ."eshop_stock";
									$stktableqty=$wpdb->get_var("SELECT available FROM $stocktable where post_id=$eshopid AND option_id=$optnum");
									if(isset($stktableqty) && is_numeric($stktableqty)) $stkqty=$stktableqty;
									if(!ctype_digit(trim($qty))|| strlen($qty)>3){
										$v='999';
										if(isset($max)) $v=$max;
										$error='<p><strong class="error">'.sprintf(__('Error: The quantity must contain numbers only, with a maximum of %s.','eshop'),$v).'</strong></p>';
									}elseif('yes' == $eshopoptions['stock_control'] &&  $stkqty<$qty){
										$error='<p><strong class="error">'.__('Error: That quantity is not available for that product.','eshop').'</strong></p>';
									}elseif(isset($min) && isset($qty) && $qty < $min){
										$qty=$min;
										$v='999';
										if(isset($max)) $v=$max;
										$k=$min;
										$enote='<p><strong class="error">'.sprintf(__('Warning: The quantity must be greater than %s, with a maximum of %s.','eshop'),$k,$v).'</strong></p>';
									}elseif(isset($max) && isset($qty) && $qty > $max){
										$qty=$max;
										$v=$max;
										$k=1;
										if(isset($min)) $k=$min;
										$enote='<p><strong class="error">'.sprintf(__('Warning: The quantity must be greater than %s, with a maximum of %s.','eshop'),$k,$v).'</strong></p>';
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
		}
		//any errors will print here.
		if(isset($error)){
			$_SESSION['eshopcart'.$blog_id]['error']= $error;
		}
		if(isset($enote)){
			$_SESSION['eshopcart'.$blog_id]['enote']= $enote;
		}
		if(isset($_SESSION['eshopcart'.$blog_id]) && sizeof($_SESSION['eshopcart'.$blog_id])=='0'){
			unset($_SESSION['eshopcart'.$blog_id]);
			unset($_SESSION['final_price'.$blog_id]);
			unset($_SESSION['items'.$blog_id]);
			
		}
	}
}
if (!function_exists('eshop_mg_process_product')) {
	function eshop_mg_process_product($txn_id,$checked,$status='Completed'){
		global $wpdb;
		//tables
		$detailstable=$wpdb->prefix.'eshop_orders';
		$itemstable=$wpdb->prefix ."eshop_order_items";
		$stocktable=$wpdb->prefix ."eshop_stock";
		$mtable=$wpdb->prefix.'postmeta';
		$producttable=$wpdb->prefix.'eshop_downloads';
		$wpdb->query("UPDATE $detailstable set status='$status',transid='$txn_id' where checkid='$checked'");
		
		//product stock control updater & stats

		$query=$wpdb->get_results("SELECT item_qty,post_id,option_id,item_id,down_id FROM $itemstable WHERE checkid='$checked' AND post_id!='0'");
		foreach($query as $row){
			$pid=$row->post_id;
			$uqty=$row->item_qty;
			$optid=$row->option_id;
			////test downloads
			//check if downloadable product
			$fileid=$row->down_id;
			if($fileid!=0){
				$grabit=$wpdb->get_row("SELECT title, files FROM $producttable where id='$fileid'");
				//add 1 to number of purchases here (duplication but left in)
				$wpdb->query("UPDATE $producttable SET purchases=purchases+$uqty where title='$grabit->title' && files='$grabit->files' limit 1");
				$chkit= $wpdb->get_var("SELECT purchases FROM $stocktable WHERE post_id='$pid'");
				if($chkit!=''){	
					$wpdb->query("UPDATE $stocktable set purchases=purchases+$uqty where post_id=$pid && option_id=$optid");
				}else{
					$wpdb->query("INSERT INTO $stocktable (available, purchases, post_id, option_id) VALUES ('0','$uqty','$pid', '$optid')");
				}
			}else{
				$chkit= $wpdb->get_var("SELECT purchases FROM $stocktable WHERE post_id='$pid' && option_id=$optid");
				if($chkit!=''){						
					$wpdb->query("UPDATE $stocktable set available=available-$uqty, purchases=purchases+$uqty where post_id=$pid && option_id=$optid");
				}else{
					$wpdb->query("INSERT INTO $stocktable (available, purchases, post_id, option_id) VALUES ('0','$uqty','$pid', '$optid')");
				}
			}

		}
	}
}
if (!function_exists('eshop_contains')) {
    /**
     * Return true if one string can be found in another
     * as used above
     * @param $haystack the string to search *in*
     * @param $needle the string to search *for*
     */
    function eshop_contains($haystack, $needle){
        $pos = strpos($haystack, $needle);
        
        if ($pos === false) {
            return false;
        }
        else {
            return true;
        }
    }   
}
if (!function_exists('eshop_send_customer_email')) {
    function eshop_send_customer_email($checked, $mg_id){
    	global $wpdb;
    	//this is an email sent to the customer:
		//first extract the order details
		$array=eshop_rtn_order_details($checked);
		$etable=$wpdb->prefix.'eshop_emails';
		
		//grab the template
		$thisemail=$wpdb->get_row("SELECT emailSubject,emailContent FROM ".$etable." WHERE (id='".$mg_id."' AND emailUse='1') OR id='1'  order by id DESC limit 1");
		$this_email = stripslashes($thisemail->emailContent);
		
		// START SUBST
		$csubject=stripslashes($thisemail->emailSubject);
		$this_email = eshop_email_parse($this_email,$array);

		//try and decode various bits
		$this_email=html_entity_decode($this_email,ENT_QUOTES);
		
		$headers=eshop_from_address();
		wp_mail($array['eemail'], $csubject, $this_email,$headers);
		do_action('eshop_send_customer_email', $csubject, $this_email, $headers, $array);
		//affiliate
		if($array['affiliate']!=''){
			do_action('eShop_process_aff_commission', array("id" =>$array['affiliate'],"sale_amt"=>$array['total'], 
			"txn_id"=>$array['transid'], "buyer_email"=>$array['eemail']));
		}
	}
}
if (!function_exists('eshop_test_or_live')) {
	function eshop_test_or_live(){
		global $eshopoptions, $wp_admin_bar;
		if ( !is_object( $wp_admin_bar ) ) {
			if($eshopoptions['status']=='testing'){
				if(is_user_logged_in() && current_user_can('eShop_admin')){
					add_action('wp_head','eshop_test_mode');
					add_action('wp_footer','eshop_test_mode_text');
				}
			}
		} else {
			add_action( 'wp_before_admin_bar_render', 'eshop_admin_bar_menu', 150 );
		}

	}
}
if (!function_exists('eshop_admin_bar_menu')) {
	function eshop_admin_bar_menu() {
		global $wp_admin_bar;
		$eshopoptions = get_option('eshop_plugin_settings');
		if ( !is_object( $wp_admin_bar ) )
			return false;
		if($eshopoptions['status']=='testing'){
			$title=__('eShop Test Mode','eshop');
			$extras=__('Admin note: eShop is currently in test mode, and only admins can place orders.','eshop');
		}else{
			$title=__('eShop is Live','eshop');
		}
		/* Add the Blog Info menu */
		$wp_admin_bar->add_menu( array( 'id' => 'eshopadminbar', 'title' => $title, 'href' => '' ) );
		if(isset($extras))
			$wp_admin_bar->add_menu(  array( 'parent'=>'eshopadminbar','id' => 'eshopadminbar-a', 'title' => $extras, 'href'=>'' ) );

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
	?>
<style type="text/css">
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
</style>
<?php
	return;
	}
}
?>