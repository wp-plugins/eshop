<?php
/*  based on:
 * PHP cash Class File
*/
global $wpdb;
$detailstable=$wpdb->prefix.'eshop_orders';

//sanitise
include_once(WP_PLUGIN_DIR.'/eshop/cart-functions.php');
$_POST=sanitise_array($_POST);

include_once (WP_PLUGIN_DIR.'/eshop/cash/index.php');
// Setup class
require_once(WP_PLUGIN_DIR.'/eshop/cash/cash.class.php');  // include the class file
$p = new cash_class;             // initiate an instance of the class
$p->cash_url = get_permalink(get_option('eshop_cart_success'));     // cash url

$this_script = get_option('siteurl');
global $wp_rewrite;
if(get_option('eshop_checkout')!=''){
	if( $wp_rewrite->using_permalinks()){
		$p->autoredirect=get_permalink(get_option('eshop_checkout')).'?eshopaction=redirect';
	}else{
		$p->autoredirect=get_permalink(get_option('eshop_checkout')).'&amp;eshopaction=redirect';
	}
}else{
	$p->autoredirect=get_permalink(get_option('eshop_checkout')).'&amp;eshopaction=redirect';
}

// if there is no action variable, set the default action of 'process'
if (empty($_GET['eshopaction'])) $_GET['eshopaction'] = 'process';  

switch ($_GET['eshopaction']) {
    case 'redirect':
    	//auto-redirect bits
		header('Cache-Control: no-cache, no-store, must-revalidate'); //HTTP/1.1
		header('Expires: Sun, 01 Jul 2005 00:00:00 GMT');
		header('Pragma: no-cache'); //HTTP/1.0

		//enters all the data into the database
		$cash = get_option('eshop_cash'); 

		$checkid=md5($_POST['RefNr']);
		foreach ($_REQUEST as $field=>$value) { 
		  $ecash->ipn_data["$field"] = $value;
      	}
		//
		orderhandle($_POST,$checkid);

		/* ############### */
		if(get_option('eshop_status')=='live'){
			$txn_id = $wpdb->escape($ecash->ipn_data['RefNr']);
			$subject = __('Cash awaiting payment -','eshop');
		}else{
			$txn_id = __("TEST-",'eshop').$wpdb->escape($ecash->ipn_data['RefNr']);
			$subject = __('Testing: Cash awaiting payment - ','eshop');
		}
		//check txn_id is unique
		$checktrans=$wpdb->get_results("select transid from $detailstable");
		$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checkid' limit 1");
		foreach($checktrans as $trans){
			if(strpos($trans->transid, $ecash->ipn_data['RefNr'])===true){
				$astatus='Failed';
				$txn_id = __("Duplicated-",'eshop').$wpdb->escape($ecash->ipn_data['RefNr']);
			}
		}
		//the magic bit  + creating the subject for our email.
		$ok='no';
		if($astatus=='Pending'){
			$query2=$wpdb->query("UPDATE $detailstable set status='Waiting',transid='$txn_id' where checkid='$checkid'");
			$ok='yes';
			//product stock control updater
			$itemstable=$wpdb->prefix ."eshop_order_items";
			$stocktable=$wpdb->prefix ."eshop_stock";
			$mtable=$wpdb->prefix.'postmeta';
			$producttable=$wpdb->prefix.'eshop_downloads';
			$query=$wpdb->get_results("SELECT item_qty,post_id,item_id,down_id FROM $itemstable WHERE checkid='$checkid' AND item_id!='postage'");
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
			//only need to send out for the successes!
			//lets make sure this is here and available
			include_once(WP_PLUGIN_DIR.'/eshop/cart-functions.php');
			//this is an email sent to the customer:
			//first extract the order details
			$array=eshop_rtn_order_details($checkid);

			$etable=$wpdb->prefix.'eshop_emails';
			//grab the template
			$thisemail=$wpdb->get_row("SELECT emailSubject,emailContent FROM ".$etable." WHERE (id='5' AND emailUse='1') OR id='1'  order by id DESC limit 1");
			$this_email = stripslashes($thisemail->emailContent);
			// START SUBST
			$csubject=stripslashes($thisemail->emailSubject);
			$this_email = eshop_email_parse($this_email,$array,'no');

			//try and decode various bits - may need tweaking Mike, we may have to write 
			//a function to handle this depending on what you are using - but for now...
			$this_email=html_entity_decode($this_email,ENT_QUOTES);
			$headers=eshop_from_address();
			wp_mail($array['eemail'], $csubject, $this_email,$headers);
			
		}

		$subject .=" Ref:".$ecash->ipn_data['RefNr'];
		// email to business a complete copy of the notification from cash to keep!!!!!
		$array=eshop_rtn_order_details($checkid);
		$ecash->ipn_data['payer_email']=$array['ename'].' '.$array['eemail'].' ';
		 $to = $cash['email'];    //  your email
		 $body =  __("A cash purchase was made",'eshop')."\n";
		 $body .= "\n".__("from ",'eshop').$ecash->ipn_data['payer_email'].__(" on ",'eshop').date('m/d/Y');
		 $body .= __(" at ",'eshop').date('g:i A')."\n\n".__('Details','eshop').":\n";
		 foreach ($ecash->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
		 $body .= "\n\n".__('Regards, Your friendly automated response.','eshop')."\n\n";
		$headers=eshop_from_address();
		wp_mail($to, $subject, $body, $headers);


		/* ############### */
		$p = new cash_class; 
		if(get_option('eshop_cart_success')!=''){
			if( $wp_rewrite->using_permalinks()){
				$ilink=get_permalink(get_option('eshop_cart_success')).'?eshopaction=success';
			}else{
				$ilink=get_permalink(get_option('eshop_cart_success')).'&amp;eshopaction=success';
			}
		}else{
			$ilink=get_permalink(get_option('eshop_checkout')).'&amp;eshopaction=success';
		}
		$p->cash_url = $ilink;     // cash url
		$echoit.=$p->eshop_submit_cash_post($_POST);
		//$p->dump_fields();      // for debugging, output a table of all the fields
		
		break;
        
   case 'process':      // Process and order...
	
      /****** The order has already gone into the database at this point ******/
      
		global $wp_rewrite,$blog_id;

		//goes direct to this script as nothing needs showing on screen.

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
	
		if(get_option('eshop_status')!='live' && is_user_logged_in()||get_option('eshop_status')=='live'){
			$echoit .= $p->submit_cash_post(); // submit the fields to cash
    		//$p->dump_fields();      // for debugging, output a table of all the fields
    	}
      	break;
}
?>