<?php
if ('webtopay.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');
     
/*******************************************************************************
 *                      PHP webtopay IPN Integration Class
 *******************************************************************************
 *      Author:     Rich Pedley
 *      Based on: Paypal class
 *      
 *      To submit an order to webtopay, have your order form POST to a file with:
 *
 *          $p = new webtopay_class;
 *          $p->add_field('business', 'somebody@domain.com');
 *          $p->add_field('first_name', $_POST['first_name']);
 *          ... (add all your fields in the same manor)
 *          $p->submit_webtopay_post();
 *
 *      To process an IPN, have your IPN processing file contain:
 *
 *          $p = new webtopay_class;
 *          if ($p->validate_ipn()) {
 *          ... (IPN is verified.  Details are in the ipn_data() array)
 *          }
 * 
 *******************************************************************************
*/

class webtopay_class {
    
   var $last_error;                 // holds the last error encountered
   var $ipn_response;               // holds the IPN response from paypal   
   var $ipn_data = array();         // array contains the POST values for IPN
   var $fields = array();           // array holds the fields to submit to paypal
   
   function webtopay_class() {
       
      // initialization constructor.  Called when class is created.
      $this->last_error = '';
      $this->ipn_response = '';
    
   }
   
   function add_field($field, $value) {
      
      // adds a key=>value pair to the fields array, which is what will be 
      // sent to webtopay as POST variables.  If the value is already in the 
      // array, it will be overwritten.
      
      $this->fields["$field"] = $value;
   }

   function submit_webtopay_post() {
      // The user will briefly see a message on the screen that reads:
      // "Please wait, your order is being processed..." and then immediately
      // is redirected to webtopay.

      $echo= "<form method=\"post\" class=\"eshop\" action=\"".$this->autoredirect."\"><div>\n";

      foreach ($this->fields as $name => $value) {
         $echo.= "<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
      }
      $refid=uniqid(rand());
      $echo .= "<input type=\"hidden\" name=\"RefNr\" value=\"$refid\" />\n";
      $echo.='<label for="ppsubmit"><small>'.__('<strong>Note:</strong> Submit to finalize order at webtopay.','eshop').'</small><br />
      <input class="button submit2" type="submit" id="ppsubmit" name="ppsubmit" value="'.__('Proceed to Checkout &raquo;','eshop').'" /></label>';
	  $echo.="</div></form>\n";
      
      return $echo;
   }
	function eshop_submit_webtopay_post($_POST) {
      // The user will briefly see a message on the screen that reads:
      // "Please wait, your order is being processed..." and then immediately
      // is redirected to webtopay.
      $webtopay = get_option('eshop_webtopay');
		$echortn='<div id="process">
         <p><strong>'.__('Please wait, your order is being processed&#8230;','eshop').'</strong></p>
	     <p>'. __('If you are not automatically redirected to webtopay, please use the <em>Proceed to webtopay</em> button.','eshop').'</p>
         <form method="post" id="eshopgateway" class="eshop" action="'.$this->webtopay_url.'">
          <p>';
          	$replace = array("&#039;","'", "\"","&quot;","&amp;","&");
          	
			$webtopay = get_option('eshop_webtopay'); 
			
			$Cost = $_POST['amount']-$_POST['shipping_1'];
			
			$ExtraCost = $_POST['shipping_1'];
			
			$desc = str_replace($replace, " ", $webtopay['description']);
			
			if($_POST['amount']<$webtopay['minimum'])
			{
				$adjust = $webtopay['minimum'] - $_POST['amount'];
				
				$Cost = $Cost + $adjust;
				
				$desc .= ' '.sprintf(__('webtopay minimum of %s applied.','eshop'),$webtopay['minimum']);
			}
			
			// - Callback cannot be with GET vars -
			
			$callbackURL = strtr($_POST['notify_url'], array('&amp;' => '&'));
			
			list($callbackURL, $getCallback) = explode('?', $callbackURL);
			
			$echortn.=' 
			<input type="hidden" name="MerchantID" value="'.$webtopay['id'].'" />
			<input type="hidden" name="OrderID" value="'.$_POST['RefNr'].'" />
			<input type="hidden" name="Lang" value="' . $webtopay['lang'] . '" />
			<input type="hidden" name="Currency" value="' . get_option('eshop_currency') . '" />
			
			<input type="hidden" name="Amount" value="'. (($Cost + $ExtraCost) * 100) .'" />
			
			<input type="hidden" name="AcceptURL" value="'.get_permalink(get_option('eshop_cart_success')).'" />
			
			<input type="hidden" name="CancelUrl" value="'.get_permalink(get_option('eshop_checkout')).'" />
			<input type="hidden" name="CallbackURL" value="'.$callbackURL.'" />
			
			<input type="hidden" name="PayText" value="'.__('Payment for goods and services (of no. [order_nr]) ([site_name])','eshop').'" />
			
			<input type="hidden" name="p_firstname" value="'.$_POST['first_name'].'">			
			<input type="hidden" name="p_lastname" value="'.$_POST['last_name'].'">			
			<input type="hidden" name="p_email" value="'.$_POST['email'].'">			
			<input type="hidden" name="p_street" value="' . $_POST['address1'].' '. $_POST['address2'] . '">			
			<input type="hidden" name="p_city" value="'.$_POST['city'].'">			
			<input type="hidden" name="p_state" value="'.$_POST['state'].'">			
			<input type="hidden" name="p_zip" value="'.$_POST['zip'].'">			
			<input type="hidden" name="p_countrycode" value="'.$_POST['country'].'">	
			
			<input type="hidden" name="BuyerEmail" value="'.$_POST['email'].'" />
			<input type="hidden" name="BuyerFirstName" value="'.$_POST['first_name'].'" />
			<input type="hidden" name="BuyerLastName" value="'.$_POST['last_name'].'" />
			<input type="hidden" name="RefNr" value="'.$_POST['RefNr'].'" />
			<input type="hidden" name="custom" value="'.$_POST['custom'].'" />
			<input type="hidden" name="OkUrl" value="'.$_POST['notify_url'].'" />';
			
			if (trim($getCallback) != '')
			{
				$arrCallback = explode('&', $getCallback);

				foreach ($arrCallback as $num => $value)
				{
					list($num, $value) = explode('=', $value);
					
					$echortn .= '<input type="hidden" name="' . $num . '" value="' . $value . '" />';
				}
			}
			
			$echortn.=' 			
			<input type="hidden" name="test" value="'.(get_option('eshop_status')=='live' ? 0 : 1).'" />

         <input class="button" type="submit" id="ppsubmit" name="ppsubmit" value="'. __('Proceed to webtopay &raquo;','eshop').'" /></p>
	     </form>
	  </div>';
		return $echortn;
   }   
   function validate_ipn() {
      // generate the post string from the _POST vars aswell as load the
      // _POST vars into an arry so we can play with them from the calling
      // script.
      foreach ($_REQUEST as $field=>$value) { 
         $this->ipn_data["$field"] = $value;
      }
     
   }

}   