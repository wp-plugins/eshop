<?php
// Setup class
require_once(WP_PLUGIN_DIR.'/eshop/epn/epn.class.php');  // include the class file
global $wpdb;
$detailstable=$wpdb->prefix.'eshop_orders';
$ps = new epn_class; 
foreach ($_REQUEST as $field=>$value) { 
  $ps->ipn_data["$field"] = $value;
}
$epn = get_option('eshop_epn'); 
if(isset($ps->ipn_data['ID'])){
	$checked=md5($ps->ipn_data['ID']);
	if(isset($_POST['approved']) && isset($_GET['epn']) && $_GET['epn']=='ok' && $_POST['approved']=='Y'){
		$eshopdosend='yes';

		if(get_option('eshop_status')=='live'){
			$txn_id = $wpdb->escape($ps->ipn_data['transid']);
			$subject = __('epn IPN -','eshop');
		}else{
			$txn_id = __("TEST-",'eshop').$wpdb->escape($ps->ipn_data['transid']);
			$subject = __('Testing: epn IPN - ','eshop');
		}
		//check txn_id is unique
		$checktrans=$wpdb->get_results("select transid from $detailstable");
		$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
		foreach($checktrans as $trans){
			if(strpos($trans->transid, $ps->ipn_data['ID'])===true){
				$astatus='Failed';
				$txn_id = __("Duplicated-",'eshop').$wpdb->escape($ps->ipn_data['ID']);
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
			//cannot print anything out at this stage. so epn users won't see the download form.
			//then it must be a success
			//close session here.
			$_SESSION = array();
			session_destroy();
			$eshopdosend='no';
		}
		if($ok=='yes'){
			//only need to send out for the successes!
			//lets make sure this is here and available
			include_once(ABSPATH.'wp-content/plugins/eshop/cart-functions.php');

			//this is an email sent to the customer:
			//first extract the order details
			$array=eshop_rtn_order_details($checked);

			$etable=$wpdb->prefix.'eshop_emails';
			//grab the template
			$thisemail=$wpdb->get_row("SELECT emailSubject,emailContent FROM ".$etable." WHERE (id='6' AND emailUse='1') OR id='1'  order by id DESC limit 1");
			$this_email = stripslashes($thisemail->emailContent);
			// START SUBST
			$csubject=stripslashes($thisemail->emailSubject);
			$this_email = eshop_email_parse($this_email,$array);

			//try and decode various bits - may need tweaking Mike, we may have to write 
			//a function to handle this depending on what you are using - but for now...
			$this_email=html_entity_decode($this_email,ENT_QUOTES);
			$headers=eshop_from_address();
			wp_mail($array['eemail'], $csubject, $this_email,$headers);
		}
	}elseif(isset($_POST['approved']) && isset($_GET['epn']) && $_GET['epn']=='fail' && $_POST['approved']=='N'){
		$eshopdosend='yes';
		if(get_option('eshop_status')=='live'){
			$txn_id = $wpdb->escape($ps->ipn_data['auth_response']);
			$subject = __('epn IPN -','eshop');
		}else{
			$txn_id = __("TEST-",'eshop').$wpdb->escape($ps->ipn_data['auth_response']);
			$subject = __('Testing: epn IPN - ','eshop');
		}
		$array=@eshop_rtn_order_details($checked);
		$ps->ipn_data['payer_email']=@$array['ename'].' '.@$array['eemail'].' ';
		$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
		//the magic bit  + creating the subject for our email.
		$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
		$subject .=__("DECLINED Payment",'eshop');	
		$subject .=" ID Ref:".$ps->ipn_data['ID'];
		$echo.='<p>'.__('Your payment was not accepted at eProcessingNetwork and your order has been cancelled','eshop').'</p>';
	}
	if(isset($eshopdosend) && $eshopdosend=='yes'){
		$subject .=" Ref:".$ps->ipn_data['ID'];
		// email to business a complete copy of the notification from epn to keep!!!!!
		$array=eshop_rtn_order_details($checked);
		$ps->ipn_data['payer_email']=$array['ename'].' '.$array['eemail'].' ';
		 $to = $epn['email'];    //  your email
		 $body =  __("An instant payment notification was received",'eshop')."\n";
		 $body .= "\n".__("from ",'eshop').$ps->ipn_data['payer_email'].__(" on ",'eshop').date('m/d/Y');
		 $body .= __(" at ",'eshop').date('g:i A')."\n\n".__('Details','eshop').":\n";
		 foreach ($ps->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
		 $body .= "\n\n".__('Regards, Your friendly automated response.','eshop')."\n\n";
		$headers=eshop_from_address();
		wp_mail($to, $subject, $body, $headers);
	}
}
?>