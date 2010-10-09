<?php
function eshop_boing($pee,$short='no',$postid=''){
	global $wpdb,$post,$eshopchk,$eshopoptions;
	if($postid=='') $postid=$post->ID;
	$stkav=get_post_meta( $postid, '_eshop_stock',true);
    $eshop_product=maybe_unserialize(get_post_meta( $postid, '_eshop_product',true ));
	$saleclass='';
	if(isset($eshop_product['sale']) && $eshop_product['sale']=='yes'){
		$saleclass=' sale';
	}
    $stocktable=$wpdb->prefix ."eshop_stock";
    $uniq=rand();
	//if the search page we don't want the form!
	//was (!strpos($pee, '[eshop_addtocart'))
	if($short!='yes' && (strpos($pee, '[eshop_details') === false) && ((is_single() || is_page())) && isset($eshopoptions['details']['display']) && 'yes' == $eshopoptions['details']['display'] && (empty($post->post_password) || ( isset($_COOKIE['wp-postpass_'.COOKIEHASH]) && $_COOKIE['wp-postpass_'.COOKIEHASH] == $post->post_password ))){
		$details='';
		if($eshopoptions['details']['show']!='')
			$details.=" show='".esc_attr($eshopoptions['details']['show'])."'";
		if($eshopoptions['details']['class']!='')
			$details.=" class='".esc_attr($eshopoptions['details']['class'])."'";
		if($eshopoptions['details']['hide']!='')
			$details.=" options_hide='".esc_attr($eshopoptions['details']['hide'])."'";
		$pee.= do_shortcode('[eshop_details'.$details.']');
	}
	if((strpos($pee, '[eshop_addtocart') === false) && ((is_single() || is_page())|| 'yes' == $eshopoptions['show_forms']) && (empty($post->post_password) || ( isset($_COOKIE['wp-postpass_'.COOKIEHASH]) && $_COOKIE['wp-postpass_'.COOKIEHASH] == $post->post_password ))){
		//need to precheck stock
		if($postid!=''){
			if(isset($eshopoptions['stock_control']) && 'yes' == $eshopoptions['stock_control']){
				$anystk=false;
				$stkq=$wpdb->get_results("SELECT option_id, available from $stocktable where post_id=$postid");
				foreach($stkq as $thisstk){
					$stkarr[$thisstk->option_id]=$thisstk->available;
				}
				$opt=$eshopoptions['options_num'];
				for($i=1;$i<=$opt;$i++){
					$currst=0;
					if(isset($stkarr[$i]) && $stkarr[$i]>0) $currst=$stkarr[$i];
					if($currst>0){
						$anystk=true; 
						$i=$opt;
					}
				}
				if($anystk==false){
					$stkav='0';
					delete_post_meta( $postid, '_eshop_stock' );
				}
			}
		}
		$replace='';
		if($stkav=='1'){
			$currsymbol=$eshopoptions['currency_symbol'];
			$replace .= '
			<form action="'.get_permalink($eshopoptions['cart']).'" method="post" class="eshop addtocart'.$saleclass.'" id="eshopprod'.$postid.$uniq.'">
			<fieldset><legend><span class="offset">'.__('Order','eshop').' '.stripslashes(esc_attr($eshop_product['description'])).'</span></legend>';
			$theid=sanitize_file_name($eshop_product['sku']);
			//option sets
			$optsets = $eshop_product['optset'];
			if(is_array($optsets)){	
				$opttable=$wpdb->prefix.'eshop_option_sets';
				$optnametable=$wpdb->prefix.'eshop_option_names';
				$optarray=array();
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
								$replace.="\n".'<span class="eshop eselect"><label for="exopt'.$optsets['optid'].$enumb.$uniq.'">'.stripslashes(esc_attr($optsets['name'])).'</label><select id="exopt'.$optsets['optid'].$enumb.$uniq.'" name="optset['.$enumb.'][id]">'."\n";
								foreach($optsets['item'] as $opsets){
									if($opsets['price']!='0.00')
										$addprice=sprintf( __(' + %1$s%2$s','eshop'), $currsymbol, number_format_i18n($opsets['price'],2));
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
										$addprice=sprintf( __(' + %1$s%2$s','eshop'), $currsymbol, number_format_i18n($opsets['price'],2));
									else
										$addprice='';
									$replace.='<span><input type="checkbox" value="'.$opsets['id'].'" id="exopt'.$optsets['optid'].$enumb.'i'.$ox.$uniq.'" name="optset['.$enumb.'][id]" /><label for="exopt'.$optsets['optid'].$enumb.'i'.$ox.$uniq.'">'.stripslashes(esc_attr($opsets['label'])). $addprice.'</label></span>'."\n";
									$enumb++;
								}
								$replace.="</fieldset>\n";
								break;
							case '2'://text
								foreach($optsets['item'] as $opsets){
									if($opsets['price']!='0.00')
										$addprice=sprintf( __(' + %1$s%2$s','eshop'), $currsymbol, number_format_i18n($opsets['price'],2));
									else
										$addprice='';
									$replace.="\n".'<span class="eshop etext"><label for="exopt'.$optsets['optid'].$enumb.$uniq.'">'.stripslashes(esc_attr($opsets['label'])).'<span>'.$addprice.'</span></label>'."\n";
									$replace.='<input type="text" id="exopt'.$optsets['optid'].$enumb.$uniq.'" name="optset['.$enumb.'][text]" value="" />'."\n";
									$replace.='<input type="hidden" name="optset['.$enumb.'][id]" value="'.$opsets['id'].'" />'."\n";
									$replace.='<input type="hidden" name="optset['.$enumb.'][type]" value="'.$optsets['type'].'" />'."\n";
								}
								$replace.="</span>\n";
								break;
							case '3'://textarea
								foreach($optsets['item'] as $opsets){
									if($opsets['price']!='0.00')
										$addprice=sprintf( __(' + %1$s%2$s','eshop'), $currsymbol, number_format_i18n($opsets['price'],2));
									else
										$addprice='';
										
									$replace.="\n".'<span class="eshop etextarea"><label for="exopt'.$optsets['optid'].$enumb.$uniq.'">'.stripslashes(esc_attr($opsets['label'])).'<span>'.$addprice.'</span></label>'."\n";
									$replace.='<textarea id="exopt'.$optsets['optid'].$enumb.$uniq.'" name="optset['.$enumb.'][text]" rows="4" cols="40"></textarea>'."\n";
									$replace.='<input type="hidden" name="optset['.$enumb.'][id]" value="'.$opsets['id'].'" />'."\n";
									$replace.='<input type="hidden" name="optset['.$enumb.'][type]" value="'.$optsets['type'].'" />'."\n";
								}
								$replace.="</span>\n";
								break;
						}
						$enumb++;

					}
				}
			}

					
			if($eshopoptions['options_num']>1){
			
				if(isset($eshop_product['cart_radio']) && $eshop_product['cart_radio']=='1'){
					$opt=$eshopoptions['options_num'];
					$uniq=apply_filters('eshop_uniq',$uniq);
					$replace.="\n<ul class=\"eshopradio\">\n";
					for($i=1;$i<=$opt;$i++){
						$option=$eshop_product['products'][$i]['option'];
						$price=$eshop_product['products'][$i]['price'];
						if($i=='1') $esel=' checked="checked"';
						else $esel='';
						$currst=1;
						if(isset($eshopoptions['stock_control']) && 'yes' == $eshopoptions['stock_control']){
							if(isset($stkarr[$i]) && $stkarr[$i]>0) $currst=$stkarr[$i];
							else $currst=0;
						}
						if($option!='' && $currst>0){
							if($price!='0.00')
								$replace.='<li><input type="radio" value="'.$i.'" id="eshopopt'.$theid.'_'.$i.$uniq.'" name="option"'.$esel.' /><label for="eshopopt'.$theid.'_'.$i.$uniq.'">'.sprintf( __('%1$s @ %2$s%3$s','eshop'),stripslashes(esc_attr($option)), $currsymbol, number_format_i18n($price,2))."</label>\n</li>";
							else
								$replace.='<li><input type="radio" value="'.$i.'" id="eshopopt'.$theid.'_'.$i.$uniq.'" name="option" /><label for="eshopopt'.$theid.'_'.$i.$uniq.'">'.stripslashes(esc_attr($option)).'</label>'."\n</li>";
						}
					}
					$replace.="</ul>\n";
					//combine 2 into 1 then extract
					$filterarray[0]=$replace;
					$filterarray[1]=$eshop_product;
					$filterarray=apply_filters('eshop_after_radio',$filterarray);
					$replace=$filterarray[0];
					

				}else{			
					$opt=$eshopoptions['options_num'];
					$replace.="\n".'<label for="eopt'.$theid.$uniq.'"><select id="eopt'.$theid.$uniq.'" name="option">';
					for($i=1;$i<=$opt;$i++){
						if(isset($eshop_product['products'][$i])){
							$option=$eshop_product['products'][$i]['option'];
							$price=$eshop_product['products'][$i]['price'];
							$currst=1;
							if(isset($eshopoptions['stock_control']) && 'yes' == $eshopoptions['stock_control']){
								if(isset($stkarr[$i]) && $stkarr[$i]>0) $currst=$stkarr[$i];
								else $currst=0;
							}
							if($option!='' && $currst>0){
								if($price!='0.00')
									$replace.='<option value="'.$i.'">'.sprintf( __('%1$s @ %2$s%3$s','eshop'),stripslashes(esc_attr($option)), $currsymbol, number_format_i18n($price,2)).'</option>'."\n";
								else
									$replace.='<option value="'.$i.'">'.stripslashes(esc_attr($option)).'</option>'."\n";
							}
						}
					}
					$replace.='</select></label>';
				}
			}else{
				$option=$eshop_product['products']['1']['option'];
				$price=$eshop_product['products']['1']['price'];
				$currst=1;
				if(isset($eshopoptions['stock_control']) && 'yes' == $eshopoptions['stock_control']){
					if(isset($stkarr[1]) && $stkarr[1]>0) $currst=$stkarr[1];
				}
				$replace .='<input type="hidden" name="option" value="1" />';
				if($currst>0){
					if($price!='0.00'){
						$replace.='
						<span class="sgloptiondetails"><span class="sgloption">'.stripslashes(esc_attr($option)).'</span> @ <span class="sglprice">'.sprintf( __('%1$s%2$s','eshop'), $currsymbol, number_format_i18n($price,2)).'</span></span>
						';
					}else{
						$replace.='
						<span class="sgloptiondetails"><span class="sgloption">'.stripslashes(esc_attr($option)).'</span></span>
						';
					}
				}
			}
			$addqty=1;
			if(isset($eshopoptions['min_qty']) && $eshopoptions['min_qty']!='') 
				$addqty=$eshopoptions['min_qty'];

			if($short=='yes'){
				$replace .='<input type="hidden" name="qty" value="'.$addqty.'" />';
			}else{
				$replace .='<label for="qty'.$theid.$uniq.'" class="qty">'.__('<dfn title="Quantity">Qty</dfn>:','eshop').'</label>
				<input type="text" value="'.$addqty.'" id="qty'.$theid.$uniq.'" maxlength="3" size="3" name="qty" class="iqty" />';
			}

			$replace .='
			<input type="hidden" name="pclas" value="'.$eshop_product['shiprate'].'" />
			<input type="hidden" name="pname" value="'.stripslashes(esc_attr($eshop_product['description'])).'" />
			<input type="hidden" name="pid" value="'.$eshop_product['sku'].'" />
			<input type="hidden" name="purl" value="'.get_permalink($postid).'" />
			<input type="hidden" name="postid" value="'.$postid.'" />
			<input type="hidden" name="eshopnon" value="set" />';
			$replace .= wp_nonce_field('eshop_add_product_cart','_wpnonce'.$uniq,true,false);
			if($eshopoptions['addtocart_image']=='img'){
				$eshopfiles=eshop_files_directory();
				$imgloc=apply_filters('eshop_theme_addtocartimg',$eshopfiles['1'].'addtocart.png');
				$replace .='<input class="buttonimg eshopbutton" src="'.$imgloc.'" value="'.__('Add to Cart','eshop').'" title="'.__('Add selected item to your shopping basket','eshop').'" type="image" />';
			}else{
				$replace .='<input class="button eshopbutton" value="'.__('Add to Cart','eshop').'" title="'.__('Add selected item to your shopping basket','eshop').'" type="submit" />';
			}
			$replace .='<div class="eshopajax"></div></fieldset>
			</form>';
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