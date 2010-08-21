<?php
/*  based on:
 * PHP Payson IPN Integration Class Demonstration File
 *  4.16.2005 - Micah Carrick, email@micahcarrick.com
*/
global $wpdb,$wp_query,$wp_rewrite,$blog_id,$eshopoptions;
$detailstable=$wpdb->prefix.'eshop_orders';
$derror=__('There appears to have been an error, please contact the site admin','eshop');

//sanitise
include_once(WP_PLUGIN_DIR.'/eshop/cart-functions.php');
$_POST=sanitise_array($_POST);

include_once (WP_PLUGIN_DIR.'/eshop/payson/index.php');
// Setup class
require_once(WP_PLUGIN_DIR.'/eshop/payson/payson.class.php');  // include the class file
$p = new payson_class;             // initiate an instance of the class

if($eshopoptions['status']=='live'){
	$p->payson_url = 'https://www.payson.se/merchant/default.aspx';     // payson url
}else{
	$p->payson_url = 'https://www.payson.se/testagent/default.aspx';   // testing payson url
}

$this_script = site_url();
if($eshopoptions['checkout']!=''){
	$p->autoredirect=add_query_arg('eshopaction','redirect',get_permalink($eshopoptions['checkout']));
}else{
	die('<p>'.$derror.'</p>');
}

// if there is no action variable, set the default action of 'process'
if(!isset($wp_query->query_vars['eshopaction']))
	$eshopaction='process';
else
	$eshopaction=$wp_query->query_vars['eshopaction'];

switch ($eshopaction) {
    case 'redirect':
    	//auto-redirect bits
		header('Cache-Control: no-cache, no-store, must-revalidate'); //HTTP/1.1
		header('Expires: Sun, 01 Jul 2005 00:00:00 GMT');
		header('Pragma: no-cache'); //HTTP/1.0

		//enters all the data into the database
		$payson = $eshopoptions['payson']; 
		$Key=$payson['key'];
		$Cost=$_POST['amount']-$_POST['shipping_1'];
		$ExtraCost=$_POST['shipping_1'];
		//payson uses comma not decimal point
		$Cost=number_format($Cost, 2, ',', '');
		$ExtraCost=number_format($ExtraCost, 2, ',', '');
		$OkUrl=urlencode($_POST['notify_url']);
		$GuaranteeOffered='1';
		$MD5string = $payson['email'] . ":" . $Cost . ":" . $ExtraCost . ":" . $OkUrl . ":" . $GuaranteeOffered . $Key;
		$token=$MD5Hash = md5($MD5string);

		$checkid=md5($_POST['RefNr']);
		//
		if(isset($_COOKIE['ap_id'])) $_POST['affiliate'] = $_COOKIE['ap_id'];
		orderhandle($_POST,$checkid);
		if(isset($_COOKIE['ap_id'])) unset($_POST['affiliate']);		
		$_POST['custom']=$token;
		$p = new payson_class; 
		if($eshopoptions['status']=='live'){
			$p->payson_url = 'https://www.payson.se/merchant/default.aspx';     // payson url
		}else{
			$p->payson_url = 'https://www.payson.se/testagent/default.aspx';   // testing payson url
		}
		$echoit.=$p->eshop_submit_payson_post($_POST);
		//$p->dump_fields();      // for debugging, output a table of all the fields
		break;
        
   case 'process':      // Process and order...
	
		// There should be no output at this point.  To process the POST data,
		// the submit_payson_post() function will output all the HTML tags which
		// contains a FORM which is submited instantaneously using the BODY onload
		// attribute.  In other words, don't echo or printf anything when you're
		// going to be calling the submit_payson_post() function.
		
		// This is where you would have your form validation  and all that jazz.
		// You would take your POST vars and load them into the class like below,
		// only using the POST values instead of constant string expressions.

		// For example, after ensureing all the POST variables from your custom
		// order form are valid, you might have:
		//
		// $p->add_field('first_name', $_POST['first_name']);
		// $p->add_field('last_name', $_POST['last_name']);
      
      /****** The order has already gone into the database at this point ******/
      
		//goes direct to this script as nothing needs showing on screen.
		if($eshopoptions['cart_success']!=''){
			$ilink=add_query_arg('eshopaction','paysonipn',get_permalink($eshopoptions['cart_success']));
		}else{
			die('<p>'.$derror.'</p>');
		}
		$p->add_field('notify_url', $ilink);

		$p->add_field('shipping_1', number_format($_SESSION['shipping'.$blog_id],2));
		$sttable=$wpdb->prefix.'eshop_states';
		$getstate=$eshopoptions['shipping_state'];
		if($eshopoptions['show_allstates'] != '1'){
			$stateList=$wpdb->get_results("SELECT id,code,stateName FROM $sttable WHERE list='$getstate' ORDER BY stateName",ARRAY_A);
		}else{
			$stateList=$wpdb->get_results("SELECT id,code,stateName,list FROM $sttable ORDER BY list,stateName",ARRAY_A);
		}
		foreach($stateList as $code => $value){
			$eshopstatelist[$value['id']]=$value['code'];
		}
		foreach($_POST as $name=>$value){
			//have to do a discount code check here - otherwise things just don't work - but fine for free shipping codes
			if(strstr($name,'amount_')){
				if(isset($_SESSION['eshop_discount'.$blog_id]) && eshop_discount_codes_check()){
					$chkcode=valid_eshop_discount_code($_SESSION['eshop_discount'.$blog_id]);
					if($chkcode && apply_eshop_discount_code('discount')>0){
						$discount=apply_eshop_discount_code('discount')/100;
						$value = number_format(round($value-($value * $discount), 2),2);
						$vset='yes';
					}
				}
				if(is_discountable(calculate_total())!=0 && !isset($vset)){
					$discount=is_discountable(calculate_total())/100;
					$value = number_format(round($value-($value * $discount), 2),2);
				}
			}
			if(sizeof($stateList)>0 && ($name=='state' || $name=='ship_state')){
				if($value!='')
					$value=$eshopstatelist[$value];
			}
			
			$p->add_field($name, $value);
		}
	
		if($eshopoptions['status']!='live' && is_user_logged_in()||$eshopoptions['status']=='live'){
			$echoit .= $p->submit_payson_post(); // submit the fields to payson
    		//$p->dump_fields();      // for debugging, output a table of all the fields
    	}
      	break;
   case 'paysonipn':          // Payson is calling page for IPN validation...
   
		// It's important to remember that payson calling this script.  There
		// is no output here.  This is where you validate the IPN data and if it's
		// valid, update your database to signify that the user has payed.  If
		// you try and use an echo or printf function here it's not going to do you
		// a bit of good.  This is on the "backend".  That is why, by default, the
		// class logs all IPN data to a text file.
		// the loggin to a text file isn't working, so we have coded an email to be sent instead.

			// Payment has been recieved and IPN is verified.  This is where you
			// update your database to activate or process the order, or setup
			// the database with the user's order details, email an administrator,
			// etc.  You can access a slew of information via the ipn_data() array.

			// Check the payson documentation for specifics on what information
			// is available in the IPN POST variables.  Basically, all the POST vars
			// which payson sends, which we send back for validation, are now stored
			// in the ipn_data() array.
 		/*
		updating db.
		*/
		foreach ($_REQUEST as $field=>$value) { 
		  $ps->ipn_data["$field"] = $value;
		}

		$payson = $eshopoptions['payson']; 
		$Key=$payson['key'];
		$strOkURL = $ps->ipn_data["OkURL"];
		$strRefNr = $ps->ipn_data["RefNr"];
		$strPaysonRef = $ps->ipn_data["Paysonref"];
		$strTestMD5String = $strOkURL . $strPaysonRef . $Key;
		$token = md5($strTestMD5String);
		$checked=md5($strRefNr);
		$eshopdosend='yes';
		if($token == $_REQUEST["MD5"]){
			if($eshopoptions['status']=='live'){
				$txn_id = $wpdb->escape($ps->ipn_data['RefNr']);
				$subject = __('Payson IPN -','eshop');
			}else{
				$txn_id = __("TEST-",'eshop').$wpdb->escape($ps->ipn_data['RefNr']);
				$subject = __('Testing: Payson IPN - ','eshop');
			}
			//check txn_id is unique
			$checktrans=$wpdb->get_results("select transid from $detailstable");
			$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
			foreach($checktrans as $trans){
				if(strpos($trans->transid, $ps->ipn_data['RefNr'])===true){
					$astatus='Failed';
					$txn_id = __("Duplicated-",'eshop').$wpdb->escape($ps->ipn_data['RefNr']);
				}
			}
			//the magic bit  + creating the subject for our email.
			if($astatus=='Pending'){
				$subject .=__("Completed Payment",'eshop');	
				$ok='yes';
				eshop_mg_process_product($txn_id,$checked);
				/*
				$query2=$wpdb->query("UPDATE $detailstable set status='Completed',transid='$txn_id' where checkid='$checked'");
				$subject .=__("Completed Payment",'eshop');	
				$ok='yes';
				//product stock control updater
				$itemstable=$wpdb->prefix ."eshop_order_items";
				$stocktable=$wpdb->prefix ."eshop_stock";
				$mtable=$wpdb->prefix.'postmeta';
				$producttable=$wpdb->prefix.'eshop_downloads';
				$query=$wpdb->get_results("SELECT item_qty,post_id,item_id,down_id FROM $itemstable WHERE checkid='$checked' AND post_id!='0'");
				foreach($query as $row){
					$pid=$row->post_id;
					$uqty=$row->item_qty;
					////test downloads
					//check if downloadable product
					$fileid=$row->down_id;
					if($fileid!=0){
						$grabit=$wpdb->get_row("SELECT title, files FROM $producttable where id='$fileid'");
						//add 1 to number of purchases here (duplication but left in)
						$wpdb->query("UPDATE $producttable SET purchases=purchases+$uqty where title='$grabit->title' && files='$grabit->files' limit 1");
						$chkit= $wpdb->get_var("SELECT purchases FROM $stocktable WHERE post_id='$pid'");
						if($chkit!=''){	
							$wpdb->query("UPDATE $stocktable set purchases=purchases+$uqty where post_id=$pid");
						}else{
							$wpdb->query("INSERT INTO $stocktable (available, purchases, post_id) VALUES ('0','$uqty','$pid')");
						}
					}else{
						$chkit= $wpdb->get_var("SELECT purchases FROM $stocktable WHERE post_id='$pid'");
						if($chkit!=''){						
							$wpdb->query("UPDATE $stocktable set available=available-$uqty, purchases=purchases+$uqty where post_id=$pid");
						}else{
							$wpdb->query("INSERT INTO $stocktable (available, purchases, post_id) VALUES ('0','$uqty','$pid')");
						}
					}

				}
				*/
			}else{
				//cannot print anything out at this stage. so payson users won't see the download form.
				//then it must be a success
				//close session here.
				$_SESSION = array();
				session_destroy();
				$eshopdosend='no';
			}

			if($eshopdosend=='yes'){
				$subject .=" Ref:".$ps->ipn_data['RefNr'];
				// email to business a complete copy of the notification from payson to keep!!!!!
				$array=eshop_rtn_order_details($checked);
				$ps->ipn_data['payer_email']=$array['ename'].' '.$array['eemail'].' ';
				 $to = $payson['email'];    //  your email
				 $body =  __("An instant payment notification was received",'eshop')."\n";
				 $body .= "\n".__("from ",'eshop').$ps->ipn_data['payer_email'].__(" on ",'eshop').date('m/d/Y');
				 $body .= __(" at ",'eshop').date('g:i A')."\n\n".__('Details','eshop').":\n";
				 if(isset($array['dbid']))
				 	$body .= get_option( 'siteurl' ).'/wp-admin/admin.php?page=eshop_orders.php&view='.$array['dbid']."\n";

				 foreach ($ps->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
				 $body .= "\n\n".__('Regards, Your friendly automated response.','eshop')."\n\n";

				$headers=eshop_from_address();
				wp_mail($to, $subject, $body, $headers);
			}
			if($ok=='yes'){
				//only need to send out for the successes!
				//lets make sure this is here and available
				include_once(WP_PLUGIN_DIR.'/eshop/cart-functions.php');

				//this is an email sent to the customer:
				//first extract the order details
				$array=eshop_rtn_order_details($checked);

				$etable=$wpdb->prefix.'eshop_emails';
				//grab the template
				$thisemail=$wpdb->get_row("SELECT emailSubject,emailContent FROM ".$etable." WHERE (id='4' AND emailUse='1') OR id='1'  order by id DESC limit 1");
				$this_email = stripslashes($thisemail->emailContent);
				// START SUBST
				$csubject=stripslashes($thisemail->emailSubject);
				$this_email = eshop_email_parse($this_email,$array);

				//try and decode various bits - may need tweaking Mike, we may have to write 
				//a function to handle this depending on what you are using - but for now...
				$this_email=html_entity_decode($this_email,ENT_QUOTES);
				$headers=eshop_from_address();
				wp_mail($array['eemail'], $csubject, $this_email,$headers);
				//affiliate
				if($array['affiliate']!=''){
					do_action('eShop_process_aff_commission', array("id" =>$array['affiliate'],"sale_amt"=>$array['total'], 
					"txn_id"=>$array['transid'], "buyer_email"=>$array['eemail']));
				}
			}

		}else{
			$payson = $eshopoptions['payson']; 
			$Key=$payson['key'];
			$strOkURL = $_POST["OkURL"];
			$strRefNr = $_POST["RefNr"];
			$strPaysonRef = $_POST["Paysonref"];
			$strTestMD5String = $strOkURL . $strPaysonRef . $Key;
			$token = md5($strTestMD5String);
			$checked=md5($token);	
			if($eshopoptions['status']=='live'){
				$txn_id = $wpdb->escape($ps->ipn_data['RefNr']);
				$subject = __('Payson IPN -','eshop');
			}else{
				$txn_id = __("TEST-",'eshop').$wpdb->escape($ps->ipn_data['RefNr']);
				$subject = __('Testing: Payson IPN - ','eshop');
			}
			$array=@eshop_rtn_order_details($checked);
			$ps->ipn_data['payer_email']=@$array['ename'].' '.@$array['eemail'].' ';
			$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
			//the magic bit  + creating the subject for our email.
			if($astatus=='Pending'){
				$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
				$subject .=__("INVALID Payment",'eshop');	
			}else{
				$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
				$subject .=__("Invalid and Failed Payment",'eshop');
			}
			$subject .=__(" Ref:",'eshop').$ps->ipn_data['RefNr'];
			// email to business a complete copy of the notification from payson to keep!!!!!
			 $to = $payson['email'];    //  your email
			 $body =  __("An instant payment notification was received",'eshop')."\n";
			 $body .= "\n".__('from','eshop')." ".$ps->ipn_data['payer_email'].__(" on ",'eshop').date('m/d/Y');
			 $body .= __(' at ','eshop').date('g:i A')."\n\n".__('Details:','eshop')."\n";
			 foreach ($ps->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
			 $body .= "\n\n".__("Regards, Your friendly automated response.",'eshop')."\n\n";
			 $headers=eshop_from_address();
			 wp_mail($to, $subject, $body, $headers);
		}
		break;
}
?>