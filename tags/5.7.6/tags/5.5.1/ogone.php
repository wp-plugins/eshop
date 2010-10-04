<?php
/*  based on:
 * PHP Paypal IPN Integration Class Demonstration File
 *  4.16.2005 - Micah Carrick, email@micahcarrick.com
*/
global $wpdb,$wp_query,$wp_rewrite,$blog_id,$eshopoptions;
$detailstable=$wpdb->prefix.'eshop_orders';
$derror=__('There appears to have been an error, please contact the site admin','eshop');

//sanitise
include_once(WP_PLUGIN_DIR.'/eshop/cart-functions.php');
$_POST=sanitise_array($_POST);

include_once (WP_PLUGIN_DIR.'/eshop/ogone/index.php');
// Setup class
require_once(WP_PLUGIN_DIR.'/eshop/ogone/ogone.class.php');  // include the class file
$p = new ogone_class;             // initiate an instance of the class
if($eshopoptions['status']=='live'){
	$p->ogone_url = 'https://secure.ogone.com/ncol/prod/orderstandard.asp';     // ogone url
}else{
	$p->ogone_url = 'https://secure.ogone.com/ncol/test/orderstandard.asp';   // testing ogone url
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
		$ogone = $eshopoptions['ogone']; 
		$Pspid=$ogone['PSPID'];
		$Secret=$ogone['secret'];
		$description=$ogone['COM'];
		// a sequence number is randomly generated
		$refid=uniqid(rand());
		$amount=($_POST['amount']+$_POST['shipping_1'])*100;
		//change to sha
		if($eshopoptions['cart_success']!=''){
			$slink=add_query_arg('eshopaction','success',get_permalink($eshopoptions['cart_success']));
		}else{
			die('<p>'.$derror.'</p>');
		}
		if($eshopoptions['cart_cancel']!=''){
			$clink=add_query_arg('eshopaction','cancel',get_permalink($eshopoptions['cart_cancel']));
		}else{
			die('<p>'.$eshopoptions['cart_cancel'].$derror.'</p>');
		}
		$sha=
		'ACCEPTURL='.$slink.$Secret.'AMOUNT='.$amount.$Secret.'CANCELURL='.$clink.$Secret.'CN='.$_POST['first_name'].' '.$_POST['last_name'].$Secret.'COM='.$description.$Secret.'CURRENCY='.$eshopoptions['currency'].$Secret.'DECLINEURL='.$clink.$Secret.'EMAIL='.$_POST['email'].$Secret.'EXCEPTIONURL='.$clink.$Secret.'LANGUAGE='.$eshopoptions['location'].$Secret.'OPERATION=SAL'.$Secret.'ORDERID='.$refid.$Secret.'OWNERADDRESS='.$_POST['address1'].$Secret.'OWNERCTY='.$_POST['country'].$Secret.'OWNERTELNO='.$_POST['phone'].$Secret.'OWNERTOWN='.$_POST['city'].$Secret.'OWNERZIP='.$_POST['zip'].$Secret.'PSPID='.$Pspid.$Secret;
		$SHASign=strtoupper(sha1($sha));
		$checkid=md5($refid);
		if(isset($_COOKIE['ap_id'])) $_POST['affiliate'] = $_COOKIE['ap_id'];
		orderhandle($_POST,$checkid);
		if(isset($_COOKIE['ap_id'])) unset($_POST['affiliate']);
		//$p = new ogone_class; 
		$p->add_field('amount',$amount);
		$p->add_field('CN',$_POST['first_name'].' '.$_POST['last_name']);
		$p->add_field('COM',$description);
		$p->add_field('currency',$eshopoptions['currency']);
		$p->add_field('email',$_POST['email']);
		$p->add_field('language',$eshopoptions['location']);
		$p->add_field('orderID',$refid);
		$p->add_field('ownerzip',$_POST['zip']);
		$p->add_field('owneraddress',$_POST['address1']);
		$p->add_field('ownercty',$_POST['country']);
		$p->add_field('ownertelno',$_POST['phone']);
		$p->add_field('ownertown',$_POST['city']);
		$p->add_field('PSPID',$Pspid);
		$p->add_field('SHASign',$SHASign);
		$p->add_field('operation','SAL');
		$p->add_field('accepturl',$slink);
		$p->add_field('declineurl',$clink);
		$p->add_field('exceptionurl',$clink);
		$p->add_field('cancelurl',$clink);
		$echoit.=$p->eshop_submit_ogone_post($_POST);
		
		break;
        
   case 'process':      // Process and order...
	
		// There should be no output at this point.  To process the POST data,
		// the submit_ogone_post() function will output all the HTML tags which
		// contains a FORM which is submited instantaneously using the BODY onload
		// attribute.  In other words, don't echo or printf anything when you're
		// going to be calling the submit_ogone_post() function.
		
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
			$echoit .= $p->submit_ogone_post(); // submit the fields to ogone
    		//$p->dump_fields();      // for debugging, output a table of all the fields
    	}
      	break;
   case 'ogoneipn':          // ogone is calling page for IPN validation...

		// It's important to remember that ogone calling this script.  There
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

			// Check the ogone documentation for specifics on what information
			// is available in the IPN POST variables.  Basically, all the POST vars
			// which ogone sends, which we send back for validation, are now stored
			// in the ipn_data() array.
 		/*
		updating db.
		*/
		$ps = new ogone_class; // initiate an instance of the class

		foreach ($_REQUEST as $field=>$value) { 
		  $ps->ipn_data["$field"] = $value;
		}

		$ogone = $eshopoptions['ogone']; 
		$secret=$ogone['secret'];
		$transid=$ps->ipn_data['orderID'];
		$amount=$ps->ipn_data["amount"];
		$checked=md5($transid);
		
		//validate
			$validate=$ps->ipn_data;
			if(isset($validate['eshopaction']))
				unset($validate['eshopaction']);
			unset($validate['SHASIGN']);
			foreach($validate as $k=>$v){
				$toval[strtoupper($k)]=$v;
			}
			ksort($toval);
			$tosha='';
			foreach($toval as $k=>$v){
				$tosha.=$k.'='.$v.$secret;
			}
		$ps->ipn_data["mycheckid"]=$checked;
		if('9' == $_REQUEST["STATUS"]  && strtoupper(sha1($tosha)) == $ps->ipn_data['SHASIGN']){
			if($eshopoptions['status']=='live'){
				$txn_id = $wpdb->escape($transid);
				$subject = __('ogone IPN -','eshop');
			}else{
				$txn_id = __("TEST-",'eshop').$wpdb->escape($transid);
				$subject = __('Testing: ogone IPN - ','eshop');
			}
			//check txn_id is unique
			$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
			if($eshopoptions['status']=='live'){
				$checktrans=$wpdb->get_results("select transid from $detailstable");
				foreach($checktrans as $trans){
					if(strpos($trans->transid, $transid)===true){
						$astatus='Failed';
						$txn_id = __("Duplicated-",'eshop').$wpdb->escape($transid);
					}
				}
			}
			
			//the magic bit  + creating the subject for our email.
			if($astatus=='Pending'){
				$query2=$wpdb->query("UPDATE $detailstable set status='Completed',transid='$txn_id' where checkid='$checked'");
				$subject .=__("Completed Payment",'eshop');	
				$ok='yes';
				//product stock control updater
				$itemstable=$wpdb->prefix ."eshop_order_items";
				$stocktable=$wpdb->prefix ."eshop_stock";
				$mtable=$wpdb->prefix.'postmeta';
				$producttable=$wpdb->prefix.'eshop_downloads';
				$query=$wpdb->get_results("SELECT item_qty,post_id,item_id,down_id FROM $itemstable WHERE checkid='$checked' AND item_id!='postage'");
				foreach($query as $row){
					$pid=$row->post_id;
					$uqty=$row->item_qty;
					////test downloads
					//check if downloadable product
					$fileid=$row->down_id;
					if($fileid!=0){
						$grabit=$wpdb->get_row("SELECT title, files FROM $producttable where id='$fileid'");
						//add 1 to number of purchases here (duplication but left in)
						$wpdb->query("UPDATE $producttable SET purchases=purchases+1 where title='$grabit->title' && files='$grabit->files' limit 1");
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
			}else{
				//cannot print anything out at this stage. so ogone users won't see the download form.
				//then it must be a success
				//close session here.
				$_SESSION = array();
				session_destroy();
				$ok='no';
			}
			// email to business a complete copy of the notification from ogone to keep!!!!!

			$subject .=" Ref:".$transid;
			$array=eshop_rtn_order_details($checked);
			$ps->ipn_data['payer_email']=$array['ename'].' '.$array['eemail'].' ';
			 $to = $ogone['email'];    //  your email
			 $body =  __("An instant payment notification was received",'eshop')."\n";
			 $body .= "\n".__("from ",'eshop').$ps->ipn_data['payer_email'].__(" on ",'eshop').date('m/d/Y');
			 $body .= __(" at ",'eshop').date('g:i A')."\n\n".__('Details','eshop').":\n";
			 foreach ($ps->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
			 $body .= "\n\n".__('Regards, Your friendly automated response.','eshop')."\n\n";

			$headers=eshop_from_address();
			wp_mail($to, $subject, $body, $headers);
			if($ok=='yes'){
				//only need to send out for the successes!
				//lets make sure this is here and available
				include_once(WP_PLUGIN_DIR.'/eshop/cart-functions.php');

				//this is an email sent to the customer:
				//first extract the order details
				$array=eshop_rtn_order_details($checked);

				$etable=$wpdb->prefix.'eshop_emails';
				//grab the template
				$thisemail=$wpdb->get_row("SELECT emailSubject,emailContent FROM ".$etable." WHERE (id='10' AND emailUse='1') OR id='1'  order by id DESC limit 1");
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
				do_shortcode('[eshop_show_success]');
			}

		}else{
			$ogone = $eshopoptions['ogone']; 
			$subject='ogone testing';
			$checked=md5($ps->ipn_data["orderID"]);	
			if($eshopoptions['status']=='live'){
				$txn_id = __('Failed','eshop').$wpdb->escape($ps->ipn_data['orderID']);
				$subject = __('ogone IPN -','eshop');
			}else{
				$txn_id = __("TEST-Failed",'eshop').$wpdb->escape($ps->ipn_data['orderID']);
				$subject = __('Testing: ogone IPN - ','eshop');
			}
			$array=@eshop_rtn_order_details($checked);
			$ps->ipn_data['payer_email']=@$array['ename'].' '.@$array['eemail'];
		
			$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
			//the magic bit  + creating the subject for our email.
			if($astatus=='Pending'){
				$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
				$subject .=__("eshop INVALID Payment",'eshop');	
			}else{
				$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
				$subject .=__("eshop Invalid and Failed Payment",'eshop');
			}
			
			$subject .=__(" Ref:",'eshop').$ps->ipn_data['orderID'];
			// email to business a complete copy of the notification from ogone to keep!!!!!
			 $to = $ogone['email'];    //  your email
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