<?php
if ('eshop_products.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');
     
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
	<h2>eShop Base Products</h2>
	<p>A reference table for products in your base feed..</p>
	<?php
	
	//sort by switch statement
	$csa=$csb=$csc=$csd=$cse=$csf='';
	if(isset($_GET['by'])){
		switch ($_GET['by']) {
			case'sa'://date descending
				$sortby='Sku';
				$csa=' class="current"';
				break;
			case'sb'://company name alphabetically
				$sortby='Product Description';
				$csb=' class="current"';
				break;
			
			
			case'sd'://stock availability no longer works
				$sortby='Stock Available';
				$csd=' class="current"';
				break;
		
			
			case'sf'://date ascending
			default:
				$sortby='id';
				$csf=' class="current"';
		}
	}else{
		$csf=' class="current"';
	}
	
	
	$numoptions=get_option('eshop_options_num');
	$metatable=$wpdb->prefix.'postmeta';
	$range=10;
	$max = $wpdb->get_var("SELECT COUNT(post_id) FROM $metatable where meta_key='Option 1' AND meta_value!=''");
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
		echo '<li><span>Sort Orders by &raquo;</span></li>';
		echo '<li><a href="'.$apge.'&amp;by=sf"'.$csf.'>ID Number</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sa"'.$csa.'>Sku</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sb"'.$csb.'>Product</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sd"'.$csd.'>Stock</a></li>';
		echo '</ul>';
		
		$myrowres=$wpdb->get_results("Select DISTINCT post_id From $metatable where meta_key='Option 1' AND meta_value!='' order by post_id LIMIT $thispage");
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
		<caption>Product Quick reference table</caption>
		<thead>
		<tr>
		<th id="sku">Sku</th>
		<th id="page">Page</th>
		<th id="desc">Description</th>
		<th id="down">Download</th>
		<th id="stk">Stock</th>
		<th id="opt">Option/Price</th>
		<th id="imga">Image</th>

		</tr>
		</thead>
		<tbody>
		<?php
		foreach($grab as $foo=>$grabit){
			if($grabit['Price 1']!=''){
				//get page title
				$ptitle=get_post($grabit['id']);
				//get download file title
				if($grabit['Product Download']==''){
					$pdown='No';
				}else{
					$id=$grabit['Product Download'];
					$dltable = $wpdb->prefix ."eshop_downloads";
					$dlname=$wpdb->get_var("Select title From $dltable where id='$id' limit 1");

					$pdown='<a href="admin.php?page=eshop_downloads.php&amp;edit='.$id.'">'.$dlname.'</a>';
				}
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alt"';
				echo '<tr'.$alt.'>';
				echo '<td id="sku'.$calt.'" headers="sku"><a href="admin.php?page=eshop_base.php&amp;change='.$grabit['id'].'" title="change details">'.$grabit['Sku'].'</a></td>';
				echo '<td headers="page sku'.$calt.'"><a href="page.php?action=edit&amp;post='.$grabit['id'].'">'.$ptitle->post_title.'</a></td>';
				echo '<td headers="desc sku'.$calt.'">'.$grabit['Product Description'].'</td>';
				echo '<td headers="down sku'.$calt.'">'.$pdown.'</td>';
				if($pdown=='No'){
					$stocktable=$wpdb->prefix ."eshop_stock";
					$pid=$grabit['id'];
					$available=$wpdb->get_var("select available from $stocktable where post_id=$pid limit 1");
					if($grabit['Stock Available']=='No'){
						$available='No';
					}elseif($grabit['Stock Available']=='Yes' && $available==''){
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
					if($grabit['Option '.$i]!=''){
						echo $grabit['Option '.$i];
						echo ' @ '.$currsymbol.$grabit['Price '.$i].'<br />';
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
					echo '<p>Not available.</p>';
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
			echo $pager->get_title('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>'). '<br />';
		}else{
			echo $pager->get_title('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>'). '<br />';
		}
		echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ','&laquo; First Page','Last Page &raquo;').'';
		//echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ').'<br />';
		if($pager->_pages >= 2){
			echo ' &raquo; <a class="pag-view" href="'.wp_specialchars($_SERVER['REQUEST_URI']).'&amp;_p=1&amp;action='.$_GET['action'].'&amp;viewall=yes" title="View all '.$status.' orders">View All &raquo;</a>';
		}
		echo '</p></div>';
		//end
	}else{	
		echo '<p>There are no products available.</p>';
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
			$baseimg=$wpdb->escape($_POST['baseimg']);
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
				$baseqty='99';
				$err.='<li>Quantity was not numeric, a default of 99 has been applied.</li>';
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
				echo'<div id="message" class="error fade"><p><strong>Error</strong> the following were not valid:</p><ul>'.$err.'</ul></div>'."\n";
			}else{
				echo'<div id="message" class="updated fade"><p>eshop Base details for this product have been updated.</p></div>'."\n";
			}
		}
		$basedata=$wpdb->get_row("SELECT * FROM $basetable WHERE post_id = $change");
	?>
		<div class="wrap">
		<h2>Products</h2>
		<p>A reference table for identifying products.</p>
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
		<table id="listing" summary="product listing">
		<caption>Product Quick reference table</caption>
		<thead>
		<tr>
		<th id="sku">Sku</th>
		<th id="page">Page</th>
		<th id="desc">Description</th>
		<th id="down">Download</th>
		<th id="stk">Stock</th>
		<th id="opt">Option/Price</th>
		<th id="imga">Image</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($grab as $foo=>$grabit){
			if($grabit['Price 1']!=''){
				//get page title
				$ptitle=get_post($grabit['id']);
				//get download file title
				if($grabit['Product Download']==''){
					$pdown='No';
				}else{
					$id=$grabit['Product Download'];
					$dltable = $wpdb->prefix ."eshop_downloads";
					$dlname=$wpdb->get_var("Select title From $dltable where id='$id' limit 1");

					$pdown='<a href="admin.php?page=eshop_downloads.php&amp;edit='.$id.'">'.$dlname.'</a>';
				}
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alt"';
				echo '<tr'.$alt.'>';
				echo '<td id="sku'.$calt.'" headers="sku">'.$grabit['Sku'].'</td>';
				echo '<td headers="page sku'.$calt.'"><a href="page.php?action=edit&amp;post='.$grabit['id'].'">'.$ptitle->post_title.'</a></td>';
				echo '<td headers="desc sku'.$calt.'">'.$grabit['Product Description'].'</td>';
				echo '<td headers="down sku'.$calt.'">'.$pdown.'</td>';
				if($pdown=='No'){
					$stocktable=$wpdb->prefix ."eshop_stock";
					$pid=$grabit['id'];
					$available=$wpdb->get_var("select available from $stocktable where post_id=$pid limit 1");
					if($grabit['Stock Available']=='No'){
						$available='No';
					}elseif($grabit['Stock Available']=='Yes' && $available==''){
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
					if($grabit['Option '.$i]!=''){
						echo $grabit['Option '.$i];
						echo ' @ '.$currsymbol.$grabit['Price '.$i].'<br />';
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
					echo '<p>Not available.</p>';
				}

				echo '</td>'."\n";


				echo '</tr>'."\n";
			}


			?>
		</tbody>
		</table>
		<?php
		}
		echo '<h3>Additional settings</h3>'."\n";

		$id=$grabit['id'];
		echo '<form method="post" action="" id="eshop-gbase-alt">'."\n";
		$attachments = $wpdb->get_col("SELECT ID FROM $wpdb->posts where post_parent= ".$change." and post_type = 'attachment'");
		echo '<fieldset><legend>Image</legend>'."\n";

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
			echo '<p>No images found that were associated with this page, and hence cannot be associated with the product.</p>';
		}
		?>
		</fieldset>
		
		
		
		<fieldset id="baseothers"><legend>Others</legend>

		<label for="basebrand">Brand <small>The brand name of the product</small></label>
		<input type="text" name="basebrand" id="basebrand" value="<?php echo wp_specialchars($basedata->brand); ?>" />
		<label for="baseean">EAN <small>European Article Number is a 13 digit number often below the bar code of the item.</small></label>
		<input type="text" name="baseean" id="baseean" value="<?php echo wp_specialchars($basedata->ean); ?>" />
		<label for="baseisbn">ISBN <small>The unique 10- or 13-digit number assigned to every printed book.</small></label>
		<input type="text" name="baseisbn" id="baseisbn" value="<?php echo wp_specialchars($basedata->isbn); ?>" />
		<label for="basempn">MPN <small>Manufacturer's Part Number is a unique code determined by the manufacturer for that product.</small></label>
		<input type="text" name="basempn" id="basempn" value="<?php echo wp_specialchars($basedata->mpn); ?>" />
		<label for="baseptype">Product type <small>The type of product being offered.</small></label>
		<input type="text" name="baseptype" id="baseptype" value="<?php echo wp_specialchars($basedata->ptype); ?>" />
		<label for="baseqty">Quantity</label>
		<input type="text" name="baseqty" id="baseqty" value="<?php echo wp_specialchars($basedata->qty); ?>" />
	  <label for="basecondition">Condition <small>the condition of this product</small></label>
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
	  <fieldset><legend>Expiration date <small>(or how long a product will be available.)</small></legend>
	  <label for="baseexpiration_year">Year</label>
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
		<label for="baseexpiration_month">Month</label>

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
		<label for="baseexpiration_day">Day</label>

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
	<h2>Error</h2>
	<p>That product does not exist!</p>
	</div>
	<?php
	}
}
	eshop_show_credits();

}
?>