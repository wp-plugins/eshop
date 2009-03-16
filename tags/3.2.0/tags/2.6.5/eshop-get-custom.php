<?php

function eshop_get_custom ($field) {
	global $post;
	return get_post_meta($post->ID, $field, true);
} 

function eshop_boing($pee){
	global $wpdb,$post;
	//if the search page we don't want the form!
	if(!is_search() && !is_tag()){
		if(eshop_get_custom('Sku')!='' && eshop_get_custom('Product Description')!='' &&
		eshop_get_custom('Option 1')!='' && eshop_get_custom('Price 1')!='' &&
		eshop_get_custom('Shipping Rate')!='' && eshop_get_custom('Stock Available')=='Yes'){
			$replace='';
			//stock checker
			if($post->ID!=''){
				if('yes' == get_option('eshop_stock_control')){
					$stocktable=$wpdb->prefix ."eshop_stock";
					$currst=$wpdb->get_var("SELECT available from $stocktable where post_id=$post->ID");
					if($currst<=0){
						update_post_meta( $post->ID, 'Stock Available', 'No' );
					}
				}
			}
			if('yes' == get_option('eshop_stock_control') && 'yes' == get_option('eshop_show_stock') && isset($currst) && eshop_get_custom('Product Download')==''){
				$replace='<p class="stkqty">'.__('Stock Available:','eshop').' <span>'.$currst.'</span></p>';
			}
			if(eshop_get_custom('Sku')!='' && eshop_get_custom('Product Description')!='' &&
			eshop_get_custom('Option 1')!='' && eshop_get_custom('Price 1')!='' &&
			eshop_get_custom('Shipping Rate')!='' && eshop_get_custom('Stock Available')=='Yes'){

				$currsymbol=get_option('eshop_currency_symbol');
				$replace .= '
				<form action="'.get_permalink(get_option('eshop_cart')).'" method="post" class="addtocart">
				<fieldset><legend><span class="offset">'.__('Order','eshop').' '.eshop_get_custom('Product Description').'</span></legend>';

				if(get_option('eshop_options_num')>1){
					$opt=get_option('eshop_options_num');
					$replace.="\n".'<label for="options"><select id="options" name="option">';
					for($i=1;$i<=$opt;$i++){
						if(eshop_get_custom('Option '.$i)!=''){
							$replace.='<option value="Option '.$i.'">'.eshop_get_custom('Option '.$i).' @ '.$currsymbol.eshop_get_custom('Price '.$i).'</option>'."\n";
						}
					}
					$replace.='</select></label>';
				}else{
					$replace.='
					<input type="hidden" name="option" value="Option 1" />
					<span class="sgloption">'.eshop_get_custom('Option 1').'</span> @ '.$currsymbol.'<span class="sglprice">'.eshop_get_custom('Price 1').'</span>
					';
				}
				$replace .='
				<input type="hidden" name="pclas" value="'.eshop_get_custom('Shipping Rate').'" />
				<label for="'.eshop_get_custom('Sku').'qty" class="qty">'.__('<dfn title="Quantity">Qty</dfn>:','eshop').'</label>
				<input type="text" value="1" id="'.eshop_get_custom('Sku').'qty" maxlength="3" size="3" name="qty" class="iqty" />
				<input type="hidden" name="pname" value="'.eshop_get_custom('Product Description').'" />
				<input type="hidden" name="pid" value="'.eshop_get_custom('Sku').'" />
				<input type="hidden" name="purl" value="'.get_permalink($post->ID).'" />
				<input type="hidden" name="postid" value="'.$post->ID.'" />

				<input class="button" value="'.__('Add to Cart','eshop').'" title="'.__('Add selected item to your shopping basket','eshop').'" type="submit" />
				</fieldset>
				</form>';
				if(get_option('eshop_cart_shipping')!=''){
					$replace .='
					<p class="eshopshipping">
					<a href="'.get_permalink(get_option('eshop_cart_shipping')).'#eshopshiprates"><span>'.__('Shipping Rate:','eshop').'</span> '.eshop_get_custom('Shipping Rate').'</a>
					</p> 
					';
				}else{
					$replace .='
					<p class="eshopshipping">
					<span>'.__('Shipping Rate:','eshop').'</span> '.eshop_get_custom('Shipping Rate').'</p> 
					';

				}
				$pee = $pee.$replace; 
			}elseif(eshop_get_custom('Stock Available')=='No' && eshop_get_custom('Price 1')!=''){
				$replace = '<p class="eshopnostock">'.get_option('eshop_cart_nostock').'</p>';
				$pee = $pee.$replace;
			}
			return $pee;
		}else{
			return $pee;
		}
	}else{
		return $pee;
	}
}
?>