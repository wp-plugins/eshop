<?php
if ('eshop_products.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>'.__('Direct File Access Prohibited','eshop').'</h2>');
     
/*
See eshop.php for information and license terms
*/
if (file_exists(ABSPATH . 'wp-includes/l10n.php')) {
    require_once(ABSPATH . 'wp-includes/l10n.php');
}
else {
    require_once(ABSPATH . 'wp-includes/wp-l10n.php');
}
class eshop_multi_sort {
    var $aData;//the array we want to sort.
    var $aSortkeys;//the order in which we want the array to be sorted.
    function sortcmp($a, $b, $i=0) {
        $r = strnatcmp($a[$this->aSortkeys[$i]],$b[$this->aSortkeys[$i]]);
        if($r==0) {
            $i++;
            if ($this->aSortkeys[$i]) $r = $this->sortcmp($a, $b, $i+1);
        }
        return $r;
    }
    function sort() {
        if(count($this->aSortkeys)) {
            usort($this->aData,array($this,"sortcmp"));
        }
    }
}

function eshop_products_manager() {
	global $wpdb;
	include_once ("pager-class.php");
	$eshopprodimg='_eshop_prod_img';
	///images
	if(isset($_GET['change']) && is_numeric($_GET['change'])){
		$change=$_GET['change'];
		if(isset($_POST['submit']) && $_POST['submit']==__('Update','eshop')){
			//include 'cart-functions.php';
			$_POST=sanitise_array($_POST);
			if(isset($_POST['prodimg'])){
				$prodimg=$wpdb->escape($_POST['prodimg']);
			}else{
				$prodimg='';
			}

			//enter in db - delete old record first, 
			delete_post_meta( $change, $eshopprodimg );
			add_post_meta( $change, $eshopprodimg, $prodimg);
			//so will always be successful!
			echo'<div id="message" class="updated fade"><p>'.__('The Listing Image for this product has been updated.','eshop').'</p></div>'."\n";
		}
		$proddataimg=get_post_meta($change,$eshopprodimg,true);
		?>
		<div class="wrap">
		<h2><?php _e('Products','eshop'); ?></h2>
		<p><?php _e('A reference table for identifying products.','eshop'); ?></p>
		<?php

		//sort by switch statement
		$sortby='id';
		$csf=' class="current"';

		$numoptions=get_option('eshop_options_num');
		$metatable=$wpdb->prefix.'postmeta';

		$calt=0;
		$currsymbol=get_option('eshop_currency_symbol');
		$x=0;
		//add in post id( doh! )
		$grabit[$x]=get_post_custom($change);
		$grabit[$x]['id']=array($change);
		$x++;

		/*
		* remove the bottom array to try and flatten
		* could be rather slow, but easier than trying to create
		* a different method, at least for now!
		*/
		foreach($grabit as $foo=>$k){
			foreach($k as $bar=>$v){
				foreach($v as $nowt=>$val){
					$grab[$foo][$bar]=$val;
				}
			}
		}
		?>	
		<table id="listing" summary="<?php _e('Product listing','eshop'); ?>">
		<caption><?php _e('Product Quick reference table','eshop'); ?></caption>
		<thead>
		<tr>
		<th id="sku"><?php _e('Sku','eshop'); ?></th>
		<th id="page"><?php _e('Page','eshop'); ?></th>
		<th id="desc"><?php _e('Description','eshop'); ?></th>
		<th id="down"><?php _e('Download','eshop'); ?></th>
		<th id="feat"><?php _e('Featured','eshop'); ?></th>
		<th id="opt"><?php _e('Option/Price','eshop'); ?></th>
		<th id="imga"><?php _e('Current Image','eshop'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($grab as $foo=>$grabit){
			if($grabit['_Price 1']!=''){
				//get page title
				$ptitle=get_post($grabit['id']);
				//get download file title
				$pdown='';
				//check if downloadable product
				for($i=1;$i<=get_option('eshop_options_num');$i++){
					if($grabit["_Download ".$i]!=''){
						$dltable=$wpdb->prefix.'eshop_downloads';
						$fileid=$grabit["_Download ".$i];
						$filetitle=$wpdb->get_var("SELECT title FROM $dltable WHERE id='$fileid'");;
						$pdown.='<a href="admin.php?page=eshop_downloads.php&amp;edit='.$fileid.'">'.$filetitle.'</a>';
					}
				}
				if($pdown=='') $pdown='No';
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alt"';
				echo '<tr'.$alt.'>';
				echo '<td id="sku'.$calt.'" headers="sku">'.$grabit['_Sku'].'</td>';
				echo '<td headers="page sku'.$calt.'"><a href="page.php?action=edit&amp;post='.$grabit['id'].'">'.$ptitle->post_title.'</a></td>';
				echo '<td headers="desc sku'.$calt.'">'.stripslashes(attribute_escape($grabit['_Product Description'])).'</td>';
				echo '<td headers="down sku'.$calt.'">'.$pdown.'</td>';
				echo '<td headers="feat sku'.$calt.'">'.$grabit['_Featured Product'].'</td>';

				echo '<td headers="opt sku'.$calt.'">';
				for($i=1;$i<=$numoptions;$i++){
					if($grabit['_Option '.$i]!=''){
						echo stripslashes(attribute_escape($grabit['_Option '.$i]));
						echo ' @ '.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($grabit['_Price '.$i],2)).'<br />';
					}
				}
				echo '</td>';
				echo '<td>';
				$imgs= eshop_get_images($change);
				$x=1;
				if(is_array($imgs)){
					if($proddataimg==''){
						foreach($imgs as $k=>$v){
							$x++;
							echo '<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" />'."\n"; 
							break;
						}
					}else{
						foreach($imgs as $k=>$v){
							if($proddataimg==$v['url']){
								$x++;
								echo '<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" />'."\n"; 
								break;
							}
						}
					}
				}
				if($x==1){
					echo '<p>'.__('Not available.','eshop').'</p>';
				}
				echo '</td>'."\n";
				echo '</tr>'."\n";
			}
			?>
		</tbody>
		</table>
		<?php
		}
		echo '<h3>'.__('Associated Images','eshop').'</h3>'."\n";

		$id=$grabit['id'];
		echo '<form method="post" action="" id="eshop-gbase-alt">'."\n";
		echo '<fieldset><legend>'.__('Choose Image','eshop').'</legend>'."\n";

		$imgs= eshop_get_images($change);
		$x=1;
		if(is_array($imgs)){
			foreach($imgs as $k=>$v){
				if($proddataimg==$v['url']){
					$selected=' checked="checked"';
				}else{
					$selected='';
				}
				echo '<p class="ebaseimg"><input type="radio" value="'.$v['url'].'" name="prodimg" id="prodimg'.$x.'"'.$selected.' /><label for="prodimg'.$x.'"><img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /></label></p>'."\n"; 
				$x++;
			}
		}
		if($x==1){//in theory will never show - but just in case...
			echo '<p>'.__('No images found that were associated with this page, and hence cannot be associated with the product.','eshop').'</p>';
		}
		?>
		</fieldset>
		<p class="submit"><input type="submit" class="button-primary" name="submit" value="<?php _e('Update') ?>" /></p>
		<?php
		echo '</form></div>';
	}else{
			
	///images end
	
	
	?>
	<div class="wrap">
	<h2><?php _e('Products','eshop'); ?></h2>
	<p><?php _e('A reference table for identifying products','eshop'); ?>.</p>
	<?php
	
	//sort by switch statement
	$csa=$csb=$csc=$csd=$cse=$csf='';
	if(isset($_GET['by'])){
		switch ($_GET['by']) {
			case'sa'://date descending
				$sortby='_Sku';
				$csa=' class="current"';
				break;
			case'sb'://company name alphabetically
				$sortby='_Product Description';
				$csb=' class="current"';
				break;
			case'sc'://name alphabetically (last name)
				$sortby='_Shipping Rate';
				$csc=' class="current"';
				break;
			
			case'sd'://stock availability no longer works
				$sortby='_Stock Available';
				$csd=' class="current"';
				break;
		
			case'se'://transaction id numerically
				$sortby='_Featured Product';
				$cse=' class="current"';
				break;
			case'sf'://date ascending
			default:
				$sortby='id';
				$csf=' class="current"';
		}
	}else{
		$csf=' class="current"';
		$sortby='id';
	}
	
	
	$numoptions=get_option('eshop_options_num');
	$metatable=$wpdb->prefix.'postmeta';
	$poststable=$wpdb->prefix.'posts';
	$range=10;
	$max = $wpdb->get_var("SELECT COUNT(meta.post_id) FROM $metatable as meta, $poststable as posts where meta.meta_key='_Option 1' AND meta.meta_value!='' AND posts.ID = meta.post_id	AND (posts.post_type != 'revision' && posts.post_type != 'inherit')");
	if(get_option('eshop_records')!='' && is_numeric(get_option('eshop_records'))){
		$records=get_option('eshop_records');
	}else{
		$records='10';
	}
	
	if(isset($_GET['viewall']))$records=$max;
	$pager = new eshopPager( 
		$max ,          //see above
		$records,            // how many records to display at one time
		@$_GET['_p'] 	//this is the current page no carried via _GET
	);

	$pager->set_range($range);
	$thispage=$pager->get_limit();
	$c=$pager->get_limit_offset();
	
	if($max>0){
		$apge=wp_specialchars($_SERVER['PHP_SELF']).'?page='.$_GET['page'];
		echo '<ul id="eshopsubmenu">';
		echo '<li><span>'.__('Sort Orders by &raquo;','eshop').'</span></li>';
		echo '<li><a href="'.$apge.'&amp;by=sf"'.$csf.'>'.__('ID Number','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sa"'.$csa.'>'.__('Sku','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sb"'.$csb.'>'.__('Product','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sc"'.$csc.'>'.__('Shipping','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sd"'.$csd.'>'.__('Stock','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=se"'.$cse.'>'.__('Featured','eshop').'</a></li>';
		echo '</ul>';
		
		//$myrowres=$wpdb->get_results("Select DISTINCT post_id From $metatable where meta_key='Option 1' AND meta_value!='' order by post_id LIMIT $thispage");
		$myrowres=$wpdb->get_results("
		SELECT DISTINCT meta.post_id
		FROM $metatable as meta, $poststable as posts
		WHERE meta.meta_key = '_Option 1'
		AND meta.meta_value != ''
		AND posts.ID = meta.post_id
		AND (posts.post_type != 'revision' && posts.post_type != 'inherit')
		ORDER BY meta.post_id  LIMIT $thispage");

		$calt=0;
		$currsymbol=get_option('eshop_currency_symbol');
		$x=0;
		//add in post id( doh! )
		foreach($myrowres as $row){
			$grabit[$x]=get_post_custom($row->post_id);
			$grabit[$x]['id']=array($row->post_id);
			$x++;
		}
		/*
		* remove the bottom array to try and flatten
		* could be rather slow, but easier than trying to create
		* a different method, at least for now!
		*/
		foreach($grabit as $foo=>$k){
			foreach($k as $bar=>$v){
				foreach($v as $nowt=>$val){
					$array[$foo][$bar]=$val;
				}
			}
		}
		//then sort it how we want.
		$B = new eshop_multi_sort;
		$B->aData = $array;
		$B->aSortkeys = array($sortby);
		$B->sort();
		$grab=$B->aData;
	?>	
		<table id="listing" summary="product listing">
		<caption><?php _e('Product Quick reference table','eshop'); ?></caption>
		<thead>
		<tr>
		<th id="sku"><?php _e('Sku','eshop'); ?></th>
		<th id="page"><?php _e('Page','eshop'); ?></th>
		<th id="desc"><?php _e('Description','eshop'); ?></th>
		<th id="down"><abbr title="<?php _e('Downloads','eshop'); ?>"><?php _e('DL','eshop'); ?></abbr></th>
		<th id="ship"><abbr title="<?php _e('Shipping Rate','eshop'); ?>"><?php _e('S/R','eshop'); ?></abbr></th>
		<th id="stk"><abbr title="<?php _e('Stock Level','eshop'); ?>"><?php _e('Stk','eshop'); ?></abbr></th>
		<th id="purc"><abbr title="<?php _e('Number of Purchase','eshop'); ?>s"><?php _e('Purc.','eshop'); ?></abbr></th>
		<th id="ftrd"><abbr title="<?php _e('Marked as Featured','eshop'); ?>"><?php _e('Feat.','eshop'); ?></abbr></th>
		<th id="opt"><?php _e('Option/Price','eshop'); ?></th>
		<th id="associmg"><?php _e('Listing Images','eshop'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($grab as $foo=>$grabit){
			if($grabit['_Price 1']!=''){
				//get page title
				$ptitle=get_post($grabit['id']);
				$getid=$grabit['id'];
				//get download file title
				$pdown='';
				//check if downloadable product
				for($i=1;$i<=get_option('eshop_options_num');$i++){
					if($grabit["_Download ".$i]!=''){
						$dltable=$wpdb->prefix.'eshop_downloads';
						$fileid=$grabit["_Download ".$i];
						$filetitle=$wpdb->get_var("SELECT title FROM $dltable WHERE id='$fileid'");;
						$pdown.='<a href="admin.php?page=eshop_downloads.php&amp;edit='.$fileid.'">'.$filetitle.'</a>';
					}
				}
				if($pdown=='') $pdown='No';
				if($ptitle->post_title=='')
					$posttitle=__('(no title)');
				else
					$posttitle=$ptitle->post_title;
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alt"';
				echo '<tr'.$alt.'>';
				echo '<td id="sku'.$calt.'" headers="sku">'.$grabit['_Sku'].'</td>';
				echo '<td headers="page sku'.$calt.'"><a href="page.php?action=edit&amp;post='.$getid.'">'.$posttitle.'</a></td>';
				echo '<td headers="desc sku'.$calt.'">'.stripslashes(attribute_escape($grabit['_Product Description'])).'</td>';
				echo '<td headers="down sku'.$calt.'">'.$pdown.'</td>';
				echo '<td headers="ship sku'.$calt.'">'.$grabit['_Shipping Rate'].'</td>';
				if($pdown=='No'){
					$stocktable=$wpdb->prefix ."eshop_stock";
					$available=$wpdb->get_var("select available from $stocktable where post_id=$getid limit 1");
					if($grabit['_Stock Available']=='No'){
						$available='No';
					}elseif($grabit['_Stock Available']=='Yes' && $available==''){
						$available=__('not set','eshop');
					}
					echo '<td headers="stk sku'.$calt.'">'.$available.'</td>';
					$purchases=$wpdb->get_var("select purchases from $stocktable where post_id=$getid limit 1");
					if($purchases==''){
						$purchases='0';
					}
					echo '<td headers="purc sku'.$calt.'">'.$purchases.'</td>';
				}else{
					$dltable = $wpdb->prefix ."eshop_downloads";
					$row=$wpdb->get_row("SELECT * FROM $dltable WHERE id =$getid");
					echo '<td headers="stk sku'.$calt.'">n/a</td>';
					if($row->purchases==''){
						$row->purchases='0';
					}
					echo '<td headers="purc sku'.$calt.'">'.$row->purchases.'</td>';
				}
				
				echo '<td headers="ftrd sku'.$calt.'">'.$grabit['_Featured Product'].'</td>';

				echo '<td headers="opt sku'.$calt.'">';
				for($i=1;$i<=$numoptions;$i++){
					if($grabit['_Option '.$i]!=''){
						echo stripslashes(attribute_escape($grabit['_Option '.$i]));
						echo ' @ '.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($grabit['_Price '.$i],2)).'<br />';
					}
				}
				echo '</td>';
				echo '<td headers="associmg sku'.$calt.'">';
				
				$proddataimg=get_post_meta($getid,$eshopprodimg,true);

				$imgs= eshop_get_images($getid);
				$x=1;

				if(is_array($imgs)){
					echo '<a href="admin.php?page=eshop_products.php&amp;change='.$grabit['id'].'" title="'.__('Change image for','eshop').' '.$grabit['_Sku'].'">';
					if($proddataimg==''){
						foreach($imgs as $k=>$v){
							$x++;
							echo '<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" />'."\n"; 
							break;
						}
					}else{
						foreach($imgs as $k=>$v){
							if($proddataimg==$v['url']){
								$x++;
								echo '<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" />'."\n"; 
								break;
							}
						}
					}
					echo '</a>';
				}
				if($x==1){
					echo '<p>'.__('Not available.','eshop').'</p>';
				}
				echo '</td>';
				echo '</tr>'."\n";
			}
		}

		?>
		</tbody>
		</table>
		<?php
		//paginate
		echo '<div class="paginate"><p>';
		if($pager->_pages > 1){
			echo $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>','eshop')). '<br />';
		}else{
			echo $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>','eshop')). '<br />';
		}
		echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ',__('&laquo; First Page','eshop'),__('Last Page &raquo;','eshop')).'';
		//echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ').'<br />';
		if($pager->_pages >= 2){
			echo ' &raquo; <a class="pag-view" href="'.wp_specialchars($_SERVER['REQUEST_URI']).'&amp;_p=1&amp;action='.$_GET['action'].'&amp;viewall=yes" title="'.$status.' '.__('orders','eshop').'">'.__('View All &raquo;','eshop').'</a>';
		}
		echo '</p></div>';
		//end
	}else{	
		echo '<p>'.__('There are no products available.','eshop').'</p>';
	}
	echo '</div>';
}
	eshop_show_credits();
}
?>