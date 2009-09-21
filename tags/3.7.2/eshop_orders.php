<?php
if ('eshop_orders.php' == basename($_SERVER['SCRIPT_FILENAME']))
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

global $wpdb;

if (isset($_GET['action']) )
	$action_status = attribute_escape($_GET['action']);
else
	$_GET['action']=$action_status = 'Pending';


//admin note handling
if(isset($_POST['eshop-adnote'])){
	$dtable=$wpdb->prefix.'eshop_orders';
	if (isset($_GET['view']) && is_numeric($_GET['view'])){
		$view=$_GET['view'];
		$admin_note=$wpdb->escape($_POST['eshop-adnote']);
		$query2=$wpdb->get_results("UPDATE $dtable set admin_note='$admin_note' where id='$view'");
		echo '<div class="updated fade"><p>'.__('Admin Note changed successfully.','eshop').'</p></div>';
	}else{
		echo '<div class="error fade"><p>'.__('Error: Admin Note was not changed.','eshop').'</p></div>';
	}
}

if (!function_exists('displayorders')) {
	function displayorders($type){
		global $wpdb;
		//these should be global, but it wasn't working *sigh*
		$phpself=wp_specialchars($_SERVER['REQUEST_URI']);
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		
		if(isset($_POST['change'])){
			if($_POST['move'][0]!=''){
				foreach($_POST['move'] as $v=>$ch){
					$mark=$_POST['mark'];
					$query2=$wpdb->get_results("UPDATE $dtable set status='$mark' where checkid='$ch'");
				}
				echo '<p class="updated fade">'.__('Order status changed successfully.','eshop').'</p>';
			}else{
				echo '<p class="error">'.__('No orders were selected.','eshop').'</p>';
			}
		}
		
		//pager for when you have lots and lots of orders :)
		include_once ("pager-class.php");
		$cda=$cdd=$ctn=$cca=$cna='';
		if(isset($_GET['by'])){
			switch ($_GET['by']) {
				case'dd'://date descending
					$sortby='ORDER BY custom_field DESC';
					$cdd=' class="current"';
					break;
				case'tn'://transaction id numerically
					$sortby='ORDER BY transid ASC';
					$ctn=' class="current"';
					break;
				case'na'://name alphabetically (last name)
					$sortby='ORDER BY last_name ASC';
					$cna=' class="current"';
					break;
				case'ca'://company name alphabetically
					$sortby='ORDER BY company ASC';
					$cca=' class="current"';
					break;
				case'da'://date ascending
				default:
					$sortby='ORDER BY custom_field ASC';
					$cda=' class="current"';
			}
		}else{
			$cda=' class="current"';
			$sortby='ORDER BY custom_field ASC';
		}
		
		$range=10;
		$max = $wpdb->get_var("SELECT COUNT(id) FROM $dtable WHERE id > 0 AND status='$type'");
		if($max>0){
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
			//
			$myrowres=$wpdb->get_results("Select * From $dtable where status='$type' $sortby LIMIT $thispage");
			$calt=0;
			$apge=wp_specialchars($_SERVER['PHP_SELF']).'?page='.$_GET['page'].'&amp;action='.$_GET['action'];
			echo '<ul id="eshopsubmenu">';
			echo '<li><span>'.__('Sort Orders by &raquo;','eshop').'</span></li>';
			echo '<li><a href="'.$apge.'&amp;by=da"'.$cda.'>'.__('Date Ascending','eshop').'</a></li>';
			echo '<li><a href="'.$apge.'&amp;by=dd"'.$cdd.'>'.__('Date Descending','eshop').'</a></li>';
			echo '<li><a href="'.$apge.'&amp;by=tn"'.$ctn.'>'.__('ID Number','eshop').'</a></li>';
			echo '<li><a href="'.$apge.'&amp;by=ca"'.$cca.'>'.__('Company','eshop').'</a></li>';
			echo '<li><a href="'.$apge.'&amp;by=na"'.$cna.'>'.__('Customer','eshop').'</a></li>';

			echo '</ul>';
		
			echo "<form id=\"orderstatus\" action=\"".$phpself."\" method=\"post\">";
			echo '<div class="orderlist tablecontainer">';
			echo '<table id="listing" class="hidealllabels" summary="order listing">
			<caption class="offset">'.__('eshop Order Listing','eshop').'</caption>
			<thead>
			<tr>
			<th id="line" title="Line number">#</th>
			<th id="date">'.__('Date/Time','eshop').'</th>
			<th id="customer">'.__('Customer','eshop').'</th>
			<th id="items">'.__('Items','eshop').'</th>
			<th id="price">'.__('Price','eshop').'</th>
			<th id="downloads">'.__('Contains Downloads','eshop').'</th>
			<th id="transid">'.__('Transaction ID','eshop').'</th>
			<th id="bulk" title="Bulk operations">'.__('Bulk','eshop').'</th></tr></thead><tbody>'."\n";
			$move=array();
			foreach($myrowres as $myrow){
				//total + products
				$c++;//count for the  number of results.
				$checkid=$myrow->checkid;
				$itemrowres=$wpdb->get_results("Select * From $itable where checkid='$checkid'");
				$total=0;
				$x=-1;
				foreach($itemrowres as $itemrow){
					$value=$itemrow->item_qty * $itemrow->item_amt;
					$total=$total+$value;
					$x++;
				}
				//
				$status=$type;
				if($x>0){
					$custom=$myrow->custom_field;
					$cyear=substr($custom, 0, 4);
					$cmonth=substr($custom, 4, 2);
					$cday=substr($custom, 6, 2);
					$chours=substr($custom, 8, 2);
					$cminutes=substr($custom, 10, 2);
					$thisdate=$cyear."-".$cmonth."-".$cday.' '.__('at','eshop').' '.$chours.':'.$cminutes;
					$calt++;
					$alt = ($calt % 2) ? '' : ' class="alt"';
					if($myrow->company!=''){
						$company=__(' of ','eshop').$myrow->company;
					}else{
						$company='';
					}
					$currsymbol=get_option('eshop_currency_symbol');
					echo '<tr'.$alt.'>
					<td headers="line" id="numb'.$c.'">'.$c.'</td>
					<td headers="date numb'.$c.'">'.$thisdate.'</td>
					<td headers="customer numb'.$c.'"><a href="'.$phpself.'&amp;view='.$myrow->id.'" title="'.__('View complete order details','eshop').'">'.$myrow->first_name.' '.$myrow->last_name.$company.'</a></td>
					<td headers="items numb'.$c.'">'.$x.'</td>
					<td headers="price numb'.$c.'" class="right">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($total, 2)).'</td>
					<td headers="downloads numb'.$c.'" class="right">'.$myrow->downloads.'</td>
					<td headers="transid numb'.$c.'">'.$myrow->transid.'</td>'.
					'<td headers="bulk numb'.$c.'"><label for="move'.$c.'">Move #'.$c.'</label><input type="checkbox" value="'.$checkid.'" name="move[]" id="move'.$c.'" />'
					."</td></tr>\n";
				}

			}
			echo "</tbody></table></div>\n";
			//paginate
				echo '<div class="paginate"><p class="checkers">'.__('Bulk:','eshop').'<a href="javascript:checkedAll(\'orderstatus\', true)" title="'.__('Select all of the checkboxes above','eshop').'">'.__('Check','eshop').'</a><span class="offset"> | </span><a href="javascript:checkedAll(\'orderstatus\', false)" title="'.__('Deselect all of the checkboxes above','eshop').'">'.__('Uncheck','eshop').'</a></p><p>';
					if($pager->_pages > 1){
						echo $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span> &#8212; Displaying results <span>{FROM}</span> to <span>{TO}</span> of <span>{TOTAL}</span>','eshop')). '<br />';
					}else{
						echo $pager->get_title(__('Viewing page <span>{CURRENT}</span> of <span>{MAX}</span>','eshop')). '<br />';
					}
					echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ',__('&laquo; First Page','eshop'),__('Last Page &raquo;','eshop')).'';
					//echo $pager->get_range('<a href="{LINK_HREF}">{LINK_LINK}</a>',' &raquo; ').'<br />';
					if($pager->_pages >= 2){
						echo ' &raquo; <a class="pag-view" href="'.wp_specialchars($_SERVER['REQUEST_URI']).'&amp;_p=1&amp;action='.$_GET['action'].'&amp;viewall=yes" title="'.$status.' '.__('orders','eshop').'">'.__('View All &raquo','eshop').';</a>';
					}
					echo '</p></div>';
			//end
			
			
			//moved order status box
				?>
				<fieldset id="changestat"><legend><?php _e('Change Orders Status','eshop'); ?></legend>
				<p class="submit eshop"><label for="mark"><?php _e('Mark orders as:','eshop'); ?></label>
				<select name="mark" id="mark">
				<option value="Sent"><?php _e('Shipped','eshop'); ?></option>
				<option value="Completed"><?php _e('Active','eshop'); ?></option>
				<option value="Pending"><?php _e('Pending','eshop'); ?></option>
				<option value="Waiting"><?php _e('Awaiting Payment','eshop'); ?></option>
				<option value="Failed"><?php _e('Failed','eshop'); ?></option>
				<option value="Deleted"><?php _e('Deleted','eshop'); ?></option>
				</select>
				<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>" />
				<input type="hidden" name="change" value="yes" />
				<input type="submit" id="submit1" value="<?php _e('Change','eshop'); ?>" /></p>
				</fieldset></form>
				<?php
	//order status box code end
			
			
			
			if($type=='Deleted'){
			?>
				<div id="eshopformleft"><form id="ordersdelete" action="<?php echo wp_specialchars($_SERVER['REQUEST_URI']); ?>" method="post">
				<fieldset><legend><?php _e('Complete Order Deletion','eshop'); ?></legend>
				<p class="submit eshop"><label for="dhours"><?php _e('Orders that are ','eshop'); ?>
				<select name="dhours" id="dhours">
				<option value="72" selected="selected">72</option>
				<option value="36">48</option>
				<option value="24">24</option>
				<option value="16">16</option>
				<option value="8">8</option>
				<option value="4">4</option>
				<option value="0">0</option>
				</select> <?php _e('hours old','eshop'); ?></label>
				<input type="hidden" name="dall" value="yes" />
				<input type="submit" id="submit2" value="Delete" /></p>
				</fieldset></form></div>
			<?php
			}
			
			
		}else{
			if($type=='Completed'){$type=__('Active','eshop');}
			if($type=='Sent'){$type=__('Shipped','eshop');}
			if($type=='Waiting'){$type=__('Awaiting Payment','eshop');}
			if($type=='Pending'){$status=__('Pending','eshop');}
			if($type=='Deleted'){$status=__('Deleted','eshop');}
			if($type=='Failed'){$status=__('Failed','eshop');}
			echo "<p class=\"notice\">".__('There are no','eshop')." <span>".$type."</span> ".__('orders','eshop').".</p>";
		}
	}
}
if (!function_exists('displaystats')) {
	function displaystats(){
		global $wpdb;
		include 'eshop_statistics.php';
		//these should be global, but it wasn't working *sigh*
		$phpself=wp_specialchars($_SERVER['REQUEST_URI']);
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		$metatable=$wpdb->prefix.'postmeta';
		$poststable=$wpdb->prefix.'posts';
		$count = $wpdb->get_var("SELECT COUNT(meta.post_id) FROM $metatable as meta, $poststable as posts where meta.meta_key='_Option 1' AND meta.meta_value!='' AND posts.ID = meta.post_id	AND (posts.post_type != 'revision' && posts.post_type != 'inherit')");
		$stocked = $wpdb->get_results("
		SELECT DISTINCT meta.post_id
		FROM $metatable as meta, $poststable as posts
		WHERE meta.meta_key = '_Option 1'
		AND meta.meta_value != ''
		AND posts.ID = meta.post_id
		AND (posts.post_type != 'revision' && posts.post_type != 'inherit')
		ORDER BY meta.post_id");

		$countprod=$countfeat=0;
		foreach($stocked as $stock){
			$fcount = $wpdb->get_var("SELECT meta_value FROM $metatable where post_id='$stock->post_id' and meta_key='_Featured Product'");
			if($fcount=='Yes'){
				$countfeat++;
			}
			$pcount = $wpdb->get_var("SELECT meta_value FROM $metatable where post_id='$stock->post_id' and meta_key='_Stock Available'");
			if($pcount=='Yes'){
				$countprod++;
			}
		}
		$stktable = $wpdb->prefix ."eshop_stock";
		$stkpurc=0;
		$stkpurc=$wpdb->get_var("Select SUM(purchases) From $stktable");
		if($stkpurc<1){
			$stkpurc=0;
		}

		?>
		<div class="eshop-stats-box odd"><h3><?php _e('Product stats','eshop'); ?></h3>
		<ul class="eshop-stats">
		<li><strong><?php echo $count; ?></strong> <?php _e('Products.','eshop'); ?></li>
		<li><strong><?php echo $countprod; ?></strong> <?php _e('Products in stock.','eshop'); ?></li>
		<li><strong><?php echo $countfeat; ?></strong> <?php _e('Featured products.','eshop'); ?></li>
		<li><strong><?php echo $stkpurc; ?></strong> <?php _e('Purchases','eshop'); ?>.</li>
		</ul>
		<?php eshop_small_stats('stock'); ?>
		</div>
		<?php
		//work out totals for quick stats
		$dltable = $wpdb->prefix ."eshop_downloads";
		$total=$purchased=0;
		$total=$wpdb->get_var("Select SUM(downloads) From $dltable");
		$purchased=$wpdb->get_var("Select SUM(purchases) From $dltable");
		if($total<1){
			$total=0;
		}
		if($purchased<1){
			$purchased=0;
		}
		?>
		<div class="eshop-stats-box">
		<h3><?php _e('Product Download Stats','eshop'); ?></h3>
		<ul class="eshop-stats">
		<li><strong><?php echo $total; ?></strong> <?php _e('Total Downloads','eshop'); ?></li>
		<li><strong><?php echo $purchased; ?></strong> <?php _e('Total Purchases','eshop'); ?></li>
		</ul>
		<?php eshop_small_stats('dloads'); ?>
		</div>
		
		<hr class="eshopclear" />
		<?php
		$array=array('Pending','Waiting','Completed','Sent','Failed','Deleted');
		echo '<div class="eshop-stats-box odd"><h3>'.__('Order Stats','eshop').'</h3><ul class="eshop-stats">';
		foreach($array as $k=>$type){
			$max = $wpdb->get_var("SELECT COUNT(id) FROM $dtable WHERE id > 0 AND status='$type'");
			switch($type){
				case 'Pending':
					$type=__('Pending','eshop');
					break;
				case 'Failed':
					$type=__('Failed','eshop');
					break;
				case 'Deleted':
					$type=__('Deleted','eshop');
					break;
				case 'Completed':
					$type=__('Active','eshop');
					break;
				case 'Sent':
					$type=__('Shipped','eshop');
					break;
				case 'Waiting':
					$type=__('Awaiting Payment','eshop');
					break;
			}			
			echo '<li><strong>'.$max.'</strong> '.$type.' '.eshop_plural($max,__('order','eshop'),__('orders','eshop')).'</li>';
		}
		echo '</ul></div>';
		if(is_array(get_option('eshop_method'))){
			$paytype=get_option('eshop_method');
			?>
			<div class="eshop-stats-box">
			<h3><?php _e('Merchant Gateways Usage','eshop'); ?></h3>
			<p><?php _e('Includes all orders.','eshop'); ?></p>
				<ul class="eshop-stats">
				<?php
				foreach($paytype as $gatetype){
					$mcount=$wpdb->get_var("SELECT COUNT(id) FROM $dtable WHERE paidvia='$gatetype'");
					?>
					<li><strong><?php echo $mcount; ?></strong> <?php echo ucwords($gatetype).' '.eshop_plural($mcount,__('order','eshop'),__('orders','eshop')); ?></li>
					<?php
				}
				?>
			</ul>
			</div>
		<?php
		}
		echo '<hr class="eshopclear" />';
		$disctable=$wpdb->prefix.'eshop_discount_codes';
		$row=$wpdb->get_row("SELECT COUNT(id) as ids, SUM(IF(live='yes',1,0)) as live, SUM(USED) as total FROM $disctable WHERE id>0");
		if($row->ids>0){
		?>
		<div class="eshop-stats-box odd">
			<h3><?php _e('Discount Codes','eshop'); ?></h3>
			<ul class="eshop-stats">
			<li><strong><?php echo $row->ids; ?></strong> <?php _e('Total Available','eshop'); ?></li>
			<li><strong><?php echo $row->live; ?></strong> <?php _e('Active','eshop'); ?></li>
			<li><strong><?php echo $row->total; ?></strong> <?php _e('Total codes used','eshop'); ?></li>
			</ul>
		</div>
		<?php
		}
		if(current_user_can('eShop_admin')){
			?>
			<hr class="eshopclear" />
			<div class="eshop-stats-box">
			<h3><?php _e('Download Data','eshop'); ?></h3>
			<ul>
			<?php
				$dlpage=$phpself.'?page='.$_GET['page'].'&amp;eshopdl=yes';
			?>
			<li><a href="<?php echo $dlpage; ?>"><?php _e('Download all transactions','eshop'); ?></a></li>
			<li><a href="<?php echo $dlpage; ?>&amp;os=mac"><?php _e('Mac users Download all transactions','eshop'); ?></a></li>
			</ul>
			</div>
			<?php
		}
	}
}
if (!function_exists('deleteorder')) {
	function deleteorder($delid){
		global $wpdb;
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		$checkid=$wpdb->get_var("Select checkid From $dtable where id='$delid' && status='Deleted'");
		$delquery2=$wpdb->get_results("DELETE FROM $itable WHERE checkid='$checkid'");
		$delquery=$wpdb->get_results("DELETE FROM $dtable WHERE checkid='$checkid'");
		echo '<p class="success">'.__('That order has now been deleted from the system.','eshop').'</p>';
	}
}

//sub sub menu - may change to a little form:
$phpself='?page='.$_GET['page'];
$dtable=$wpdb->prefix.'eshop_orders';
$itable=$wpdb->prefix.'eshop_order_items';
$stable=$wpdb->prefix.'eshop_states';
$ctable=$wpdb->prefix.'eshop_countries';

/*
##########
##########
*/
if(isset($_GET['viewemail']) || isset($_POST['thisemail'])){
	include 'eshop_email.php';
}else{



//paypal tries upto 4 days after a transaction.
$delit=4;
$wpdb->query("UPDATE $dtable set status='Deleted' where status='Pending' && edited < DATE_SUB(NOW(), INTERVAL $delit DAY)");

//try and move all orders that only have downloadable products
$moveit=$wpdb->get_results("Select checkid From $dtable where downloads='yes'");

foreach($moveit as $mrow){
	$pdownload=$numbrows=0;
	$result=$wpdb->get_results("Select down_id From $itable where checkid='$mrow->checkid' AND item_id!='postage'");
	foreach($result as $crow){
		//check if downloadable product
		if($crow->down_id != '0')
			$pdownload++;

		$numbrows++;
	}
	if($pdownload==$numbrows){
		//in theory this will only activate if the order only contains downloads
		$wpdb->query("UPDATE $dtable set status='Sent' where status='Completed' && checkid='$mrow->checkid'");
	}
}



echo '<div class="wrap">';
if(isset($_GET['view'])){
	$view=$_GET['view'];
	$status=$wpdb->get_var("Select status From $dtable where id='$view'");
	if($status=='Completed'){$status=__('Active Order','eshop');}
	if($status=='Pending'){$status=__('Pending Order','eshop');}
	if($status=='Waiting'){$status=__('Orders Awaiting Payment','eshop');}
	if($status=='Sent'){$status=__('Shipped Order','eshop');}
	if($status=='Deleted'){$status=__('Deleted Order','eshop');}
	if($status=='Failed'){$status=__('Failed Order','eshop');}
	$state=$status;
}elseif(isset($_GET['action'])){
	switch ($_GET['action']) {
		case 'Dispatch':
			$state=__('Active Orders','eshop');
			break;
		case 'Pending':
			$state=__('Pending Orders','eshop');
			break;
		case 'Failed':
			$state=__('Failed Orders','eshop');
			break;
		case 'Waiting':
			$state=__('Orders Awaiting Payment','eshop');
			break;
		case 'Sent':
			$state=__('Shipped Orders','eshop');
			break;
		case 'Deleted':
			$state=__('Deleted Orders','eshop');
			break;
		case 'Stats':
			$state=__('eShop Order Stats','eshop');
			break;
		default:
			break;
	}
}else{
	die ('<h2 class="error">'.__('Error','eshop').'</h2>');
}

echo '<h2>'.$state."</h2>\n";
echo '<ul class="subsubsub">';
if(current_user_can('eShop_admin'))
	$stati=array('Stats'=>__('Stats','eshop'),'Pending' => __('Pending','eshop'),'Waiting'=>__('Awaiting Payment','eshop'),'Dispatch'=>__('Active','eshop'),'Sent'=>__('Shipped','eshop'),'Failed'=>__('Failed','eshop'),'Deleted'=>__('Deleted','eshop'));
else
	$stati=array('Stats'=>__('Stats','eshop'));

foreach ( $stati as $status => $label ) {
	$class = '';
	if ( $status == $action_status )
		$class = ' class="current"';

	$status_links[] = "<li><a href=\"?page=eshop_orders.php&amp;action=$status\"$class>" . $label . '</a>';
}
echo implode(' | </li>', $status_links) . '</li>';
echo '</ul><br class="clear" />';

if(isset($_GET['delid']) && !isset($_GET['view'])){
	deleteorder($_GET['delid']);
	unset($_GET['view']);
	$_GET['action']=$_POST['action'];
	$_GET['action']='Deleted';
}
if(isset($_POST['dall'])){
	$dhours=$_POST['dhours'];
	if($_POST['dhours']=='0' ||$_POST['dhours']=='4'||$_POST['dhours']=='8'||$_POST['dhours']=='16'||$_POST['dhours']=='24'||$_POST['dhours']=='48'||$_POST['dhours']=='72'){
		$delay=$wpdb->escape($_POST['dhours']);
		$replace=$delay.__(' hours','eshop');
		if($delay==24){$replace=__('1 day','eshop');}
		$dtable=$wpdb->prefix.'eshop_orders';
		$itable=$wpdb->prefix.'eshop_order_items';
		$myrows=$wpdb->get_results("Select checkid From $dtable where status='Deleted' && edited < DATE_SUB(NOW(), INTERVAL $delay HOUR)");
		foreach($myrows as $myrow){
			$checkid=$myrow->checkid;
			$delquery2=$wpdb->query("DELETE FROM $itable WHERE checkid='$checkid'");
			$query2=$wpdb->query("DELETE FROM $dtable WHERE status='Deleted' && checkid='$checkid' && edited < DATE_SUB(NOW(), INTERVAL $delay HOUR)");
		}
		echo '<p class="success">'.__('Deleted orders older than','eshop').' '.$replace.' '.__('have now been <strong>completely</strong> deleted.','eshop').'</p>';
	}else{
		echo '<p class="error">'.__('There was an error, and nothing has been deleted.','eshop').'</p>';
	}
}
if(isset($_POST['mark']) && !isset($_POST['change'])){
	$mark=$_POST['mark'];
	$checkid=$_POST['checkid'];
	$query2=$wpdb->get_results("UPDATE $dtable set status='$mark' where checkid='$checkid'");
	echo '<p class="success">'.__('Order status changed successfully.','eshop').'</p>';
}
if (isset($_GET['view']) && is_numeric($_GET['view'])){
	$view=$_GET['view'];
	if (isset($_GET['adddown']) && is_numeric($_GET['adddown'])){
		$dordtable=$wpdb->prefix.'eshop_download_orders';
		$adddown=$_GET['adddown'];
		$wpdb->query("UPDATE $dordtable SET downloads=downloads+1 where id='$adddown' limit 1");
		echo '<p class="success">'.__('Download allowance increased.','eshop').'</p>';
	}
	
	$dquery=$wpdb->get_results("Select * From $dtable where id='$view'");
	foreach($dquery as $drow){
		$status=$drow->status;
		$checkid=$drow->checkid;
		$custom=$drow->custom_field;
		$transid=$drow->transid;
		$admin_note=htmlspecialchars(stripslashes($drow->admin_note));
		$paidvia=$drow->paidvia;
	}
	if($status=='Completed'){$status=__('Active','eshop');}
	if($status=='Pending'){$status=__('Pending','eshop');}
	if($status=='Sent'){$status=__('Shipped','eshop');}
	//moved order status box
	echo "<div id=\"eshopformfloat\"><form id=\"orderstatus\" action=\"".$phpself."\" method=\"post\">";
	?>
	<fieldset><legend><?php _e('Change Order Status','eshop'); ?></legend>
	<p class="submit eshop"><label for="mark"><?php _e('Mark order as:','eshop'); ?></label>
	<select name="mark" id="mark">
	<option value="Sent"><?php _e('Shipped','eshop'); ?></option>
	<option value="Completed"><?php _e('Active','eshop'); ?></option>
	<option value="Waiting"><?php _e('Awaiting Payment','eshop'); ?></option>
	<option value="Pending"><?php _e('Pending','eshop'); ?></option>
	<option value="Failed"><?php _e('Failed','eshop'); ?></option>
	<option value="Deleted"><?php _e('Deleted','eshop'); ?></option>
	</select>
	<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>" />
	<input type="hidden" name="checkid" value="<?php echo $checkid; ?>" />
	<input type="submit" id="submit3" value="<?php _e('Change','eshop'); ?>" /></p>
	</fieldset></form></div>
	<?php
	//order status box code end
	echo '<h3 class="status"><span>'.$status.'</span> '.__('Order Details','eshop').'</h3>';
	$result=$wpdb->get_results("Select * From $itable where checkid='$checkid' ORDER BY id ASC");
	$total=0;
	$calt=0;
	$currsymbol=get_option('eshop_currency_symbol');
	?>
	<div class="orders tablecontainer">
	<p><?php _e('Transaction ID:','eshop'); ?> <strong><?php echo $transid; ?></strong></p>
	<?php
	if($admin_note!=''){
		echo '<div id="eshop_admin_note" class="noprint"><h4>'.__('Admin Note:','eshop')."</h4>\n";
		echo nl2br($admin_note).'</div>'."\n";
		echo '<p class="eshop_edit_note noprint"><a href="#eshop-anote">'.__('Edit admin note','eshop').'</a></p>';
	}else{
		echo '<p class="eshop_edit_note noprint"><a href="#eshop-anote">'.__('Add admin note','eshop').'</a></p>';
	}
	?>
	
	<table id="listing" summary="<?php _e('Table for order details','eshop'); ?>">
	<caption><?php _e('Order Details','eshop'); ?></caption>
	<thead>
	<tr>
	<th id="opname"><?php _e('Product Name','eshop'); ?></th>
	<th id="oitem"><?php _e('Item or Unit Data','eshop'); ?></th>
	<th id="odown"><?php _e('Download?','eshop'); ?></th>
	<th id="oqty"><?php _e('Quantity','eshop'); ?></th>
	<th id="oprice"><?php _e('Price','eshop'); ?></th></tr>
	</thead>
	<tbody>
	<?php
	foreach($result as $myrow){
		$value=$myrow->item_qty * $myrow->item_amt;
		$total=$total+$value;
		$itemid=$myrow->item_id;
		//check if downloadable product
		$dordtable=$wpdb->prefix.'eshop_download_orders';
		$downstable=$wpdb->prefix.'eshop_downloads';
		if($myrow->down_id!='0'){
			//item is a download
			$downloadable='<span class="downprod">'.__('Yes - remaining:','eshop');
			$dltable=$wpdb->prefix.'eshop_downloads';
			$dlinfo= $wpdb->get_row("SELECT d.downloads, d.id FROM $dordtable as d, $downstable as dl WHERE d.checkid='$myrow->checkid' AND dl.id='$myrow->down_id' AND d.files=dl.files");
			$downloadable .=' '.$dlinfo->downloads.'<a href="'.$phpself.'&amp;view='.$view.'&amp;adddown='.$dlinfo->id.'" title="'.__('Increase download allowance by 1','eshop').'">'.__('Increase','eshop').'</a></span>';
		}else{
			$downloadable='<span class="offlineprod">'.__('No','eshop').'</span>';
		}
	
		// add in a check if postage here as well as a link to the product
		if($itemid=='postage'){
			$showit=__('Shipping','eshop');
			$downloadable=$itemid='&nbsp;';
		}else{
			$showit=$myrow->optname;
		}
		$calt++;
		$alt = ($calt % 2) ? '' : ' class="alt"';
		echo '<tr'.$alt.'>
		<td id="onum'.$calt.'" headers="opname">'.$showit.'</td>
		<td headers="opname onum'.$calt.'">'.$itemid.'</td>
		<td headers="opname onum'.$calt.'">'.$downloadable.'</td>
		<td headers="opname onum'.$calt.'">'.$myrow->item_qty.'</td>
		<td headers="opname onum'.$calt.'" class="right">'.sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($value, 2))."</td></tr>\n";
	}
	if($transid==__('Processing&#8230;','eshop'))
		echo "<tr><td colspan=\"4\" class=\"totalr\">".__('Total &raquo;','eshop')." </td><td class=\"total\">".sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($total, 2))."</td></tr>\n";
	else
		echo "<tr><td colspan=\"4\" class=\"totalr\">".sprintf(_c('Total paid via %1$s &raquo;','eshop'),ucfirst($paidvia))." </td><td class=\"total\">".sprintf( _c('%1$s%2$s|1-currency symbol 2-amount','eshop'), $currsymbol, number_format($total, 2))."</td></tr>\n";
	echo "</tbody></table>\n";
			
	$cyear=substr($custom, 0, 4);
	$cmonth=substr($custom, 4, 2);
	$cday=substr($custom, 6, 2);
	$chours=substr($custom, 8, 2);
	$cminutes=substr($custom, 10, 2);
	$thisdate=$cyear."-".$cmonth."-".$cday.__(' at ','eshop').$chours.':'.$cminutes;
	echo "<p>".__('Order placed on','eshop')." <strong>".$thisdate."</strong>.</p>\n";
	
	echo "</div>\n";
	if($drow->reference!=''){
		echo '<p><strong>'.__('Customer reference:','eshop').'</strong> '.$drow->reference.'</p>';
	}
	echo "<div class=\"orderaddress\"><h4>".__('Invoice','eshop')."</h4>";
	foreach($dquery as $drow){

		echo '<p><strong>'.__("Name: ",'eshop').'</strong>'.$drow->first_name." ".$drow->last_name."<br />\n";
		if($drow->company!='') echo '<strong>'.__("Company: ",'eshop').'</strong>'.$drow->company."<br />\n";
		echo '<strong>'.__('Email:','eshop').'</strong>'." <a href=\"".$phpself."&amp;viewemail=".$view."\" title=\"".__('Send a form email','eshop')."\">".$drow->email."</a><br />\n";
		if('no' == get_option('eshop_downloads_only')){
			echo '<strong>'.__("Phone: ",'eshop').'</strong>'.$drow->phone."</p>\n";

			echo '<h5>'.__('Address','eshop').'</h5>';
			$address=$drow->address1;
			if($drow->address2!='') $address.= ', '.$drow->address2;

			echo '<p><address>'.$drow->first_name." ".$drow->last_name."<br />\n";
			if($drow->company!='') echo __("Company: ",'eshop').$drow->company."<br />\n";
			echo $address."<br />\n";
			echo $drow->city."<br />\n";
			$qcode=$wpdb->escape($drow->state);
			$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
			if($qstate!=''){
				echo $qstate."<br />";
				$statezone = $wpdb->get_var("SELECT zone FROM $stable WHERE code='$qcode' limit 1");
			}else{
				echo $drow->state."<br />";
			}
			echo $drow->zip."<br />\n";

			$qcode=$wpdb->escape($drow->country);
			$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qcode' limit 1");
			$countryzone = $wpdb->get_var("SELECT zone FROM $ctable WHERE code='$qcode' limit 1");
			echo $qcountry."</address></p>";
			if(get_option('eshop_shipping_zone')=='country'){
				$qzone=$countryzone;
			}else{
				$qzone=$statezone;
				if($statezone=='') $qzone=get_option('eshop_unknown_state');
			}
			echo '<p>'.__('Shipping Zone: ','eshop')."<strong>".$qzone."</strong></p></div>\n";
			if($drow->ship_name!='' && $drow->ship_address!='' && $drow->ship_city!='' && $drow->ship_postcode!=''){
				echo "<div class=\"shippingaddress\"><h4>".__('Shipping','eshop')."</h4>";
				echo '<p><strong>'.__("Name: ",'eshop').'</strong>'.$drow->ship_name."<br />\n";
				if($drow->ship_company!='') echo '<strong>'.__("Company: ",'eshop').'</strong>'.$drow->ship_company."<br />\n";
				echo '<strong>'.__("Phone: ",'eshop').'</strong>'.$drow->ship_phone."</p>\n";
				echo '<h5>'.__('Address','eshop').'</h5>';
				echo '<p><address>'.$drow->ship_name.'<br />'."\n";
				if($drow->ship_company!='') echo $drow->ship_company."<br />\n";
				echo $drow->ship_address."<br />\n";
				echo $drow->ship_city."<br />\n";
				$qcode=$wpdb->escape($drow->ship_state);
				$qstate = $wpdb->get_var("SELECT stateName FROM $stable WHERE code='$qcode' limit 1");
				if($qstate!=''){
					$statezone = $wpdb->get_var("SELECT zone FROM $stable WHERE code='$qcode' limit 1");
					echo $qstate."<br />";
				}else{
					echo $drow->ship_state."<br />";
				}
				echo $drow->ship_postcode."<br />\n";
				$qcode=$wpdb->escape($drow->ship_country);
				$qcountry = $wpdb->get_var("SELECT country FROM $ctable WHERE code='$qcode' limit 1");
				$countryzone = $wpdb->get_var("SELECT zone FROM $ctable WHERE code='$qcode' limit 1");
				echo $qcountry."</address></p>";
				if(get_option('eshop_shipping_zone')=='country'){
					$qzone=$countryzone;
				}else{
					$qzone=$statezone;
					if($statezone=='') $qzone=get_option('eshop_unknown_state');
				}
				echo '<p>'. __('Shipping Zone:','eshop')." <strong>".$qzone."</strong></p></div>\n";
			}
		}else{
			echo '</p></div>';
		}
		echo '<hr class="eshopclear" />';
		if($drow->thememo!=''){
			echo '<div class="paypalmemo"><h4>'.__('Customer paypal memo:','eshop').'</h4><p>'.nl2br($drow->thememo).'</p></div>';
		}
		
		if($drow->comments!=''){
			echo '<div class="eshopmemo"><h4>'.__('Customer order comments:','eshop').'</h4><p>'.nl2br($drow->comments).'</p></div>';
		}
		if($drow->thememo!='' || $drow->comments!=''){
			echo '<hr class="eshopclear" />';
		}
	}
	//admin note form goes here
	?>
	<form method='post' action="" id="eshop-anote"><fieldset><legend><label for="eshop-adnote"><?php _e('Admin Note','eshop'); ?></label></legend>
	<textarea rows="5" cols="80" id="eshop-adnote" name="eshop-adnote"><?php echo $admin_note; ?></textarea>
	<p class="submit eshop"><input type="submit" class="button-primary" value="<?php _e('Update Admin Note','eshop'); ?>" name="submit" /></p>
	</fieldset>
	</form>
	<?php	
	if($status=='Deleted'){$delete="<p class=\"delete noprint\"><a href=\"".$phpself."&amp;delid=".$view."\">".__('Completely delete this order?','eshop')."</a><br />".__('<small><strong>Warning:</strong> this order will be completely deleted and cannot be recovered at a later date.</small>','eshop')."</p>";}else{$delete='';};
	echo $delete;
}else{

	if (empty($_GET['action'])) $_GET['action'] = 'Dispatch';  
	switch ($_GET['action']) {
		case 'Dispatch':
			displayorders('Completed');
			break;
		case 'Pending':
			displayorders('Pending');
			break;
		case 'Failed':
			displayorders('Failed');
			break;
		case 'Waiting':
			displayorders('Waiting');
			break;
		case 'Sent':
			displayorders('Sent');
			break;
		case 'Deleted':
			displayorders('Deleted');
			break;
		case 'stats':
		default:
			displaystats('Stats');
			break;
	}
}

echo '<br class="clearbr" />&nbsp;</div>';

}
eshop_show_credits();
?>