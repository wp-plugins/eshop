<?php
if ('payson.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');
     
/*******************************************************************************
 *                      PHP Payson IPN Integration Class
 *******************************************************************************
 *      Author:     Rich Pedley
 *      Based on: Paypal class
 *      
 *      To submit an order to payson, have your order form POST to a file with:
 *
 *          $p = new payson_class;
 *          $p->add_field('business', 'somebody@domain.com');
 *          $p->add_field('first_name', $_POST['first_name']);
 *          ... (add all your fields in the same manor)
 *          $p->submit_payson_post();
 *
 *      To process an IPN, have your IPN processing file contain:
 *
 *          $p = new payson_class;
 *          if ($p->validate_ipn()) {
 *          ... (IPN is verified.  Details are in the ipn_data() array)
 *          }
 * 
 *******************************************************************************
*/

class payson_class {
    
   var $last_error;                 // holds the last error encountered
   var $ipn_response;               // holds the IPN response from paypal   
   var $ipn_data = array();         // array contains the POST values for IPN
   var $fields = array();           // array holds the fields to submit to paypal
   
   function payson_class() {
       
      // initialization constructor.  Called when class is created.
      $this->last_error = '';
      $this->ipn_response = '';
    
   }
   
   function add_field($field, $value) {
      
      // adds a key=>value pair to the fields array, which is what will be 
      // sent to payson as POST variables.  If the value is already in the 
      // array, it will be overwritten.
      
      $this->fields["$field"] = $value;
   }

   function submit_payson_post() {
      // The user will briefly see a message on the screen that reads:
      // "Please wait, your order is being processed..." and then immediately
      // is redirected to payson.

      $echo= "<form method=\"post\" class=\"eshop\" action=\"".$this->autoredirect."\"><div>\n";

      foreach ($this->fields as $name => $value) {
         $echo.= "<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
      }
      $refid=uniqid(rand());
      $echo .= "<input type=\"hidden\" name=\"RefNr\" value=\"$refid\" />\n";
      $echo.='<label for="ppsubmit"><small>'.__('<strong>Note:</strong> Submit to finalize order at Payson.','eshop').'</small><br />
      <input class="button submit2" type="submit" id="ppsubmit" name="ppsubmit" value="'.__('Proceed to Checkout &raquo;','eshop').'" /></label>';
	  $echo.="</div></form>\n";
      
      return $echo;
   }
	function eshop_submit_payson_post($_POST) {
      // The user will briefly see a message on the screen that reads:
      // "Please wait, your order is being processed..." and then immediately
      // is redirected to payson.
      $payson = get_option('eshop_payson');
		?>
       <div id="process">
         <p><strong><?php _e('Please wait, your order is being processed&#8230;','eshop'); ?></strong></p>
	     <p><?php _e('If you are not automatically redirected to Payson, please use the <em>Proceed to Payson</em> button.','eshop'); ?></p>
         <form method="post" id="eshopgateway" class="eshop" action="<?php echo $this->payson_url; ?>">
          <p><?php
          	$replace = array("&#039;","'", "\"","&quot;","&amp;","&");
			$payson = get_option('eshop_payson'); 
			$Key=$payson['key'];
			$Cost=$_POST['amount']-$_POST['shipping_1'];
			$ExtraCost=$_POST['shipping_1'];
			$desc = str_replace($replace, " ", $payson['description']);
			if($_POST['amount']<$payson['minimum']){
				$adjust=$payson['minimum']-$_POST['amount'];
				$Cost=$Cost+$adjust;
				$desc .= ' '.sprintf(__('Payson minimum of %s SEK applied.','eshop'),$payson['minimum']);
			}
			
			$Cost=number_format($Cost, 2, ',', '');
			$ExtraCost=number_format($ExtraCost, 2, ',', '');
			$OkUrl=$_POST['notify_url'];
			$GuaranteeOffered='1';
			$MD5string = $payson['email'] . ":" . $Cost . ":" . $ExtraCost . ":" . $OkUrl . ":" . $GuaranteeOffered . $Key;
			$MD5Hash = md5($MD5string);
			$refid=$_POST['RefNr'];
			?>
			<input type="hidden" name="AgentId" value="<?php echo $payson['id']; ?>" />
			<input type="hidden" name="SellerEmail" value="<?php echo $payson['email']; ?>" />
			<input type="hidden" name="Description" value="<?php echo $desc; ?>" />
			<input type="hidden" name="GuaranteeOffered" value="1" />
			<input type="hidden" name="OkUrl" value="<?php echo $OkUrl; ?>" />
			<input type="hidden" name="CancelUrl" value="<?php echo $_POST['cancel_return']; ?>" />
			<input type="hidden" name="MD5" value="<?php echo $MD5Hash; ?>" />
			<input type="hidden" name="BuyerEmail" value="<?php echo $_POST['email']; ?>" />
			<input type="hidden" name="BuyerFirstName" value="<?php echo $_POST['first_name']; ?>" />
			<input type="hidden" name="BuyerLastName" value="<?php echo $_POST['last_name']; ?>" />
			<input type="hidden" name="Cost" value="<?php echo $Cost; ?>" />
			<input type="hidden" name="ExtraCost" value="<?php echo $ExtraCost; ?>" />
			<input type="hidden" name="RefNr" value="<?php echo $refid; ?>" />

         <input class="button" type="submit" id="ppsubmit" name="ppsubmit" value="<?php _e('Proceed to Payson &raquo;','eshop'); ?>" /></p>
	     </form>
	  </div>
	  <?php
		return;
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