<?php

function eshop_get_custom ($field) {
	global $post;
	return get_post_meta($post->ID, '_'.$field, true);
} 

function eshop_boing($pee,$short='no'){
	global $wpdb,$post,$eshopchk;
	//if the search page we don't want the form!
	
	if((!strpos($pee, '[eshop_addtocart]')) && ((is_single() || is_page())|| 'yes' == get_option('eshop_show_forms')) && (empty($post->post_password) || ( isset($_COOKIE['wp-postpass_'.COOKIEHASH]) && $_COOKIE['wp-postpass_'.COOKIEHASH] == $post->post_password ))){
		//stock checker
		if($post->ID!=''){
			if('yes' == get_option('eshop_stock_control')){
				$stocktable=$wpdb->prefix ."eshop_stock";
				$currst=$wpdb->get_var("SELECT available from $stocktable where post_id=$post->ID");
				if($currst<=0){
					update_post_meta( $post->ID, '_Stock Available', 'No' );
				}
			}
		}
		if(eshop_get_custom('Sku')!='' && eshop_get_custom('Product Description')!='' &&
		eshop_get_custom('Option 1')!='' && eshop_get_custom('Price 1')!='' &&
		eshop_get_custom('Shipping Rate')!='' && eshop_get_custom('Stock Available')=='Yes'){
			$replace='';
			
			if('yes' == get_option('eshop_stock_control') && 'yes' == get_option('eshop_show_stock') && isset($currst) && eshop_get_custom('Product Download')==''){
				$replace.='<p class="stkqty">'.__('Stock Available:','eshop').' <span>'.$currst.'</span></p>';
			}
			if('yes' == get_option('eshop_show_sku')){// && $short=='no'){
				$replace.='<p class="eshopsku">'.__('Sku:','eshop').' <span>'.sanitize_file_name(eshop_get_custom('Sku')).'</span></p>';
			}
			$currsymbol=get_option('eshop_currency_symbol');
			$replace .= '
			<form action="'.get_permalink(get_option('eshop_cart')).'" method="post" class="eshop addtocart">
			<fieldset><legend><span class="offset">'.__('Order','eshop').' '.stripslashes(attribute_escape(eshop_get_custom('Product Description'))).'</span></legend>';
			$theid=sanitize_file_name(eshop_get_custom('Sku'));
			//option sets
			$optsets = eshop_get_custom('eshoposets');
			if(is_array($optsets)){	
				$opttable=$wpdb->prefix.'eshop_option_sets';
				$optnametable=$wpdb->prefix.'eshop_option_names';

				foreach($optsets as $foo=>$opset){
					$qb[]="(n.optid=$opset && n.optid=s.optid)";
				}
				$qbs = implode("OR", $qb);
				$myrowres=$wpdb->get_results("select n.optid,n.name as name, n.type, s.name as label, s.price, s.id from $opttable as s, 
					$optnametable as n where $qbs ORDER BY type, id ASC");
				$x=0;
				foreach($myrowres as $myrow){
					$optarray[$myrow->optid]['name']=$myrow->name;
					$optarray[$myrow->optid]['optid']=$myrow->optid;
					$optarray[$myrow->optid]['type']=$myrow->type;
					$optarray[$myrow->optid]['item'][$x]['id']=$myrow->id;
					$optarray[$myrow->optid]['item'][$x]['label']=$myrow->label;
					$optarray[$myrow->optid]['item'][$x]['price']=$myrow->price;
					$x++;
				}
				$enumb=0;
				foreach($optarray as $optsets){
					switch($optsets['type']){
						case '0'://select
							$replace.="\n".'<span class="eshop eselect"><label for="exopt'.$optsets['optid'].$enumb.'">'.stripslashes(attribute_escape($optsets['name'])).'</label><select id="exopt'.$optsets['optid'].$enumb.'" name="optset[]">'."\n";
							foreach($optsets['item'] as $opsets){
								if($opsets['price']!='0.00')
									$addprice=' + '.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($opsets['price'],2));
								else
									$addprice='';
								$replace.='<option value="'.$opsets['id'].'">'.stripslashes(attribute_escape($opsets['label'])).$addprice.'</option>'."\n";
							}
							$replace.="</select></span>\n";
							break;
						
						case '1'://checkbox
						$replace.="\n".'<fieldset class="eshop echeckbox"><legend>'.stripslashes(attribute_escape($optsets['name'])).'</legend>'."\n";
						$ox=0;
						foreach($optsets['item'] as $opsets){
							$ox++;
							if($opsets['price']!='0.00')
								$addprice=' + '.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($opsets['price'],2));
							else
								$addprice='';
							$replace.='<span><input type="checkbox" value="'.$opsets['id'].'" id="exopt'.$optsets['optid'].$enumb.'i'.$ox.'" name="optset[]" /><label for="exopt'.$optsets['optid'].$enumb.'i'.$ox.'">'.stripslashes(attribute_escape($opsets['label'])). $addprice.'</label></span>'."\n";
						}
						$replace.="</fieldset>\n";
						
						break;
					}
					$enumb++;
				
				}
			}

						
			if(get_option('eshop_options_num')>1){
				$opt=get_option('eshop_options_num');
				$replace.="\n".'<label for="eopt'.$theid.'"><select id="eopt'.$theid.'" name="option">';
				for($i=1;$i<=$opt;$i++){
					if(eshop_get_custom('Option '.$i)!=''){
						if(eshop_get_custom('Price '.$i)!='0.00')
							$replace.='<option value="Option '.$i.'">'.stripslashes(attribute_escape(eshop_get_custom('Option '.$i))).' @ '.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format(eshop_get_custom('Price '.$i),2)).'</option>'."\n";
						else
							$replace.='<option value="Option '.$i.'">'.stripslashes(attribute_escape(eshop_get_custom('Option '.$i))).'</option>'."\n";
					}
				}
				$replace.='</select></label>';
			}else{
				if(eshop_get_custom('Price 1')!='0.00'){
					$replace.='
					<input type="hidden" name="option" value="Option 1" />
					<span class="sgloptiondetails"><span class="sgloption">'.stripslashes(attribute_escape(eshop_get_custom('Option 1'))).'</span> @ <span class="sglprice">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format(eshop_get_custom('Price 1'),2)).'</span></span>
					';
				}else{
					$replace.='
					<input type="hidden" name="option" value="Option 1" />
					<span class="sgloptiondetails"><span class="sgloption">'.stripslashes(attribute_escape(eshop_get_custom('Option 1'))).'</span></span>
					';
				}
			}
			if($short=='yes'){
				$replace .='<input type="hidden" name="qty" value="1" />';
			}else{
				$replace .='<label for="qty'.$theid.'" class="qty">'.__('<dfn title="Quantity">Qty</dfn>:','eshop').'</label>
				<input type="text" value="1" id="qty'.$theid.'" maxlength="3" size="3" name="qty" class="iqty" />';
			}

			$replace .='
			<input type="hidden" name="pclas" value="'.eshop_get_custom('Shipping Rate').'" />
			<input type="hidden" name="pname" value="'.stripslashes(attribute_escape(eshop_get_custom('Product Description'))).'" />
			<input type="hidden" name="pid" value="'.eshop_get_custom('Sku').'" />
			<input type="hidden" name="purl" value="'.get_permalink($post->ID).'" />
			<input type="hidden" name="postid" value="'.$post->ID.'" />';
			$eshopfiles=eshop_files_directory();
			if(get_option('eshop_addtocart_image')=='img'){
				$replace .='<input class="buttonimg" src="'.$eshopfiles['1'].'addtocart.png" value="'.__('Add to Cart','eshop').'" title="'.__('Add selected item to your shopping basket','eshop').'" type="image" />';
			}else{
				$replace .='<input class="button" value="'.__('Add to Cart','eshop').'" title="'.__('Add selected item to your shopping basket','eshop').'" type="submit" />';
			}
			$replace .='</fieldset>
			</form>';
			if($short=='no' && get_option('eshop_downloads_only') !='yes'){
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
			}
			$pee = $pee.$replace; 
		}elseif(isset($currst) && $currst<=0){
			//eshop_get_custom('Stock Available')=='No' && eshop_get_custom('Price 1')!=''){
				$replace = '<p class="eshopnostock"><span>'.get_option('eshop_cart_nostock').'</span></p>';
				$pee = $pee.$replace;
		}
		return $pee;
	}else{
		return $pee;
	}
}
?>