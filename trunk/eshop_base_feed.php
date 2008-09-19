<?php
//header('Content-Type: text/xml'); 
if (empty($wp)) {
	require_once('../../../wp-config.php');
}

if(isset($_GET['d'])){
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=gbase2.xml");
header("Pragma: no-cache");
header("Expires: 0");		
}else{
header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true);
}
function eshoprssfilter($z){
	$zz=strip_tags($z);
	$zz=convert_chars($zz);
	return trim($zz);
}

echo "<?xml version='1.0' encoding='utf-8' ?>\n";

?>
<rss version="2.0" 
xmlns:g="http://base.google.com/ns/1.0" 
xmlns:c="http://base.google.com/cns/1.0">
<channel>
<copyright><?php bloginfo_rss('name'); ?></copyright>
<pubDate><?php echo date("r"); ?></pubDate>
<title><?php bloginfo_rss('name'); wp_title_rss();; ?></title>
<link><?php bloginfo_rss('url'); ?></link>
<description><?php echo 'Product feed for ';bloginfo_rss('name'); ?></description>
<generator>eShop: Accessible e-commerce plugin for Wordpress</generator>
<?php
global $wpdb;

$metatable=$wpdb->prefix.'postmeta';
$myrowres=$wpdb->get_results("Select DISTINCT post_id From $metatable where meta_key='Option 1' AND meta_value!='' order by post_id");
$x=0;
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

//set up defaults
$basecondition=get_option('eshop_base_condition');
$basebrand=get_option('eshop_base_brand');
$baseptype=get_option('eshop_base_ptype');

$baseexpiry=get_option('eshop_base_expiry');
$basedate=date('Y-m-d',mktime(0, 0, 0, date("m") , date("d")+$baseexpiry, date("Y")));

$basepayment=get_option('eshop_base_payment');
$basepayments = explode(",", $basepayment);
foreach($array as $foo=>$grabit){
	//for the title
	$rid=$grabit['id'];

	$pdata=get_post($rid);
		$post=$pdata;

		setup_postdata($post);

	if(strlen($pdata->post_title) > 79){
		$basetitle=substr($pdata->post_title, 0, 76).'...';
	}else{
		$basetitle=$pdata->post_title;
	}
	//automatic data
	$baselink=get_permalink($rid);
	$baseid=$rid;
	$baseprice=$grabit['Price 1'];
	$attachment = $wpdb->get_var("SELECT ID FROM $wpdb->posts where post_parent= ".$rid." and post_type = 'attachment' limit 1");
	$baseimg=wp_get_attachment_url($attachment);
	$basedescription=get_the_excerpt();
	 $basecondition=$basebrand=$baseptype=$basedate=$baseimg=$baseean=$baseisbn=$basempn=$baseqty='';
	//individual set product data
	$basetable=$wpdb->prefix ."eshop_base_products";
	$basedata=$wpdb->get_row("SELECT * FROM $basetable WHERE post_id = $rid");
	//if this exists overwrite defaults
	if(is_array($basedata) && $basedata->post_id!=''){
			$basecondition=$basedata->thecondition;
			$basebrand=$basedata->brand;
			$baseptype=$basedata->ptype;
			$basedate=$basedata->expiry;
			$baseimg=$basedata->img;
			$baseean=$basedata->ean;
			$baseisbn=$basedata->isbn;
			$basempn=$basedata->mpn;
			//need checks for qty
			$baseqty=$basedata->qty;
	}
	?>
	<item>
		<link><?php echo eshoprssfilter($baselink); ?></link>
		<title><?php  echo eshoprssfilter($basetitle); ?></title>	
		<description><?php echo $basedescription; ?></description>
		<g:id><?php echo eshoprssfilter($baseid); ?></g:id>
		<g:quantity><?php echo eshoprssfilter($baseqty); ?></g:quantity>
		<g:price><?php echo eshoprssfilter($baseprice); ?></g:price>
		<g:price_type>starting</g:price_type>
<?php foreach($basepayments as $baseapayment){ 
	if($basepayment!=''){?>
		<g:payment_accepted><?php echo eshoprssfilter($baseapayment); ?></g:payment_accepted>
<?php }} ?>
<?php if($basecondition!=''){?>
		<g:condition><?php echo eshoprssfilter($basecondition); ?></g:condition>
<?php } if($baseean!=''){?>
		<g:ean><?php echo eshoprssfilter($baseean); ?></g:ean>
<?php } if($basedate!=''){?>
		<g:expiration_date><?php echo $basedate; ?></g:expiration_date>
<?php } if($basebrand!=''){?>
		<g:brand><?php echo eshoprssfilter($basebrand); ?></g:brand>
<?php } if($baseimg!=''){?>
		<g:image_link><?php echo eshoprssfilter($baseimg); ?></g:image_link>
<?php } if($baseisbn!=''){?>
		<g:isbn><?php echo eshoprssfilter($baseisbn); ?></g:isbn>
<?php } if($basempn!=''){?>
		<g:mpn><?php echo eshoprssfilter($basempn); ?></g:mpn>
<?php } if($baseptype!=''){?>
		<g:product_type><?php echo eshoprssfilter($baseptype); ?></g:product_type>
<?php } ?>
	</item>
<?php
}
?>
</channel>
</rss>