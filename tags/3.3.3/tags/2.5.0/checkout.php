<?php
if ('checkout.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');


global $wpdb;

if (!function_exists('eshopShowform')) {
	function eshopShowform($first_name,$last_name,$company,$phone,$email,$address1,$address2,$city,$state,$zip,$country,$reference,$comments,$ship_name,$ship_company,$ship_phone,$ship_address,$ship_city,$ship_postcode,$ship_state,$ship_country){
	global $wpdb;
	if(get_option('eshop_shipping_zone')=='country'){
		$creqd='<span class="reqd">*</span>';
		$sreqd='';
	}else{
		$creqd='';
		$sreqd='<span class="reqd">*</span>';
	}
	$xtralinks=eshop_show_extra_links();

$echo = <<<EOT
<div class="hr"></div>
<div class="custdetails">
<p><small class="privacy"><span class="reqd" title="Asterisk">*</span> Denotes Required Field - 
$xtralinks</small></p>
<form action="" method="post" class="eshopform">
<fieldset><legend id="mainlegend">Please Enter Your Details<br />
</legend><fieldset>
<legend>Mailing Address</legend>
 <label for="first_name">First Name <span class="reqd">*</span><br />
  <input class="med" type="text" name="first_name" value="$first_name" id="first_name" maxlength="40" size="40" /></label><br />
 <label for="last_name">Last Name <span class="reqd">*</span><br />
  <input class="med" type="text" name="last_name" value="$last_name" id="last_name" maxlength="40" size="40" /></label><br />
 <label for="company">Company<br />
  <input class="med" type="text" name="company" value="$company" id="company" size="40" /></label><br />
 <label for="email">Email <span class="reqd">*</span><br />
  <input class="med" type="text" name="email" value="$email" id="email" maxlength="40" size="40" /></label><br />
 <label for="phone">Phone <span class="reqd">*</span><br />
  <input class="med" type="text" name="phone" value="$phone" id="phone" maxlength="30" size="30" /></label><br />
 <label for="address1">Address <span class="reqd">*</span><br />
  <input class="med" type="text" name="address1" id="address1" value="$address1" maxlength="40" size="40" /></label><br />
 <label for="address2">Address (continued)<br />
  <input class="med" type="text" name="address2" id="address2" value="$address2" maxlength="40" size="40" /></label><br />
 <label for="city">City or town <span class="reqd">*</span><br />
  <input class="med" type="text" name="city" value="$city" id="city" maxlength="40" size="40" /></label><br />
 <label for="state"><abbr title="United States">US</abbr> State $sreqd<br />
  <select class="med pointer" name="state" id="state">
EOT;
// state list from db
$table=$wpdb->prefix.'eshop_states';
$List=$wpdb->get_results("SELECT code,stateName FROM $table ORDER BY stateName",ARRAY_A);
foreach($List as $key=>$value){
	$k=$value['code'];
	$v=$value['stateName'];
	$stateList[$k]=$v;
}
$echo .='<option value="" selected="selected">Select your state</option>';
$echo .='<option value="">not applicable</option>';

foreach($stateList as $code => $label)	{
	if (isset($state) && $state == $code){
		$echo.= "<option value=\"$code\" selected=\"selected\">$label</option>\n";
	}else{
		$echo.="<option value=\"$code\">$label</option>";
	}
}
$echo.= "</select></label><br />";

$echo .= <<<EOT
 <label for="zip">Zip/Post code <span class="reqd">*</span><br />
  <input class="short" type="text" name="zip" value="$zip" id="zip" maxlength="20" size="20" /></label><br />
 <label for="country">Country $creqd<br />
  <select class="med pointer" name="country" id="country">
EOT;
// country list from db
$tablec=$wpdb->prefix.'eshop_countries';
$List=$wpdb->get_results("SELECT code,country FROM $tablec ORDER BY country",ARRAY_A);
foreach($List as $key=>$value){
	$k=$value['code'];
	$v=$value['country'];
	$countryList[$k]=$v;
}
$echo .='<option value="" selected="selected">Select your Country</option>';
foreach($countryList as $code => $label)	{
	if (isset($country) && $country == $code){
		$echo.= "<option value=\"$code\" selected=\"selected\">$label</option>\n";
	}else{
		$echo.="<option value=\"$code\">$label</option>";
	}
}
$echo.= "</select></label></fieldset>";

$echo .= <<<EOT

<fieldset>
<legend>Additional information</legend>
 <label for="reference">Reference or <dfn title="Purchase Order number">PO</dfn><br />
  <input type="text" class="med" name="reference" value="$reference" id="reference" size="30" /></label><br />
 <label for="eshop-comments">Comments or special instructions<br />
  <textarea class="textbox" name="comments" id="eshop-comments" cols="60" rows="5">$comments</textarea></label></fieldset>

<fieldset>
<legend>Shipping address (if different)</legend>
 <label for="ship_name">Name<br />
  <input class="med" type="text" name="ship_name" id="ship_name" value="$ship_name" maxlength="40" size="40" /></label><br />
 <label for="ship_company">Company<br />
  <input class="med" type="text" name="ship_company" value="$ship_company" id="ship_company" size="40" /></label><br />
 <label for="ship_phone">Phone<br />
  <input class="med" type="text" name="ship_phone" value="$ship_phone" id="ship_phone" maxlength="30" size="30" /></label><br />
 <label for="ship_address">Address<br />
  <input class="med" type="text" name="ship_address" id="ship_address" value="$ship_address" maxlength="40" size="40" /></label><br />
 <label for="ship_city">City or town<br />
  <input class="med" type="text" name="ship_city" id="ship_city" value="$ship_city" maxlength="40" size="40" /></label><br />
 <label for="shipstate"><abbr title="United States">US</abbr> State<br />
  <select class="med pointer" name="ship_state" id="shipstate">
EOT;
//state list from db, as above
$echo .='<option value="" selected="selected">Select your state</option>';
$echo .='<option value="">not applicable</option>';
foreach($stateList as $code => $label){
	if (isset($ship_state) && $ship_state == $code){
		$echo.="<option value=\"$code\" selected=\"selected\">$label</option>";
	}else{
		$echo.="<option value=\"$code\">$label</option>";
	}
}
$final_price=number_format($_SESSION['final_price'], 2);
$echo .= <<<EOT
</select></label><br />
 <label for="ship_postcode">Zip/Post Code<br />
  <input class="short" type="text" name="ship_postcode" id="ship_postcode" value="$ship_postcode" maxlength="20" size="20" /></label>
  <br />
  <input type="hidden" name="amount" value="$final_price" />
<label for="shipcountry">Country<br />
  <select class="med pointer" name="ship_country" id="shipcountry">
EOT;
$echo .='<option value="" selected="selected">Select your Country</option>';
foreach($countryList as $code => $label)	{
	if (isset($ship_country) && $ship_country == $code){
		$echo.= "<option value=\"$code\" selected=\"selected\">$label</option>\n";
	}else{
		$echo.="<option value=\"$code\">$label</option>";
	}
}
$echo.= "</select></label>";

$x=0;
foreach ($_SESSION['shopcart'] as $productid => $opt){
	$x++;
	$echo.= "\n  <input type=\"hidden\" name=\"item_name_".$x."\" value=\"".$opt['pname']."\" />";
	$echo.= "\n  <input type=\"hidden\" name=\"".$itemoption.$x."\" value=\"".$opt['size']."\" />";
	$echo.= "\n  <input type=\"hidden\" name=\"quantity_".$x."\" value=\"".$opt['qty']."\" />";
	$echo.= "\n  <input type=\"hidden\" name=\"amount_".$x."\" value=\"".number_format($opt["price"], 2)."\" />";
	$echo.= "\n  <input type=\"hidden\" name=\"item_number_".$x."\" value=\"".$opt['pid']." : ".$opt['item']."\" />";
	
	$echo.= "\n  <input type=\"hidden\" name=\"postid_".$x."\" value=\"".$opt['postid']."\" />";

}

$echo .= <<<EOT
</fieldset>
<label for="submitit">
  <small><strong>Note:</strong> Submit to show shipping charges.</small><br />
   <input type="submit" class="button" id="submitit" name="submit" value="Proceed to Confirmation &raquo;" /></label>
</fieldset>
</form>
</div>
EOT;
	return $echo;
	}
}
if (!function_exists('eshop_checkout')) {
 function eshop_checkout($_POST){
	$echoit='';
	include_once('wp-includes/wp-db.php');
	include_once "cart-functions.php";
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
			include($paymentmethod.'.php');
		}
	}
		
	include($paymentmethod.'/index.php');

	if(isset($_SESSION['shopcart'])){
		$shopcart=$_SESSION['shopcart'];
		$numberofproducts=sizeof($_SESSION['shopcart']);
		$productsandqty='';
		while (list ($product, $amount) = each ($_SESSION['shopcart'])){
			$productsandqty.=" $product-$amount";
			$productsandqty=trim($productsandqty);
		}
		$keys = array_keys($_SESSION['shopcart']);
		$productidkeys=implode(",", $keys);
		$productidkeys=trim($productidkeys);
		//reqd for shipping - finds the correct state for working out shipping, and set things up for later usage.
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

		//
		$shiparray=array();
		foreach ($_SESSION['shopcart'] as $productid => $opt){
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
		//show the cart
		if((isset($_GET['action']) && $_GET['action']!='redirect')||!isset($_GET['action'])){
			$echoit.= display_cart($_SESSION['shopcart'], false,get_option('eshop_checkout'),$pzone,$shiparray);
		}
	}

	if (isset ($_POST['submit'])) {
		//form handling

		foreach($_POST as $key=>$value) {
			$key = $value;
			}
			$chkerror=0;
			$error="<p><strong class=\"error\">There were some errors with the details you entered&#8230;</strong></p><ul class=\"errors\">";
		if(isset($_POST['first_name'])){
			$valid=checkAlpha($_POST['first_name']);
			if($valid==FALSE){
				$error.= "<li><strong>First name</strong> - missing or incorrect.</li>";
				$chkerror++;
			}
		}
		if(isset($_POST['last_name'])){
				$valid=checkAlpha($_POST['last_name']);
				if($valid==FALSE) {
					$error.= "<li><strong>Last name</strong> - missing or incorrect.</li>";
					$chkerror++;
				}
		}
		if(isset($_POST['email'])){
				$valid=checkEmail($_POST['email']);
				if($valid==FALSE){
					$error.= "<li><strong>Email address</strong> - missing or incorrect.</li>";
					$chkerror++;
				}
		}
		if(isset($_POST['phone'])){
				$valid=checkPhone($_POST['phone']);
				if($valid==FALSE){
					$error.= "<li><strong>Phone Number</strong> - missing or incorrect.</li>";
					$chkerror++;
				}
		}
		if(isset($_POST['address1'])){
				$valid=checkAlpha($_POST['address1']);
				if($valid==FALSE){
					$error.= "<li><strong>Address</strong> - missing or incorrect.</li>";
					$chkerror++;
				}
		}
		if(isset($_POST['city'])){
				$valid=checkAlpha($_POST['city']);
				if($valid==FALSE){
					$error.= "<li><strong>City or town</strong> - missing or incorrect.</li>";
					$chkerror++;
				}
		}
		if(get_option('eshop_shipping_zone')=='country'){
			if(isset($_POST['country'])){
				$valid=checkAlpha($_POST['country']);
				if($valid==FALSE){
					$error.= "<li><strong>Country</strong> - missing or incorrect.</li>";
					$chkerror++;
				}
			}
		}else{
			if(isset($_POST['state'])){
				$valid=checkAlpha($_POST['state']);
				if($valid==FALSE){
					$error.= "<li><strong><abbr title=\"United States\">US</abbr> State</strong> - missing or incorrect.</li>";
					$chkerror++;
				}
			}
		}
		
		if(isset($_POST['country']) && $_POST['country']=='US' && $_POST['state']==''){
			//must pick a state for US deliveries
				$error.= "<li><strong><abbr title=\"United States\">US</abbr> State</strong> - missing or incorrect.</li>";
				$chkerror++;

		}
		if(isset($_POST['zip'])){
				$valid=checkAlphaNum($_POST['zip']);
				if($valid==FALSE){
					$error.= "<li><strong>Zip/Post code</strong> - missing or incorrect.</li>";
					$chkerror++;
				}
		}
		$error.="</ul>";
		if($chkerror!=0){
				$echoit.= $error;
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
		}else{
			if(!isset($_GET['action'])){
				$echoit.= "<div class=\"hr\"></div><h3><span class=\"noprint\">Please Confirm </span>Your Details</h3>";
				// create a custom id, and shove details in database
				$date=date('YmdHis');
				$_SESSION['date']=$date;
				$fprice=number_format($_SESSION['final_price'], 2);
				$_POST['amount']=$fprice;
				$_POST['custom']=$date;
				$_POST['numberofproducts']=sizeof($_SESSION['shopcart']);
				//shipping - replicated here, but currently easier than a function
				$shiparray=array();
				foreach ($_SESSION['shopcart'] as $productid => $opt){
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

				//shipping
				$_POST['shipping_1']=$shipping;
				$ctable=$wpdb->prefix.'eshop_countries';
				$stable=$wpdb->prefix.'eshop_states';
				$echoit.='
				<h4>Mailing Address</h4>
				 <ul>';
				$echoit.= "<li><span class=\"items\">Full name:</span> ".$_POST['first_name']." ".$_POST['last_name']."</li>\n";
				$echoit.= "<li><span class=\"items\">Company:</span> ".$_POST['company']."</li>\n";
				$echoit.= "<li><span class=\"items\">Email:</span> ".$_POST['email']."</li>\n";
				$echoit.= "<li><span class=\"items\">Phone:</span> ".$_POST['phone']."</li>\n";
				$echoit.= "<li><span class=\"items\">Address:</span> ".$_POST['address1']." ".$_POST['address2']."</li>\n";
				$echoit.= "<li><span class=\"items\">City or town:</span> ".$_POST['city']."</li>\n";
				if($_POST['country']=='US'){
					$qcode=$wpdb->escape($_POST['state']);
					$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
					$echoit.= "<li><span class=\"items\">State:</span> ".$qstate."</li>\n";
				}
				$echoit.= "<li><span class=\"items\">Zip/Post code:</span> ".$_POST['zip']."</li>\n";
				$qccode=$wpdb->escape($_POST['country']);
				$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qccode' limit 1");
				$echoit.= "<li><span class=\"items\">Country:</span> ".$qcountry."</li>\n";
				
				$echoit.= "</ul>\n";

				if( (trim($_POST['reference'])!='') && trim($_POST['comments'])==''){
					$echoit.= "<h4>Additional information</h4>\n<ul>\n";
					$echoit.= '<li><span class="items">Reference or PO:</span> '.$_POST['reference'].'</li>'."\n";
					$echoit.= '</ul>'."\n";
				}
				if( (trim($_POST['reference'])=='') && trim($_POST['comments'])!=''){
					$echoit.= "<h4>Additional information</h4>\n<ul>\n";
					$echoit.= '<li><span class="items">Comments or instructions:</span> '.$_POST['comments'].'</li>'."\n";
					$echoit.= '</ul>'."\n";
				}
				if( (trim($_POST['reference'])!='') && trim($_POST['comments'])!=''){
					$echoit.= "<h4>Additional information</h4>\n<ul>\n";
					$echoit.= '<li><span class="items">Reference or PO:</span> '.$_POST['reference'].'</li>'."\n";
					$echoit.= '<li><span class="items">Comments or instructions:</span> '.$_POST['comments'].'</li>'."\n";
					$echoit.= '</ul>'."\n";
				}

				if($_POST['ship_name']!='' || $_POST['ship_address']!='' || $_POST['ship_city']!='' || $_POST['ship_postcode']!=''){
					$echoit.= "<h4>Shipping Address</h4>\n<ul>\n";
					$echoit.= "<li><span class=\"items\">Full name:</span> ".$_POST['ship_name']."</li>\n";
					$echoit.= "<li><span class=\"items\">Company:</span> ".$_POST['ship_company']."</li>\n";
					$echoit.= "<li><span class=\"items\">Phone:</span> ".$_POST['ship_phone']."</li>\n";
					$echoit.= "<li><span class=\"items\">Address:</span> ".$_POST['ship_address']."</li>\n";
					$echoit.= "<li><span class=\"items\">City or town:</span> ".$_POST['ship_city']."</li>\n";
					if($_POST['ship_country']=='US'){
						$qcode=$wpdb->escape($_POST['ship_state']);
						$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
						$echoit.= "<li><span class=\"items\">State:</span> ".$qstate."</li>\n";
					}
					$echoit.= "<li><span class=\"items\">Zip/Post code:</span> ".$_POST['ship_postcode']."</li>\n";
					$qccode=$wpdb->escape($_POST['ship_country']);
					$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qccode' limit 1");
					$echoit.= "<li><span class=\"items\">Country:</span> ".$qcountry."</li>\n";
					$echoit.= "</ul>\n";
				}
				$echoit.= "\n";
			}
			//add to a session to store address:
			$_SESSION['addy']['first_name']=$_POST['first_name'];
			$_SESSION['addy']['last_name']=$_POST['last_name'];
			$_SESSION['addy']['company']=$_POST['company'];
			$_SESSION['addy']['phone']=$_POST['phone'];
			$_SESSION['addy']['reference']=$_POST['reference'];
			$_SESSION['addy']['email']=$_POST['email'];
			$_SESSION['addy']['address1']=$_POST['address1'];
			$_SESSION['addy']['address2']=$_POST['address2'];
			$_SESSION['addy']['city']=$_POST['city'];
			$_SESSION['addy']['country']=$_POST['country'];
			$_SESSION['addy']['state']=$_POST['state'];
			$_SESSION['addy']['zip']=$_POST['zip'];
			$_SESSION['addy']['ship_name']=$_POST['ship_name'];
			$_SESSION['addy']['ship_company']=$_POST['ship_company'];
			$_SESSION['addy']['ship_phone']=$_POST['ship_phone'];
			$_SESSION['addy']['ship_address']=$_POST['ship_address'];
			$_SESSION['addy']['ship_city']=$_POST['ship_city'];
			$_SESSION['addy']['ship_country']=$_POST['ship_country'];
			$_SESSION['addy']['ship_state']=$_POST['ship_state'];
			$_SESSION['addy']['ship_postcode']=$_POST['ship_postcode'];
			$_SESSION['addy']['comments']=$_POST['comments'];
			
			//grab all the POST variables and store in cookie
			$array=$_POST;
			//but first make a few extra equal nothing
			//add others in here if needed
			$array['comments']=$array['reference']='';
			$biscuits=eshop_build_cookie($array);
			setcookie("greencart", $biscuits,time()+60*60*24*365);
			include($paymentmethod.'.php');
		}
	}else{
		//for first time form usage.
		if(isset($_SESSION['addy'])){
			$first_name=$_SESSION['addy']['first_name'];
			$last_name=$_SESSION['addy']['last_name'];
			$company=$_SESSION['addy']['company'];
			$phone=$_SESSION['addy']['phone'];
			$reference=$_SESSION['addy']['reference'];
			$email=$_SESSION['addy']['email'];
			$address1=$_SESSION['addy']['address1'];
			$address2=$_SESSION['addy']['address2'];
			$city=$_SESSION['addy']['city'];
			$country=$_SESSION['addy']['country'];
			$state=$_SESSION['addy']['state'];
			$zip=$_SESSION['addy']['zip'];
			$ship_name=$_SESSION['addy']['ship_name'];
			$ship_company=$_SESSION['addy']['ship_company'];
			$ship_phone=$_SESSION['addy']['ship_phone'];
			$ship_address=$_SESSION['addy']['ship_address'];
			$ship_city=$_SESSION['addy']['ship_city'];
			$ship_country=$_SESSION['addy']['ship_country'];
			$ship_state=$_SESSION['addy']['ship_state'];
			$ship_postcode=$_SESSION['addy']['ship_postcode'];
			$comments=$_SESSION['addy']['comments'];
		}else{
			if(isset($_COOKIE["greencart"]) && calculate_items()!=0){
			$crumbs=eshop_break_cookie($_COOKIE["greencart"]);
				foreach($crumbs as $k=>$v){
					$$k=$v;
				}
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
			}
		}
	}

	if($chkerror!=0 || (!isset ($_POST['submit'])) && $numberofproducts>=1){
		// only show form if not filled in.
		$echoit.= eshopShowform($first_name,$last_name,$company,$phone,$email,$address1,$address2,$city,$state,$zip,$country,$reference,$comments,$ship_name,$ship_company,$ship_phone,$ship_address,$ship_city,$ship_postcode,$ship_state,$ship_country);
	}

	if(isset($_SESSION['shopcart'])){
		if($chkerror==0 && !isset($_GET['action'])){
			$echoit.='<ul class="continue-proceed"><li><a href="'.get_permalink(get_option('eshop_cart')).'">&laquo; Edit Cart or Continue Shopping</a></li></ul>';
		}else{	
			$echoit.='<ul class="continue-proceed"><li><a href="'.get_permalink(get_option('eshop_checkout')).'">&laquo; Edit Details or Continue Shopping</a></li></ul>';
		}
	}else{
		$echoit.= "<p><strong class=\"error\">Your shopping cart is currently empty.</strong></p>";
	}
	return $echoit;
 }
}
?>