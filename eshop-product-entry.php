<?php
//make it available
add_action('admin_menu', 'eshop_add_custom_box');
/* Use the save_post action to do something with the data entered */
add_action('save_post', 'eshop_save_postdata');
add_action('admin_head-post.php', 'eshop_check_error'); // called after the redirect
/* Adds a custom section to the "advanced" Post and Page edit screens */
function eshop_add_custom_box() {

  if( function_exists( 'add_meta_box' )) {
  	get_currentuserinfo() ;
	if(current_user_can('eShop')){
    	add_meta_box( 'epagepostcustom', __( 'Product Entry', 'eshop' ), 
                'eshop_inner_custom_box', 'post', 'normal','high' );
   		add_meta_box( 'epagepostcustom', __( 'Product Entry', 'eshop' ), 
                'eshop_inner_custom_box', 'page', 'normal' );
    }
   }
}
   
/* Prints the inner fields for the custom post/page section */
function eshop_inner_custom_box($post) {
    global $wpdb,$eshopoptions;
      // Use nonce for verification
    echo '<input type="hidden" name="eshop_noncename" id="eshop_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
    // The actual fields for data entry
    $osets=array();
    if(isset($_REQUEST[ 'post' ])){
    	$stkav=get_post_meta( $_REQUEST[ 'post' ], '_eshop_stock',true );
    	$eshop_product=get_post_meta( $_REQUEST[ 'post' ], '_eshop_product',true );
    }else{
    	$stkav='';
    	$eshop_product=array();
    }
    if(isset($eshop_product['optset']))
		$osets=$eshop_product['optset'];
 
    //recheck stkqty
    $stocktable=$wpdb->prefix ."eshop_stock";
    $stktableqty=$wpdb->get_var("SELECT available FROM $stocktable where post_id=$post->ID");
    if(isset($stktableqty) && is_numeric($stktableqty)) $eshop_product['qty']=$stktableqty;
    ?>
    <h4><?php _e('Product','eshop'); ?></h4>

    <p><label for="eshop_sku"><?php _e('Sku','eshop'); ?> </label><input id="eshop_sku" name="eshop_sku" value="<?php if (isset($eshop_product['sku'])) echo $eshop_product['sku']; ?>" type="text" size="20" /> <?php _e('(unique identification reference eg. abc001)','eshop'); ?></p>
    <p><label for="eshop_product_description"><?php _e('Product Description','eshop'); ?> </label><input id="eshop_product_description" name="eshop_product_description" value="<?php if (isset($eshop_product['description'])) echo $eshop_product['description']; ?>" type="text" size="30" /></p>
    <?php
    //get list of download products for selection 
    $producttable = $wpdb->prefix ."eshop_downloads";
    $myrowres=$wpdb->get_results("Select * From $producttable");
    //check for existence of downloads
    $eshopdlavail = $wpdb->get_var("SELECT COUNT(id) FROM $producttable WHERE id > 0");
    $numoptions=$eshopoptions['options_num'];
    ?>
    <table class="hidealllabels widefat eshoppopt" summary="<?php _e('Product Options by option price and download','eshop'); ?>">
    <caption><?php _e('Product Options','eshop'); ?></caption>
    <thead><tr><th id="eshopnum">#</th><th id="eshopoption"><?php _e('Option','eshop'); ?></th><th id="eshopprice"><?php _e('Price','eshop'); ?></th><?php if($eshopdlavail>0){ ?><th id="eshopdownload"><?php _e('Download','eshop'); ?></th><?php } ?><?php if($eshopoptions['shipping']=='4'){?><th id="eshopweight"><?php _e('Weight','eshop'); ?></th><?php } ?></tr></thead>
        <tbody>
        <?php
		for($i=1;$i<=$numoptions;$i++){
			if(isset($eshop_product['products']) && is_array($eshop_product['products'])){
				$opt=$eshop_product['products'][$i]['option'];
				$price=$eshop_product['products'][$i]['price'];
				$downl=$eshop_product['products'][$i]['download'];
				if(isset($eshop_product['products'][$i]['weight'])) 
					$weight=$eshop_product['products'][$i]['weight'];
				else
					$weight='';
			}else{
				$weight=$opt=$price=$downl='';
			}
			?>
			<tr>
			<th id="eshopnumrow<?php echo $i; ?>" headers="eshopnum"><?php echo $i; ?></th>
			<td headers="eshopoption eshopnumrow<?php echo $i; ?>"><label for="eshop_option_<?php echo $i; ?>"><?php _e('Option','eshop'); ?> <?php echo $i; ?></label><input id="eshop_option_<?php echo $i; ?>" name="eshop_option_<?php echo $i; ?>" value="<?php echo $opt; ?>" type="text" size="20" /></td>
			<td headers="eshopprice eshopnumrow<?php echo $i; ?>"><label for="eshop_price_<?php echo $i; ?>"><?php _e('Price','eshop'); ?> <?php echo $i; ?></label><input id="eshop_price_<?php echo $i; ?>" name="eshop_price_<?php echo $i; ?>" value="<?php echo $price; ?>" type="text" size="6" /></td>
			<?php if($eshopdlavail>0){ ?>
			<td headers="eshopdownload eshopnumrow<?php echo $i; ?>"><label for="eshop_download_<?php echo $i; ?>"><?php _e('Download','eshop'); ?> <?php echo $i; ?></label><select name="eshop_download_<?php echo $i; ?>" id="eshop_download_<?php echo $i; ?>">
			   <option value=""><?php _e('No (or select)','eshop'); ?></option>
				<?php
				foreach($myrowres as $prow){
					$checked = ( trim( $prow->id ) == trim( $downl ) ) ? ' selected="selected"' : '';
					echo '<option value="'.$prow->id.'"'.$checked.'>'.$prow->title.'</option>'."\n";
				}
				?>
				</select></td>
			<?php } ?>
			<?php if($eshopoptions['shipping']=='4'){//shipping by weight 
			?>
			<td headers="eshopweight eshopnumrow<?php echo $i; ?>"><label for="eshop_weight_<?php echo $i; ?>"><?php _e('Weight','eshop'); ?> <?php echo $i; ?></label><input id="eshop_weight_<?php echo $i; ?>" name="eshop_weight_<?php echo $i; ?>" value="<?php echo $weight; ?>" type="text" size="6" /></td>
			<?php } ?>
				</tr>
				<?php
		 }
    ?>
    </tbody>
	</table>
	<?php
	$opttable=$wpdb->prefix.'eshop_option_names';
	$myrowres=$wpdb->get_results("select *	from $opttable ORDER BY name ASC");
	if(sizeof($myrowres)>0){
	?>
	<div id="eshoposetc">
	<h4><?php _e('Option Sets','eshop'); ?></h4>
	<div id="eshoposets">
	<ul>
	<?php
	$oi=1;
	if(!is_array($osets)) $osets=array();
	foreach($myrowres as $row){
	?>
		<li><input type="checkbox" name="eshoposets[]" id="osets<?php echo $oi; ?>" value="<?php echo $row->optid; ?>"<?php if(in_array($row->optid,$osets)) echo ' checked="checked"'; ?> /><label for="osets<?php echo $oi; ?>"><?php echo stripslashes(esc_attr($row->name))?></label></li>
	<?php
		$oi++;
	}
	?>
	</ul>
	</div>
	</div>
	<?php } ?>
	<div id="eshoposetsc">
    <h4><?php _e('Product Settings','eshop'); ?></h4>
    <?php
	if($eshopoptions['downloads_only'] !='yes' && $eshopoptions['shipping']!='4'){
		?>
		<p><label for="eshop_shipping_rate"><?php _e('Shipping Rate','eshop'); ?></label> <select name="eshop_shipping_rate" id="eshop_shipping_rate">
		<option value=""><?php _e('No (or select)','eshop'); ?></option>
		<?php
		if(isset($eshop_product['shiprate']) && $eshop_product['shiprate']!=''){
			$selected = $shiprate;
		}else{
			$selected = '';
			$eshop_product['shiprate']='';
		}
		
		$shipcodes=array('A','B','C','D','E','F');
		$size = sizeof($shipcodes)-1;
		for($i=0;$i<=$size;$i++){
			$checked = ( trim($shipcodes[$i]) == trim( $eshop_product['shiprate'] ) ) ? 'selected="selected"' : '';
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
    <p><input id="eshop_featured_product" name="eshop_featured_product" value="Yes"<?php echo isset($eshop_product['featured']) && $eshop_product['featured']=='Yes' ? 'checked="checked"' : ''; ?> type="checkbox" /> <label for="eshop_featured_product" class="selectit"><?php _e('Featured Product','eshop'); ?></label></p>
    <p><input id="eshop_stock_available" name="eshop_stock_available" value="Yes"<?php echo $stkav=='1' ? 'checked="checked"' : ''; ?> type="checkbox" /> <label for="eshop_stock_available" class="selectit"><?php _e('Stock Available','eshop'); ?></label></p>
    <?php
    if($eshopoptions['stock_control']=='yes'){
    ?>
    <p><label for="eshop_stock_quantity"><?php _e('Stock Quantity','eshop'); ?></label> <input id="eshop_stock_quantity" name="eshop_stock_quantity" value="<?php if(isset($eshop_product['qty'])) echo $eshop_product['qty']; ?>" type="text" size="4" /></p>
    <?php
    }
    ?>
    <h4><?php _e('Form Settings','eshop'); ?></h4>
    <p><label for="eshop_cart_radio"><?php _e('Show Options as','eshop'); ?></label> 
    <select name="eshop_cart_radio" id="eshop_cart_radio">
    	<option value="0"<?php if(isset($eshop_product['cart_radio']) && $eshop_product['cart_radio']=='0') echo ' selected="selected"'; ?>><?php _e('Dropdown Select','eshop'); ?></option>
		<option value="1"<?php if(isset($eshop_product['cart_radio']) && $eshop_product['cart_radio']=='1') echo ' selected="selected"'; ?>><?php _e('Radio Buttons','eshop'); ?></option>
    </select></p>
    <?php
	echo '</div><div class="clear"></div>';

}

/* When the post is saved, saves our custom data */
function eshop_save_postdata( $post_id ) {
	global $wpdb,$eshopoptions;
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if (!isset($_POST['eshop_noncename'])){
		return $post_id;
	}
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
	$stkav=get_post_meta( $post_id, '_eshop_stock',true );
    $eshop_product=get_post_meta( $post_id, '_eshop_product',true );
	
	$eshop_product['sku']=htmlspecialchars($_POST['eshop_sku']);
	$numoptions=$eshopoptions['options_num'];
	for($i=1;$i<=$numoptions;$i++){
		$eshop_product['products'][$i]['option']=htmlspecialchars($_POST['eshop_option_'.$i]);
		$eshop_product['products'][$i]['price']=$_POST['eshop_price_'.$i];
		if(!is_numeric($_POST['eshop_price_'.$i]) && $_POST['eshop_price_'.$i]!=''){
			add_filter('redirect_post_location','eshop_price_error');
		}
		$eshop_product['products'][$i]['download']=$thisdl=$_POST['eshop_download_'.$i];
		$eshop_product['products'][$i]['weight']=$_POST['eshop_weight_'.$i];
		if(!is_numeric($_POST['eshop_weight_'.$i]) && $_POST['eshop_weight_'.$i]!=''){
			add_filter('redirect_post_location','eshop_weight_error');
		}
	}
	$eshop_product['description']=htmlspecialchars($_POST['eshop_product_description']);
	$eshop_product['shiprate']=$_POST['eshop_shipping_rate'];
	if($eshop_product['shiprate']=='') $mydata['_Shipping Rate']='F';
	if(isset($_POST['eshop_featured_product'])){
		$eshop_product['featured']='Yes';
		update_post_meta( $id, '_eshop_featured', 'Yes');
	}else{
		$eshop_product['featured']='no';
		delete_post_meta( $id, '_eshop_featured');
	}
	if(isset($_POST['eshop_stock_available']))
		$stkav='1';
	else
		$stkav='0';
	$eshop_product['qty']=$_POST['eshop_stock_quantity'];
	if($eshop_product['qty']!='' && is_numeric($eshop_product['qty'])){
		$meta_value=$eshop_product['qty'];
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
	//form setup
	$eshop_product['cart_radio']=$_POST['eshop_cart_radio'];
	//option sets
	if(isset($_POST['eshoposets'])){
		$eshop_product['optset']=$_POST['eshoposets'];
	}else{
		$eshop_product['optset']='';
	}
	update_post_meta( $id, '_eshop_stock', $stkav);
	update_post_meta( $id, '_eshop_product', $eshop_product);
	
	if($stkav=='1' && ($eshop_product['sku']=='' || $eshop_product['description']=='' || $eshop_product['products']['1']['option']=='' || $eshop_product['products']['1']['price']=='')){
		update_post_meta( $id, '_eshop_stock', '0');
		add_filter('redirect_post_location','eshop_error');
	}
	if($stkav=='0' && $eshop_product['sku']=='' && $eshop_product['description']=='' && $eshop_product['products']['1']['option']=='' && $eshop_product['products']['1']['price']==''){
	//not a product
		delete_post_meta( $id, '_eshop_stock');
		delete_post_meta( $id, '_eshop_product');
	}
	return;
}
 
		
?>