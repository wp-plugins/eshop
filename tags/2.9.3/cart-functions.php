<?php
if ('cart-functions.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');

if (!function_exists('display_cart')) {
	function display_cart($shopcart, $change, $eshopcheckout,$pzone='',$shiparray=''){
		//The cart display.
		global $wpdb;
		$echo ='';
		$check=0;
		$tempshiparray=array();
		//this checks for an empty cart, may not be required but leaving in just in case.
		foreach ($_SESSION['shopcart'] as $productid => $opt){
			//foreach($opt as $option=>$qty){
			if(is_array($opt)){
				foreach($opt as $qty){
					$check=$check+$qty;
				}
			}
		}
		//therefore if cart exists and has products
		if($check > 0){
			global $final_price, $sub_total;
			// no fieldset/legend added - do we need it?
			if ($change == 'true'){
				$echo.= '<form action="'.wp_specialchars($_SERVER['REQUEST_URI']).'" method="post" class="eshopcart">';
			}
			$echo.= '<table class="cart" summary="'.__('Shopping cart contents overview','eshop').'">
			<caption>'.__('Shopping Cart','eshop').'</caption>
			<thead>
			<tr class="thead">
			<th id="cartItem" class="nb">'.__('Item Description','eshop').'</th>
			<th id="cartQty" class="bt"><dfn title="'.__('Quantity','eshop').'">'.__('Qty','eshop').'</dfn></th>
			<th id="cartTotal" class="btbr">'.__('Total','eshop').'</th>
			</tr></thead><tbody>';
			//display each item as a table row
			$calt=0;
			$shipping=0;
			$currsymbol=get_option('eshop_currency_symbol');
			foreach ($_SESSION['shopcart'] as $productid => $opt){
				if(is_array($opt)){
					$key=$opt['option'];
					$calt++;
					$alt = ($calt % 2) ? '' : ' class="alt"';
					$echo.= "\n<tr".$alt.">";
					$echo.= '<td id="prod'.$calt.'" headers="cartItem" class="leftb">'.stripslashes($opt["pname"]).' ('.$opt['pid'].' : '.stripslashes($opt['item']).')</td>'."\n";
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
					$line_total=$opt["price"]*$opt["qty"];
					$echo.= "</td>\n<td headers=\"cartTotal prod$calt\" class=\"amts\">".$currsymbol.number_format($line_total,2)."</td></tr>\n";
					$sub_total+=$line_total;

				}
			}
			// display subtotal row - total for products only
			$echo.= "<tr class=\"stotal\"><th id=\"subtotal\" class=\"leftb\">".__('Sub-Total','eshop')."</th><td headers=\"subtotal cartTotal\" class=\"amts lb\" colspan=\"2\">".$currsymbol.number_format($sub_total, 2)."</td></tr>\n";
				
			// SHIPPING PRICE HERE
			$shipping=0;
			//$pzone will only be set after the checkout address fields have been filled in
			// we can only work out shipping after that point
			if($pzone!=''){
				//shipping for cart.
				if(get_option('eshop_shipping_zone')=='country'){
					$table=$wpdb->prefix.'eshop_countries';
				}else{
					$table=$wpdb->prefix.'eshop_states';
				}
				$table2=$wpdb->prefix.'eshop_shipping_rates';
				switch(get_option('eshop_shipping')){
					case '1'://( per quantity of 1, prices reduced for additional items )
						foreach ($shiparray as $nowt => $shipclass){
							//add to temp array for shipping
							if(!in_array($shipclass, $tempshiparray)) {
								if($shipclass!='F'){
									array_push($tempshiparray, $shipclass);
									$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $table WHERE code='$pzone' limit 1");
									$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='$shipclass' and items='1' limit 1");
									$shipping+=$shipcost;
								}
							}else{
								if($shipclass!='F'){
									$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $table WHERE code='$pzone' limit 1");
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
									$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $table WHERE code='$pzone' limit 1");
									$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='$shipclass' and items='1' limit 1");
									$shipping+=$shipcost;
								}
							}
						}
						break;
					case '3'://( one overall charge no matter how many are ordered )
						$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $table WHERE code='$pzone' limit 1");
						$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='A' and items='1' limit 1");
						$shipping+=$shipcost;
						break;
				}

				//display shipping cost
				$echo.= '<tr class="alt">
				<th headers="cartItem" id="scharge" class="leftb">'.__('Shipping','eshop');
				if(get_option('eshop_cart_shipping')!=''){
					$ptitle=get_post(get_option('eshop_cart_shipping'));
					$echo.=' <small>(<a href="'.get_permalink(get_option('eshop_cart_shipping')).'">'.$ptitle->post_title.'</a>)</small>';
				}
				$echo.='</th>
				<td headers="cartItem scharge" class="amts lb" colspan="2">'.$currsymbol.number_format($shipping,2).'</td>
				</tr>';
				$_SESSION['shipping']=$shipping;
				$final_price=$sub_total+$shipping;
				$_SESSION['final_price']=$final_price;
				$echo.= '<tr class="total"><th id="cTotal" class="leftb">'.__('Total Order Charges','eshop')."</th>\n<td headers=\"cTotal cartTotal\"  colspan=\"2\" class = \"amts lb\"><strong>".$currsymbol.number_format($final_price, 2)."</strong></td></tr>";
			}
			$echo.= "</tbody></table>\n";
			// display unset/update buttons
			if($change == true){
				$echo.= "<div class=\"cartopt\"><input type=\"hidden\" name=\"save\" value=\"true\" />\n"; 
				$echo.= "<p><label for=\"unset\"><input type=\"submit\" class=\"button\" id=\"unset\" name=\"unset\" value=\"".__('Empty Cart','eshop')."\" /></label>";
				$echo.= "<label for=\"update\"><input type=\"submit\" class=\"button\" id=\"update\" name=\"update\" value=\"".__('Update Cart','eshop')."\" /></label></p>\n";
				$echo.= "</div>\n";
			}
			if ($change == 'true'){
				$echo.= "</form>\n";
			}
		}else{
			//if cart is empty - display a message - this is only a double check and should never be hit
			$echo.= "<p class=\"error\">".__('Your shopping cart is currently empty.','eshop')."</p>\n";
		}
		if(get_option('eshop_status')!='live'){
			$echo ="<p class=\"testing\"><strong>".__('Test Mode &#8212; No money will be collected.','eshop')."</strong></p>\n".$echo;
		}
		return $echo;
	}
}
if (!function_exists('calculate_price')) {
	function calculate_price(){

		// sum total price for all items in shopping shopcart
		$price = 0.0;

		if(is_array($_SESSION['shopcart'])){
			foreach ($_SESSION['shopcart'] as $productid => $opt){
				$price+=$opt['price'];
			}
		}
		return number_format($price, 2);
	}
}
if (!function_exists('calculate_items')) {
	function calculate_items(){
		// sum total items in shopping shopcart
		$items = 0;
		if(is_array($_SESSION['shopcart']))	{
			foreach ($_SESSION['shopcart'] as $productid => $opt){
				if(is_array($opt)){
					foreach($opt as $option=>$qty){
						$items += $qty;
					}
				}
			}
		}
		return $items;
	}
}
if (!function_exists('checkAlpha')) {
	//check string is alpha only.
	function checkAlpha($text){
		 return preg_match ("/[A-z-]/", $text);
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
		global $wpdb;
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
		$country=$wpdb->escape($_POST['country']);

		$ship_name=$wpdb->escape($_POST['ship_name']);
		$ship_phone=$wpdb->escape($_POST['ship_phone']);
		$ship_company=$wpdb->escape($_POST['ship_company']);
		$ship_address=$wpdb->escape($_POST['ship_address']);
		$ship_city=$wpdb->escape($_POST['ship_city']);
		$ship_postcode=$wpdb->escape($_POST['ship_postcode']);
		$ship_country=$wpdb->escape($_POST['ship_country']);
		$ship_state=$wpdb->escape($_POST['ship_state']);
		$reference=$wpdb->escape($_POST['reference']);
		$comments=$wpdb->escape($_POST['comments']);

		$detailstable=$wpdb->prefix.'eshop_orders';
		$itemstable=$wpdb->prefix.'eshop_order_items';
		$processing=__('Processing&#8230;','eshop');
		$query1=$wpdb->query("INSERT INTO $detailstable
			(checkid, first_name, last_name,company,email,phone, address1, address2, city,
			state, zip, country, reference, ship_name,ship_company,ship_phone, 
			ship_address, ship_city, ship_postcode,	ship_state, ship_country, 
			custom_field,transid,edited,comments)VALUES(
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
			'$comments'
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
			$item_id=$wpdb->escape($_POST[$chk_id]);
			$item_qty=$wpdb->escape($_POST[$chk_qty]);
			$item_amt=$wpdb->escape($_POST[$chk_amt]);
			$optname=$wpdb->escape($_POST[$chk_opt]);
			$post_id=$wpdb->escape($_POST[$chk_postid]);
			$queryitem=$wpdb->query("INSERT INTO $itemstable
				(checkid, item_id,item_qty,item_amt,optname,post_id)values(
				'$checkid',
				'$item_id',
				'$item_qty',
				'$item_amt','$optname','$post_id');");
			$i++;
			$mtable=$wpdb->prefix.'postmeta';
			$dlchk= $wpdb->get_var("SELECT meta_value FROM $mtable WHERE meta_key='_Product Download' AND post_id='$post_id'");
			if($dlchk!=''){
				//order contains downloads
				$wpdb->query("UPDATE $detailstable set downloads='yes' where checkid='$checkid'");
				//add to download orders table
				
				$dloadtable=$wpdb->prefix.'eshop_download_orders';
				//$email,$checkid already set
				
				$producttable=$wpdb->prefix.'eshop_downloads';
				$grabit=$wpdb->get_row("SELECT title, files FROM $producttable where id='$dlchk'");
				/*
				//add 1 to number of purchases - can't achieve this for ordinary products very easily 
				$prodtable = $wpdb->prefix ."eshop_downloads";
				$wpdb->query("UPDATE $prodtable SET purchases=purchases+1 where title='$grabit->title' && files='$grabit->files' limit 1");
				*/
				$downloads = get_option('eshop_downloads_num');

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
			}
		}
		$postage=$wpdb->escape($_POST['shipping_1']);
		$querypostage=$wpdb->query("INSERT INTO  $itemstable 
				(checkid, item_id,item_qty,item_amt)values(
				'$checkid',
				'postage',
				'1',
				'$postage');");
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
		return is_array($array) ? array_map('sanitise_array', $array) : wp_specialchars($array,ENT_QUOTES);
	}
}
if(!function_exists('eshop_build_cookie')) {
	function eshop_build_cookie($var_array) {
		$out='';
	  if (is_array($var_array)) {
		foreach ($var_array as $index => $data) {
		  $out.= ($data!="") ? $index."=".$data."|" : "";
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
		global $wpdb;
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
		}
		if($status=='Completed'){$status=__('Order Received','eshop');}
		if($status=='Pending'){$status=__('Pending Payment','eshop');}
		$contact=$cart=$address=$extras= '';
		$result=$wpdb->get_results("Select * From $itable where checkid='$checkid' ORDER BY id ASC");
		$total=0;
		$currsymbol=get_option('eshop_currency_symbol');
		$cart.=__('Transaction id:','eshop').' '.$transid."\n";
		$containsdownloads=0;
		foreach($result as $myrow){
			$value=$myrow->item_qty * $myrow->item_amt;
			$total=$total+$value;
			$itemid=$myrow->item_id;
			// add in a check if postage here as well as a link to the product
			if($itemid=='postage'){
				$cart.= __('Shipping Charge:','eshop').' '.$currsymbol.number_format($value, 2)."\n\n";
			}else{
				$cart.= $myrow->optname." ".$itemid."\n".__('Quantity:','eshop')." ".$myrow->item_qty."\n".__('Price:','eshop')." ".$currsymbol.number_format($value, 2)."\n\n";
			}
			$mtable=$wpdb->prefix.'postmeta';
			$dlchk= $wpdb->get_var("SELECT meta_value FROM $mtable WHERE meta_key='_Product Download' AND post_id='$myrow->post_id'");
			if($dlchk!=''){
				$containsdownloads++;
			}
		}
		
		$cart.= __('Total','eshop').' '.$currsymbol.number_format($total, 2)."\n";
		$cyear=substr($custom, 0, 4);
		$cmonth=substr($custom, 4, 2);
		$cday=substr($custom, 6, 2);
		$thisdate=$cyear."-".$cmonth."-".$cday;
		$cart.= "\n".__('Order placed on','eshop')." ".$thisdate."\n";

		foreach($dquery as $drow){
			$address.= "\n".__('Mailing Address:','eshop')."\n".$drow->address1.", ".$drow->address2."\n";
			$address.= $drow->city."\n";
			$qcode=$wpdb->escape($drow->state);
			$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
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
				$sqstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
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
		$firstname=$drow->first_name;
		$eemail=$drow->email;
		if($containsdownloads>0){
			$downtable=$wpdb->prefix.'eshop_download_orders';
			$chkcode= $wpdb->get_var("SELECT code FROM $downtable WHERE checkid='$drow->checkid' && email='$drow->email'");
			$downloads=get_permalink(get_option('eshop_show_downloads'))."\n";
			$downloads.=__('Email:','eshop').' '.$drow->email."\n";
			$downloads.=__('Code:','eshop').' '.$chkcode."\n";
		}
		$array=array("status"=>$status,"firstname"=>$firstname, "ename"=>$ename,"eemail"=>$eemail,"cart"=>$cart,"downloads"=>$downloads,"address"=>$address,"extras"=>$extras, "contact"=>$contact);
		return $array;
	}
}

if (!function_exists('eshop_get_shipping')) {
    /**
     * returns a table of the shipping rates
     */
    function eshop_get_shipping() { 
		global $wpdb;
		$dtable=$wpdb->prefix.'eshop_shipping_rates';
		$query=$wpdb->get_results("SELECT * from $dtable");
		$currsymbol=get_option('eshop_currency_symbol');

		$eshopshiptable='<table id="eshopshiprates" summary="'.__('This is a table of our online order shipping rates','eshop').'">';
		$eshopshiptable.='<caption><span>'.__('Shipping rates by class and zone','eshop').' <small>'.__('(subject to change)','eshop').'</small></span></caption>';
		$eshopshiptable.='<thead><tr><th id="class">'.__('Ship Class','eshop').'</th><th id="zone1">'.__('Zone 1','eshop').'</th><th id="zone2">'.__('Zone 2','eshop').'</th><th id="zone3">'.__('Zone 3','eshop').'</th><th id="zone4">'.__('Zone 4','eshop').'</th><th id="zone5">'.__('Zone 5','eshop').'</th></tr></thead>';
		$x=1;
		$eshopshiptable.='<tbody>';
		$x=1;
		$calt=0;
		switch (get_option('eshop_shipping')){
			case '1':// ( per quantity of 1, prices reduced for additional items )
				
				$query=$wpdb->get_results("SELECT * from $dtable ORDER BY class ASC, items ASC");
		
				foreach ($query as $row){
					$calt++;
					$alt = ($calt % 2) ? '' : ' class="alt"';
					$eshopshiptable.= '<tr'.$alt.'>';
					if($row->items==1){
						$eshopshiptable.= '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>'.__('(First Item)','eshop').'</small></th>'."\n";
					}else{
						$eshopshiptable.= '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>'.__('(Additional Items)','eshop').'</small></th>'."\n";
					}
					$eshopshiptable.= '<td headers="zone1 cname'.$x.'">'.$currsymbol.$row->zone1.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone2 cname'.$x.'">'.$currsymbol.$row->zone2.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone3 cname'.$x.'">'.$currsymbol.$row->zone3.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone4 cname'.$x.'">'.$currsymbol.$row->zone4.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone5 cname'.$x.'">'.$currsymbol.$row->zone5.'</td>'."\n";
					$eshopshiptable.= '</tr>';
					$x++;
				}
				break;
			case '2'://( once per shipping class no matter what quantity is ordered )
				$query=$wpdb->get_results("SELECT * from $dtable where items='1' ORDER BY 'class'  ASC");
				foreach ($query as $row){
					$calt++;
					$alt = ($calt % 2) ? '' : ' class="alt"';
					$eshopshiptable.= '<tr'.$alt.'>';
					$eshopshiptable.= '<th id="cname'.$x.'" headers="class">'.$row->class.'</th>'."\n";
					$eshopshiptable.= '<td headers="zone1 cname'.$x.'">'.$currsymbol.$row->zone1.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone2 cname'.$x.'">'.$currsymbol.$row->zone2.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone3 cname'.$x.'">'.$currsymbol.$row->zone3.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone4 cname'.$x.'">'.$currsymbol.$row->zone4.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone5 cname'.$x.'">'.$currsymbol.$row->zone5.'</td>'."\n";	
					$eshopshiptable.= '</tr>';
					$x++;
				}
				break;
			case '3'://( one overall charge no matter how many are ordered )

				$query=$wpdb->get_results("SELECT * from $dtable where items='1' and class='".__('A','eshop')." ORDER BY 'class'  ASC");
		
				foreach ($query as $row){
					$calt++;
					$alt = ($calt % 2) ? '' : ' class="alt"';
					$eshopshiptable.= '<tr'.$alt.'>';
					$eshopshiptable.= '<th id="cname'.$x.'" headers="class">'.$row->class.' <small>'.__('(Overall charge)','eshop').'</small></th>'."\n";
					$eshopshiptable.= '<td headers="zone1 cname'.$x.'">'.$currsymbol.$row->zone1.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone2 cname'.$x.'">'.$currsymbol.$row->zone2.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone3 cname'.$x.'">'.$currsymbol.$row->zone3.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone4 cname'.$x.'">'.$currsymbol.$row->zone4.'</td>'."\n";
					$eshopshiptable.= '<td headers="zone5 cname'.$x.'">'.$currsymbol.$row->zone5.'</td>'."\n";
					$eshopshiptable.= '</tr>';
					$x++;
				}
				break;
		}
		$calt++;
		$alt = ($calt % 2) ? '' : ' class="alt"';
		$eshopshiptable.= '<tr'.$alt.'>';
		$eshopshiptable.= '<th id="cname'.$x.'" headers="class">'.__('F','eshop').' <small>'.__('(Free)','eshop').'</small></th>'."\n";
		$eshopshiptable.= '<td headers="zone1 zone2 zone3 zone4 zone5 cname'.$x.'" colspan="5" class="center">'.$currsymbol.'0.00</td>'."\n";
		$eshopshiptable.= '</tr>';
		$eshopshiptable.='</tbody>';
		$eshopshiptable.='</table>';

		if('yes' == get_option('eshop_show_zones')){
			$eshopshiptable.=eshop_show_zones();
		}
		return $eshopshiptable;

	}
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
			$List=$wpdb->get_results("SELECT code,country FROM $tablec ORDER BY country",ARRAY_A);
			foreach($List as $key=>$value){
				$k=$value['code'];
				$v=$value['country'];
				$countryList[$k]=$v;
			}
			if(isset($_POST) && $_POST['country']!=''){
				$country=$_POST['country'];
			}
			$echo ='<form action="#customzone" method="post" class="eshopzones"><fieldset>
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
				$echo .='<p id="customzone">'.$qcountry['country'].' '.__('is in Zone','eshop').' '.$qcountry['zone'].'.</p>';
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
				$echo .='<p id="customzone">'.$qstate['stateName'].' '.__('is in Zone','eshop').' '.$qstate['zone'].'.</p>';
			}
		}
		if(get_bloginfo('version')<'2.5.1')
			remove_filter('the_content', 'wpautop');

		return $echo;
	}
}

if (!function_exists('eshop_add_excludes')) {
	function eshop_add_excludes($excludes) {
		if(!isset($_SESSION['shopcart'])){
			$excludes[]=get_option('eshop_cart');
			$excludes[]=get_option('eshop_checkout');
		}
		$excludes[]=get_option('eshop_show_downloads');
		$excludes[]=get_option('eshop_cart_success');
		$excludes[]=get_option('eshop_cart_cancel');
		return $excludes;
	}
}
if (!function_exists('fold_page_menus')) {
	function fold_page_menus($exclusions = "") {
		//left in for backwards compatability
	}
}
if (!function_exists('eshop_fold_menus')) {
	function eshop_fold_menus($exclusions = "") {
		global $post, $wpdb;
		//code taken from fold page menu plugin and adapted
		if (isset($post->ID))
			$id=$post->ID;
		else
			$id=get_option('eshop_cart');//fix to hide menus on other pages
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
		global $wpdb;
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
						$item=$chkrow->files;
						$wpdb->query("UPDATE $ordertable SET downloads=downloads-1 where email='$email' && code='$code' && id='$id' limit 1");
						//update product with number of downloads made
						$prodtable = $wpdb->prefix ."eshop_downloads";
						$wpdb->query("UPDATE $prodtable SET downloads=downloads+1 where title='$chkrow->title' && files='$item' limit 1");
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
				$chkresult = $wpdb->get_results("Select files from $ordertable where email='$email' && code='$code' && downloads!='0'");
				if($chkcount>0){
					foreach($chkresult as $drow){
						$item=$drow->files;
						$dload=$dir_upload.$drow->files;
						$test->add_files(array($dload));
					}
				}	
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
	?>
	<p class="creditline"><?php _e('Powered by','eshop'); ?> <a href="http://www.quirm.net/" title="<?php __('Created by','eshop'); ?> Rich Pedley">eShop</a>
	<dfn title="<?php echo ESHOP_VERSION; ?>">v.2</dfn></p> 
	<?php 
	}
}
if (!function_exists('eshop_visible_credits')) {
	function eshop_visible_credits($pee){
		//for front end
		if('yes' == get_option('eshop_credits')){
			 echo '<p class="creditline">'.__('Powered by','eshop').' <a href="http://www.quirm.net/" title="'.__('Created by','eshop').' Rich Pedley">eShop</a>
		<dfn title="'.__('Version','eshop').' '.ESHOP_VERSION.'">v.2</dfn></p> ';
		}else{
			echo '<!--'.__('Powered by','eshop').' eShop v'.ESHOP_VERSION.' by Rich Pedley http://www.quirm.net/-->';
		}
		return;
	}
}
if (!function_exists('eshop_show_extra_links')) {
	function eshop_show_extra_links(){
		$xtralinks='';
		if(get_option('eshop_cart_shipping')!=''){
			$ptitle=get_post(get_option('eshop_cart_shipping'));
			$xtralinks.='<a href="'.get_permalink(get_option('eshop_cart_shipping')).'">'.$ptitle->post_title.'</a>, ';
		}
		if(get_option('eshop_xtra_privacy')!=''){
			$ptitle=get_post(get_option('eshop_xtra_privacy'));
			if($ptitle->post_title!=''){
				$xtralinks.='<a href="'.get_permalink(get_option('eshop_xtra_privacy')).'">'.$ptitle->post_title.'</a>, ';
			}
		}
		if(get_option('eshop_xtra_help')!=''){
			$ptitle=get_post(get_option('eshop_xtra_help'));
			if($ptitle->post_title!=''){
				$xtralinks.='<a href="'.get_permalink(get_option('eshop_xtra_help')).'">'.$ptitle->post_title.'</a>, ';
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
        $url_dir=$dirs['baseurl'];
		$plugin_dir=ABSPATH.PLUGINDIR;
		$eshop_goto=$upload_dir.'/../eshop_downloads';
		$eshop_from=$plugin_dir.'/eshop/downloads';
		if(!file_exists($eshop_goto.'/.htaccess')){
			wp_mkdir_p( $upload_dir );
			wp_mkdir_p( $eshop_goto );
			if ($handle = opendir($eshop_from)) {
				/* This is the correct way to loop over the directory. */
				while (false !== ($file = readdir($handle))) {
					if($file!='' && $file!='.' && $file!='..'){
						copy($eshop_from.'/'.$file,$eshop_goto.'/'.$file);
						chmod($eshop_goto.'/'.$file,0666);
					}
				}
				closedir($handle);
			}
		}
		return $eshop_goto.'/';
    }
}
if (!function_exists('eshop_files_directory')) {
    function eshop_files_directory(){
        $dirs=wp_upload_dir();
        $upload_dir=$dirs['basedir'];
        $url_dir=$dirs['baseurl'];
       	$plugin_dir=ABSPATH.PLUGINDIR;
       	$eshop_goto=$upload_dir.'/eshop_files';
       	$eshop_from=$plugin_dir.'/eshop/files';
       	if(!file_exists($eshop_goto.'/eshop.css')){
			wp_mkdir_p( $upload_dir );
			wp_mkdir_p( $eshop_goto );
			if ($handle = opendir($eshop_from)) {
				/* This is the correct way to loop over the directory. */
				while (false !== ($file = readdir($handle))) {
					if($file!='' && $file!='.' && $file!='..'){
						copy($eshop_from.'/'.$file,$eshop_goto.'/'.$file);
						chmod($eshop_goto.'/'.$file,0666);
					}
				}
				closedir($handle);
			}
			$urlpath=$url_dir.'/eshop_files/';
			$urlpath=preg_replace('/\/wp-content\/blogs\.dir\/\d+/', '', $urlpath);
			$rtn=array(0=>$eshop_goto.'/',1=>$urlpath);
			return $rtn;
		}else{
			$urlpath=$url_dir.'/eshop_files/';
			$urlpath=preg_replace('/\/wp-content\/blogs\.dir\/\d+/', '', $urlpath);
			$rtn=array(0=>$eshop_goto.'/',1=>$urlpath);
			return $rtn;
		}
		return 0;
    }
}

if (!function_exists('eshop_get_images')) {
	function eshop_get_images($pID){
		$attargs = array(
			'post_type' => 'attachment',
			'numberposts' => null,
			'post_status' => null,
			'post_parent' => $pID
			); 
		$attachments = get_posts($attargs);
		$echo='';
		if ($attachments) {
			$echo=array();
			$x=0;
			foreach ($attachments as $attachment) {
				$img_url= wp_get_attachment_thumb_url($attachment->ID);
				/*
				//this section has been removed - its causing problems with wp2.5+ and hopefully is no longer required.
				$chkimg=wp_get_attachment_url($attachment->ID);
				if($img_url==$chkimg){
					$img_url = preg_replace('!(\.[^.]+)?$!', __('.thumbnail') . '$1', $img_url, 1);
				}else{
					$img_url = wp_get_attachment_thumb_url($attachment->ID);
				}
				*/
				@list($width, $height) = getimagesize($img_url);
				$echo[$x]['url']=$img_url;
				$echo[$x]['alt']=apply_filters('the_title', $attachment->post_title);
				//if there was an error we still want to show the picture!
				if($height=='' || $width=='')
				    $echo[$x]['size']='';
				else
    				$echo[$x]['size']='height="'.$height.'" width="'.$width.'"';
				$x++;
			}
		}
		return $echo;
	}
}
?>