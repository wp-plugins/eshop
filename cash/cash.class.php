<?php
if ('cash.class.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');
     
/*******************************************************************************
 *                      PHP cash IPN Integration Class
 *******************************************************************************
 *      Author:     Rich Pedley
 *      Based on: Paypal class
 *      
 *      To submit an order to cash, have your order form POST to a file with:
 *
 *          $p = new cash_class;
 *          $p->add_field('business', 'somebody@domain.com');
 *          $p->add_field('first_name', $_POST['first_name']);
 *          ... (add all your fields in the same manor)
 *          $p->submit_cash_post();
 *
 *      To process an IPN, have your IPN processing file contain:
 *
 *          $p = new cash_class;
 *          if ($p->validate_ipn()) {
 *          ... (IPN is verified.  Details are in the ipn_data() array)
 *          }
 * 
 *******************************************************************************
*/

class cash_class {
    
   var $last_error;                 // holds the last error encountered
   var $ipn_response;               // holds the IPN response from paypal   
   var $ipn_data = array();         // array contains the POST values for IPN
   var $fields = array();           // array holds the fields to submit to paypal
   
   function cash_class() {
       
      // initialization constructor.  Called when class is created.
      $this->last_error = '';
      $this->ipn_response = '';
    
   }
   
   function add_field($field, $value) {
      
      // adds a key=>value pair to the fields array, which is what will be 
      // sent to cash as POST variables.  If the value is already in the 
      // array, it will be overwritten.
      
      $this->fields["$field"] = $value;
   }

   function submit_cash_post() {
      // The user will briefly see a message on the screen that reads:
      // "Please wait, your order is being processed..." and then immediately
      // is redirected to cash.

      $echo= "<form method=\"post\" class=\"eshop eshop-confirm\" action=\"".$this->autoredirect."\"><div>\n";

      foreach ($this->fields as $name => $value) {
      		$pos = strpos($name, 'amount');
      		if ($pos === false) {
      			$value=stripslashes($value);
      	       $echo.= "<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
      	    }else{
      	    	$echo .= eshopTaxCartFields($name,$value);
      	    }
      }
      $refid=uniqid(rand());
      $echo .= "<input type=\"hidden\" name=\"RefNr\" value=\"$refid\" />\n";
      $echo.='<label for="ppsubmit" class="finalize"><small>'.__('<strong>Note:</strong> Submit to finalize your order.','eshop').'</small><br />
      <input class="button submit2" type="submit" id="ppsubmit" name="ppsubmit" value="'.__('Proceed to Checkout &raquo;','eshop').'" /></label>';
	  $echo.="</div></form>\n";
      
      return $echo;
   }
	function eshop_submit_cash_post($espost) {
      // The user will briefly see a message on the screen that reads:
      // "Please wait, your order is being processed..." and then immediately
      // is redirected to cash.
      global $eshopoptions;
      $cash = $eshopoptions['cash'];
		$echortn ='<div id="process">
         <p><strong>'. __('Please wait, your order is being processed&#8230;','eshop').'</strong></p>
	     <p>'. __('If you are not automatically redirected, please use the <em>Proceed</em> button.','eshop').'</p>
         <form method="post" id="eshopgateway" class="eshop" action="'.$this->cash_url.'">
          <p>';
          	$replace = array("&#039;","'", "\"","&quot;","&amp;","&");
			$cash = $eshopoptions['cash']; 
			$refid=$espost['RefNr'];
			$echortn .='<input type="hidden" name="BuyerEmail" value="'.$espost['email'].'" />
			<input type="hidden" name="BuyerFirstName" value="'.$espost['first_name'].'" />
			<input type="hidden" name="BuyerLastName" value="'.$espost['last_name'].'" />
			<input type="hidden" name="RefNr" value="'.$refid.'" />
         <input class="button" type="submit" id="ppsubmit" name="ppsubmit" value="'. __('Proceed &raquo;','eshop').'" /></p>
	     </form>
	  </div>';
		global $eshopoptions;
		if($eshopoptions['status']!='live'){
			$echortn .= "<p class=\"testing\"><strong>".__('Test Mode &#8212; No money will be collected. This page will not auto redirect in test mode.','eshop')."</strong></p>\n";
		}	  
		return $echortn;
   } 
   function dump_fields() {
    
         // Used for debugging, this function will output all the field/value pairs
         // that are currently defined in the instance of the class using the
         // add_field() function.
         
         echo "<h3>eshop_paypal_class->dump_fields() Output:</h3>";
         echo "<table width=\"95%\" border=\"1\" cellpadding=\"2\" cellspacing=\"0\">
               <tr>
                  <td bgcolor=\"black\"><b><font color=\"white\">Field Name</font></b></td>
                  <td bgcolor=\"black\"><b><font color=\"white\">Value</font></b></td>
               </tr>"; 
         
         ksort($this->fields);
         foreach ($this->fields as $key => $value) {
            echo "<tr><td>$key</td><td>".urldecode($value)."&nbsp;</td></tr>";
         }
    
         echo "</table><br>"; 
   }
}   