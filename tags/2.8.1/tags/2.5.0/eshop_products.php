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

function eshop_products_manager() {
	global $wpdb;
	include_once ("pager-class.php");
	
	?>
	<div class="wrap">
	<h2>Products</h2>
	<p>A reference table for identifying products.</p>
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
			case'sc'://name alphabetically (last name)
				$sortby='Shipping Rate';
				$csc=' class="current"';
				break;
			
			case'sd'://stock availability no longer works
				$sortby='Stock Available';
				$csd=' class="current"';
				break;
		
			case'se'://transaction id numerically
				$sortby='Featured Product';
				$cse=' class="current"';
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
		echo '<li><a href="'.$apge.'&amp;by=sc"'.$csc.'>Shipping</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sd"'.$csd.'>Stock</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=se"'.$cse.'>Featured</a></li>';
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
		<th id="ship">Shipping</th>
		<th id="stk">Stock</th>
		<th id="purc">Purchases</th>
		<th id="ftrd">Featured</th>
		<th id="opt">Option/Price</th>
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
				echo '<td headers="ship sku'.$calt.'">'.$grabit['Shipping Rate'].'</td>';
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
					$purchases=$wpdb->get_var("select purchases from $stocktable where post_id=$pid limit 1");
					if($purchases==''){
						$purchases='0';
					}
					echo '<td headers="purc sku'.$calt.'">'.$purchases.'</td>';
				}else{
					$dltable = $wpdb->prefix ."eshop_downloads";
					$row=$wpdb->get_row("SELECT * FROM $dltable WHERE id =$id");
					echo '<td headers="stk sku'.$calt.'">n/a</td>';
					if($row->purchases==''){
						$row->purchases='0';
					}
					echo '<td headers="purc sku'.$calt.'">'.$row->purchases.'</td>';
				}
				
				echo '<td headers="ftrd sku'.$calt.'">'.$grabit['Featured Product'].'</td>';

				echo '<td headers="opt sku'.$calt.'">';
				for($i=1;$i<=$numoptions;$i++){
					if($grabit['Option '.$i]!=''){
						echo $grabit['Option '.$i];
						echo ' @ '.$currsymbol.$grabit['Price '.$i].'<br />';
					}
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
	eshop_show_credits();
}
?>