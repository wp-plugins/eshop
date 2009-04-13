<?php
//adds form to page edit
class eshop_custom_field_gui {

  function sanitize_name( $name ) {
    $name = sanitize_title( $name ); // taken from WP's wp-includes/functions-formatting.php
    $name = str_replace( '-', '_', $name );
    
    return $name;
  }
  
 
  function make_textfield( $name, $size = 25 ) {
    $title = $name;
    $name = 'eshop_' . eshop_custom_field_gui::sanitize_name( $name );
    
    if( isset( $_REQUEST[ 'post' ] ) ) {
      $value = get_post_meta( $_REQUEST[ 'post' ], $title );
      $value = $value[ 0 ];
    }
    
    $out = 
      '<label for="'. $name .'">' . $title . ' </label>' .
      '<input id="' . $name . '" name="' . $name . '" value="' . attribute_escape($value) . '" type="text" size="' . $size . '" /><br />';

    return $out;
  }
  function make_stock_control( $name, $size = 25 ) {
  		global $wpdb;
      $title = $name;
      $name = 'eshop_' . eshop_custom_field_gui::sanitize_name( $name );
      if( isset( $_REQUEST[ 'post' ] ) ) {
      	$table=$wpdb->prefix ."eshop_stock";
      	$idd=$_REQUEST[ 'post' ];
	  	$value=$wpdb->get_var("select available from $table where post_id=$idd limit 1");
        
      }
      
      $out = 
        '<label for="'. $name .'">' . $title . ' </label>' .
        '<input id="' . $name . '" name="' . $name . '" value="' . attribute_escape($value) . '" type="text" size="' . $size . '" /><br />';
  
      return $out;
  }
  function make_checkbox( $name, $default ) {
    $title = $name;
    $name = 'eshop_' . eshop_custom_field_gui::sanitize_name( $name );
    
    if( isset( $_REQUEST[ 'post' ] ) ) {
      $checked = get_post_meta( $_REQUEST[ 'post' ], $title );
      $checked = $checked ? 'checked="checked"' : '';
    }
    else {
      if ( isset( $default ) && trim( $default ) == 'checked' ) {
        $checked = 'checked="checked"';
      }    
    }
   
    $out .=  
      '<input class="checkbox" name="' . $name . '" value="true" id="' . $name . '" "' . $checked . '" type="checkbox" />';
	$out .=
      '<label for="'. $name .'">' . $title. ' </label><br />';
      
    
    return $out;
  }
  
  function make_radio( $name, $values, $default ) {
    $title = $name;
    $name = 'eshop_' . eshop_custom_field_gui::sanitize_name( $name );
    
    if( isset( $_REQUEST[ 'post' ] ) ) {
      $selected = get_post_meta( $_REQUEST[ 'post' ], $title );
      if($selected[ 0 ]!=''){
      	$selected = $selected[ 0 ];
      }else{
      	$selected = $default;
      }
    }
    else {
      $selected = $default;
    }
  
    $out =
      '<fieldset><legend>' . $title . ' </legend>';
    
    foreach( $values as $val ) {
      $id = $name . '_' . eshop_custom_field_gui::sanitize_name( $val );
      
      $checked = ( trim( $val ) == trim( $selected ) ) ? 'checked="checked"' : '';
      
      $out .=  
        '<label for="' . $id . '" class="selectit"><input id="' . $id . '" name="' . $name . '" value="' . $val . '" ' . $checked . ' type="radio" /> ' . $val . '</label>';
    }   
    $out .= '</fieldset>';
    
    return $out;      
  }
  
  function make_select( $name, $values, $default ) {
    $title = $name;
    $name = 'eshop_' . eshop_custom_field_gui::sanitize_name( $name );
    
    if( isset( $_REQUEST[ 'post' ] ) ) {
      $selected = get_post_meta( $_REQUEST[ 'post' ], $title );
      if($selected[ 0 ]!=''){
	     $selected = $selected[ 0 ];
	  }else{
	      $selected = $default;
      }
    }
    else {
      $selected = $default;
    }
    
    $out =
      '<label for="' . $name . '">' . $title . ' </label>'.
      '<select name="' . $name . '" id="' . $name . '">' .
      '<option value="">Select</option>';
      
    foreach( $values as $val ) {
      $checked = ( trim( $val ) == trim( $selected ) ) ? 'selected="selected"' : '';
    
      $out .=
        '<option value="' . $val . '" ' . $checked . ' >' . $val. '</option>'; 
    }
    $out .= '</select><br />';
    
    return $out;
  }
  function make_productselect( $name, $values, $default) {
      $title = $name;
      $name = 'eshop_' . eshop_custom_field_gui::sanitize_name( $name );
      
      if( isset( $_REQUEST[ 'post' ] ) ) {
        $selected = get_post_meta( $_REQUEST[ 'post' ], $title );
        if($selected[ 0 ]!=''){
  	     $selected = $selected[ 0 ];
  	  }else{
  	      $selected = $default;
        }
      }
      else {
        $selected = $default;
      }
      
      $out =
        '<label for="' . $name . '">' . $title . ' </label>'.
        '<select name="' . $name . '" id="' . $name . '">' .
        '<option value="">No (or select)</option>';
        
      foreach( $values as $key=>$val ) {
        $checked = ( trim( $key ) == trim( $selected ) ) ? 'selected="selected"' : '';
      
        $out .=
          '<option value="' . $key . '" ' . $checked . ' >' . $val. '</option>'; 
      }
      $out .= '</select><br />';
      
      return $out;
  }
  function make_textarea( $name, $rows, $cols ) {
    $title = $name;
    $name = 'eshop_' . eshop_custom_field_gui::sanitize_name( $name );
    
    if( isset( $_REQUEST[ 'post' ] ) ) {
      $value = get_post_meta( $_REQUEST[ 'post' ], $title );
      $value = $value[ 0 ];
    }
    
    $out = 
      '<label for="' . $name . '">' . $title . ' </label><br />' .
      '<textarea id="' . $name . '" name="' . $name . '" type="textfield" rows="' .$rows. '" cols="' .$cols. '">' .attribute_escape($value). '</textarea><br />' ;
      
    return $out;
  }


  	function eshop_insert_gui() {
		global $wpdb;
		$stk=get_post_meta( $_REQUEST[ 'post' ], 'Stock Available');
		if($stk[0]!='Yes')
			$class=' closed';
		else
			$class='';
		$out = '<input type="hidden" name="eshop-custom-field-gui-verify-key" id="eshop-custom-field-gui-verify-key"
				value="' . wp_create_nonce('eshop-custom-field-gui') . '" />';
		$out .= '<fieldset id="eshopcustom">
		<div id="pagepostcustom" class="postbox'.$class.'">
		<h3>Product Entry</h3>
		<div class="inside">
		<div id="eshopcustomstuff">
		';

		$out .= eshop_custom_field_gui::make_textfield( 'Sku', '20')."\n";
		$out .= eshop_custom_field_gui::make_textfield( 'Product Description', '30')."\n";


		$numoptions=get_option('eshop_options_num');
		for($i=1;$i<=$numoptions;$i++){
			$optfield=eshop_custom_field_gui::make_textfield( 'Option '.$i, '20');
			$out .= substr($optfield,0,-6)."\n";//remove <br />
			$out .= eshop_custom_field_gui::make_textfield( 'Price '.$i, '6')."\n";
		}
		//get list of download products for selection
		$producttable = $wpdb->prefix ."eshop_downloads";
		$max = $wpdb->get_var("SELECT COUNT(id) FROM $producttable WHERE id > 0");
		if($max>0){ // only show if downloads available!
			$myrowres=$wpdb->get_results("Select * From $producttable");
			foreach($myrowres as $prow){
				$v=$prow->id;
				$values[$v]=$prow->title;
			}

			$out .= eshop_custom_field_gui::make_productselect('Product Download', $values,'' )."\n";
		}
		//end

		$out .= eshop_custom_field_gui::make_select('Shipping Rate', explode( '#', 'A#B#C#D#E#F' ), 'A' )."\n";
		$out .= eshop_custom_field_gui::make_radio('Featured Product', explode( '#', 'Yes#No' ), 'No' )."\n";

		$out .= eshop_custom_field_gui::make_radio('Stock Available', explode( '#', 'Yes#No' ), 'No' )."\n";
		if(get_option('eshop_stock_control')=='yes'){
			$out .= eshop_custom_field_gui::make_stock_control( 'Stock Quantity', '4')."\n";
		}
		$out .= '</div</div></div></fieldset>';
		echo $out;
	}

	function eshop_edit_meta_value( $id ) {
		global $wpdb;

		if( !isset( $id ) )
			$id = $_REQUEST[ 'post_ID' ];

		if( !current_user_can('edit_post', $id) )
			return $id;

		if( !wp_verify_nonce($_REQUEST['eshop-custom-field-gui-verify-key'], 'eshop-custom-field-gui') )
			return $id;

		$fields['Sku']['type']='text';
		$numoptions=get_option('eshop_options_num');
		for($i=1;$i<=$numoptions;$i++){
			$fields['Option '.$i]['type']='text';
			$fields['Price '.$i]['type']='text';
		}
		$fields['Product Description']['type']='text';
		$fields['Product Download']['type']='select';

		$fields['Shipping Rate']['type']='select';

		$fields['Featured Product']['type']='radio';
		$fields['Stock Available']['type']='radio';
		$fields['Stock Quantity']['type']='gobble';


		foreach( $fields as $title  => $data) {
			$name = 'eshop_' . eshop_custom_field_gui::sanitize_name( $title );
			$title = $wpdb->escape(stripslashes(trim($title)));
			$meta_value = stripslashes(trim($_REQUEST[ "$name" ]));
			if($title=='Stock Quantity'){
				$stocktable=$wpdb->prefix ."eshop_stock";
				$results=$wpdb->get_results("select post_id from $stocktable");
				if(!empty($results)){
					$found='no';
					foreach($results as $r){
						if($id==$r->post_id){//update
							$wpdb->query("UPDATE $stocktable set available=$meta_value where post_id=$id");
							$found='yes';
						}
					}
					if($found=='no'){
						$wpdb->query("INSERT INTO $stocktable (post_id,available,purchases) VALUES ($id,$meta_value,0)");
					}
				}else{
					$wpdb->query("INSERT INTO $stocktable (post_id,available,purchases) VALUES ($id,$meta_value,0)");
				}
				
			}
			if( isset( $meta_value ) && !empty( $meta_value ) ) {
				delete_post_meta( $id, $title );
				if( $data[ 'type' ] == 'text' || 
					$data[ 'type' ] == 'radio'  ||
					$data[ 'type' ] == 'select' || 
					$data[ 'type' ] == 'textarea' ) {
					add_post_meta( $id, $title, $meta_value );
				}
				else if( $data[ 'type' ] == 'checkbox' )
					add_post_meta( $id, $title, 'true' );
				}
				else {
					delete_post_meta( $id, $title );
				}
			}
			//takes the first price, and fills the others if they are not set
			$numboptions=get_option('eshop_options_num');
			for($i=1;$i<=$numboptions;$i++){
				$otitle='Option '.$i;
				$oname = 'eshop_' . eshop_custom_field_gui::sanitize_name( $otitle );
				$otitle = $wpdb->escape(stripslashes(trim($otitle)));
				$ometa_value = stripslashes(trim($_REQUEST[ "$oname" ]));
				$ptitle='Price '.$i;
				$pname = 'eshop_' . eshop_custom_field_gui::sanitize_name( $ptitle );
				$ptitle = $wpdb->escape(stripslashes(trim($ptitle)));
				$pmeta_value = stripslashes(trim($_REQUEST[ "$pname" ]));
				if($ometa_value!='' && $pmeta_value!=''){
					$temp_price=$pmeta_value;
				}elseif($ometa_value!='' && $pmeta_value==''){
					add_post_meta( $id, $ptitle, $temp_price );
				}elseif($ometa_value=='' && $pmeta_value!=''){
					delete_post_meta( $id, $ptitle );
				}
			}
		}
  	
}


?>