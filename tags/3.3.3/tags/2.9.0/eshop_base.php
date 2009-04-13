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

function eshop_base_manager() {
	global $wpdb;
	include_once ("pager-class.php");
	include 'eshop-base-functions.php';

if(!isset($_GET['change'])){
	?>
	<div class="wrap">
	<h2><?php _e('eShop Base Products','eshop'); ?></h2>
	<p><?php _e('A reference table for products in your base feed.','eshop'); ?></p>
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
			
			
			case'sd'://stock availability no longer works
				$sortby='_Stock Available';
				$csd=' class="current"';
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
		echo '<li><a href="'.$apge.'&amp;by=sd"'.$csd.'>'.__('Stock','eshop').'</a></li>';
		echo '</ul>';
		
//$myrowres=$wpdb->get_results("Select DISTINCT post_id From $metatable where meta_key='Option 1' AND meta_value!='' order by post_id LIMIT $thispage");
		$myrowres=$wpdb->get_results("
		SELECT DISTINCT meta.post_id
		FROM $metatable as meta, $poststable as posts
		WHERE meta.meta_key = '_Option 1'
		AND meta.meta_value != ''
		AND posts.ID = meta.post_id
		AND (posts.post_type != 'revision' && posts.post_type != 'inherit')
		ORDER BY meta.post_id  LIMIT $thispage");		$calt=0;
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
		<table id="listing" summary="<?php _e('product listing','eshop'); ?>">
		<caption><?php _e('Product Quick reference table','eshop'); ?></caption>
		<thead>
		<tr>
		<th id="sku"><?php _e('Sku','eshop'); ?></th>
		<th id="page"><?php _e('Page','eshop'); ?></th>
		<th id="desc"><?php _e('Description','eshop'); ?></th>
		<th id="down"><?php _e('Download','eshop'); ?></th>
		<th id="stk"><?php _e('Stock','eshop'); ?></th>
		<th id="opt"><?php _e('Option/Price','eshop'); ?></th>
		<th id="imga"><?php _e('Image','eshop'); ?></th>

		</tr>
		</thead>
		<tbody>
		<?php
		foreach($grab as $foo=>$grabit){
			if($grabit['_Price 1']!=''){
				//get page title
				$ptitle=get_post($grabit['id']);
				//get download file title
				if($grabit['_Product Download']==''){
					$pdown='No';
				}else{
					$id=$grabit['_Product Download'];
					$dltable = $wpdb->prefix ."eshop_downloads";
					$dlname=$wpdb->get_var("Select title From $dltable where id='$id' limit 1");

					$pdown='<a href="admin.php?page=eshop_downloads.php&amp;edit='.$id.'">'.$dlname.'</a>';
				}
				if($ptitle->post_title=='')
					$posttitle=__('(no title)');
				else
					$posttitle=$ptitle->post_title;
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alt"';
				echo '<tr'.$alt.'>';
				echo '<td id="sku'.$calt.'" headers="sku"><a href="admin.php?page=eshop_base.php&amp;change='.$grabit['id'].'" title="'.__('change details','eshop').'">'.$grabit['_Sku'].'</a></td>';
				echo '<td headers="page sku'.$calt.'"><a href="page.php?action=edit&amp;post='.$grabit['id'].'">'.$posttitle.'</a></td>';
				echo '<td headers="desc sku'.$calt.'">'.stripslashes(attribute_escape($grabit['_Product Description'])).'</td>';
				echo '<td headers="down sku'.$calt.'">'.$pdown.'</td>';
				if($pdown=='No'){
					$stocktable=$wpdb->prefix ."eshop_stock";
					$pid=$grabit['id'];
					$available=$wpdb->get_var("select available from $stocktable where post_id=$pid limit 1");
					if($grabit['_Stock Available']=='No'){
						$available='No';
					}elseif($grabit['_Stock Available']=='Yes' && $available==''){
						$available='not set';
					}
					echo '<td headers="stk sku'.$calt.'">'.$available.'</td>';
					
				}else{
					$dltable = $wpdb->prefix ."eshop_downloads";
					$row=$wpdb->get_row("SELECT * FROM $dltable WHERE id =$id");
					echo '<td headers="stk sku'.$calt.'">n/a</td>';
					if($row->purchases==''){
						$row->purchases='0';
					}
				}
				
				echo '<td headers="opt sku'.$calt.'">';
				for($i=1;$i<=$numoptions;$i++){
					if($grabit['_Option '.$i]!=''){
						echo stripslashes(attribute_escape($grabit['_Option '.$i]));
						echo ' @ '.$currsymbol.$grabit['_Price '.$i].'<br />';
					}
				}
				echo '</td>';
				echo '<td>';
				$attachments = $wpdb->get_col("SELECT ID FROM $wpdb->posts where post_parent= ".$grabit['id']." and post_type = 'attachment' limit 1");
				$basetable=$wpdb->prefix ."eshop_base_products";
				$getid=$grabit['id'];
				$basedimg=$wpdb->get_var("SELECT img FROM $basetable WHERE post_id = $getid");
				
				$imgs= eshop_get_images($getid);
				$x=1;
			
				if(is_array($imgs)){
					if($basedimg==''){
						foreach($imgs as $k=>$v){
							$x++;
							echo '<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" />'."\n"; 
							break;
						}
					}else{
						foreach($imgs as $k=>$v){
							if($basedimg==$v['url']){
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
				echo '</td>';
				echo '</tr>';
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
}else{
//////////change one.
//form checks:
	$basetable=$wpdb->prefix ."eshop_base_products";
	$change=$_GET['change'];
	if(is_numeric($change)){
		if(isset($_POST['submit'])){
			include 'cart-functions.php';
			if (get_magic_quotes_gpc()==0) {
				$_POST = stripslashes_array($_POST);
			}
			$_POST=sanitise_array($_POST);
			$err='';
			if(isset($_POST['baseimg'])){
				$baseimg=$wpdb->escape($_POST['baseimg']);
			}else{
				$baseimg='';
			}
			$basebrand=$wpdb->escape($_POST['basebrand']);
			$baseean=$wpdb->escape($_POST['baseean']);
			$baseisbn=$wpdb->escape($_POST['baseisbn']);
			$basempn=$wpdb->escape($_POST['basempn']);
			$baseptype=$wpdb->escape($_POST['baseptype']);
			$baseqty=$wpdb->escape($_POST['baseqty']);
			$basecondition=$wpdb->escape($_POST['basecondition']);
			$baseexpiration_year=$_POST['baseexpiration_year'];
			$baseexpiration_month=$_POST['baseexpiration_month'];
			$baseexpiration_day=$_POST['baseexpiration_day'];

			if(!is_numeric($baseqty)){
				$baseqty='25';
				$err.='<li>'.__('Quantity was not numeric, a default of 25 has been applied.','eshop').'</li>';
			}
			$baseexpiration=$wpdb->escape($baseexpiration_year.'-'.$baseexpiration_month.'-'.$baseexpiration_day);

			//enter in db - delete old record first, 
			//then it will always be an insert and easier than checking for update.
			$wpdb->query("DELETE FROM $basetable WHERE post_id = $change limit 1");
			$wpdb->query("INSERT INTO $basetable (
			post_id,img,brand,ptype,thecondition,expiry,ean,isbn,mpn,qty
			)VALUES(
			'$change','$baseimg','$basebrand','$baseptype','$basecondition','$baseexpiration',
			'$baseean','$baseisbn','$basempn','$baseqty'
			)");

			if($err!=''){
				echo'<div id="message" class="error fade"><p>'.__('<strong>Error</strong> the following were not valid:','eshop').'</p><ul>'.$err.'</ul></div>'."\n";
			}else{
				echo'<div id="message" class="updated fade"><p>'.__('eshop Base details for this product have been updated.','eshop').'</p></div>'."\n";
			}
		}
		$basedata=$wpdb->get_row("SELECT * FROM $basetable WHERE post_id = $change");
		
		if($basedata==''){
				$basedata->post_id=$basedata->img=$basedata->brand=$basedata->ptype=$basedata->thecondition=$basedata->expiry=$basedata->ean=$basedata->isbn=$basedata->mpn=$basedata->qty='';
		}
		
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
		<table id="listing" summary="<?php _e('product listin','eshop'); ?>g">
		<caption><?php _e('Product Quick reference table','eshop'); ?></caption>
		<thead>
		<tr>
		<th id="sku"><?php _e('Sku','eshop'); ?></th>
		<th id="page"><?php _e('Page','eshop'); ?></th>
		<th id="desc"><?php _e('Description','eshop'); ?></th>
		<th id="down"><?php _e('Download','eshop'); ?></th>
		<th id="stk"><?php _e('Stock','eshop'); ?></th>
		<th id="opt"><?php _e('Option/Price','eshop'); ?></th>
		<th id="imga"><?php _e('Image','eshop'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($grab as $foo=>$grabit){
			if($grabit['_Price 1']!=''){
				//get page title
				$ptitle=get_post($grabit['id']);
				//get download file title
				if($grabit['_Product Download']==''){
					$pdown='No';
				}else{
					$id=$grabit['_Product Download'];
					$dltable = $wpdb->prefix ."eshop_downloads";
					$dlname=$wpdb->get_var("Select title From $dltable where id='$id' limit 1");

					$pdown='<a href="admin.php?page=eshop_downloads.php&amp;edit='.$id.'">'.$dlname.'</a>';
				}
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alt"';
				echo '<tr'.$alt.'>';
				echo '<td id="sku'.$calt.'" headers="sku">'.$grabit['_Sku'].'</td>';
				echo '<td headers="page sku'.$calt.'"><a href="page.php?action=edit&amp;post='.$grabit['id'].'">'.$ptitle->post_title.'</a></td>';
				echo '<td headers="desc sku'.$calt.'">'.stripslashes(attribute_escape($grabit['_Product Description'])).'</td>';
				echo '<td headers="down sku'.$calt.'">'.$pdown.'</td>';
				if($pdown=='No'){
					$stocktable=$wpdb->prefix ."eshop_stock";
					$pid=$grabit['id'];
					$available=$wpdb->get_var("select available from $stocktable where post_id=$pid limit 1");
					if($grabit['_Stock Available']=='No'){
						$available='No';
					}elseif($grabit['_Stock Available']=='Yes' && $available==''){
						$available='not set';
					}
					echo '<td headers="stk sku'.$calt.'">'.$available.'</td>';

				}else{
					$dltable = $wpdb->prefix ."eshop_downloads";
					$row=$wpdb->get_row("SELECT * FROM $dltable WHERE id =$id");
					echo '<td headers="stk sku'.$calt.'">n/a</td>';
					if($row->purchases==''){
						$row->purchases='0';
					}
				}

				echo '<td headers="opt sku'.$calt.'">';
				for($i=1;$i<=$numoptions;$i++){
					if($grabit['_Option '.$i]!=''){
						echo stripslashes(attribute_escape($grabit['_Option '.$i]));
						echo ' @ '.$currsymbol.$grabit['_Price '.$i].'<br />';
					}
				}
				echo '</td>';


				echo '<td>';

				$imgs= eshop_get_images($change);
				$x=1;
				
	
				if(is_array($imgs)){
					if($basedata->img==''){
						foreach($imgs as $k=>$v){
							$x++;
							echo '<img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" />'."\n"; 
							break;
						}
					}else{
						foreach($imgs as $k=>$v){
							if($basedata->img==$v['url']){
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
		echo '<h3>'.__('Additional settings','eshop').'</h3>'."\n";

		$id=$grabit['id'];
		echo '<form method="post" action="" id="eshop-gbase-alt">'."\n";
		$attachments = $wpdb->get_col("SELECT ID FROM $wpdb->posts where post_parent= ".$change." and post_type = 'attachment'");
		echo '<fieldset><legend>'.__('Image','eshop').'</legend>'."\n";

		$imgs= eshop_get_images($change);
		$x=1;
		if(is_array($imgs)){
			foreach($imgs as $k=>$v){
				if($basedata->img==$v['url']){
					$selected=' checked="checked"';
				}else{
					$selected='';
				}
				echo '<p class="ebaseimg"><input type="radio" value="'.$v['url'].'" name="baseimg" id="baseimg'.$x.'"'.$selected.' /><label for="baseimg'.$x.'"><img src="'.$v['url'].'" '.$v['size'].' alt="'.$v['alt'].'" /></label></p>'."\n"; 
				$x++;
			}
		}
		if($x==1){
			echo '<p>'.__('No images found that were associated with this page, and hence cannot be associated with the product.','eshop').'</p>';
		}
		?>
		</fieldset>
		
		
		
		<fieldset id="baseothers"><legend><?php _e('Others','eshop'); ?></legend>

		<label for="basebrand"><?php _e('Brand <small>The brand name of the product</small>','eshop'); ?></label>
		<input type="text" name="basebrand" id="basebrand" value="<?php echo wp_specialchars($basedata->brand); ?>" />
		<label for="baseean"><?php _e('EAN <small>European Article Number is a 13 digit number often below the bar code of the item.</small>','eshop'); ?></label>
		<input type="text" name="baseean" id="baseean" value="<?php echo wp_specialchars($basedata->ean); ?>" />
		<label for="baseisbn"><?php _e('ISBN <small>The unique 10- or 13-digit number assigned to every printed book.</small>','eshop'); ?></label>
		<input type="text" name="baseisbn" id="baseisbn" value="<?php echo wp_specialchars($basedata->isbn); ?>" />
		<label for="basempn"><?php _e('MPN <small>Manufacturer\'s Part Number is a unique code determined by the manufacturer for that product.</small>','eshop'); ?></label>
		<input type="text" name="basempn" id="basempn" value="<?php echo wp_specialchars($basedata->mpn); ?>" />
		<label for="baseptype"><?php _e('Product type <small>The type of product being offered.</small>','eshop'); ?></label>
		<input type="text" name="baseptype" id="baseptype" value="<?php echo wp_specialchars($basedata->ptype); ?>" />
		<label for="baseqty"><?php _e('Quantity','eshop'); ?></label>
		<input type="text" name="baseqty" id="baseqty" value="<?php echo wp_specialchars($basedata->qty); ?>" />
	  <label for="basecondition"><?php _e('Condition <small>the condition of this product</small>','eshop'); ?></label>
	  <select name="basecondition" id="basecondition">
		<?php
		//'
		foreach($currentconditions as $code){
			if($basecondition==''){
				if($code == get_option('eshop_base_condition')){
					$sel=' selected="selected"';
				}else{
					$sel='';
				}
			}elseif($code==$basecondition){
				$sel=' selected="selected"';
			}else{
				$sel='';
			}
			echo '<option value="'. $code .'"'. $sel .'>'. $code .'</option>'."\n";
		}

		?>
	  </select>
	  <fieldset><legend><?php _e('Expiration date <small>(or how long a product will be available.)</small>','eshop'); ?></legend>
	  <label for="baseexpiration_year"><?php _e('Year','eshop'); ?></label>
		<select name="baseexpiration_year" id="baseexpiration_year">
		<?php
		// work this out!!!
		if($basedata->expiry==''){
			$baseexpiry=get_option('eshop_base_expiry');
			$basedate=date('Y-m-d',mktime(0, 0, 0, date("m") , date("d")+$baseexpiry, date("Y")));
			list($baseexpiration_year, $baseexpiration_month, $baseexpiration_day) = split('[/.-]', $basedate);
		}else{
			list($baseexpiration_year, $baseexpiration_month, $baseexpiration_day) = split('[/.-]', $basedata->expiry);
		}
		
		for($i=date('Y');$i<=date('Y')+5;$i++){
			if($i==$baseexpiration_year){
				$sel=' selected="selected"';
			}else{
				$sel='';
			}
			echo '<option value="'.$i.'"'.$sel.'>'.$i.'</option>'."\n";
		}
		?>
	  </select>
		<label for="baseexpiration_month"><?php _e('Month','eshop'); ?></label>

		  <select name="baseexpiration_month" id="baseexpiration_month">
		<?php

		for($i=1;$i<=12;$i++){
			if($i==$baseexpiration_month){
				$sel=' selected="selected"';
			}else{
				$sel='';
			}
			echo '<option value="'.$i.'"'.$sel.'>'.$i.'</option>'."\n";
		}
		?>
	  </select>
		<label for="baseexpiration_day"><?php _e('Day','eshop'); ?></label>

		  <select name="baseexpiration_day" id="baseexpiration_day">
		<?php

		for($i=1;$i<=31;$i++){
			if($i==$baseexpiration_day){
				$sel=' selected="selected"';
			}else{
				$sel='';
			}
			echo '<option value="'.$i.'"'.$sel.'>'.$i.'</option>'."\n";
		}
		?>
	  </select>
	  </fieldset>
	  </fieldset>
	  <p class="submit">
	  <input type="submit" name="submit" value="<?php _e('Update') ?>" />
	</p>
		<?php


		echo '</form></div>';
	}else{
	?>
	<div class="wrap">
	<h2><?php _e('Error','eshop'); ?></h2>
	<p><?php _e('That product does not exist!','eshop'); ?></p>
	</div>
	<?php
	}
}
	eshop_show_credits();
}
?>