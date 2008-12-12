<?php
//make it available
add_action('admin_menu', 'eshop_add_custom_box');
/* Use the save_post action to do something with the data entered */
add_action('save_post', 'eshop_save_postdata');

/* Adds a custom section to the "advanced" Post and Page edit screens */
function eshop_add_custom_box() {

  if( function_exists( 'add_meta_box' )) {
    add_meta_box( 'epagepostcustom', __( 'Product Entry', 'eshop' ), 
                'eshop_inner_custom_box', 'post', 'normal','high' );
    add_meta_box( 'epagepostcustom', __( 'Product Entry', 'eshop' ), 
                'eshop_inner_custom_box', 'page', 'normal' );
   }
}
   
/* Prints the inner fields for the custom post/page section */
function eshop_inner_custom_box() {
	global $wpdb;
  	// Use nonce for verification
	echo '<input type="hidden" name="eshop_noncename" id="eshop_noncename" value="' . 
	wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	// The actual fields for data entry
	$sku=$prod=$shiprate=$stkqty='';
	$stkav=$featured='No';
	if( isset( $_REQUEST[ 'post' ] ) ) {
		$sku = get_post_meta( $_REQUEST[ 'post' ], '_Sku' );
	    $sku = stripslashes(attribute_escape($sku[ 0 ]));
	    
	    $prod=get_post_meta( $_REQUEST[ 'post' ], '_Product Description' );
	    $prod=stripslashes(attribute_escape($prod[ 0 ]));
	    
	    $shiprate = get_post_meta( $_REQUEST[ 'post' ], '_Shipping Rate' );
		$shiprate = attribute_escape($shiprate[ 0 ]);
		
		$featured = get_post_meta( $_REQUEST[ 'post' ], '_Featured Product' );
		$featured = attribute_escape($featured[ 0 ]);
		
		$stkav = get_post_meta( $_REQUEST[ 'post' ], '_Stock Available' );
		$stkav = attribute_escape($stkav[ 0 ]);
		
		$stkqty = get_post_meta( $_REQUEST[ 'post' ], '_Stock Quantity' );
		$stkqty = attribute_escape($stkqty[ 0 ]);
		
    }
    if($stkav=='' && $featured==''){
    	$stkav=$featured='No';
    }
	?>
	<p><label for="eshop_sku"><?php _e('Sku','eshop'); ?> </label><input id="eshop_sku" name="eshop_sku" value="<?php echo $sku; ?>" type="text" size="20" /></p>
	<p><label for="eshop_product_description"><?php _e('Product Description','eshop'); ?> </label><input id="eshop_product_description" name="eshop_product_description" value="<?php echo $prod; ?>" type="text" size="30" /></p>
	<p>
	<?php
	$numoptions=get_option('eshop_options_num');
	for($i=1;$i<=$numoptions;$i++){
		if( isset( $_REQUEST[ 'post' ] ) ) {
			$opt = get_post_meta( $_REQUEST[ 'post' ], '_Option '.$i );
			$opt = stripslashes(attribute_escape($opt[ 0 ]));

			$price=get_post_meta( $_REQUEST[ 'post' ], '_Price '.$i );
			$price=stripslashes(attribute_escape($price[ 0 ]));
   		}else{
   			$opt=$price='';
   		}
		?>
		<label for="eshop_option_<?php echo $i; ?>"><?php _e('Option','eshop'); ?> <?php echo $i; ?> </label><input id="eshop_option_<?php echo $i; ?>" name="eshop_option_<?php echo $i; ?>" value="<?php echo $opt; ?>" type="text" size="20" />
		<label for="eshop_price_<?php echo $i; ?>"><?php _e('Price','eshop'); ?> <?php echo $i; ?> </label><input id="eshop_price_<?php echo $i; ?>" name="eshop_price_<?php echo $i; ?>" value="<?php echo $price; ?>" type="text" size="6" /><br />
		<?php
	}
	?>
	</p>
	<?php
	//get list of download products for selection
	$producttable = $wpdb->prefix ."eshop_downloads";
	$max = $wpdb->get_var("SELECT COUNT(id) FROM $producttable WHERE id > 0");
	if($max>0){ // only show if downloads available!
		$myrowres=$wpdb->get_results("Select * From $producttable");
		if( isset( $_REQUEST[ 'post' ] ) ) {
			$downl = get_post_meta( $_REQUEST[ 'post' ], '_Product Download' );
			$downl = attribute_escape($downl[ 0 ]);
			if($downl!=''){
				$selected = $downl;
			}else{
				$selected = '';
			}
		}else{
		      $selected = '';
    	}
		?>
		<p><label for="eshop_product_download"><?php _e('Product Download','eshop'); ?></label><select name="eshop_product_download" id="eshop_product_download">
		<option value=""><?php _e('No (or select)','eshop'); ?></option>
		<?php
		foreach($myrowres as $prow){
			$checked = ( trim( $prow->id ) == trim( $selected ) ) ? 'selected="selected"' : '';
			echo '<option value="'.$prow->id.'"'.$checked.'>'.$prow->title.'</option>'."\n";
		}
		?>
		</select></p>
		<?php
	}
	if(get_option('eshop_downloads_only') !='yes'){
	?>
	<p><label for="eshop_shipping_rate"><?php _e('Shipping Rate','eshop'); ?> </label><select name="eshop_shipping_rate" id="eshop_shipping_rate">
	<option value="">Select</option>
	<?php
	if( isset( $_REQUEST[ 'post' ] ) ) {
		if($shiprate!=''){
			$selected = $shiprate;
		}else{
			$selected = '';
		}
	}else{
		  $selected = '';
	}
	$shipcodes=array('A','B','C','D','E','F');
	$size = sizeof($shipcodes)-1;
	for($i=0;$i<=$size;$i++){
		$checked = ( trim($shipcodes[$i]) == trim( $shiprate ) ) ? 'selected="selected"' : '';
		echo '<option value="'.$shipcodes[$i].'"'.$checked.'>'.$shipcodes[$i]."</option>\n";
	}
	?>
	</select></p>
	<?php
	}else{
	?>
	<input type="hidden" name="eshop_shipping_rate" value="F" />
	<?php
	}
	?>
	
	<h4><?php _e('Featured Product','eshop'); ?></h4>
	<p>
	<input id="eshop_featured_product_yes" name="eshop_featured_product" value="Yes"<?php echo $featured=='Yes' ? 'checked="checked"' : ''; ?> type="radio" /> <label for="eshop_featured_product_yes" class="selectit"><?php _e('Yes','eshop'); ?></label>
	<input id="eshop_featured_product_no" name="eshop_featured_product" value="No" <?php echo $featured=='No' ? 'checked="checked"' : ''; ?>type="radio" /> <label for="eshop_featured_product_no" class="selectit"><?php _e('No','eshop'); ?></label>
	</p>
	<h4><?php _e('Stock Available','eshop'); ?></h4>
	<p>
	<input id="eshop_stock_available_yes" name="eshop_stock_available" value="Yes"<?php echo $stkav=='Yes' ? 'checked="checked"' : ''; ?> type="radio" /> <label for="eshop_stock_available_yes" class="selectit"><?php _e('Yes','eshop'); ?></label>
	<input id="eshop_stock_available_no" name="eshop_stock_available" value="No"<?php echo $stkav=='No' ? 'checked="checked"' : ''; ?> type="radio" /> <label for="eshop_stock_available_no" class="selectit"><?php _e('No','eshop'); ?></label>
	</p>
	<?php
	if(get_option('eshop_stock_control')=='yes'){
	?>
		<p>
		<label for="eshop_stock_quantity"><?php _e('Stock Quantity','eshop'); ?></label><input id="eshop_stock_quantity" name="eshop_stock_quantity" value="<?php echo $stkqty; ?>" type="text" size="4" />
		</p>
	<?php
	}
}

/* When the post is saved, saves our custom data */
function eshop_save_postdata( $post_id ) {
	global $wpdb;
  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['eshop_noncename'], plugin_basename(__FILE__) )) {
    return $post_id;
  }

  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ))
      return $post_id;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ))
      return $post_id;
  }
	if( !isset( $id ) )
		$id = $post_id;
  // OK, we're authenticated: we need to find and save the data

	$mydata['_Sku']=$_POST['eshop_sku'];
	$numoptions=get_option('eshop_options_num');
	for($i=1;$i<=$numoptions;$i++){
		$mydata['_Option '.$i]=$_POST['eshop_option_'.$i];
		$mydata['_Price '.$i]=$_POST['eshop_price_'.$i];
	}
	$mydata['_Product Description']=$_POST['eshop_product_description'];
	$mydata['_Product Download']=$_POST['eshop_product_download'];
	$mydata['_Shipping Rate']=$_POST['eshop_shipping_rate'];
	$mydata['_Featured Product']=$_POST['eshop_featured_product'];
	$mydata['_Stock Available']=$_POST['eshop_stock_available'];
	$mydata['_Stock Quantity']=$_POST['eshop_stock_quantity'];
	if($mydata['_Stock Quantity']!='' && is_numeric($mydata['_Stock Quantity'])){
		$meta_value=$mydata['_Stock Quantity'];
		$stocktable=$wpdb->prefix ."eshop_stock";
		$results=$wpdb->get_results("select post_id from $stocktable");
		if(!empty($results)){
			$found='no';
			foreach($results as $r){
				if($id==$r->post_id){//update
					$wpdb->query($wpdb->prepare("UPDATE $stocktable set available=$meta_value where post_id=$id"));
					$found='yes';
				}
			}
			if($found=='no'){
				$wpdb->query($wpdb->prepare("INSERT INTO $stocktable (post_id,available,purchases) VALUES ($id,$meta_value,0)"));
			}
		}else{
			$wpdb->query($wpdb->prepare("INSERT INTO $stocktable (post_id,available,purchases) VALUES ($id,$meta_value,0)"));
		}

	}
	if($mydata['_Sku']=='' && $mydata['_Option 1']=='' &&	$mydata['_Price 1']=='' && $mydata['_Product Description']=='' 
	&& $mydata['_Product Download']=='' && $mydata['_Shipping Rate']=='' && $mydata['_Stock Quantity']==''){
		//delete all
		foreach($mydata as $title=>$meta_value){
			delete_post_meta( $id, $title );
		}
	}else{
		foreach($mydata as $title=>$meta_value){
			delete_post_meta( $id, $title );
			add_post_meta( $id, $title, $meta_value);
		}
		$numboptions=get_option('eshop_options_num');
		for($i=1;$i<=$numboptions;$i++){
			$otitle='_Option '.$i;
			$ometa_value = $_POST['eshop_option_'.$i];
			$ptitle='_Price '.$i;
			$pmeta_value = $_POST['eshop_price_'.$i];
			if($ometa_value!='' && $pmeta_value!=''){
				$temp_price=$pmeta_value;
			}elseif($ometa_value!='' && $pmeta_value==''){
				add_post_meta( $id, $ptitle, $temp_price );
			}elseif($ometa_value=='' && $pmeta_value!=''){
				delete_post_meta( $id, $ptitle );
			}
		}
	}
   return $mydata;
}
?>
