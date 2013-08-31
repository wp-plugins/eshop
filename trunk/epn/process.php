<?php
// Setup class
require_once(ESHOP_PATH.'epn/epn.class.php');  // include the class file
include_once(ESHOP_PATH.'cart-functions.php');
global $wpdb,$eshopoptions;
$detailstable=$wpdb->prefix.'eshop_orders';
$ps = new epn_class; 
foreach ($_REQUEST as $field=>$value) { 
  $ps->ipn_data["$field"] = $value;
}
$epn = $eshopoptions['epn']; 
if(isset($ps->ipn_data['ID'])){
	$checked=md5($ps->ipn_data['ID']);
	if(isset($_POST['approved']) && isset($_GET['epn']) && $_GET['epn']=='ok' && $_POST['approved']=='Y'){
		$eshopdosend='yes';

		if($eshopoptions['status']=='live'){
			$txn_id = esc_sql($ps->ipn_data['transid']);
			$subject = __('epn IPN -','eshop');
		}else{
			$txn_id = __("TEST-",'eshop').esc_sql($ps->ipn_data['transid']);
			$subject = __('Testing: epn IPN - ','eshop');
		}
		//check txn_id is unique
		$checktrans=$wpdb->get_results("select transid from $detailstable");
		$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
		foreach($checktrans as $trans){
			if(strpos($trans->transid, $ps->ipn_data['ID'])===true){
				$astatus='Failed';
				$txn_id = __("Duplicated-",'eshop').esc_sql($ps->ipn_data['ID']);
			}
		}
		//the magic bit  + creating the subject for our email.
		if($astatus=='Pending'){
			$subject .=__("Completed Payment",'eshop');	
			$ok='yes';
			eshop_mg_process_product($txn_id,$checked);
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
			eshop_send_customer_email($checked, '6');

		}
	}elseif(isset($_POST['approved']) && isset($_GET['epn']) && $_GET['epn']=='fail' && $_POST['approved']=='N'){
		$eshopdosend='yes';
		if($eshopoptions['status']=='live'){
			$txn_id = esc_sql($ps->ipn_data['auth_response']);
			$subject = __('epn IPN -','eshop');
		}else{
			$txn_id = __("TEST-",'eshop').esc_sql($ps->ipn_data['auth_response']);
			$subject = __('Testing: epn IPN - ','eshop');
		}
		$array=@eshop_rtn_order_details($checked);
		$ps->ipn_data['payer_email']=@$array['ename'].' '.@$array['eemail'].' ';
		$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checked' limit 1");
		//the magic bit  + creating the subject for our email.
		$query2=$wpdb->query("UPDATE $detailstable set status='Failed',transid='$txn_id' where checkid='$checked'");
		do_action( 'eshop_order_status_updated', $checked, 'Failed' );
		$subject .=__("DECLINED Payment",'eshop');	
		$subject .=" ID Ref:".$ps->ipn_data['ID'];
		$echo.='<p>'.__('Your payment was not accepted at eProcessingNetwork and your order has been cancelled','eshop').'</p>';
	}
	if(isset($eshopdosend) && $eshopdosend=='yes'){
		$subject .=__(" Ref:",'eshop').$ps->ipn_data['ID'];
		// email to business a complete copy of the notification from epn to keep!!!!!
		$array=eshop_rtn_order_details($checked);
		$ps->ipn_data['payer_email']=$array['ename'].' '.$array['eemail'].' ';
		 $body =  __("An instant payment notification was received",'eshop')."\n";
		 $body .= "\n".__("from ",'eshop').$ps->ipn_data['payer_email'].__(" on ",'eshop').date('m/d/Y');
		 $body .= __(" at ",'eshop').date('g:i A')."\n\n".__('Details','eshop').":\n";
		 if(isset($array['dbid']))
		 	$body .= get_option( 'siteurl' ).'/wp-admin/admin.php?page=eshop-orders.php&view='.$array['dbid']."&eshop\n";

		 foreach ($ps->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
		 $body .= "\n\n".__('Regards, Your friendly automated response.','eshop')."\n\n";
		$headers=eshop_from_address();
		$to = apply_filters('eshop_gateway_details_email', array($epn['email']));
		wp_mail($to, $subject, $body, $headers);
	}
}
?>