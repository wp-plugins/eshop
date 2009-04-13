<?php
if ('checkout.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');


global $wpdb;

if (!function_exists('eshopShowform')) {
	function eshopShowform($first_name,$last_name,$company,$phone,$email,$address1,$address2,$city,$state,$zip,$country,$reference,$comments,$ship_name,$ship_company,$ship_phone,$ship_address,$ship_city,$ship_postcode,$ship_state,$ship_country){
	global $wpdb, $blog_id;
	if(get_option('eshop_shipping_zone')=='country'){
		$creqd='<span class="reqd">*</span>';
		$sreqd='';
	}else{
		$creqd='';
		$sreqd='<span class="reqd">*</span>';
	}
	$xtralinks=eshop_show_extra_links();

	$echo = '
	<div class="hr"></div>
	<div class="custdetails">
	<p><small class="privacy"><span class="reqd" title="Asterisk">*</span> '.__('Denotes Required Field ','eshop').'
	'.$xtralinks.'</small></p>
	<form action="'.wp_specialchars($_SERVER['REQUEST_URI']).'" method="post" class="eshopform">
	<fieldset><legend id="mainlegend">'. __('Please Enter Your Details','eshop').'<br />
	</legend><fieldset>';
	if('no' == get_option('eshop_downloads_only')){
		$echo .='<legend>'.__('Mailing Address','eshop').'</legend>';
	}else{
		$echo .='<legend>'.__('Contact Details','eshop').'</legend>';
	}
	$echo .='<label for="first_name">'.__('First Name','eshop').' <span class="reqd">*</span><br />
	  <input class="med" type="text" name="first_name" value="'.$first_name.'" id="first_name" maxlength="40" size="40" /></label><br />
	 <label for="last_name">'.__('Last Name','eshop').' <span class="reqd">*</span><br />
	  <input class="med" type="text" name="last_name" value="'.$last_name.'" id="last_name" maxlength="40" size="40" /></label><br />';
	if('no' == get_option('eshop_downloads_only')){
	$echo .='<label for="company">'.__('Company','eshop').'<br />
	  <input class="med" type="text" name="company" value="'.$company.'" id="company" size="40" /></label><br />';
	}
	$echo .='<label for="email">'.__('Email','eshop').' <span class="reqd">*</span><br />
	  <input class="med" type="text" name="email" value="'.$email.'" id="email" maxlength="40" size="40" /></label><br />';
	if('no' == get_option('eshop_downloads_only')){
		$echo .='<label for="phone">'.__('Phone','eshop').' <span class="reqd">*</span><br />
		  <input class="med" type="text" name="phone" value="'.$phone.'" id="phone" maxlength="30" size="30" /></label><br />
		 <label for="address1">'.__('Address','eshop').' <span class="reqd">*</span><br />
		  <input class="med" type="text" name="address1" id="address1" value="'.$address1.'" maxlength="40" size="40" /></label><br />
		 <label for="address2">'.__('Address','eshop').' (continued)<br />
		  <input class="med" type="text" name="address2" id="address2" value="'.$address2.'" maxlength="40" size="40" /></label><br />
		 <label for="city">'.__('City or town','eshop').' <span class="reqd">*</span><br />
		  <input class="med" type="text" name="city" value="'.$city.'" id="city" maxlength="40" size="40" /></label><br />'."\n";

		// state list from db
		$table=$wpdb->prefix.'eshop_states';
		$getstate=get_option('eshop_shipping_state');
		$List=$wpdb->get_results("SELECT code,stateName FROM $table WHERE list='$getstate' ORDER BY stateName",ARRAY_A);
		if(sizeof($List)>0){
			$echo .='<label for="state">'.__('State/County/Province','eshop').' '.$sreqd.'<br />
			  <select class="med pointer" name="state" id="state">';
			foreach($List as $key=>$value){
				$k=$value['code'];
				$v=$value['stateName'];
				$stateList[$k]=$v;
			}
			$echo .='<option value="" selected="selected">'.__('Please Select','eshop').'</option>';
			$echo .='<option value="">'.__('not applicable','eshop').'</option>';

			foreach($stateList as $code => $label)	{
				if (isset($state) && $state == $code){
					$echo.= "<option value=\"$code\" selected=\"selected\">$label</option>\n";
				}else{
					$echo.="<option value=\"$code\">$label</option>";
				}
			}
			$echo.= "</select></label><br />";
		}else{
			$echo .='<input type="hidden" name="state" value="" />';
		}
		$echo .= '
		 <label for="zip">'.__('Zip/Post code','eshop').' <span class="reqd">*</span><br />
		  <input class="short" type="text" name="zip" value="'.$zip.'" id="zip" maxlength="20" size="20" /></label><br />
		 <label for="country">'.__('Country','eshop').' '.$creqd.'<br />
		  <select class="med pointer" name="country" id="country">
		';
		// country list from db
		$tablec=$wpdb->prefix.'eshop_countries';
		$List=$wpdb->get_results("SELECT code,country FROM $tablec GROUP BY list,country",ARRAY_A);
		foreach($List as $key=>$value){
			$k=$value['code'];
			$v=$value['country'];
			$countryList[$k]=$v;
		}
		$echo .='<option value="" selected="selected">'.__('Select your Country','eshop').'</option>';
		foreach($countryList as $code => $label)	{
			if (isset($country) && $country == $code){
				$echo.= "<option value=\"$code\" selected=\"selected\">$label</option>\n";
			}else{
				$echo.="<option value=\"$code\">$label</option>";
			}
		}
		$echo.= "</select></label></fieldset>";
	}
	$echo .= '

	<fieldset>
	<legend>'.__('Additional information','eshop').'</legend>
	 <label for="reference">'.__('Reference or <dfn title="Purchase Order number">PO</dfn>','eshop').'<br />
	  <input type="text" class="med" name="reference" value="'.$reference.'" id="reference" size="30" /></label><br />
	 <label for="eshop-comments">'.__('Comments or special instructions','eshop').'<br />
	  <textarea class="textbox" name="comments" id="eshop-comments" cols="60" rows="5">'.$comments.'</textarea></label></fieldset>';

	if('no' == get_option('eshop_downloads_only')){
		$echo .='<fieldset>
		<legend>'.__('Shipping address (if different)','eshop').'</legend>
		 <label for="ship_name">'.__('Name','eshop').'<br />
		  <input class="med" type="text" name="ship_name" id="ship_name" value="'.$ship_name.'" maxlength="40" size="40" /></label><br />
		 <label for="ship_company">'.__('Company','eshop').'<br />
		  <input class="med" type="text" name="ship_company" value="'.$ship_company.'" id="ship_company" size="40" /></label><br />
		 <label for="ship_phone">'.__('Phone','eshop').'<br />
		  <input class="med" type="text" name="ship_phone" value="'.$ship_phone.'" id="ship_phone" maxlength="30" size="30" /></label><br />
		 <label for="ship_address">'.__('Address','eshop').'<br />
		  <input class="med" type="text" name="ship_address" id="ship_address" value="'.$ship_address.'" maxlength="40" size="40" /></label><br />
		 <label for="ship_city">'.__('City or town','eshop').'<br />
		  <input class="med" type="text" name="ship_city" id="ship_city" value="'.$ship_city.'" maxlength="40" size="40" /></label><br />'."\n";
		  
		if(isset($stateList) && sizeof($stateList)>0){
			$echo .='<label for="shipstate">'.__('State/County/Province','eshop').'<br />
			  <select class="med pointer" name="ship_state" id="shipstate">';
			//state list from db, as above
			$echo .='<option value="" selected="selected">'.__('Please Select','eshop').'</option>';
			$echo .='<option value="">'.__('not applicable','eshop').'</option>';
			foreach($stateList as $code => $label){
				if (isset($ship_state) && $ship_state == $code){
					$echo.="<option value=\"$code\" selected=\"selected\">$label</option>";
				}else{
					$echo.="<option value=\"$code\">$label</option>";
				}
			}
			$echo .= '</select></label><br />';
		}else{
			$echo .='<input type="hidden" name="ship_state" value="" />';
		}
		$final_price=number_format($_SESSION['final_price'.$blog_id], 2);
		$echo .='<label for="ship_postcode">'.__('Zip/Post Code','eshop').'<br />
		  <input class="short" type="text" name="ship_postcode" id="ship_postcode" value="'.$ship_postcode.'" maxlength="20" size="20" /></label>
		  <br />
		  <input type="hidden" name="amount" value="'.$final_price.'" />
		<label for="shipcountry">'.__('Country','eshop').'<br />
		  <select class="med pointer" name="ship_country" id="shipcountry">
		';
		$echo .='<option value="" selected="selected">'.__('Select your Country','eshop').'</option>';
		foreach($countryList as $code => $label)	{
			if (isset($ship_country) && $ship_country == $code){
				$echo.= "<option value=\"$code\" selected=\"selected\">$label</option>\n";
			}else{
				$echo.="<option value=\"$code\">$label</option>";
			}
		}
		$echo.= "</select></label>";
	}
	$x=0;
	$discounttotal=0;
	foreach ($_SESSION['shopcart'.$blog_id] as $productid => $opt){
		$x++;
		$echo.= "\n  <input type=\"hidden\" name=\"item_name_".$x."\" value=\"".$opt['pname']."\" />";
	//	$echo.= "\n  <input type=\"hidden\" name=\"".$itemoption.$x."\" value=\"".$opt['size']."\" />";
		$echo.= "\n  <input type=\"hidden\" name=\"quantity_".$x."\" value=\"".$opt['qty']."\" />";
		/* DISCOUNT */
		$amt=number_format(round($opt["price"], 2),2);
		if(is_discountable(calculate_total())!=0){
			$discount=is_discountable(calculate_total())/100;
			$amt = number_format(round($amt-($amt * $discount), 2),2);
		}
		$echo.= "\n  <input type=\"hidden\" name=\"amount_".$x."\" value=\"".$amt."\" />";
		$echo.= "\n  <input type=\"hidden\" name=\"item_number_".$x."\" value=\"".$opt['pid']." : ".$opt['item']."\" />";
		$echo.= "\n  <input type=\"hidden\" name=\"postid_".$x."\" value=\"".$opt['postid']."\" />";
	}
	$echo.= "\n  <input type=\"hidden\" name=\"numberofproducts\" value=\"".$x."\" />";
	$echo .= '</fieldset>';
	if('no' == get_option('eshop_downloads_only')){
		$echo .='<label for="submitit"><small>'.__('<strong>Note:</strong> Submit to show shipping charges.','eshop').'</small></label><br />';
	}
	
	if(eshop_discount_codes_check()){
		if(!isset($eshop_discount)) $eshop_discount='';
		$echo .='<p><label for="eshop_discount">'.__('Discount Code (case sensitive)','eshop').'</label><br />
	  	<input class="med" type="text" name="eshop_discount" value="'.$eshop_discount.'" id="eshop_discount" size="40" /></p>'."\n";
	}
	
	
	$echo .='<input type="submit" class="button" id="submitit" name="submit" value="'.__('Proceed to Confirmation &raquo;','eshop').'" />
	</fieldset>
	</form>
	</div>
	';
	if(get_bloginfo('version')<'2.5.1')
		remove_filter('the_content', 'wpautop');
		
	return $echo;
	}
}
if (!function_exists('eshop_checkout')) {
 	function eshop_checkout($_POST){
 		global $blog_id;
		$echoit='';
		include_once(ABSPATH.'wp-includes/wp-db.php');
		include_once ABSPATH.PLUGINDIR."/eshop/cart-functions.php";
		$paymentmethod=get_option('eshop_method');

		global $wpdb;

		//left over from previous script, leaving in just in case another payment method is used.
		$chkerror=0;
		$numberofproducts=0;

		//on windows this check isn't working correctly, so I've added ==0 
		if (get_magic_quotes_gpc()) {
			$_COOKIE = stripslashes_array($_COOKIE);
			$_FILES = stripslashes_array($_FILES);
			$_GET = stripslashes_array($_GET);
			$_POST = stripslashes_array($_POST);
			$_REQUEST = stripslashes_array($_REQUEST);
		}

		//sanitise, ie encode all special entities - neat huh! - but possibly messing up paypal
		//$_POST=sanitise_array($_POST);

		// if everything went ok do the following, hopefully the rest won't happen!
		if(isset($_GET['action'])){
			if($_GET['action']=='success'){
				include(ABSPATH.PLUGINDIR.'/eshop/'.$paymentmethod.'.php');
			}
		}

		include(ABSPATH.PLUGINDIR.'/eshop/'.$paymentmethod.'/index.php');

		if(isset($_SESSION['shopcart'.$blog_id])){
			$shopcart=$_SESSION['shopcart'.$blog_id];
			$numberofproducts=sizeof($_SESSION['shopcart'.$blog_id]);
			$productsandqty='';
			while (list ($product, $amount) = each ($_SESSION['shopcart'.$blog_id])){
				$productsandqty.=" $product-$amount";
				$productsandqty=trim($productsandqty);
			}
			$keys = array_keys($_SESSION['shopcart'.$blog_id]);
			$productidkeys=implode(",", $keys);
			$productidkeys=trim($productidkeys);
			//reqd for shipping - finds the correct state for working out shipping, and set things up for later usage.
			if(isset($_POST['ship_name'])){
				if($_POST['ship_name']!='' || $_POST['ship_address']!='' 
				|| $_POST['ship_city']!='' || $_POST['ship_postcode']!=''
				|| $_POST['ship_company']!='' || $_POST['ship_phone']!=''
				|| $_POST['ship_country']!='' || $_POST['ship_state']!=''){
					if($_POST['ship_name']==''){
						$_POST['ship_name']=$_POST['first_name']." ".$_POST['last_name'];
					}
					if($_POST['ship_company']==''){
						$_POST['ship_company']=$_POST['company'];
					}
					if($_POST['ship_phone']==''){
						$_POST['ship_phone']=$_POST['phone'];
					}
					if($_POST['ship_address']==''){
						$_POST['ship_address']=$_POST['address1'];
						if($_POST['address2']!=''){
							$_POST['ship_address'].=", ".$_POST['address2'];
						}
					}
					if($_POST['ship_city']==''){
						$_POST['ship_city']=$_POST['city'];
					}
					if($_POST['ship_postcode']==''){
						$_POST['ship_postcode']=$_POST['zip'];
					}
					if($_POST['ship_country']==''){
						$_POST['ship_country']=$_POST['country'];
					}
					if($_POST['ship_state']==''){
						$_POST['ship_state']=$_POST['state'];
					}
				}else{
					$_POST['ship_name']=$_POST['first_name']." ".$_POST['last_name'];
					$_POST['ship_company']=$_POST['company'];
					$_POST['ship_phone']=$_POST['phone'];
					if($_POST['ship_address']==''){
						$_POST['ship_address']=$_POST['address1'];
						if($_POST['address2']!=''){
							$_POST['ship_address'].=", ".$_POST['address2'];
						}
					}
					$_POST['ship_city']=$_POST['city'];
					$_POST['ship_postcode']=$_POST['zip'];
					$_POST['ship_country']=$_POST['country'];
					$_POST['ship_state']=$_POST['state'];
				}

				if(get_option('eshop_shipping_zone')=='country'){
					if($_POST['ship_country']!=''){
						$pzone=$_POST['ship_country'];
					}else{
						$pzone=$_POST['country'];
					}
				}else{
					if($_POST['ship_state']!=''){
						$pzone=$_POST['ship_state'];
					}else{
						$pzone=$_POST['state'];
					}
				}
		}else{
			$pzone='';
		}
		//
		$shiparray=array();
		foreach ($_SESSION['shopcart'.$blog_id] as $productid => $opt){
			if(is_array($opt)){
				switch(get_option('eshop_shipping')){
				case '1'://( per quantity of 1, prices reduced for additional items )
					for($i=1;$i<=$opt['qty'];$i++){
						array_push($shiparray, $opt["pclas"]);
					}
					break;
				case '2'://( once per shipping class no matter what quantity is ordered )
					if(!in_array($opt["pclas"], $shiparray)) {
						array_push($shiparray, $opt["pclas"]);
					}
					break;
				case '3'://( one overall charge no matter how many are ordered )
					if(!in_array($opt["pclas"], $shiparray)) {
						array_push($shiparray, 'A');
					}
					break;
				}
			}
		}
		//need to check the discount codes here as well:
		if(eshop_discount_codes_check()){
			$_SESSION['eshop_discount'.$blog_id]='';
			unset($_SESSION['eshop_discount'.$blog_id]);
			if(isset($_POST['eshop_discount']) && $_POST['eshop_discount']!=''){
				$chkcode=valid_eshop_discount_code($_POST['eshop_discount']);
				if($chkcode)
					$_SESSION['eshop_discount'.$blog_id]=$_POST['eshop_discount'];
			}
		}
		//show the cart
		if((isset($_GET['action']) && $_GET['action']!='redirect')||!isset($_GET['action'])){
			$echoit.= display_cart($_SESSION['shopcart'.$blog_id], false,get_option('eshop_checkout'),$pzone,$shiparray);
		}
	}

	if (isset ($_POST['submit'])) {
		//form handling

		foreach($_POST as $key=>$value) {
			$key = $value;
			}
		$error='';		
		if(isset($_POST['first_name'])){
			$valid=checkAlpha($_POST['first_name']);
			if($valid==FALSE){
				$error.= '<li>'.__('<strong>First name</strong> - missing or incorrect.','eshop').'</li>';
			}
		}
		if(isset($_POST['last_name'])){
				$valid=checkAlpha($_POST['last_name']);
				if($valid==FALSE) {
					$error.= '<li>'.__('<strong>Last name</strong> - missing or incorrect.','eshop').'</li>';
				}
		}
		if(isset($_POST['email'])){
				$valid=checkEmail($_POST['email']);
				if($valid==FALSE){
					$error.= '<li>'.__('<strong>Email address</strong> - missing or incorrect.','eshop').'</li>';
				}
		}
		if(isset($_POST['phone'])){
				$valid=checkPhone($_POST['phone']);
				if($valid==FALSE){
					$error.= '<li>'.__('<strong>Phone Number</strong> - missing or incorrect','eshop').'.</li>';
				}
		}
		if(isset($_POST['address1'])){
				$valid=checkAlpha($_POST['address1']);
				if($valid==FALSE){
					$error.= '<li>'.__('<strong>Address</strong> - missing or incorrect.','eshop').'</li>';
				}
		}
		if(isset($_POST['city'])){
				$valid=checkAlpha($_POST['city']);
				if($valid==FALSE){
					$error.= '<li>'.__('<strong>City or town</strong> - missing or incorrect.','eshop').'</li>';
				}
		}
		if(get_option('eshop_shipping_zone')=='country'){
			if(isset($_POST['country'])){
				$valid=checkAlpha($_POST['country']);
				if($valid==FALSE){
					$error.= '<li>'.__('<strong>Country</strong> - missing or incorrect.','eshop').'</li>';
				}
			}
		}else{
			if(isset($_POST['state'])){
				$valid=checkAlpha($_POST['state']);
				if($valid==FALSE){
					$error.= '<li>'.__('<strong>State/County/Province</strong> - missing or incorrect.','eshop').'</li>';
				}
			}
		}
		
		if(isset($_POST['country']) && $_POST['country']=='US' && $_POST['state']==''){
			//must pick a state for US deliveries
				$error.= '<li>'.__('<strong><abbr title="United States">US</abbr> State</strong> - missing or incorrect.','eshop').'</li>';
		}
		if(isset($_POST['zip'])){
				$valid=checkAlphaNum($_POST['zip']);
				if($valid==FALSE){
					$error.= '<li>'.__('<strong>Zip/Post code</strong> - missing or incorrect.','eshop').'</li>';
				}
		}
		if(eshop_discount_codes_check()){
			$_SESSION['eshop_discount'.$blog_id]='';
			unset($_SESSION['eshop_discount'.$blog_id]);
			if(isset($_POST['eshop_discount']) && $_POST['eshop_discount']!=''){
				$chkcode=valid_eshop_discount_code($_POST['eshop_discount']);
				if(!$chkcode)
					$error.= '<li>'.__('<strong>Discount Code</strong> - is not valid.','eshop').'</li>';
				else
					$_SESSION['eshop_discount'.$blog_id]=$_POST['eshop_discount'];
			}
		}

		if($error!=''){
				$echoit.= "<p><strong class=\"error\">".__('There were some errors with the details you entered&#8230;','eshop')."</strong></p><ul class=\"errors\">".$error.'</ul>';
				$first_name=$_POST['first_name'];
				$last_name=$_POST['last_name'];
				$company=$_POST['company'];
				$phone=$_POST['phone'];
				$reference=$_POST['reference'];
				$email=$_POST['email'];
				$address1=$_POST['address1'];
				$address2=$_POST['address2'];
				$city=$_POST['city'];
				$country=$_POST['country'];
				$state=$_POST['state'];
				$zip=$_POST['zip'];
				$ship_name=$_POST['ship_name'];
				$ship_company=$_POST['ship_company'];
				$ship_phone=$_POST['ship_phone'];
				$ship_address=$_POST['ship_address'];
				$ship_city=$_POST['ship_city'];
				$ship_country=$_POST['ship_country'];
				$ship_state=$_POST['ship_state'];
				$ship_postcode=$_POST['ship_postcode'];
				$comments=$_POST['comments'];
				$chkerror='1';
		}else{
			if(!isset($_GET['action'])){
				$echoit.= "<div class=\"hr\"></div><h3>".__('<span class="noprint">Please Confirm </span>Your Details','eshop').'</h3>';
				// create a custom id, and shove details in database
				$date=date('YmdHis');
				$_SESSION['date'.$blog_id]=$date;
				$fprice=number_format($_SESSION['final_price'.$blog_id], 2);
				$_POST['amount']=$fprice;
				$_POST['custom']=$date;
				$_POST['numberofproducts']=sizeof($_SESSION['shopcart'.$blog_id]);
				//shipping - replicated here, but currently easier than a function
				$shiparray=array();
				foreach ($_SESSION['shopcart'.$blog_id] as $productid => $opt){
					if(is_array($opt)){
						switch(get_option('eshop_shipping')){
						case '1'://( per quantity of 1, prices reduced for additional items )
							for($i=1;$i<=$opt['qty'];$i++){
								array_push($shiparray, $opt["pclas"]);
							}
							break;
						case '2'://( once per shipping class no matter what quantity is ordered )
							if(!in_array($opt["pclas"], $shiparray)) {
								array_push($shiparray, $opt["pclas"]);
							}
							break;
						case '3'://( one overall charge no matter how many are ordered )
							if(!in_array($opt["pclas"], $shiparray)) {
								array_push($shiparray, 'A');
							}
							break;
						}
					}
				}
				//shipping for form.
				if(get_option('eshop_shipping_zone')=='country'){
					$tablec=$wpdb->prefix.'eshop_countries';
				}else{
					$tablec=$wpdb->prefix.'eshop_states';
				}
				$table2=$wpdb->prefix.'eshop_shipping_rates';
				$tempshiparray=array();
				$shipping=0;
				switch(get_option('eshop_shipping')){
					case '1'://( per quantity of 1, prices reduced for additional items )
						foreach ($shiparray as $nowt => $shipclass){
							//add to temp array for shipping
							if(!in_array($shipclass, $tempshiparray)) {
								if($shipclass!='F'){
									array_push($tempshiparray, $shipclass);
									$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $tablec WHERE code='$pzone' limit 1");
									$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='$shipclass' and items='1' limit 1");
									$shipping+=$shipcost;
								}
							}else{
								if($shipclass!='F'){
									$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $tablec WHERE code='$pzone' limit 1");
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
									$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $tablec WHERE code='$pzone' limit 1");
									$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='$shipclass' and items='1' limit 1");
									$shipping+=$shipcost;
								}
							}
						}
						break;
					case '3'://( one overall charge no matter how many are ordered )
						$shipzone = 'zone'.$wpdb->get_var("SELECT zone FROM $tablec WHERE code='$pzone' limit 1");
						$shipcost = $wpdb->get_var("SELECT $shipzone FROM $table2 WHERE class='A' and items='1' limit 1");
						$shipping+=$shipcost;
						break;
				}
				//discount shipping
				if(is_shipfree(calculate_total())) $shipping=0;

				//shipping
				$_POST['shipping_1']=$shipping;
				$ctable=$wpdb->prefix.'eshop_countries';
				$stable=$wpdb->prefix.'eshop_states';
				if('no' == get_option('eshop_downloads_only')){
					$echoit.='<h4>'.__('Mailing Address','eshop').'</h4><ul>';
				}else{
					$echoit.='<h4>'.__('Contact Details','eshop').'</h4><ul>';
				}
				$echoit.= "<li><span class=\"items\">".__('Full name:','eshop')."</span> ".$_POST['first_name']." ".$_POST['last_name']."</li>\n";
				if('no' == get_option('eshop_downloads_only')){
					$echoit.= "<li><span class=\"items\">".__('Company:','eshop')."</span> ".$_POST['company']."</li>\n";
				}
				$echoit.= "<li><span class=\"items\">".__('Email:','eshop')."</span> ".$_POST['email']."</li>\n";
				if('no' == get_option('eshop_downloads_only')){
					$echoit.= "<li><span class=\"items\">".__('Phone:','eshop')."</span> ".$_POST['phone']."</li>\n";
					$echoit.= "<li><span class=\"items\">".__('Address:','eshop')."</span> ".$_POST['address1']." ".$_POST['address2']."</li>\n";
					$echoit.= "<li><span class=\"items\">".__('City or town:','eshop')."</span> ".$_POST['city']."</li>\n";
					$qcode=$wpdb->escape($_POST['state']);
					$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
					if($qstate!='')
						$echoit.= "<li><span class=\"items\">".__('State/County/Province:','eshop')."</span> ".$qstate."</li>\n";
					$echoit.= "<li><span class=\"items\">".__('Zip/Post code:','eshop')."</span> ".$_POST['zip']."</li>\n";
					$qccode=$wpdb->escape($_POST['country']);
					$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qccode' limit 1");
					$echoit.= "<li><span class=\"items\">".__('Country:','eshop')."</span> ".$qcountry."</li>\n";
				}
				$echoit.= "</ul>\n";

				if( (trim($_POST['reference'])!='') && trim($_POST['comments'])==''){
					$echoit.= "<h4>".__('Additional information','eshop')."</h4>\n<ul>\n";
					$echoit.= '<li><span class="items">'.__('Reference or PO:','eshop').'</span> '.$_POST['reference'].'</li>'."\n";
					$echoit.= '</ul>'."\n";
				}
				if( (trim($_POST['reference'])=='') && trim($_POST['comments'])!=''){
					$echoit.= "<h4>".__('Additional information','eshop')."</h4>\n<ul>\n";
					$echoit.= '<li><span class="items">'.__('Comments or instructions:','eshop').'</span> '.$_POST['comments'].'</li>'."\n";
					$echoit.= '</ul>'."\n";
				}
				if( (trim($_POST['reference'])!='') && trim($_POST['comments'])!=''){
					$echoit.= "<h4>".__('Additional information','eshop')."</h4>\n<ul>\n";
					$echoit.= '<li><span class="items">'.__('Reference or PO:','eshop').'</span> '.$_POST['reference'].'</li>'."\n";
					$echoit.= '<li><span class="items">'.__('Comments or instructions:','eshop').'</span> '.$_POST['comments'].'</li>'."\n";
					$echoit.= '</ul>'."\n";
				}
				if('no' == get_option('eshop_downloads_only')){
					if($_POST['ship_name']!='' || $_POST['ship_address']!='' || $_POST['ship_city']!='' || $_POST['ship_postcode']!=''){
						$echoit.= "<h4>".__('Shipping Address','eshop')."</h4>\n<ul>\n";
						$echoit.= "<li><span class=\"items\">".__('Full name:','eshop')."</span> ".$_POST['ship_name']."</li>\n";
						$echoit.= "<li><span class=\"items\">".__('Company:','eshop')."</span> ".$_POST['ship_company']."</li>\n";
						$echoit.= "<li><span class=\"items\">".__('Phone:','eshop')."</span> ".$_POST['ship_phone']."</li>\n";
						$echoit.= "<li><span class=\"items\">".__('Address:','eshop')."</span> ".$_POST['ship_address']."</li>\n";
						$echoit.= "<li><span class=\"items\">".__('City or town:','eshop')."</span> ".$_POST['ship_city']."</li>\n";
						$qcode=$wpdb->escape($_POST['ship_state']);
						$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
						if($qstate!='')
							$echoit.= "<li><span class=\"items\">".__('State/County/Province:','eshop')."</span> ".$qstate."</li>\n";
						$echoit.= "<li><span class=\"items\">".__('Zip/Post code:','eshop')."</span> ".$_POST['ship_postcode']."</li>\n";
						$qccode=$wpdb->escape($_POST['ship_country']);
						$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qccode' limit 1");
						$echoit.= "<li><span class=\"items\">".__('Country:','eshop')."</span> ".$qcountry."</li>\n";
						$echoit.= "</ul>\n";
					}
				}
				$echoit.= "\n";
			}
			//add to a session to store address:
			$_SESSION['addy'.$blog_id]['first_name']=$_POST['first_name'];
			$_SESSION['addy'.$blog_id]['last_name']=$_POST['last_name'];
			$_SESSION['addy'.$blog_id]['company']=$_POST['company'];
			$_SESSION['addy'.$blog_id]['phone']=$_POST['phone'];
			$_SESSION['addy'.$blog_id]['reference']=$_POST['reference'];
			$_SESSION['addy'.$blog_id]['email']=$_POST['email'];
			$_SESSION['addy'.$blog_id]['address1']=$_POST['address1'];
			$_SESSION['addy'.$blog_id]['address2']=$_POST['address2'];
			$_SESSION['addy'.$blog_id]['city']=$_POST['city'];
			$_SESSION['addy'.$blog_id]['country']=$_POST['country'];
			$_SESSION['addy'.$blog_id]['state']=$_POST['state'];
			$_SESSION['addy'.$blog_id]['zip']=$_POST['zip'];
			$_SESSION['addy'.$blog_id]['ship_name']=$_POST['ship_name'];
			$_SESSION['addy'.$blog_id]['ship_company']=$_POST['ship_company'];
			$_SESSION['addy'.$blog_id]['ship_phone']=$_POST['ship_phone'];
			$_SESSION['addy'.$blog_id]['ship_address']=$_POST['ship_address'];
			$_SESSION['addy'.$blog_id]['ship_city']=$_POST['ship_city'];
			$_SESSION['addy'.$blog_id]['ship_country']=$_POST['ship_country'];
			$_SESSION['addy'.$blog_id]['ship_state']=$_POST['ship_state'];
			$_SESSION['addy'.$blog_id]['ship_postcode']=$_POST['ship_postcode'];
			$_SESSION['addy'.$blog_id]['comments']=$_POST['comments'];
			
			//grab all the POST variables and store in cookie
			$array=$_POST;
			//but first make a few extra equal nothing
			//add others in here if needed
			$array['comments']=$array['reference']='';
			$biscuits=eshop_build_cookie($array);
			setcookie("eshopcart", $biscuits,time()+60*60*24*365);
			include(ABSPATH.PLUGINDIR.'/eshop/'.$paymentmethod.'.php');
		}
	}else{
		//for first time form usage.
		if(isset($_SESSION['addy'.$blog_id])){
			$first_name=$_SESSION['addy'.$blog_id]['first_name'];
			$last_name=$_SESSION['addy'.$blog_id]['last_name'];
			$company=$_SESSION['addy'.$blog_id]['company'];
			$phone=$_SESSION['addy'.$blog_id]['phone'];
			$reference=$_SESSION['addy'.$blog_id]['reference'];
			$email=$_SESSION['addy'.$blog_id]['email'];
			$address1=$_SESSION['addy'.$blog_id]['address1'];
			$address2=$_SESSION['addy'.$blog_id]['address2'];
			$city=$_SESSION['addy'.$blog_id]['city'];
			$country=$_SESSION['addy'.$blog_id]['country'];
			$state=$_SESSION['addy'.$blog_id]['state'];
			$zip=$_SESSION['addy'.$blog_id]['zip'];
			$ship_name=$_SESSION['addy'.$blog_id]['ship_name'];
			$ship_company=$_SESSION['addy'.$blog_id]['ship_company'];
			$ship_phone=$_SESSION['addy'.$blog_id]['ship_phone'];
			$ship_address=$_SESSION['addy'.$blog_id]['ship_address'];
			$ship_city=$_SESSION['addy'.$blog_id]['ship_city'];
			$ship_country=$_SESSION['addy'.$blog_id]['ship_country'];
			$ship_state=$_SESSION['addy'.$blog_id]['ship_state'];
			$ship_postcode=$_SESSION['addy'.$blog_id]['ship_postcode'];
			$comments=$_SESSION['addy'.$blog_id]['comments'];
		}else{
			$first_name='';
			$last_name='';
			$company='';
			$phone='';
			$reference='';
			$email='';
			$address1='';
			$address2='';
			$city='';
			$country='';
			$state='';
			$zip='';
			$ship_name='';
			$ship_company='';
			$ship_phone='';
			$ship_address='';
			$ship_city='';
			$ship_postcode='';
			$ship_country='';
			$ship_state='';
			$comments='';
			if(isset($_COOKIE["eshopcart"]) && calculate_items()!=0){
			$crumbs=eshop_break_cookie($_COOKIE["eshopcart"]);
				foreach($crumbs as $k=>$v){
					$$k=$v;
				}
			}
		}
	}

	if($chkerror!=0 || (!isset ($_POST['submit'])) && $numberofproducts>=1){
		// only show form if not filled in.
		$echoit.= eshopShowform($first_name,$last_name,$company,$phone,$email,$address1,$address2,$city,$state,$zip,$country,$reference,$comments,$ship_name,$ship_company,$ship_phone,$ship_address,$ship_city,$ship_postcode,$ship_state,$ship_country);
	}

	if(isset($_SESSION['shopcart'.$blog_id])){
		if($chkerror==0 && !isset($_GET['action'])){
			$echoit.='<ul class="continue-proceed"><li><a href="'.get_permalink(get_option('eshop_cart')).'">'.__('&laquo; Edit Cart or Continue Shopping','eshop').'</a></li></ul>';
		}else{	
			$echoit.='<ul class="continue-proceed"><li><a href="'.get_permalink(get_option('eshop_checkout')).'">'.__('&laquo; Edit Details or Continue Shopping','eshop').'</a></li></ul>';
		}
	}else{
		$echoit.= "<p><strong class=\"error\">".__('Your shopping cart is currently empty.','eshop')."</strong></p>";
	}
	return $echoit;
 }
}
?>