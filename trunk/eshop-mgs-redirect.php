<?php
if (!function_exists('eshop_authorizenet_redirect')) {
 	function eshop_authorizenet_redirect($espost){
 		global $blog_id,$eshopoptions,$wpdb,$wp_query;
		if($espost['eshop_payment']!='authorizenet')
			return;
		$paymentmethod='authorizenet';

		if (isset ($espost['ppsubmit'])) {
			$eshopmgincpath=apply_filters('eshop_mg_inc_path',ESHOP_PATH.$paymentmethod.'.php',$paymentmethod);
			include($eshopmgincpath);
		}

//enters all the data into the database
    	//auto-redirect bits
		header('Cache-Control: no-cache, no-store, must-revalidate'); //HTTP/1.1
		header('Expires: Sun, 01 Jul 2005 00:00:00 GMT');
		header('Pragma: no-cache'); //HTTP/1.0
		$authorizenet = $eshopoptions['authorizenet']; 
		$Key=$authorizenet['key'];
		$LID=$authorizenet['id'];
		$secret=$authorizenet['secret'];
		$description=$authorizenet['desc'];
		// a sequence number is randomly generated
		$sequence	= rand(1, 1000);
		// a timestamp is generated
		$timestamp	= time ();
		$pvalue=str_replace(',','',$espost['amount']);
		//next 2 lines added to solve an issue 12/8/22
		$pship=str_replace(',','',$espost['shipping_1']);
		$pvalue+=$pship;
		if(isset($_SESSION['shipping'.$blog_id]['tax'])) $pvalue += $_SESSION['shipping'.$blog_id]['tax'];
		// above may be able to be changed by using + eshopShipTaxAmt() for the shipping.
		if(isset($espost['tax'])) $pvalue += str_replace(',','',$espost['tax']);
		$pvalue = number_format($pvalue, 2, '.', '');
		$subinv=uniqid(rand()).'eShop';
		$invoice=substr($subinv,0,20);
		
		if( phpversion() >= '5.1.2' ){
			$fingerprint = hash_hmac("md5", $LID . "^" . $sequence . "^" . $timestamp . "^" . $pvalue . "^", $Key); 
		}else{ 
			$fingerprint = bin2hex(mhash(MHASH_MD5, $LID . "^" . $sequence . "^" . $timestamp . "^" . $pvalue . "^", $Key)); 
		}

		$md5hash=$secret.$LID.$invoice.$pvalue;
		$checkid=md5($md5hash);
		if(isset($_COOKIE['ap_id'])) $espost['affiliate'] = $_COOKIE['ap_id'];
		orderhandle($espost,$checkid);
		if(isset($_COOKIE['ap_id'])) unset($espost['affiliate']);
		//we use some of this data, so it needs to be available
		$espost['auth']['sequence']=$sequence;
		$espost['auth']['timestamp']=$timestamp;
		$espost['auth']['subinv']=$subinv;
		$espost['auth']['invoice']=$invoice;
		$espost['auth']['fingerprint']=$fingerprint;
		$_SESSION['espost'.$blog_id]=$espost;
		return($espost);
	}
}
if (!function_exists('eshop_paypal_redirect')) {
 	function eshop_paypal_redirect($espost){
 		global $blog_id,$eshopoptions,$wpdb,$wp_query;
		if($espost['eshop_payment']!='paypal')
			return;
		$paymentmethod='paypal';
		if (isset ($espost['ppsubmit'])) {
			$eshopmgincpath=apply_filters('eshop_mg_inc_path',ESHOP_PATH.$paymentmethod.'.php',$paymentmethod);
			include($eshopmgincpath);
		}

		//auto-redirect bits
		header('Cache-Control: no-cache, no-store, must-revalidate'); //HTTP/1.1
		header('Expires: Sun, 01 Jul 2005 00:00:00 GMT');
		header('Pragma: no-cache'); //HTTP/1.0

		//enters all the data into the database
		$token = uniqid(md5($_SESSION['date'.$blog_id]), true);

		//was $pvalue = $espost['amount'] + $espost['shipping_1'];
		$pvalue = $espost['amount'] + eshopShipTaxAmt();
		$espost['custom']=$_SESSION['date'.$blog_id];
		//eShop own check for extra security
		$eshopemailbus=$eshopoptions['business'];
		if(isset( $eshopoptions['business_sec'] ) && $eshopoptions['business_sec'] !=''){
			$eshopemailbus=$eshopoptions['business_sec'];
			$espost['business']=$eshopemailbus;
		}
		$checkid=md5($eshopemailbus.$token.number_format($pvalue,2));
		//affiliates
		if(isset($_COOKIE['ap_id'])) $espost['affiliate'] = $_COOKIE['ap_id'];
		orderhandle($espost,$checkid);
		if(isset($_COOKIE['ap_id'])) unset($espost['affiliate']);
		$espost['custom']=$token;
		$_SESSION['espost'.$blog_id]=$espost;
		return($espost);
	}
}
if (!function_exists('eshop_cash_redirect')) {
 	function eshop_cash_redirect($espost){
 		global $blog_id,$eshopoptions,$wpdb,$wp_query;
		if($espost['eshop_payment']!='cash')
			return;

		//enters all the data into the database
		$cash = $eshopoptions['cash']; 
		if(!isset($espost['RefNr'])){
			$espost['RefNr']=uniqid(rand());
			//$ecash->ipn_data['RefNr']=$espost['RefNr'];
		}
		$checkid=md5($espost['RefNr']);
		//affiliate
		if(isset($_COOKIE['ap_id'])) $espost['affiliate'] = $_COOKIE['ap_id'];
		orderhandle($espost,$checkid);
		if(isset($_COOKIE['ap_id'])) unset($espost['affiliate']);
		$_SESSION['espost'.$blog_id]=$espost;
		return($espost);
	}
}
if (!function_exists('eshop_bank_redirect')) {
 	function eshop_bank_redirect($espost){
 		global $blog_id,$eshopoptions,$wpdb,$wp_query;
		if($espost['eshop_payment']!='bank')
			return;

		//enters all the data into the database
		$cash = $eshopoptions['bank']; 
		if(!isset($espost['RefNr'])){
			$espost['RefNr']=uniqid(rand());
		}
		$checkid=md5($espost['RefNr']);
		//affiliate
		if(isset($_COOKIE['ap_id'])) $espost['affiliate'] = $_COOKIE['ap_id'];
		orderhandle($espost,$checkid);
		if(isset($_COOKIE['ap_id'])) unset($espost['affiliate']);
		$_SESSION['espost'.$blog_id]=$espost;
		return($espost);
	}
}
?>