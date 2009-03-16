<?php

/*  PHP Paypal IPN Integration Class Demonstration File
 *  4.16.2005 - Micah Carrick, email@micahcarrick.com
 *
 *  This file demonstrates the usage of paypal.class.php, a class designed  
 *  to aid in the interfacing between your website, paypal, and the instant
 *  payment notification (IPN) interface.  This single file serves as 4 
 *  virtual pages depending on the "action" varialble passed in the URL. It's
 *  the processing page which processes form data being submitted to paypal, it
 *  is the page paypal returns a user to upon success, it's the page paypal
 *  returns a user to upon canceling an order, and finally, it's the page that
 *  handles the IPN request from Paypal.
 *
 *  I tried to comment this file, aswell as the acutall class file, as well as
 *  I possibly could.  Please email me with questions, comments, and suggestions.
 *  See the header of paypal.class.php for additional resources and information.
*/
global $wpdb;
$detailstable=$wpdb->prefix.'eshop_orders';

//sanitise
include_once(ABSPATH.'wp-content/plugins/eshop/cart-functions.php');
$_POST=sanitise_array($_POST);

include_once (ABSPATH.'wp-content/plugins/eshop/paypal/index.php');
// Setup class
require_once(ABSPATH.'wp-content/plugins/eshop/paypal/paypal.class.php');  // include the class file
$p = new paypal_class;             // initiate an instance of the class



if(get_option('eshop_status')=='live'){
	$p->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url
}else{
	$p->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
}

// setup a variable for this script (ie: 'http://www.micahcarrick.com/paypal.php')
//e.g. $this_script = 'http://'.$_SERVER['HTTP_HOST'].htmlentities($_SERVER['PHP_SELF']);
$this_script = get_option('siteurl');
global $wp_rewrite;

if(get_option('eshop_checkout')!=''){
	if( $wp_rewrite->using_permalinks()){
		$p->autoredirect=get_permalink(get_option('eshop_checkout')).'?action=redirect';
	}else{
		$p->autoredirect=get_permalink(get_option('eshop_checkout')).'&amp;action=redirect';
	}
}else{
	$p->autoredirect=get_permalink(get_option('eshop_checkout')).'&amp;action=redirect';
}

// if there is no action variable, set the default action of 'process'
if (empty($_GET['action'])) $_GET['action'] = 'process';  

switch ($_GET['action']) {
    case 'redirect':
    	//auto-redirect bits
		header('Cache-Control: no-cache, no-store, must-revalidate'); //HTTP/1.1
		header('Expires: Sun, 01 Jul 2005 00:00:00 GMT');
		header('Pragma: no-cache'); //HTTP/1.0

		//enters all the data into the database
		$token = uniqid(md5($_SESSION['date'.$blog_id]), true);
		$checkid=md5(get_option('eshop_business').$token.number_format($_SESSION['final_price'.$blog_id],2));
		//
		orderhandle($_POST,$checkid);
		$_POST['custom']=$token;
		$p = new paypal_class; 
		if(get_option('eshop_status')=='live'){
			$p->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';     // paypal url
		}else{
			$p->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';   // testing paypal url
		}
		$echoit.=$p->eshop_submit_paypal_post($_POST);
		//$p->dump_fields();      // for debugging, output a table of all the fields
		break;
        
   case 'process':      // Process and order...
	
		// There should be no output at this point.  To process the POST data,
		// the submit_paypal_post() function will output all the HTML tags which
		// contains a FORM which is submited instantaneously using the BODY onload
		// attribute.  In other words, don't echo or printf anything when you're
		// going to be calling the submit_paypal_post() function.
		
		// This is where you would have your form validation  and all that jazz.
		// You would take your POST vars and load them into the class like below,
		// only using the POST values instead of constant string expressions.

		// For example, after ensureing all the POST variables from your custom
		// order form are valid, you might have:
		//
		// $p->add_field('first_name', $_POST['first_name']);
		// $p->add_field('last_name', $_POST['last_name']);
      
      /****** The order has already gone into the database at this point ******/
      
		global $wp_rewrite,$blog_id;
		$p->add_field('business', get_option('eshop_business'));
		if(get_option('eshop_cart_success')!=''){
			if( $wp_rewrite->using_permalinks()){
				$slink=get_permalink(get_option('eshop_cart_success')).'?action=success';
			}else{
				$slink=get_permalink(get_option('eshop_cart_success')).'&amp;action=success';
			}
		}else{
			$slink=get_permalink(get_option('eshop_checkout')).'&amp;action=success';
		}
		if(get_option('eshop_cart_cancel')!=''){
			if( $wp_rewrite->using_permalinks()){
				$clink=get_permalink(get_option('eshop_cart_cancel')).'?action=cancel';
			}else{
				$clink=get_permalink(get_option('eshop_cart_cancel')).'&amp;action=cancel';
			}
		}else{
			$clink=get_permalink(get_option('eshop_cart')).'&amp;action=cancel';
		}
		$p->add_field('return', $slink);
		$p->add_field('cancel_return', $clink);
		//goes direct to this script as nothing needs showing on screen.
		if(get_option('eshop_cart_success')!=''){
			if( $wp_rewrite->using_permalinks()){
				$ilink=get_permalink(get_option('eshop_cart_success')).'?action=paypalipn';
			}else{
				$ilink=get_permalink(get_option('eshop_cart_success')).'&amp;action=paypalipn';
			}
		}else{
			$ilink=get_permalink(get_option('eshop_checkout')).'&amp;action=paypalipn';
		}
		$p->add_field('notify_url', $ilink);

		$p->add_field('shipping_1', number_format($_SESSION['shipping'.$blog_id],2));
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
			
			$p->add_field($name, $value);
		}
		
	//	$p->add_field('return_method','2'); //1=GET 2=POST
	// was return method now rm - go figure.
		$p->add_field('rm','2'); //1=GET 2=POST

		
		//settings in paypal/index.php to change these
		$p->add_field('currency_code',get_option('eshop_currency')); //['USD,GBP,JPY,CAD,EUR']
		$p->add_field('lc',get_option('eshop_location'));
		$p->add_field('cmd','_ext-enter');
		$p->add_field('redirect_cmd','_cart');
		$p->add_field('upload','1');
		//$p->add_field('address_override','1');//causes errors :(
		if(get_option('eshop_status')!='live' && is_user_logged_in()||get_option('eshop_status')=='live'){
			$echoit .= $p->submit_paypal_post(); // submit the fields to paypal
    		//$p->dump_fields();      // for debugging, output a table of all the fields
    	}
      	break;
      
   case 'success':      // Order was successful...
   			// NOW HANDLED BY ESHOP.PHP with <!--eshop_show_success-->
		// This is where you would probably want to thank the user for their order
		// or what have you.  The order information at this point is in POST 
		// variables.  However, you don't want to "process" the order until you
		// get validation from the IPN.  That's where you would have the code to
		// email an admin, update the database with payment status, activate a
		// membership, etc.  
		$_SESSION = array();
      	session_destroy();
      	if(get_option('eshop_status')=='live'){
			$txn_id = $wpdb->escape($_POST['txn_id']);
		}else{
			$txn_id = "TEST-".$wpdb->escape($_POST['txn_id']);
		}
		$frow=$wpdb->get_var("select first_name from $detailstable where transid='$txn_id' limit 1");
		$lrow=$wpdb->get_var("select last_name from $detailstable where transid='$txn_id' limit 1");
		if($frow!='' && $lrow!=''){
			$echoit .= "<h3>".__('Thank you for your order','eshop').", ".$frow." ".$lrow."!</h3>";
		}else{
			$echoit .= "<h3>".__('Thank you for your order!','eshop')."</h3>";
		}
		//echo 'name='.$row->first_name.' '.$row->last_name.'<br>';
		// You could also simply re-direct them to another page, or your own 
		// order status page which presents the user with the status of their
		// order based on a database (which can be modified with the IPN code 
		// below).
       	break;
      	
    case 'cancel':       // Order was canceled...
	  		/*
	  		The script doesn't get here, so for cancelled orders see the bottom of cart.php
	  		Unfortunate side effect is that the order is left in pending
	  		*/
	  		// The order was canceled before being completed.
	  		/* commented out until i can think of a way to get this to work :(  -Rich
	  		$checked=md5($p->ipn_data['business'].$p->ipn_data['custom'].$p->ipn_data['payer_email'].$p->ipn_data['mc_gross']);
	  		$tstatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
	  		if(get_option('eshop_status')=='live'){
	  			$txn_id = 'Cancelled-'.$wpdb->escape($p->ipn_data['txn_id']);
	  		}else{
	  			$txn_id = "TEST-Cancelled-".$wpdb->escape($p->ipn_data['txn_id']);
	  		}
	  		if($tstatus=='Pending'){
	  			$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
	  		}
	  		*/

		break;
   
      
   case 'paypalipn':          // Paypal is calling page for IPN validation...
   
		// It's important to remember that paypal calling this script.  There
		// is no output here.  This is where you validate the IPN data and if it's
		// valid, update your database to signify that the user has payed.  If
		// you try and use an echo or printf function here it's not going to do you
		// a bit of good.  This is on the "backend".  That is why, by default, the
		// class logs all IPN data to a text file.
		// the loggin to a text file isn't working, so we have coded an email to be sent instead.

		if ($p->validate_ipn()) {
			// Payment has been recieved and IPN is verified.  This is where you
			// update your database to activate or process the order, or setup
			// the database with the user's order details, email an administrator,
			// etc.  You can access a slew of information via the ipn_data() array.

			// Check the paypal documentation for specifics on what information
			// is available in the IPN POST variables.  Basically, all the POST vars
			// which paypal sends, which we send back for validation, are now stored
			// in the ipn_data() array.
 		/*
		updating db.
		*/
			$checked=md5($p->ipn_data['business'].$p->ipn_data['custom'].number_format($p->ipn_data['mc_gross'],2));

			if(get_option('eshop_status')=='live'){
				$txn_id = $wpdb->escape($p->ipn_data['txn_id']);
				$subject = __('Paypal IPN -','eshop');
			}else{
				$txn_id = __("TEST-",'eshop').$wpdb->escape($p->ipn_data['txn_id']);
				$subject = __('Testing: Paypal IPN - ','eshop');
			}
			//check txn_id is unique
			$checktrans=$wpdb->get_results("select transid from $detailstable");
			$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
			foreach($checktrans as $trans){
				if(strpos($trans->transid, $p->ipn_data['txn_id'])===true){
					$astatus='Failed';
					$txn_id = __("Duplicated-",'eshop').$wpdb->escape($p->ipn_data['txn_id']);
					$extradetails = __("Duplicated Transaction Id.",'eshop');
				}
			}
			//check reciever email is correct - we will use business for now
			if($p->ipn_data['receiver_email']!= get_option('eshop_business')){
				$astatus='Failed';
				$txn_id = __("Fraud-",'eshop').$wpdb->escape($p->ipn_data['txn_id']);
				$extradetails = __("The business email address in eShop does not match your main email address at Paypal.",'eshop');
			}
			//add any memo from user at paypal here
			$memo=$wpdb->escape($p->ipn_data['memo']);
			$mquery=$wpdb->query("UPDATE $detailstable set thememo='$memo' where checkid='$checked'");
			//the magic bit  + creating the subject for our email.
			if($astatus=='Pending' && $_POST['payment_status']=='Completed'){
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
			}else{
				$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
				$subject .=__("A Failed Payment",'eshop');
				$ok='no';
				$extradetails = __("The transaction was not completed successfully.eShop thought the order to be no longer pending.",'eshop');
				if($_POST['payment_status']!='Completed' && isset($_POST['pending_reason']))
					$extradetails = __("The transaction was not completed successfully at Paypal. The pending reason for this is",'eshop').' '.$_POST['pending_reason'];
			}
			$subject .=" Ref:".$p->ipn_data['txn_id'];
			// email to business a complete copy of the notification from paypal to keep!!!!!
			 $to = get_option('eshop_business');    //  your email
			 $body =  __("An instant payment notification was received",'eshop')."\n";
			 $body .= "\n".__("from ",'eshop').$p->ipn_data['payer_email'].__(" on ",'eshop').date('m/d/Y');
			 $body .= __(" at ",'eshop').date('g:i A')."\n\n".__('Details','eshop').":\n";
			 //debug
			//$body .= 'checked:'.$checked."\n".$p->ipn_data['business'].$p->ipn_data['custom'].$p->ipn_data['payer_email'].$p->ipn_data['mc_gross']."\n";
			if(isset($extradetails)) $body .= $extradetails."\n\n";
			 foreach ($p->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
			 $body .= "\n\n".__('Regards, Your friendly automated response.','eshop')."\n\n";

			$headers=eshop_from_address();
			wp_mail($to, $subject, $body, $headers);

			if($ok=='yes'){
				//only need to send out for the successes!
				//lets make sure this is here and available
				include_once(ABSPATH.'wp-content/plugins/eshop/cart-functions.php');

				//this is an email sent to the customer:
				//first extract the order details
				$array=eshop_rtn_order_details($checked);

				$etable=$wpdb->prefix.'eshop_emails';
				//grab the template
				$thisemail=$wpdb->get_row("SELECT emailSubject,emailContent FROM ".$etable." WHERE (id='3' AND emailUse='1') OR id='1'  order by id DESC limit 1");
				$this_email = stripslashes($thisemail->emailContent);
				// START SUBST
				$csubject=stripslashes($thisemail->emailSubject);
				$this_email = str_replace('{STATUS}', $array['status'], $this_email);
				$this_email = str_replace('{FIRSTNAME}', $array['firstname'], $this_email);
				$this_email = str_replace('{NAME}', $array['ename'], $this_email);
				$this_email = str_replace('{EMAIL}', $array['eemail'], $this_email);
				$this_email = str_replace('{CART}', $array['cart'], $this_email);
				$this_email = str_replace('{DOWNLOADS}', $array['downloads'], $this_email);
				$this_email = str_replace('{ADDRESS}', $array['address'], $this_email);
				$this_email = str_replace('{REFCOMM}', $array['extras'], $this_email);
				$this_email = str_replace('{CONTACT}', $array['contact'], $this_email);

				//try and decode various bits - may need tweaking Mike, we may have to write 
				//a function to handle this depending on what you are using - but for now...
				$this_email=html_entity_decode($this_email,ENT_QUOTES);
				$headers=eshop_from_address();
				wp_mail($array['eemail'], $csubject, $this_email,$headers);
			}
      	}else{
      		$checked=md5($p->ipn_data['business'].$p->ipn_data['custom'].number_format($p->ipn_data['mc_gross'],2));
			if(get_option('eshop_status')=='live'){
				$txn_id = $wpdb->escape($p->ipn_data['txn_id']);
				$subject = __('Paypal IPN -','eshop');
			}else{
				$txn_id = __("TEST-",'eshop').$wpdb->escape($p->ipn_data['txn_id']);
				$subject = __('Testing: Paypal IPN - ','eshop');
			}

			$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
			//add any memo from user at paypal here
			$memo=$wpdb->escape($p->ipn_data['memo']);
			$mquery=$wpdb->query("UPDATE $detailstable set thememo='$memo' where checkid='$checked'");
			//the magic bit  + creating the subject for our email.
			if($astatus=='Pending' && $p->ipn_data['payment_status']=='Completed'){
				$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
				$subject .=__("INVALID Payment",'eshop');	
				$extradetails = __("The order may be a duplicate, and Paypal has reported an invalid payment.",'eshop');	
			}else{
				$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
				$subject .=__("Invalid and Failed Payment",'eshop');
				$extradetails = __("The order may be a duplicate, and Paypal has reported an invalid, and failed payment.",'eshop');
				if($_POST['payment_status']!='Completed' && isset($_POST['pending_reason']))
					$extradetails = __("Paypal has reported an invalid, and failed payment. The pending reason for this is",'eshop').' '.$_POST['pending_reason'];

			}
			$subject .=" Ref:".$p->ipn_data['txn_id'];
			// email to business a complete copy of the notification from paypal to keep!!!!!
			 $to = get_option('eshop_business');    //  your email
			 $body =  __("An instant payment notification was received",'eshop')."\n";
			 $body .= "\n".__('from','eshop')." ".$p->ipn_data['payer_email'].__(" on ",'eshop').date('m/d/Y');
			 $body .= __(' at ','eshop').date('g:i A')."\n\n".__('Details:','eshop')."\n";
			 if(isset($extradetails)) $body .= $extradetails."\n\n";
			 foreach ($p->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
			 $body .= "\n\n".__("Regards, Your friendly automated response.",'eshop')."\n\n";
			 $headers=eshop_from_address();
			 wp_mail($to, $subject, $body, $headers);
			}
      	break;
 }     
?>