<?php
/*  based on:
 * PHP bank Class File
*/
global $wpdb,$wp_query,$wp_rewrite,$blog_id,$eshopoptions;
$detailstable=$wpdb->prefix.'eshop_orders';
$derror=__('There appears to have been an error, please contact the site admin','eshop');

//sanitise
include_once(ESHOP_PATH.'cart-functions.php');
$espost=sanitise_array($espost);

include_once (ESHOP_PATH.'bank/index.php');
// Setup class
require_once(ESHOP_PATH.'bank/bank.class.php');  // include the class file
$p = new bank_class;             // initiate an instance of the class
$p->bank_url = get_permalink($eshopoptions['cart_success']);     // bank url

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
    	if(isset($_SESSION['espost'.$blog_id]))
			$espost=$_SESSION['espost'.$blog_id];
		else
			break;

		//enters all the data into the database
		$bank = $eshopoptions['bank']; 
		if(!isset($espost['RefNr'])){
			$espost['RefNr']=uniqid(rand());
			$ebank->ipn_data['RefNr']=$espost['RefNr'];
		}
		$checkid=md5($espost['RefNr']);
		foreach ($_REQUEST as $field=>$value) { 
		  $ebank->ipn_data["$field"] = $value;
      	}

		if($eshopoptions['status']=='live'){
			$txn_id = esc_sql($ebank->ipn_data['RefNr']);
			$subject = __('bank awaiting payment -','eshop');
		}else{
			$txn_id = __("TEST-",'eshop').esc_sql($ebank->ipn_data['RefNr']);
			$subject = __('Testing: bank awaiting payment - ','eshop');
		}
		//check txn_id is unique
		$checktrans=$wpdb->get_results("select transid from $detailstable");
		$astatus=$wpdb->get_var("select status from $detailstable where checkid='$checkid' limit 1");
		foreach($checktrans as $trans){
			if(strpos($trans->transid, $ebank->ipn_data['RefNr'])===true){
				$astatus='Failed';
				$txn_id = __("Duplicated-",'eshop').esc_sql($ebank->ipn_data['RefNr']);
			}
		}
		//the magic bit  + creating the subject for our email.
		$ok='no';
		if($astatus=='Pending'){
			$subject .=__("Completed Payment",'eshop');	
			$ok='yes';
			eshop_mg_process_product($txn_id,$checkid,'Waiting');
			eshop_send_customer_email($checkid, '11');
		}

		$subject .=__(" Ref:",'eshop').$ebank->ipn_data['RefNr'];
		// email to business a complete copy of the notification from bank to keep!!!!!
		$array=eshop_rtn_order_details($checkid);
		$ebank->ipn_data['payer_email']=$array['ename'].' '.$array['eemail'].' ';
		 $body =  __("A bank purchase was made",'eshop')."\n";
		 $body .= "\n".__("from ",'eshop').$ebank->ipn_data['payer_email'].__(" on ",'eshop').date('m/d/Y');
		 $body .= __(" at ",'eshop').date('g:i A')."\n\n";
		 $body .= __('Details','eshop').":\n";
		 if(isset($array['dbid']))
		 	$body .= get_bloginfo( 'wpurl' ).'/wp-admin/admin.php?page=eshop-orders.php&view='.$array['dbid']."&eshop\n";
		 foreach ($ebank->ipn_data as $key => $value) { $body .= "\n$key: $value"; }
		 $body .= "\n\n".__('Regards, Your friendly automated response.','eshop')."\n\n";
		$headers=eshop_from_address();
		$to = apply_filters('eshop_gateway_details_email', array($bank['email']));
		wp_mail($to, $subject, $body, $headers);


		/* ############### */
		$p = new bank_class; 
		if($eshopoptions['cart_success']!=''){
			$ilink=add_query_arg('eshopaction','success',get_permalink($eshopoptions['cart_success']));
		}else{
			die('<p>'.$derror.'</p>');
		}
		$p->bank_url = $ilink;     // bank url
		$echoit.=$p->eshop_submit_bank_post($espost);
		//$p->dump_fields();      // for debugging, output a table of all the fields
		
		break;
        
   case 'process':      // Process and order...
	
      /****** The order has already gone into the database at this point ******/
      
		//goes direct to this script as nothing needs showing on screen.

		$p->add_field('shipping_1', eshopShipTaxAmt());
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
		foreach($espost as $name=>$value){
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
		//required for discounts to work -updating amount.
			$runningtotal=0;
			for ($i = 1; $i <= $espost['numberofproducts']; $i++) {
				$runningtotal+=$espost['quantity_'.$i]*$espost['amount_'.$i];
			}
		$p->add_field('amount',$runningtotal);
		if($eshopoptions['status']!='live' && is_user_logged_in() &&  current_user_can('eShop_admin')||$eshopoptions['status']=='live'){
			$echoit .= $p->submit_bank_post(); // submit the fields to bank
    		//$p->dump_fields();      // for debugging, output a table of all the fields
    	}
      	break;
}
?>