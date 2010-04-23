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
	global $wpdb, $user_ID,$eshopoptions;
	get_currentuserinfo();
	//add in if current user can here
	if(current_user_can('eShop_admin')){
		$eshopfilter='all';
		if(isset($_POST['eshopfiltering'])){
			$eshopfilter=$_POST['eshopfilter'];
		}
		?>
		<div class="wrap">
		<div id="eshopicon" class="icon32"></div><h2><?php _e('Authors','eshop'); ?></h2>
		<?php if(isset($msg)) echo '<div class="updated fade"><p>'.$msg.'</p></div>'; ?>
		<form action="" method="post" class="eshop filtering">
		<p><label for="eshopfilter"><?php _e('Show products for','eshop'); ?></label><select name="eshopfilter" id="eshopfilter">
		<?php
		echo eshop_authors($eshopfilter);
		?>
		</select><input type="submit" name="eshopfiltering" id="submit"  class="submit button-primary" value="Filter" /></p>
		</form>
		</div>
	<?php
	}
	?>
	<div class="wrap">
	<h2><?php _e('Products','eshop'); ?></h2>
	<p><?php _e('A reference table for identifying products','eshop'); ?>.</p>
	<?php
	if(isset($_POST['eshopqp'])){
		foreach($_POST['product'] as $id=>$type){
			$pid=$id;
			$stkav=get_post_meta( $pid, '_eshop_stock',true );
			$eshop_product=get_post_meta( $pid, '_eshop_product',true );
			if(isset($type['stkqty']) && is_numeric($type['stkqty'])){
				$meta_value=$type['stkqty'];
				$stocktable=$wpdb->prefix ."eshop_stock";
				$results=$wpdb->get_results("select post_id from $stocktable where post_id=$pid");
				if(!empty($results)){
					$wpdb->query($wpdb->prepare("UPDATE $stocktable set available=$meta_value where post_id=$pid"));
				}else{
					$wpdb->query($wpdb->prepare("INSERT INTO $stocktable (post_id,available,purchases) VALUES ($pid,$meta_value,0)"));
				}
			}
					
			if(isset($type['featured'])){
				$eshop_product['featured']='Yes';
				update_post_meta( $id, '_eshop_featured', 'Yes');
			}else{
				$eshop_product['featured']='no';
				delete_post_meta( $id, '_eshop_featured');
			}
			if(isset($type['stkavail']))
				$stkav='1';
			else
				$stkav='0';
			update_post_meta( $pid, '_eshop_stock', $stkav);
			update_post_meta( $pid, '_eshop_product', $eshop_product);
		}
		echo'<div id="message" class="updated fade">'.__('Products have been updated','eshop')."</div>\n";

	
	}
	//sort by switch statement
	$csa=$csb=$csc=$csd=$cse=$csf='';
	if(isset($_GET['by'])){
		switch ($_GET['by']) {
			case'sa'://date descending
				$sortby='sku';
				$csa=' class="current"';
				break;
			case'sb'://description alphabetically
				$sortby='description';
				$csb=' class="current"';
				break;
			case'sc'://name alphabetically (last name)
				$sortby='shiprate';
				$csc=' class="current"';
				break;
			
			case'sd'://stock availability 
				$sortby='qty';
				$csd=' class="current"';
				break;
		
			case'se'://transaction id numerically
				$sortby='featured';
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
	
	if(current_user_can('eShop_admin')){
		if($eshopfilter=='all')
			$addtoq='';
		elseif(is_numeric($eshopfilter))
			$addtoq="AND posts.post_author = $eshopfilter";
		else
			die('There was an error');
	}else{
		$addtoq="AND posts.post_author = $user_ID ";
	}
	$numoptions=$eshopoptions['options_num'];
	$metatable=$wpdb->prefix.'postmeta';
	$poststable=$wpdb->prefix.'posts';
	$range=10;
	$max = $wpdb->get_var("SELECT COUNT(meta.post_id) FROM $metatable as meta, $poststable as posts where meta.meta_key='_eshop_product' AND meta.meta_value!='' AND posts.ID = meta.post_id	AND posts.post_status = 'publish' ".$addtoq);
	if($eshopoptions['records']!='' && is_numeric($eshopoptions['records'])){
		$records=$eshopoptions['records'];
	}else{
		$records='10';
	}
	if(isset($_GET['_p']) && is_numeric($_GET['_p']))$epage=$_GET['_p'];
		else $epage='1';
		if(!isset($_GET['eshopall'])){
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array'
				));
			$offset=($epage*$records)-$records;
		}else{
			$page_links = paginate_links( array(
				'base' => add_query_arg( '_p', '%#%' ),
				'format' => '',
				'total' => ceil($max / $records),
				'current' => $epage,
				'type'=>'array',
				'show_all' => true,
			));
			$offset='0';
			$records=$max;
	}
	
	if($max>0){
		$apge=esc_url($_SERVER['PHP_SELF']).'?page='.$_GET['page'];
		echo '<ul id="eshopsubmenu">';
		echo '<li><span>'.__('Sort Orders by &raquo;','eshop').'</span></li>';
		echo '<li><a href="'.$apge.'&amp;by=sf"'.$csf.'>'.__('ID Number','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sa"'.$csa.'>'.__('Sku','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sb"'.$csb.'>'.__('Product','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sc"'.$csc.'>'.__('Shipping','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=sd"'.$csd.'>'.__('Stock','eshop').'</a></li>';
		echo '<li><a href="'.$apge.'&amp;by=se"'.$cse.'>'.__('Featured','eshop').'</a></li>';
		echo '</ul>';
		
		if(current_user_can('eShop_admin')){
			if($eshopfilter=='all')
				$addtoq='';
			elseif(is_numeric($eshopfilter))
				$addtoq="AND posts.post_author = $eshopfilter";
			else
				die('There was an error');
		}else{
			$addtoq="AND posts.post_author = $user_ID ";
		}
		
		$myrowres=$wpdb->get_results("
		SELECT DISTINCT meta.post_id
		FROM $metatable as meta, $poststable as posts
		WHERE meta.meta_key = '_eshop_product'
		AND meta.meta_value != ''
		AND posts.ID = meta.post_id
		$addtoq
		ORDER BY meta.post_id  LIMIT $offset, $records");

		$calt=0;
		$currsymbol=$eshopoptions['currency_symbol'];
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
				if($bar=='_eshop_product'){
					$x=unserialize($v[0]);
					foreach($x as $nowt=>$val){
						$array[$foo][$nowt]=$val;
					}
				}
				foreach($v as $nowt=>$val){
					$array[$foo][$bar]=$val;
				}
			}
		}
		//then sort it how we want.
		$B = new eshop_multi_sort;
		$B->aData = $array;
		$B->aSortkeys =  array($sortby);
		$B->sort();
		$grab=$B->aData;
	?>	
		<form action="" method="post" class="eshop">
		<table id="listing" class="hidealllabels" summary="product listing">
		<caption><?php _e('Product Quick reference table','eshop'); ?></caption>
		<thead>
		<tr>
		<th id="sku"><?php _e('Sku','eshop'); ?></th>
		<th id="ids"><?php _e('ID','eshop'); ?></th>
		<th id="page"><?php _e('Page','eshop'); ?></th>
		<th id="desc"><?php _e('Description','eshop'); ?></th>
		<th id="down"><abbr title="<?php _e('Downloads','eshop'); ?>"><?php _e('DL','eshop'); ?></abbr></th>
		<th id="ship"><abbr title="<?php _e('Shipping Rate','eshop'); ?>"><?php _e('S/R','eshop'); ?></abbr></th>
		<th id="stk"><abbr title="<?php _e('Stock Level','eshop'); ?>"><?php _e('Stk','eshop'); ?></abbr></th>
		<th id="stkavail"><abbr title="<?php _e('Stock Available','eshop'); ?>"><?php _e('Stk avail.','eshop'); ?></abbr></th>
		<th id="purc"><abbr title="<?php _e('Number of Purchases','eshop'); ?>"><?php _e('Purc.','eshop'); ?></abbr></th>
		<th id="ftrd"><abbr title="<?php _e('Marked as Featured','eshop'); ?>"><?php _e('Feat.','eshop'); ?></abbr></th>
		<th id="opt"><?php _e('Option/Price','eshop'); ?></th>
		<th id="associmg"><?php _e('Thumbnail','eshop'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach($grab as $foo=>$grabit){
			$eshop_product=unserialize($grabit['_eshop_product']);
			$stkav=$grabit['_eshop_stock'];
			$pdownloads='no';
			if($eshop_product['products']['1']['price']!=''){
			//reset array
				$purcharray=array();
				//get page title
				$ptitle=get_post($grabit['id']);
				$getid=$grabit['id'];
				//get download file title
				$pdown='';
				//check if downloadable product
				for($i=1;$i<=$eshopoptions['options_num'];$i++){
					if($eshop_product['products'][$i]['option']!=''){
						if($eshop_product['products'][$i]['download']!=''){
							$dltable=$wpdb->prefix.'eshop_downloads';
							$fileid=$eshop_product['products'][$i]['download'];
							$filetitle=$wpdb->get_var("SELECT title FROM $dltable WHERE id='$fileid'");;
							$pdown.='<a href="admin.php?page=eshop_downloads.php&amp;edit='.$fileid.'">'.$filetitle.'</a>';
							$pdownloads='yes';
						}else{
							$pdown.='<br />';
						}
					}
				}
				if($ptitle->post_title=='')
					$posttitle=__('(no title)');
				else
					$posttitle=$ptitle->post_title;
				$calt++;
				$alt = ($calt % 2) ? '' : ' class="alt"';
				echo '<tr'.$alt.'>';
				echo '<td id="sku'.$calt.'" headers="sku">'.$eshop_product['sku'].'</td>';
				echo '<td headers="ids sku'.$calt.'">'.$getid.'</td>';
				echo '<td headers="page sku'.$calt.'"><a href="page.php?action=edit&amp;post='.$getid.'" title="id: '.$getid.'">'.$posttitle.'</a></td>';
				echo '<td headers="desc sku'.$calt.'">'.stripslashes(esc_attr($eshop_product['description'])).'</td>';
				echo '<td headers="down sku'.$calt.'">'.$pdown.'</td>';
				echo '<td headers="ship sku'.$calt.'">'.$eshop_product['shiprate'].'</td>';
				if($eshopoptions['stock_control']=='yes'){
					$stocktable=$wpdb->prefix ."eshop_stock";
					$available=$wpdb->get_var("select available from $stocktable where post_id=$getid limit 1");
					$stocktable=$wpdb->prefix ."eshop_stock";
					if($available=='')
						$available='0';
					if(is_numeric($available) && $eshopoptions['stock_control']=='yes'){
						$eavailable='<label for="stock'.$calt.'">'.__('Stock','eshop').'</label><input type="text" value="'.$available.'" id="stock'.$calt.'" name="product['.$getid.'][stkqty]" size="4" />';
						$available=$eavailable;
					}
					if($stkav=='1')
						$stkchk=' checked="checked"';
					else
						$stkchk='';
				}else{
					$available='n/a';
				}
				
				echo '<td headers="stk sku'.$calt.'">'.$available.'</td>';
				echo '<td headers="stkavail sku'.$calt.'"><label for="stkavail'.$calt.'">'.__('Stock Available','eshop').'</label><input type="checkbox" value="1" name="product['.$getid.'][stkavail]" id="stkavail'.$calt.'"'.$stkchk.' /></td>';

				$purcharray=array();
				$dltable = $wpdb->prefix ."eshop_downloads";
				for($i=1;$i<=$eshopoptions['options_num'];$i++){
					if($eshop_product['products'][$i]['option']!=''){
						if($eshop_product['products'][$i]['download']!=''){
							$fileid=$eshop_product['products'][$i]['download'];
							$purchases=$wpdb->get_var("SELECT purchases FROM $dltable WHERE id='$fileid'");
							if($purchases!='')
								$purcharray[]=$purchases;
							else
								$purcharray[]='0';
						}else{
							$purchases=$wpdb->get_var("select purchases from $stocktable where post_id=$getid limit 1");
							if($purchases!='')
								$purcharray[]=$purchases;
							else
								$purcharray[]='0';
						}
					}
					if($pdownloads=='no') break;
				}
				//Featured Product
				if($eshop_product['featured']=='Yes')
					$fchk=' checked="checked"';
				else
					$fchk='';
				$feat='<label for="stkavail'.$calt.'">'.__('Featured Product','eshop').'</label><input type="checkbox" value="1" name="product['.$getid.'][featured]" id="featured'.$calt.'"'.$fchk.' />';
				echo '<td headers="purc sku'.$calt.'">'.implode("<br />",$purcharray).'</td>';
				echo '<td headers="ftrd sku'.$calt.'">'.$feat.'</td>';

				echo '<td headers="opt sku'.$calt.'">';
				for($i=1;$i<=$numoptions;$i++){
					if($eshop_product['products'][$i]['option']!=''){
						echo stripslashes(esc_attr($eshop_product['products'][$i]['option']));
						echo ' @ '.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($eshop_product['products'][$i]['price'],2)).'<br />';
					}
				}
				echo '</td>';
				echo '<td headers="associmg sku'.$calt.'">';
				$w=get_option('thumbnail_size_w');
				$h=get_option('thumbnail_size_h');
				$imgsize='50';
				$w=round(($w*$imgsize)/100);
				$h=round(($h*$imgsize)/100);
				if (has_post_thumbnail( $getid ) ) {
					 echo '<a class="itemref" href="'.get_permalink($getid).'" title="view page">'.get_the_post_thumbnail( $getid, array($w, $h)).'</a>'."\n";
				}else{
					$eimage=eshop_files_directory();
					 echo '<a class="itemref" href="'.get_permalink($getid).'" title="view page"><img src="'.$eimage['1'].'noimage.png" height="'.$h.'" width="'.$w.'" alt="" /></a>'."\n";
				}
				echo '</td>';
				echo '</tr>'."\n";
			}
		}

		?>
		</tbody>
		</table>
		<p><input type="submit" name="eshopqp" id="submitit" class="submit button-primary" value="Update Products" /></p>
		</form>
		<?php
		//paginate
		echo '<div class="paginate">';;
			if($records!=$max){
				$eecho = $page_links;
			}
			echo sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>',
				number_format_i18n( ( $epage - 1 ) * $records + 1 ),
				number_format_i18n( min( $epage * $records, $max ) ),
				number_format_i18n( $max)
			);
			if(isset($eecho)){
				$thispage=esc_url(add_query_arg('eshopall', 'yes', $_SERVER['REQUEST_URI']));
				echo "<ul class='page-numbers'>\n\t<li>".join("</li>\n\t<li>", $eecho)."</li>\n<li>".'<a href="'.$thispage.'">View All</a>'."</li>\n</ul>\n";
			}
			echo '<br /></div>';

		//end
	}else{	
		echo '<p>'.__('There are no products available.','eshop').'</p>';
	}
	echo '</div>';
	eshop_show_credits();
}
function eshop_authors($filter=''){
	global $wpdb;
	$all_logins = $wpdb->get_results( "SELECT ID, user_login FROM $wpdb->users ORDER BY user_login ");
	$selected=' selected="selected"';
	$sel='';
	if($filter=='all') $sel=$selected;
	$echo= '<option value="all"'.$sel.'>'.__('All','eshop').'</option>'."\n";
	$sel='';
	if($filter=='') $sel=$selected;
	foreach ($all_logins as $login) {
		$user_info = get_userdata($login->ID);
		$enic='';
		if($user_info->nickname!='' && $user_info->display_name!=$user_info->nickname) $enic='['.$user_info->nickname.']';
		$thisone='';
		if($filter!='' && $filter==$login->ID) $thisone=$selected;
		$echo.='<option value="'.$login->ID.'"'.$thisone.'>'.$user_info->display_name.$enic.'</option>'."\n";
	}
	return $echo;
}
?>