<?php
if ('cart-functions.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');

if (!function_exists('display_cart')) {
	function display_cart($shopcart, $change, $eshopcheckout,$pzone='',$shiparray=''){
		//The cart display.
		global $wpdb, $blog_id,$eshopoptions;
		$echo ='';
		$check=0;
		$sub_total=0;
		$tempshiparray=array();
		//this checks for an empty cart, may not be required but leaving in just in case.
		foreach ($_SESSION['eshopcart'.$blog_id] as $productid => $opt){
			//foreach($opt as $option=>$qty){
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
			<tr class="thead">
			<th id="cartItem" class="nb">'.__('Item Description','eshop').'</th>
			<th id="cartQty" class="bt">'.__('<dfn title="Quantity">Qty</dfn>','eshop').'</th>
			<th id="cartTotal" class="btbr">'.__('Total','eshop').'</th>
			</tr></thead><tbody>';
			//display each item as a table row
			$calt=0;
			$shipping=0;
			$totalweight=0;
			$currsymbol=$eshopoptions['currency_symbol'];
			foreach ($_SESSION['eshopcart'.$blog_id] as $productid => $opt){
				$addoprice=0;
				if(is_array($opt)){
					$key=$opt['option'];
					$calt++;
					$alt = ($calt % 2) ? '' : ' class="alt"';
					$echo.= "\n<tr".$alt.">";
					//do the math for weight
					$eshop_product=get_post_meta( $opt['postid'], '_eshop_product',true );

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
							$eimg='<a class="itemref" href="'.get_permalink($opt['postid']).'"><img src="'.$eimage['1'].'noimage.png" height="'.$h.'" width="'.$w.'" alt="" /></a>'."\n";
						}
					}
					/* end */
					//opsets
					if(isset($opt['optset'])){
						$oset=$qb=array();
						
						$optings=unserialize($opt['optset']);
						$opttable=$wpdb->prefix.'eshop_option_sets';
						foreach($optings as $foo=>$opst){
							$qb[]="id=$opst";
						}
						$qbs = implode(" OR ", $qb);
						$otable=$wpdb->prefix.'eshop_option_sets';
						$orowres=$wpdb->get_results("select name, price, id from $otable where $qbs ORDER BY id ASC");
						$x=0;
						foreach($orowres as $orow){
							$oset[]=$orow->name;
							$addoprice=$addoprice+$orow->price;
							$x++;
						}
						$optset='('.implode(', ',$oset).')';
					}else{
						$optset='';
					}
					$echo.= '<td id="prod'.$calt.'" headers="cartItem" class="leftb">'.$eimg.'<a href="'.get_permalink($opt['postid']).'">'.stripslashes($opt["pname"]).' <span class="eshopidetails">('.$opt['pid'].' : '.stripslashes($opt['item']).')</span></a>'.$optset.'</td>'."\n";
					$echo.= "<td class=\"cqty lb\" headers=\"cartQty prod$calt\">";
					// if we allow changes, quantities are in text boxes
					if ($change == true){
						//generate acceptable id
						$toreplace=array(" ","-","$");
						$accid=$productid.$key;
						$accid=str_replace($toreplace, "", $accid);
						$echo.= '<label for="'.$accid.'"><input class="short" type="text" id="'.$accid.'" name="'.$productid.'['.$key.']" value="'.$opt["qty"].'" size="3" maxlength="3" /></label>';
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
					$echo.= "</td>\n<td headers=\"cartTotal prod$calt\" class=\"amts\">".sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($line_total,2))."</td></tr>\n";
					if(isset($disc_line))
						$sub_total+=$disc_line*$opt["qty"];
					else		
						$sub_total+=$line_total;
					//weight
					$totalweight+=$opt['weight']*$opt['qty'];
				}
			}
			// display subtotal row - total for products only
			$disc_applied='';
			if(is_discountable(calculate_total())>0){
				$discount=is_discountable(calculate_total());
				$disc_applied='<small>('.sprintf(__('Including Discount of <span>%s%%</span>','eshop'),number_format(round($discount, 2),2)).')</small>';
			}
			$echo.= "<tr class=\"stotal\"><th id=\"subtotal\" class=\"leftb\">".__('Sub-Total','eshop').' '.$disc_applied."</th><td headers=\"subtotal cartTotal\" class=\"amts lb\" colspan=\"2\">".sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($sub_total,2))."</td></tr>\n";
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
					$dacode='code';
				}else{
					$table=$wpdb->prefix.'eshop_states';
					$dacode='id';
				}
				$table2=$wpdb->prefix.'eshop_shipping_rates';
				switch($eshopoptions['shipping']){
					case '1'://( per quantity of 1, prices reduced for additional items )
						foreach ($shiparray as $nowt => $shipclass){
							//add to temp array for shipping
							if(!in_array($shipclass, $tempshiparray)) {
								if($shipclass!='F'){
									array_push($tempshiparray, $shipclass);
									if($pzone!=$eshopoptions['unknown_state'])
										$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $table WHERE $dacode='$pzone' limit 1");
									else
										$shipzone='zone'.$pzone;
									$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='$shipclass' and items='1' limit 1");
									$shipping+=$shipcost;
								}
							}else{
								if($shipclass!='F'){
									if($pzone!=$eshopoptions['unknown_state'])
										$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $table WHERE $dacode='$pzone' limit 1");
									else
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
									if($pzone!=$eshopoptions['unknown_state'])
										$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $table WHERE $dacode='$pzone' limit 1");
									else
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
								if($pzone!=$eshopoptions['unknown_state'])
									$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $table WHERE $dacode='$pzone' limit 1");
								else
									$shipzone='zone'.$pzone;						
								$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='A' and items='1' limit 1");
								$shipping+=$shipcost;
							}
						}
						break;
					case '4'://by weight/zone etc
						//$totalweight
						if($pzone!=$eshopoptions['unknown_state'])
							$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $table WHERE $dacode='$pzone' limit 1");
						else
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
				<td headers="cartItem scharge" class="amts lb" colspan="2">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($shipping,2)).'</td>
				</tr>';
				$_SESSION['shipping'.$blog_id]=$shipping;
				$final_price=$sub_total+$shipping;
				$_SESSION['final_price'.$blog_id]=$final_price;
				$echo.= '<tr class="total"><th id="cTotal" class="leftb">'.__('Total Order Charges','eshop')."</th>\n<td headers=\"cTotal cartTotal\"  colspan=\"2\" class = \"amts lb\"><strong>".sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($final_price, 2))."</strong></td></tr>";
			}

			$echo.= "</tbody></table>\n";
			// display unset/update buttons
			if($change == true){
				$echo.= "<div class=\"cartopt\"><input type=\"hidden\" name=\"save\" value=\"true\" />\n"; 
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
		$_SESSION['eshop_totalweight'.$blog_id]['totalweight']=$totalweight;
		return $echo;
	}
}
if (!function_exists('calculate_price')) {
	function calculate_price(){
		global $blog_id;
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
		if (!is_user_logged_in() && isset($eshopoptions['users']) && $eshopoptions['users']=='yes') {
			//set up blank user if in case anything goes phooey
			$user_id=0;
			require ( ABSPATH . WPINC . '/registration.php' );
			//auto create a new user if they don't exist - only works if not logged in ;)
			$user_email=$_POST['email'];
			if(isset($_POST['altstate']) && $_POST['altstate']!='')
				$_POST['altstate'];
			$utable=$wpdb->prefix ."users";
			$username = $_POST['first_name'].$_POST['last_name'];
			$eshopch = $wpdb->get_results("SHOW TABLE STATUS LIKE '$utable'");

			//a unique'ish number
			$altusername=$_POST['first_name'].$_POST['last_name'].$eshopch[0]->Auto_increment;
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

					update_user_meta( $user_id, 'eshop', $eshopuser );
					update_user_meta( $user_id, 'first_name', $_POST['first_name'] );
					update_user_meta( $user_id, 'last_name',$_POST['last_name'] );
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
		$phone=$wpdb->escape($_POST['phone']);
		$company=$wpdb->escape($_POST['company']);
		$email=$wpdb->escape($_POST['email']);
		$address1=$wpdb->escape($_POST['address1']);
		$address2=$wpdb->escape($_POST['address2']);
		$city=$wpdb->escape($_POST['city']);
		$zip=$wpdb->escape($_POST['zip']);
		$state=$wpdb->escape($_POST['state']);
		if($_POST['state']=='' && $_POST['altstate']!='')
			$state=$wpdb->escape($_POST['altstate']);

		$country=$wpdb->escape($_POST['country']);

		$ship_name=$wpdb->escape($_POST['ship_name']);
		$ship_phone=$wpdb->escape($_POST['ship_phone']);
		$ship_company=$wpdb->escape($_POST['ship_company']);
		$ship_address=$wpdb->escape($_POST['ship_address']);
		$ship_city=$wpdb->escape($_POST['ship_city']);
		$ship_postcode=$wpdb->escape($_POST['ship_postcode']);
		$ship_country=$wpdb->escape($_POST['ship_country']);
		$ship_state=$wpdb->escape($_POST['ship_state']);
		if($_POST['ship_state']=='' && $_POST['ship_altstate']!='')
			$ship_state=$wpdb->escape($_POST['ship_altstate']);
		$reference=$wpdb->escape($_POST['reference']);
		$comments=$wpdb->escape($_POST['comments']);
		
		$paidvia=$wpdb->escape($_SESSION['eshop_payment'.$blog_id]);
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
				custom_field,transid,edited,comments,paidvia,affiliate,user_id)VALUES(
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
				'$paidvia',
				'$affiliate',
				'$user_id'
					);");
			$i=1;
			//this is here to generate just one code per order
			$code=eshop_random_code(); 
			while($i<=$_POST['numberofproducts']){
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
					$opttable=$wpdb->prefix.'eshop_option_sets';
					foreach($optings as $foo=>$opst){
						$qb[]="id=$opst";
					}
					$qbs = implode(" OR ", $qb);
					$otable=$wpdb->prefix.'eshop_option_sets';
					$orowres=$wpdb->get_results("select name from $otable where $qbs ORDER BY id ASC");
					foreach($orowres as $orow){
						$oset[]=$orow->name;
					}
					$optset=$wpdb->escape(implode(', ',$oset));
				}else{
					$optset='';
				}

				//end

				$thechk=$_SESSION['eshopcart'.$blog_id][$dlchking]['option'];
				if(strpos($thechk,' ')===true){
					$edown=explode(' ',$thechk);
					$edl=$edown[1];
				}else{
					$edl=$thechk;
				}
				$eshop_product=get_post_meta( $post_id, '_eshop_product',true );
				$dlchk=$eshop_product['products'][$edl]['download'];
				if($dlchk!=''){
					//there are downloads.
					$queryitem=$wpdb->query("INSERT INTO $itemstable
					(checkid, item_id,item_qty,item_amt,optname,post_id,down_id,optsets,weight)values(
					'$checkid','$item_id','$item_qty','$item_amt','$optname','$post_id',
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
					(checkid, item_id,item_qty,item_amt,optname,post_id,optsets,weight)values(
					'$checkid','$item_id','$item_qty','$item_amt','$optname','$post_id','$optset','$weight');");
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
					(checkid, item_id,item_qty,item_amt)values(
					'$checkid',
					'$postage_name',
					'1',
					'$postage');");
			//update the discount codes used, and remove from remaining
			$disctable=$wpdb->prefix.'eshop_discount_codes';
			if(eshop_discount_codes_check()){
				if(valid_eshop_discount_code($_SESSION['eshop_discount'.$blog_id])){
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
		foreach ($_SESSION['eshopcart'.$blog_id] as $productid => $opt){
			$post_id=$opt['postid'];
			$option=$opt['option'];
			$eshop_product=get_post_meta( $post_id, '_eshop_product',true );
			$dlchk=$eshop_product['products'][$option]['download'];
			if($dlchk!='')
				$num++;
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
				$cart.= __('Shipping Charge:','eshop').' '.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($value, 2))."\n\n";
			}else{
				$cart.= $myrow->optname." ".$itemid."\n".__('Quantity:','eshop')." ".$myrow->item_qty."\n".__('Price:','eshop')." ".sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($value, 2))."\n\n";
			}
		
			//check if downloadable product
			if($myrow->down_id!='0'){
				$containsdownloads++;
			}
		}
		$arrtotal=number_format($total, 2);
		$cart.= __('Total','eshop').' '.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($total, 2))."\n";
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
		$array=array("status"=>$status,"firstname"=>$firstname, "ename"=>$ename,"eemail"=>$eemail,"cart"=>$cart,"downloads"=>$downloads,"address"=>$address,"extras"=>$extras, "contact"=>$contact,"date"=>$edited,"affiliate"=>$affiliate,"user_id"=>$user_id,"transid"=>$transid,"total"=>$arrtotal);
		return $array;
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
			if($id!='all'){
				//single file handling
				$ordertable = $wpdb->prefix ."eshop_download_orders";
				$chkcount = $wpdb->get_var("SELECT COUNT(id) FROM $ordertable where email='$email' && code='$code' && id='$id' && downloads!=0");
				$chkresult = $wpdb->get_results("Select * from $ordertable where email='$email' && code='$code' && id='$id' && downloads!=0");
				if($chkcount>0){
					foreach($chkresult as $chkrow){
						// make sure output buffering is disabled
					   	ob_end_clean();

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
						readfile("$dload");
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
if (!function_exists('eshop_show_credits')) {
	function eshop_show_credits(){
	//for admin
	$version = explode(".", ESHOP_VERSION);
	?>
	<p class="creditline"><?php _e('Powered by','eshop'); ?> <a href="http://www.quirm.net/" title="<?php _e('Created by','eshop'); ?> Rich Pedley">eShop</a>
	<dfn title="<?php echo ESHOP_VERSION; ?>">v.<?php echo $version[0]; ?></dfn></p> 
	<?php 
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
		$xtralinks='';
		if($eshopoptions['cart_shipping']!='' && $eshopoptions['downloads_only']!='yes'){
			$ptitle=get_post($eshopoptions['cart_shipping']);
			$xtralinks.='<a href="'.get_permalink($eshopoptions['cart_shipping']).'">'.$ptitle->post_title.'</a>, ';
		}
		if($eshopoptions['xtra_privacy']!=''){
			$ptitle=get_post($eshopoptions['xtra_privacy']);
			if($ptitle->post_title!=''){
				$xtralinks.='<a href="'.get_permalink($eshopoptions['xtra_privacy']).'">'.$ptitle->post_title.'</a>, ';
			}
		}
		if($eshopoptions['xtra_help']!=''){
			$ptitle=get_post($eshopoptions['xtra_help']);
			if($ptitle->post_title!=''){
				$xtralinks.='<a href="'.get_permalink($eshopoptions['xtra_help']).'">'.$ptitle->post_title.'</a>, ';
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
			$isaproduct=get_post_meta($post->ID,'_Price 1',true);
			$w=get_option('thumbnail_size_w');
			$h=get_option('thumbnail_size_h');
			if (has_post_thumbnail( $post->ID ) ) {
				$eimg =get_the_post_thumbnail( $post->ID, array($w, $h))."\n";
			}else{
				$eimage=eshop_files_directory();
				$eimg ='<img src="'.$eimage['1'].'noimage.png" height="'.$h.'" width="'.$w.'" alt="" />'."\n";
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

if (!function_exists('eshop_update_nag')) {
	function eshop_update_nag() {
		global $eshopoptions;
		if ( $eshopoptions['version']!='' && $eshopoptions['version'] >= ESHOP_VERSION )
			return false;

		if ( current_user_can('manage_options') )
			$msg = sprintf( __('<strong>eShop %1$s</strong> is now ready to use. <strong>You must now <a href="%2$s">deactivate and re-activate the plugin</a></strong>.','eshop'), ESHOP_VERSION, 'plugins.php#active-plugins-table' );
		else
			$msg = sprintf( __('<strong>eShop %1$s<strong> needs updating! Please notify the site administrator.','eshop'), ESHOP_VERSION );

		echo "<div id='update-nag'>$msg</div>";
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
	function eshop_email_parse($this_email,$array, $d='yes',$userpass='yes'){
		require_once ( ABSPATH . WPINC . '/registration.php' );
		$this_email = str_replace('{STATUS}', $array['status'], $this_email);
		$this_email = str_replace('{FIRSTNAME}', $array['firstname'], $this_email);
		$this_email = str_replace('{NAME}', $array['ename'], $this_email);
		$this_email = str_replace('{EMAIL}', $array['eemail'], $this_email);
		$this_email = str_replace('{CART}', $array['cart'], $this_email);
		if($d=='yes')
			$this_email = str_replace('{DOWNLOADS}', $array['downloads'], $this_email);
		else
			 $this_email = str_replace('{DOWNLOADS}', '', $this_email);
		$this_email = str_replace('{ADDRESS}', $array['address'], $this_email);
		$this_email = str_replace('{REFCOMM}', $array['extras'], $this_email);
		$this_email = str_replace('{CONTACT}', $array['contact'], $this_email);
		$this_email = str_replace('{ORDERDATE}', $array['date'], $this_email);
		if($userpass=='yes' && $array['user_id']!=0 && !is_user_logged_in()){
			$random_password = wp_generate_password( 12, false );
			$user_info=get_userdata($array['user_id']);
			$userdetails='Login: '.$user_info->user_login."\n";
			$uid=$array['user_id'];
			wp_update_user(array('ID'=>$uid,'user_pass'=>$random_password));
			$userdetails.='Password: '.$random_password."\n";
			$this_email = str_replace('{LOGIN_DETAILS}', $userdetails, $this_email);
		}else{
			$this_email = str_replace('{LOGIN_DETAILS}', '', $this_email);
		}

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
if (!function_exists('eshop_error_message')) {
	function eshop_error_message($num){ 
		$messages=array(
		'1'=> __('Stock Available not set, as all details were not filled in.','eshop'),
		'2'=> __('Price incorrect, please only enter a numeric value.','eshop'),
		'3'=> __('Weight incorrect, please only enter a numeric value.','eshop')

		);
		if(array_key_exists($num, $messages)){
		?>
		<div class="error fade"><p><?php echo $messages[$num]; ?></p></div>
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

?>
