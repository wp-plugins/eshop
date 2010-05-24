<?php
function eshop_boing($pee,$short='no'){
	global $wpdb,$post,$eshopchk,$eshopoptions;
	$stkav=get_post_meta( $post->ID, '_eshop_stock',true );
    $eshop_product=get_post_meta( $post->ID, '_eshop_product',true );
	//if the search page we don't want the form!
	if((!strpos($pee, '[eshop_addtocart')) && ((is_single() || is_page())|| 'yes' == $eshopoptions['show_forms']) && (empty($post->post_password) || ( isset($_COOKIE['wp-postpass_'.COOKIEHASH]) && $_COOKIE['wp-postpass_'.COOKIEHASH] == $post->post_password ))){
		//stock checker
		if($post->ID!=''){
			if('yes' == $eshopoptions['stock_control']){
				$stocktable=$wpdb->prefix ."eshop_stock";
				$currst=$wpdb->get_var("SELECT available from $stocktable where post_id=$post->ID");
				if($currst<=0){
					$stkav='0';
					delete_post_meta( $post->ID, '_eshop_stock' );
				}
			}
		}
		$replace='';
		if($stkav=='1'){
			
			if('yes' == $eshopoptions['stock_control'] && 'yes' == $eshopoptions['show_stock'] && isset($currst) && $eshop_product['download']==''){
				$replace.='<p class="stkqty">'.__('Stock Available:','eshop').' <span>'.$currst.'</span></p>';
			}
			if('yes' == $eshopoptions['show_sku']){// && $short=='no'){
				$replace.='<p class="eshopsku">'.__('Sku:','eshop').' <span>'.sanitize_file_name($eshop_product['sku']).'</span></p>';
			}
			$currsymbol=$eshopoptions['currency_symbol'];
			$replace .= '
			<form action="'.get_permalink($eshopoptions['cart']).'" method="post" class="eshop addtocart">
			<fieldset><legend><span class="offset">'.__('Order','eshop').' '.stripslashes(esc_attr($eshop_product['description'])).'</span></legend>';
			$theid=sanitize_file_name($eshop_product['sku']);
			//option sets
			$optsets = $eshop_product['optset'];
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
				if(is_array($optarray)){
					foreach($optarray as $optsets){
						switch($optsets['type']){
							case '0'://select
								$replace.="\n".'<span class="eshop eselect"><label for="exopt'.$optsets['optid'].$enumb.'">'.stripslashes(esc_attr($optsets['name'])).'</label><select id="exopt'.$optsets['optid'].$enumb.'" name="optset[]">'."\n";
								foreach($optsets['item'] as $opsets){
									if($opsets['price']!='0.00')
										$addprice=' + '.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($opsets['price'],2));
									else
										$addprice='';
									$replace.='<option value="'.$opsets['id'].'">'.stripslashes(esc_attr($opsets['label'])).$addprice.'</option>'."\n";
								}
								$replace.="</select></span>\n";
								break;

							case '1'://checkbox
							$replace.="\n".'<fieldset class="eshop echeckbox"><legend>'.stripslashes(esc_attr($optsets['name'])).'</legend>'."\n";
							$ox=0;
							foreach($optsets['item'] as $opsets){
								$ox++;
								if($opsets['price']!='0.00')
									$addprice=' + '.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($opsets['price'],2));
								else
									$addprice='';
								$replace.='<span><input type="checkbox" value="'.$opsets['id'].'" id="exopt'.$optsets['optid'].$enumb.'i'.$ox.'" name="optset[]" /><label for="exopt'.$optsets['optid'].$enumb.'i'.$ox.'">'.stripslashes(esc_attr($opsets['label'])). $addprice.'</label></span>'."\n";
							}
							$replace.="</fieldset>\n";

							break;
						}
						$enumb++;

					}
				}
			}

						
			if($eshopoptions['options_num']>1){
				$opt=$eshopoptions['options_num'];
				$replace.="\n".'<label for="eopt'.$theid.'"><select id="eopt'.$theid.'" name="option">';
				for($i=1;$i<=$opt;$i++){
					$option=$eshop_product['products'][$i]['option'];
					$price=$eshop_product['products'][$i]['price'];
					if($option!=''){
						if($price!='0.00')
							$replace.='<option value="'.$i.'">'.stripslashes(esc_attr($option)).' @ '.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($price,2)).'</option>'."\n";
						else
							$replace.='<option value="'.$i.'">'.stripslashes(esc_attr($option)).'</option>'."\n";
					}
				}
				$replace.='</select></label>';
			}else{
				$option=$eshop_product['products']['1']['option'];
				$price=$eshop_product['products']['1']['price'];
				if($price!='0.00'){
					$replace.='
					<input type="hidden" name="option" value="1" />
					<span class="sgloptiondetails"><span class="sgloption">'.stripslashes(esc_attr($option)).'</span> @ <span class="sglprice">'.sprintf( _x('%1$s%2$s','1-currency symbol 2-amount','eshop'), $currsymbol, number_format($price,2)).'</span></span>
					';
				}else{
					$replace.='
					<input type="hidden" name="option" value="1" />
					<span class="sgloptiondetails"><span class="sgloption">'.stripslashes(esc_attr($option)).'</span></span>
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
			<input type="hidden" name="pclas" value="'.$eshop_product['shiprate'].'" />
			<input type="hidden" name="pname" value="'.stripslashes(esc_attr($eshop_product['description'])).'" />
			<input type="hidden" name="pid" value="'.$eshop_product['sku'].'" />
			<input type="hidden" name="purl" value="'.get_permalink($post->ID).'" />
			<input type="hidden" name="postid" value="'.$post->ID.'" />';
			$eshopfiles=eshop_files_directory();
			if($eshopoptions['addtocart_image']=='img'){
				$replace .='<input class="buttonimg" src="'.$eshopfiles['1'].'addtocart.png" value="'.__('Add to Cart','eshop').'" title="'.__('Add selected item to your shopping basket','eshop').'" type="image" />';
			}else{
				$replace .='<input class="button" value="'.__('Add to Cart','eshop').'" title="'.__('Add selected item to your shopping basket','eshop').'" type="submit" />';
			}
			$replace .='</fieldset>
			</form>';
			if($short=='no' && $eshopoptions['downloads_only'] !='yes'){
				if($eshopoptions['cart_shipping']!=''){
					$replace .='
					<p class="eshopshipping">
					<a href="'.get_permalink($eshopoptions['cart_shipping']).'#eshopshiprates"><span>'.__('Shipping Rate:','eshop').'</span> '.$eshop_product['shiprate'].'</a>
					</p> 
					';
				}else{
					$replace .='
					<p class="eshopshipping">
					<span>'.__('Shipping Rate:','eshop').'</span> '.$eshop_product['shiprate'].'</p> 
					';
				}
			}
			$pee = $pee.$replace; 
		}elseif(isset($currst) && $currst<=0 && is_array($eshop_product)){
			//eshop_get_custom('Stock Available')=='No' && eshop_get_custom('Price 1')!=''){
				$replace = '<p class="eshopnostock"><span>'.$eshopoptions['cart_nostock'].'</span></p>';
				$pee = $pee.$replace;
		}
		return $pee;
	}else{
		return $pee;
	}
}
?>