<?php
if ('eshop-install.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');

if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
} else {
    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
}

if(eshop_create_dirs()==false){
	deactivate_plugins('eshop/eshop.php'); //Deactivate ourself
	wp_die(__('ERROR! eShop requires that the wp-content directory is writable before the plugin can be activated.','eshop')); 
}
global $eshopoptions, $charset_collate;
/** create capability */
if (!function_exists('eshop_caps')) {
    /**
     * installation routine to set up tables
     */
    function eshop_caps() {
        global $wpdb, $user_level, $wp_rewrite, $wp_version;
			$role = get_role('administrator');
			if ($role !== NULL){
				$role->add_cap('eShop');
				$role->add_cap('eShop_admin');
			}
			$role = get_role('editor');
			if ($role !== NULL)
				$role->add_cap('eShop');
    }
}
eshop_caps();

/***
* default options(mainly for settings) go here
*/
function eshop_option_setup() {
	$new_options = array(
		'addtocart_image'=>'',
		'base_brand'=>'',
		'base_condition'=>'',
		'base_expiry'=>'',
		'base_payment'=>'',
		'base_ptype'=>'',
		'business'=>'',
		'cart'=>'',
		'cart_cancel'=>'',
		'cart_nostock'=>'Out of Stock',
		'cart_shipping'=>'',
		'cart_success'=>'',
		'checkout'=>'',
		'credits'=> 'no',
		'cron_email'=>'',
		'currency_symbol'=>'&pound;',
		'currency'=>'GBP',
		'discount_shipping'=>'',
		'discount_spend1'=>'',
		'discount_value1'=>'',
		'discount_spend2'=>'',
		'discount_value2'=>'',
		'discount_spend3'=>'',
		'discount_value3'=>'',
		'downloads_hideall'=>'no',
		'downloads_num'=>'3',
		'downloads_only'=> 'no',
		'first_time'=> 'yes',
		'fold_menu'=> 'no',
		'from_email'=>'',
		'hide_addinfo'=>'yes',
		'hide_cartco'=>'',
		'hide_shipping'=>'',
		'image_in_cart'=>'',
		'location'=>'GB',
		'method'=>array('paypal'),
		'options_num'=>'3',
		'paypal_noemail'=>'',
		'records'=>'10',
		'search_img'=> 'no',
		'set_cacheability'=>'no',
		'shipping_state'=> 'GB',
		'shipping_zone'=> 'country',
		'shipping'=> '1',
		'shop_page'=>'',
		'show_allstates'=>'',
		'show_downloads'=>'',
		'show_forms'=>'',
		'show_zones'=>'no',
		'status'=> 'testing',
		'stock_control'=>'no',
		'style' => 'yes',
		'sysemails' =>'',
		'tandc'=>'',
		'tandc_id'=>'',
		'tandc_use'=>'',
		'unknown_state'=> '5',
		'version'=>'',
		'xtra_help' =>'',
		'xtra_privacy' =>''
	);

	// if old options exist, update to new system only need pre 5.0
	if ( isset($eshopoptions['version']) && $eshopoptions['version'] < '5.2.0' ){
		foreach( $new_options as $key => $value ) {
			$existing = get_option( 'eshop_' . $key );
			if($existing!='')
				$new_options[$key] = $existing;
			delete_option( 'eshop_' . $key );
		}
	}
	add_option( 'eshop_plugin_settings', $new_options );
}
eshop_option_setup();
//do same for post meta

function eshop_postmeta_setup() {
	$eshopoptions = get_option('eshop_plugin_settings');
	$new_options = array(
	'_Sku'=>'sku',
	'_Product Description'=>'description',
	'_Shipping Rate'=>'shiprate',
	'_Featured Product'=>'featured',
	'_Stock Quantity'=>'qty',
	'_eshoposets'=>'optset');	
	//add on options and prices into the mix
	$numoptions=$eshopoptions['options_num'];
	if(!is_numeric($numoptions)) $numoptions='3';
	for($i=1;$i<=$numoptions;$i++){
		$new_options['_Option '.$i]=array($i=>'option');
		$new_options['_Price '.$i]=array($i=>'price');
		$new_options['_Download '.$i]=array($i=>'download');
	}
	//go through every page and post
	$args = array(
		'post_type' => 'any',
		'numberposts' => -1,
		); 
	if ( isset($eshopoptions['version']) && $eshopoptions['version'] < '5.2.0' ){
		//add in transfer from prod download to _download here
		$allposts = get_posts($args);
		foreach( $allposts as $postinfo) {
			//if(get_post_meta($postinfo->ID, '_eshop_product')!='')
			//	break;
			foreach($new_options as $oldfield=>$newfield){
				$eshopvalue=get_post_meta($postinfo->ID, $oldfield,true);
				if(is_array($newfield)){
					foreach($newfield as $k=>$v){
						$thenew_options['products'][$k][$v]=$eshopvalue;
					}
				}else{
					$thenew_options[$newfield]=$eshopvalue;
				}
				if($oldfield=='_Featured Product' && $eshopvalue=='Yes'){
					add_post_meta( $postinfo->ID, '_eshop_featured', 'Yes');
				}
			}
			if($thenew_options['sku']!='' && $thenew_options['description']!='' && $thenew_options['products']['1']['option']!='' && $thenew_options['products']['1']['price']!=''){
				add_post_meta( $postinfo->ID, '_eshop_product', $thenew_options);
			}
			$stock=get_post_meta($postinfo->ID, '_Stock Available',true);
			if(trim($stock)=='Yes'){
				add_post_meta( $postinfo->ID, '_eshop_stock', '1');
			}
		}
		//just make sure they are all gone
		foreach($new_options as $oldfield=>$newfield){
			delete_post_meta_by_key($oldfield);
		}
		delete_post_meta_by_key('_Stock Available');
		delete_post_meta_by_key('_eshop_prod_img');
	}
	
	/* post meta end */
}
eshop_postmeta_setup();

//update post meta if stock control is on only
function eshop_updatestockcontrol(){
	global $wpdb,$eshopoptions;
	$dib='_eshop_product';
	$querystr = $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '$dib'" );
 	$metaResults = $wpdb->get_results($querystr);
	$stocktable=$wpdb->prefix ."eshop_stock";
	 if ($metaResults){
		//Loop through each result post to display appropriate contents
		foreach ($metaResults as $post){
			$eprod=get_post_meta($post->post_id, '_eshop_product', true);
			$stktableqty=$wpdb->get_row("SELECT available,purchases FROM $stocktable where post_id=$post->post_id");
			$available=$stktableqty->available;
			$purc=$stktableqty->purchases;
			if(isset($available) && is_numeric($available)) $newqty=$available;	
			else $newqty=0;
			unset($eprod['qty']);
			$numoptions=$eshopoptions['options_num'];
			if(!is_numeric($numoptions)) $numoptions='3';
			//update the first, add the rest
			$sql = "DELETE FROM $stocktable WHERE post_id = $post->post_id limit 1";
			$wpdb->query($wpdb->prepare($sql));
			$eprod['products'][1]['stkqty']=$newqty;
			for($i=1;$i<=$numoptions;$i++){
				$sql = "INSERT INTO $stocktable (post_id,option_id,available,purchases) VALUES ($post->post_id,$i,$newqty,$purc)";
				$wpdb->query($wpdb->prepare($sql));
				$eprod['products'][$i]['stkqty']=$newqty;
			}
			update_post_meta( $post->post_id, '_eshop_product', $eprod);		
		}
	}
}

$eshopoptions = get_option('eshop_plugin_settings');
$table = $wpdb->prefix . "eshop_states";
	
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
   			id INT NOT NULL AUTO_INCREMENT,
		  	code char(4) NOT NULL default '',
			stateName varchar(30) NOT NULL default '',
			zone tinyint(1) NOT NULL default '0',
			list char(2) NOT NULL default '',
			  PRIMARY KEY  (id),
			KEY zone (zone)
			) $charset_collate;";
	
	dbDelta($sql);
	$wpdb->query("INSERT INTO ".$table." (code,stateName,zone,list) VALUES  
	('AK', 'Alaska', 5,'US'),
	('HI', 'Hawaii', 5,'US'),
	('AL', 'Alabama', 2,'US'),
	('AZ', 'Arizona', 4,'US'),
	('AR', 'Arkansas', 3,'US'),
	('CA', 'California', 5,'US'),
	('CO', 'Colorado', 4,'US'),
	('CT', 'Connecticut', 1,'US'),
	('DE', 'Delaware', 2,'US'),
	('DC', 'District Of Columbia', 2,'US'),
	('FL', 'Florida', 2,'US'),
	('GA', 'Georgia', 2,'US'),
	('ID', 'Idaho', 4,'US'),
	('IL', 'Illinois', 3,'US'),
	('IN', 'Indiana', 2,'US'),
	('IA', 'Iowa', 3,'US'),
	('KS', 'Kansas', 3,'US'),
	('KY', 'Kentucky', 2,'US'),
	('LA', 'Louisiana', 3,'US'),
	('ME', 'Maine', 1,'US'),
	('MD', 'Maryland', 2,'US'),
	('MA', 'Massachusetts', 1,'US'),
	('MI', 'Michigan', 2,'US'),
	('MN', 'Minnesota', 3,'US'),
	('MS', 'Mississippi', 3,'US'),
	('MO', 'Missouri', 3,'US'),
	('MT', 'Montana', 4,'US'),
	('NE', 'Nebraska', 3,'US'),
	('NV', 'Nevada', 5,'US'),
	('NH', 'New Hampshire', 1,'US'),
	('NJ', 'New Jersey', 2,'US'),
	('NM', 'New Mexico', 4,'US'),
	('NY', 'New York', 2,'US'),
	('NC', 'North Carolina', 2,'US'),
	('ND', 'North Dakota', 3,'US'),
	('OH', 'Ohio', 2,'US'),
	('OK', 'Oklahoma', 3,'US'),
	('OR', 'Oregon', 5,'US'),
	('PA', 'Pennsylvania', 2,'US'),
	('RI', 'Rhode Island', 1,'US'),
	('SC', 'South Carolina', 2,'US'),
	('SD', 'South Dakota', 3,'US'),
	('TN', 'Tennessee', 3,'US'),
	('TX', 'Texas', 3,'US'),
	('UT', 'Utah', 4,'US'),
	('VT', 'Vermont', 1,'US'),
	('VA', 'Virginia', 2,'US'),
	('WA', 'Washington', 5,'US'),
	('WV', 'West Virginia', 2,'US'),
	('WI', 'Wisconsin', 3,'US'),
	('WY', 'Wyoming', 4,'US'),
	('AB', 'Alberta', 3,'CA'),
	('BC', 'British Columbia', 3,'CA'),
	('MB', 'Manitoba', 3,'CA'),
	('NB', 'New Brunswick', 3,'CA'),
	('NL', 'Newfoundland & Labrador', 3,'CA'),
	('NT', 'Northwest Territories', 3,'CA'),
	('NU', 'Nunavut', 3,'CA'),
	('NS', 'Nova Scotia', 3,'CA'),
	('ON', 'Ontario', 3,'CA'),
	('PE', 'Prince Edward Island', 3,'CA'),
	('QC', 'Quebec', 3,'CA'),
	('SK', 'Saskatchewan', 3,'CA'),
	('YU', 'Yukon', 3,'CA'),
	('Lond','London', 1,'GB'),
	('Beds','Bedfordshire', 1,'GB'),
	('Buck','Buckinghamshire', 1,'GB'),
	('Camb','Cambridgeshire', 1,'GB'),
	('Ches','Cheshire', 1,'GB'),
	('Corn','Cornwall and Isles of Scilly', 1,'GB'),
	('Cumb','Cumbria', 1,'GB'),
	('Derb','Derbyshire', 1,'GB'),
	('Dev','Devon', 1,'GB'),
	('Dors','Dorset', 1,'GB'),
	('Durh','Durham', 1,'GB'),
	('ESus','East Sussex', 1,'GB'),
	('Esse','Essex', 1,'GB'),
	('Glos','Gloucestershire', 1,'GB'),
	('GL','Greater London', 1,'GB'),
	('GM','Greater Manchester', 1,'GB'),
	('Hamp','Hampshire', 1,'GB'),
	('Hert','Hertfordshire', 1,'GB'),
	('Kent','Kent', 1,'GB'),
	('Lanc','Lancashire', 1,'GB'),
	('Leic','Leicestershire', 1,'GB'),
	('Linc','Lincolnshire', 1,'GB'),
	('Mers','Merseyside', 1,'GB'),
	('Norf','Norfolk', 1,'GB'),
	('NYor','North Yorkshire', 1,'GB'),
	('Nort','Northamptonshire', 1,'GB'),
	('Norh','Northumberland', 1,'GB'),
	('Nott','Nottinghamshire', 1,'GB'),
	('Ox','Oxfordshire', 1,'GB'),
	('Shrp','Shropshire', 1,'GB'),
	('Som','Somerset', 1,'GB'),
	('SYor','South Yorkshire', 1,'GB'),
	('Staf','Staffordshire', 1,'GB'),
	('Suff','Suffolk', 1,'GB'),
	('Surr','Surrey', 1,'GB'),
	('Tyne','Tyne and Wear', 1,'GB'),
	('Warw','Warwickshire', 1,'GB'),
	('WMid','West Midlands', 1,'GB'),
	('WSus','West Sussex', 1,'GB'),
	('WYor','West Yorkshire', 1,'GB'),
	('Wilt','Wiltshire', 1,'GB'),
	('Worc','Worcestershire', 1,'GB'),
	('Flin','Flintshire', 1,'GB'),
	('Glam','Glamorgan', 1,'GB'),
	('Meri','Merionethshire', 1,'GB'),
	('Mon','Monmouthshire', 1,'GB'),
	('Mont','Montgomeryshire', 1,'GB'),
	('Pemb','Pembrokeshire', 1,'GB'),
	('Radn','Radnorshire', 1,'GB'),
	('Angl','Anglesey', 1,'GB'),
	('Brec','Breconshire', 1,'GB'),
	('Caer','Caernarvonshire', 1,'GB'),
	('Card','Cardiganshire', 1,'GB'),
	('Carm','Carmarthenshire', 1,'GB'),
	('Denb','Denbighshire', 1,'GB'),
	('Kirk','Kirkcudbrightshire', 1,'GB'),
	('Lana','Lanarkshire', 1,'GB'),
	('Midl','Midlothian', 1,'GB'),
	('Mora','Moray', 1,'GB'),
	('Nair','Nairnshire', 1,'GB'),
	('Orkn','Orkney', 1,'GB'),
	('Peeb','Peebleshire', 1,'GB'),
	('Pert','Perthshire', 1,'GB'),
	('Renf','Renfrewshire', 1,'GB'),
	('Ross','Ross &amp; Cromarty', 1,'GB'),
	('Roxb','Roxburghshire', 1,'GB'),
	('Selk','Selkirkshire', 1,'GB'),
	('Shet','Shetland', 1,'GB'),
	('Stir','Stirlingshire', 1,'GB'),
	('Suth','Sutherland', 1,'GB'),
	('WLot','West Lothian', 1,'GB'),
	('Wigt','Wigtownshire', 1,'GB'),
	('Aber','Aberdeenshire', 1,'GB'),
	('Angu','Angus', 1,'GB'),
	('Argy','Argyll', 1,'GB'),
	('Ayrs','Ayrshire', 1,'GB'),
	('Banf','Banffshire', 1,'GB'),
	('Berw','Berwickshire', 1,'GB'),
	('Bute','Bute', 1,'GB'),
	('Cait','Caithness', 1,'GB'),
	('Clac','Clackmannanshire', 1,'GB'),
	('Dumf','Dumfriesshire', 1,'GB'),
	('Dumb','Dumbartonshire', 1,'GB'),
	('ELot','East Lothian', 1,'GB'),
	('Fife','Fife', 1,'GB'),
	('Inve','Inverness', 1,'GB'),
	('Kinc','Kincardineshire', 1,'GB'),
	('Kinr','Kinross-shire', 1,'GB')
	;");
}
$table = $wpdb->prefix . "eshop_shipping_rates";
if ($wpdb->get_var("show tables like '$table'") != $table) {
   $sql = "CREATE TABLE ".$table." (
   	id INT NOT NULL AUTO_INCREMENT,
	class char(1) NOT NULL default '',
	items SMALLINT( 2 ) NOT NULL default '0',
	zone1 float(16,2) NOT NULL default '0.00',
	zone2 float(16,2) NOT NULL default '0.00',
	zone3 float(16,2) NOT NULL default '0.00',
	zone4 float(16,2) NOT NULL default '0.00',
	zone5 float(16,2) NOT NULL default '0.00',
	weight float(16,2) NOT NULL default '0.00', 
	ship_type int(11) NOT NULL default '0',
	  PRIMARY KEY  (id)
	) $charset_collate;";
	
	dbDelta($sql);

	$wpdb->query("INSERT INTO ".$table."(class,items,zone1,zone2,zone3,zone4,zone5) VALUES 
	('A',1, 10.00, 15.00, 20.00, 25.00, 30.00),
	('B',1, 15.00, 20.00, 30.00, 40.00, 50.00),
	('C',1, 40.00, 45.00, 50.00, 50.00, 50.00),
	('D',1, 30.00, 35.00, 40.00, 40.00, 40.00),
	('E',1, 50.00, 60.00, 70.00, 80.00, 90.00),
	('A',2, 5.00, 8.00, 10.00, 15.00, 10.00),
	('B',2, 7.00, 10.00, 20.00, 20.00, 15.00),
	('C',2, 20.00, 25.00, 40.00, 25.00, 20.00),
	('D',2, 15.00, 25.00, 30.00, 20.00, 25.00),
	('E',2, 25.00, 30.00, 60.00, 40.00, 30.00);");
}

$table = $wpdb->prefix . "eshop_order_items";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	id int(11) NOT NULL auto_increment,
	checkid varchar(255) NOT NULL default '',
	item_id varchar(255) NOT NULL default '0',
	item_qty int(11) NOT NULL default '0',
	item_amt float(16,2) NOT NULL default '0.00',
	optname varchar(255) NOT NULL default '',
	optsets text NOT NULL default '',
	post_id int(11) NOT NULL default '0',
	option_id int(11) NOT NULL default '0',
	down_id int(11) NOT NULL default '0',
	weight float(16,2) NOT NULL default '0.00',
	  PRIMARY KEY  (id),
	KEY custom_field (checkid)
	) $charset_collate;";
	
	dbDelta($sql);
}

$table = $wpdb->prefix . "eshop_orders";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	id int(11) NOT NULL auto_increment,
	checkid varchar(255) NOT NULL default '',
	status set('Sent','Completed','Pending','Failed','Deleted','Waiting') NOT NULL default 'Pending',
	first_name varchar(50) NOT NULL default '',
	last_name varchar(50) NOT NULL default '',
	company varchar(255) NOT NULL default '',
	email varchar(100) NOT NULL default '',
	phone varchar(30) NOT NULL default '',
	address1 varchar(255) NOT NULL default '',
	address2 varchar(255) NOT NULL default '',
	city varchar(100) NOT NULL default '',
	state varchar(100) NOT NULL default '',
	zip varchar(20) NOT NULL default '',
	country varchar(3) NOT NULL default '',
	reference varchar(255) NOT NULL default '',
	ship_name varchar(100) NOT NULL default '',
	ship_company varchar(255) NOT NULL default '',
	ship_phone varchar(30) NOT NULL default '',
	ship_address varchar(255) NOT NULL default '',
	ship_city varchar(100) NOT NULL default '',
	ship_state varchar(100) NOT NULL default '',
	ship_postcode varchar(20) NOT NULL default '',
	ship_country varchar(3) NOT NULL default '',
	custom_field varchar(15) NOT NULL default '',
	transid varchar(255) NOT NULL default '',
	comments text NOT NULL default '',
	thememo text NOT NULL default '',
	edited datetime NOT NULL default '0000-00-00 00:00:00',
	downloads set('yes','no') NOT NULL default 'no',
	admin_note TEXT NOT NULL default '',
	paidvia VARCHAR(255) NOT NULL default '',
	affiliate varchar(255) NOT NULL default '',
	user_id int(11) NOT NULL,
	user_notes text NOT NULL default '',
	  PRIMARY KEY  (id),
	KEY custom_field (checkid),
	KEY status (status)
	) $charset_collate;";
	
	dbDelta($sql);
}

$table = $wpdb->prefix ."eshop_stock";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	  id int(11) NOT NULL auto_increment,
	  post_id int(11) NOT NULL default '0',
	  option_id int(11) NOT NULL default '0',
	  available int(11) NOT NULL default '0',
	  purchases int(11) NOT NULL default '0',
	    PRIMARY KEY  (id),
	  KEY post_id (post_id,available,purchases)
	) $charset_collate;";
	
	dbDelta($sql);
}

$table = $wpdb->prefix ."eshop_downloads";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	  id int(11) NOT NULL auto_increment,
	  title varchar(255) NOT NULL default '',
	  added datetime NOT NULL default '0000-00-00 00:00:00',
	  files varchar(255) NOT NULL default '',
	  downloads int(11) NOT NULL default '0',
	  purchases int(11) NOT NULL default '0',
	    PRIMARY KEY  (id)
			) $charset_collate;";
	
	dbDelta($sql);
}
$table = $wpdb->prefix ."eshop_download_orders";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	  	id int(11) NOT NULL auto_increment,
	  	checkid varchar(255) NOT NULL default '',
		title varchar(255) NOT NULL default '',
		purchased datetime NOT NULL default '0000-00-00 00:00:00',
		files varchar(255) NOT NULL default '',
		downloads smallint(1) NOT NULL default '3',
		code varchar(20) NOT NULL default '',
		email varchar(255) NOT NULL default '',
		  PRIMARY KEY  (id),
		KEY code (code,email)
		) $charset_collate;";
	
	dbDelta($sql);
}

$table = $wpdb->prefix . "eshop_countries";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
		code char(2) NOT NULL default '',
		country varchar(50) NOT NULL default '',
		zone tinyint(1) NOT NULL default '0',
		list tinyint(1) NOT NULL default '1',
		  PRIMARY KEY  (code),
		KEY zone (zone)
		) $charset_collate;";
	
	dbDelta($sql);
	$wpdb->query("INSERT INTO ".$table." (code,country,zone) VALUES  
		('AD', 'Andorra', 1),
		('AE', 'United Arab Emirates', 2),
		('AG', 'Antigua and Barbuda', 2),
		('AI', 'Anguilla', 2),
		('AL', 'Albania', 1),
		('AM', 'Armenia', 1),
		('AN', 'Netherlands Antilles', 2),
		('AO', 'Angola', 2),
		('AR', 'Argentina', 2),
		('AT', 'Austria', 1),
		('AU', 'Australia', 3),
		('AW', 'Aruba', 2),
		('AZ', 'Azerbaijan Republic', 1),
		('BA', 'Bosnia and Herzegovina', 1),
		('BB', 'Barbados', 2),
		('BE', 'Belgium', 1),
		('BF', 'Burkina Faso', 2),
		('BG', 'Bulgaria', 1),
		('BH', 'Bahrain', 2),
		('BI', 'Burundi', 2),
		('BJ', 'Benin', 2),
		('BM', 'Bermuda', 2),
		('BN', 'Brunei', 2),
		('BO', 'Bolivia', 2),
		('BR', 'Brazil', 2),
		('BS', 'Bahamas', 2),
		('BT', 'Bhutan', 2),
		('BW', 'Botswana', 2),
		('BZ', 'Belize', 2),
		('CA', 'Canada', 2),
		('CD', 'Democratic Republic of the Congo', 2),
		('CG', 'Republic of the Congo', 2),
		('CH', 'Switzerland', 1),
		('CK', 'Cook Islands', 3),
		('CL', 'Chile', 2),
		('CN', 'China', 3),
		('CO', 'Colombia', 2),
		('CR', 'Costa Rica', 2),
		('CV', 'Cape Verde', 2),
		('CY', 'Cyprus', 1),
		('CZ', 'Czech Republic', 1),
		('DE', 'Germany', 1),
		('DJ', 'Djibouti', 2),
		('DK', 'Denmark', 1),
		('DM', 'Dominica', 2),
		('DO', 'Dominican Republic', 2),
		('DZ', 'Algeria', 2),
		('EC', 'Ecuador', 2),
		('EE', 'Estonia', 1),
		('ER', 'Eritrea', 2),
		('ES', 'Spain', 1),
		('ET', 'Ethiopia', 2),
		('FI', 'Finland', 1),
		('FJ', 'Fiji', 3),
		('FK', 'Falkland Islands', 2),
		('FM', 'Federated States of Micronesia', 3),
		('FO', 'Faroe Islands', 1),
		('FR', 'France', 1),
		('GA', 'Gabon Republic', 2),
		('GB', 'United Kingdom', 1),
		('GD', 'Grenada', 2),
		('GF', 'French Guiana', 2),
		('GI', 'Gibraltar', 1),
		('GL', 'Greenland', 1),
		('GM', 'Gambia', 2),
		('GN', 'Guinea', 2),
		('GP', 'Guadeloupe', 3),
		('GR', 'Greece', 1),
		('GT', 'Guatemala', 2),
		('GW', 'Guinea Bissau', 2),
		('GY', 'Guyana', 2),
		('HK', 'Hong Kong', 2),
		('HN', 'Honduras', 2),
		('HR', 'Croatia', 1),
		('HU', 'Hungary', 1),
		('ID', 'Indonesia', 2),
		('IE', 'Ireland', 1),
		('IL', 'Israel', 2),
		('IN', 'India', 2),
		('IS', 'Iceland', 1),
		('IT', 'Italy', 1),
		('JM', 'Jamaica', 2),
		('JO', 'Jordan', 2),
		('JP', 'Japan', 3),
		('KE', 'Kenya', 2),
		('KG', 'Kyrgyzstan', 1),
		('KH', 'Cambodia', 2),
		('KI', 'Kiribati', 3),
		('KM', 'Comoros', 2),
		('KN', 'St. Kitts and Nevis', 2),
		('KR', 'South Korea', 3),
		('KW', 'Kuwait', 2),
		('KY', 'Cayman Islands', 2),
		('KZ', 'Kazakhstan', 1),
		('LA', 'Laos', 3),
		('LC', 'St. Lucia', 2),
		('LI', 'Liechtenstein', 1),
		('LK', 'Sri Lanka', 2),
		('LS', 'Lesotho', 2),
		('LT', 'Lithuania', 1),
		('LU', 'Luxembourg', 1),
		('LV', 'Latvia', 1),
		('MA', 'Morocco', 2),
		('MG', 'Madagascar', 2),
		('MH', 'Marshall Islands', 3),
		('ML', 'Mali', 2),
		('MN', 'Mongolia', 3),
		('MQ', 'Martinique', 3),
		('MR', 'Mauritania', 2),
		('MS', 'Montserrat', 2),
		('MT', 'Malta', 1),
		('MU', 'Mauritius', 2),
		('MV', 'Maldives', 2),
		('MW', 'Malawi', 2),
		('MX', 'Mexico', 2),
		('MY', 'Malaysia', 2),
		('MZ', 'Mozambique', 2),
		('NA', 'Namibia', 2),
		('NC', 'New Caledonia', 3),
		('NE', 'Niger', 2),
		('NF', 'Norfolk Island', 3),
		('NI', 'Nicaragua', 2),
		('NL', 'Netherlands', 1),
		('NO', 'Norway', 1),
		('NP', 'Nepal', 2),
		('NR', 'Nauru', 3),
		('NU', 'Niue', 3),
		('NZ', 'New Zealand', 3),
		('OM', 'Oman', 2),
		('PA', 'Panama', 2),
		('PE', 'Peru', 2),
		('PF', 'French Polynesia', 3),
		('PG', 'Papua New Guinea', 3),
		('PH', 'Philippines', 3),
		('PL', 'Poland', 1),
		('PM', 'St. Pierre and Miquelon', 2),
		('PN', 'Pitcairn Islands', 3),
		('PT', 'Portugal', 1),
		('PW', 'Palau', 3),
		('QA', 'Qatar', 2),
		('RE', 'Reunion', 2),
		('RO', 'Romania', 1),
		('RU', 'Russia', 1),
		('RW', 'Rwanda', 2),
		('SA', 'Saudi Arabia', 2),
		('SB', 'Solomon Islands', 3),
		('SC', 'Seychelles', 2),
		('SE', 'Sweden', 1),
		('SG', 'Singapore', 2),
		('SH', 'St. Helena', 2),
		('SI', 'Slovenia', 1),
		('SJ', 'Svalbard and Jan Mayen Islands', 3),
		('SK', 'Slovakia', 1),
		('SL', 'Sierra Leone', 2),
		('SM', 'San Marino', 1),
		('SN', 'Senegal', 2),
		('SO', 'Somalia', 2),
		('SR', 'Suriname', 2),
		('ST', 'Sao Tome and Principe', 2),
		('SV', 'El Salvador', 2),
		('SZ', 'Swaziland', 2),
		('TC', 'Turks and Caicos Islands', 2),
		('TD', 'Chad', 2),
		('TG', 'Togo', 2),
		('TH', 'Thailand', 2),
		('TJ', 'Tajikistan', 1),
		('TM', 'Turkmenistan', 1),
		('TN', 'Tunisia', 2),
		('TO', 'Tonga', 3),
		('TR', 'Turkey', 1),
		('TT', 'Trinidad and Tobago', 2),
		('TV', 'Tuvalu', 3),
		('TW', 'Taiwan', 3),
		('TZ', 'Tanzania', 2),
		('UA', 'Ukraine', 1),
		('UG', 'Uganda', 2),
		('US', 'United States', 2),
		('UY', 'Uruguay', 2),
		('VA', 'Vatican City State', 1),
		('VC', 'Saint Vincent and the Grenadines', 2),
		('VE', 'Venezuela', 2),
		('VG', 'British Virgin Islands', 2),
		('VN', 'Vietnam', 2),
		('VU', 'Vanuatu', 3),
		('WF', 'Wallis and Futuna Islands', 3),
		('WS', 'Samoa', 3),
		('YE', 'Yemen', 2),
		('YT', 'Mayotte', 3),
		('ZA', 'South Africa', 2),
		('ZM', 'Zambia', 2);");
}
$table = $wpdb->prefix ."eshop_base_products";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	  post_id bigint(20) NOT NULL default '0',
	  img text NOT NULL default '',
	  brand varchar(255) NOT NULL default '',
	  ptype varchar(255) NOT NULL default '',
	  thecondition varchar(255) NOT NULL default '',
	  expiry date NOT NULL default '0000-00-00',
	  ean varchar(255) NOT NULL default '',
	  isbn varchar(255) NOT NULL default '',
	  mpn varchar(255) NOT NULL default '',
	  qty int(5) NOT NULL default '0',
	  xtra text NOT NULL default '',
	  PRIMARY KEY  (post_id)
	) $charset_collate;";
	
	dbDelta($sql);
}
$table = $wpdb->prefix ."eshop_discount_codes";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	  id int(11) NOT NULL auto_increment,
	  dtype tinyint(1) NOT NULL default '0',
	  disccode varchar(255) NOT NULL default '',
	  percent float(5,2) NOT NULL default '0.00',
	  remain varchar(11) NOT NULL default '',
	  used int(11) NOT NULL default '0',
	  enddate date NOT NULL default '0000-00-00',
	  live char(3) NOT NULL default 'no',
	  PRIMARY KEY  (id),
	  UNIQUE KEY disccode (disccode)
	) $charset_collate;";
	
	dbDelta($sql);
}
//new for 4.0.0
$table = $wpdb->prefix . "eshop_option_sets";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	id int(11) NOT NULL auto_increment,
	optid int(11) NOT NULL default '0',
	name varchar(255) NOT NULL default '',
	price float(16,2) NOT NULL default '0.00',
	weight float(8,2) NOT NULL default '0.00',
	  PRIMARY KEY  (id)
	) $charset_collate;";
	
	dbDelta($sql);
}
$table = $wpdb->prefix . "eshop_option_names";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	optid int(11) NOT NULL auto_increment,
	name varchar(255) NOT NULL default '',
	type tinyint(1) NOT NULL default '0',
	`description` TEXT NOT NULL  default '',
	  PRIMARY KEY  (optid)
	) $charset_collate;";
	
	dbDelta($sql);
}


$table = $wpdb->prefix ."eshop_emails";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
		`id` INT NOT NULL AUTO_INCREMENT ,
		`emailUse` tinyint(1) NOT NULL default '0',
		`emailType` VARCHAR( 50 ) NOT NULL default '',
		`emailSubject` VARCHAR( 255 ) NOT NULL default '',
		`emailContent` TEXT NOT NULL default '',
		PRIMARY KEY ( `id` )
		) $charset_collate;";
	
	dbDelta($sql);
	//enter new defauts:
	$esubject=__('Your order from ','eshop').get_bloginfo('name');
	$emailfile='[AUTO-RESPONSE - PLEASE DO NOT REPLY]

Dear {FIRSTNAME},

Your order has been received. Thank you! 
The details of your order follow...

--------------- ORDER DETAILS ---------------

{STATUS}
{CART}


--------------- DOWNLOAD DETAILS ---------------
These are available for download via:
{DOWNLOADS}


--------------- CUSTOMER DETAILS ---------------

{NAME}
{ADDRESS}

--------------- CONTACT DETAILS ---------------

{CONTACT}

--------------- OTHER INFORMATION (if applicable) ---------------

{REFCOMM}
---

If you have questions or concerns, please contact us.
Thank you for ordering with us.';
$wpdb->query("INSERT INTO ".$table." (id,emailUse,emailType, emailSubject,emailContent) VALUES ('1','1','".__('Automatic default email','eshop')."','$esubject','$emailfile')"); 
$esubject=get_bloginfo('name').__(' Notification','eshop');
$emailfile='[SPECIAL COMMUNICATION/NOTIFICATION]

Dear {FIRSTNAME},

*** Enter a custom message here ***

Respectfully,
*** Enter your name, title, and email here ***



--------------- ORDER DETAILS ---------------

{STATUS}
{CART}

--------------- DOWNLOAD DETAILS ---------------
These are available for download via:
{DOWNLOADS}

--------------- CUSTOMER DETAILS ---------------

{NAME}
{ADDRESS}

--------------- CONTACT DETAILS ---------------

{CONTACT}

--------------- OTHER INFORMATION (if applicable) ---------------

{REFCOMM}

---

If you have questions or concerns, please contact us.
Again, thank you for ordering with us.
';
//"
	$wpdb->query("INSERT INTO ".$table." (id,emailUse,emailType,emailSubject,emailContent) VALUES ('2','1','".__('Admin Order Form email','eshop')."','$esubject','$emailfile')"); 
	//payment option emails
}
//additions
$esubject=__('Your order from ','eshop').get_bloginfo('name');

if($wpdb->get_var("select id from ".$table." where id=3 limit 1")!='3')
	$wpdb->query("INSERT INTO ".$table." (id,emailType,emailSubject) VALUES ('3','".__('Automatic Paypal email','eshop')."','$esubject')"); 
if($wpdb->get_var("select id from ".$table." where id=4 limit 1")!='4')
	$wpdb->query("INSERT INTO ".$table." (id,emailType,emailSubject) VALUES ('4','".__('Automatic Payson email','eshop')."','$esubject')");
if($wpdb->get_var("select id from ".$table." where id=5 limit 1")!='5')
	$wpdb->query("INSERT INTO ".$table." (id,emailType,emailSubject) VALUES ('5','".__('Automatic Cash email','eshop')."','$esubject')"); 

if($wpdb->get_var("select id from ".$table." where id=6 limit 1")!='6')
	$wpdb->query("INSERT INTO ".$table." (id,emailType,emailSubject) VALUES ('6','".__('Automatic ePN email','eshop')."','$esubject')"); 
if($wpdb->get_var("select id from ".$table." where id=7 limit 1")!='7')
	$wpdb->query("INSERT INTO ".$table." (id,emailType,emailSubject) VALUES ('7','".__('Automatic webtopay email','eshop')."','$esubject')"); 

if($wpdb->get_var("select id from ".$table." where id=8 limit 1")!='8')
	$wpdb->query("INSERT INTO ".$table." (id,emailType,emailSubject) VALUES ('8','".__('Automatic Authorize.net email','eshop')."','$esubject')"); 
if($wpdb->get_var("select id from ".$table." where id=9 limit 1")!='9')
	$wpdb->query("INSERT INTO ".$table." (id,emailType,emailSubject) VALUES ('9','".__('Automatic iDeal Lite email','eshop')."','$esubject')"); 

if($wpdb->get_var("select id from ".$table." where id=10 limit 1")!='10')
	$wpdb->query("INSERT INTO ".$table." (id,emailType,emailSubject) VALUES ('10','".__('Automatic ogone email','eshop')."','$esubject')"); 
if($wpdb->get_var("select id from ".$table." where id=11 limit 1")!='11')
	$wpdb->query("INSERT INTO ".$table." (id,emailType,emailSubject) VALUES ('11','".__('Automatic Bank email','eshop')."','$esubject')"); 
	
//changes for 5.6.1
if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '5.6.1' ){
	$table = $wpdb->prefix . "eshop_orders";
	$sql="ALTER TABLE $table CHANGE `comments` `comments` TEXT NULL DEFAULT '',
	CHANGE `thememo` `thememo` TEXT NULL DEFAULT '',
	CHANGE `admin_note` `admin_note` TEXT NULL DEFAULT '',
	CHANGE `user_notes` `user_notes` TEXT NULL DEFAULT ''";
	$wpdb->query($sql);
	
	$table = $wpdb->prefix . "eshop_order_items";
	$sql="ALTER TABLE $table CHANGE `optsets` `optsets` TEXT NULL DEFAULT ''";
	$wpdb->query($sql);
	
	$table = $wpdb->prefix . "eshop_base_products";
	$sql="ALTER TABLE $table CHANGE `img` `img` TEXT NULL DEFAULT '',
	CHANGE `xtra` `xtra` TEXT NULL DEFAULT ''";
	$wpdb->query($sql);
	
	$table = $wpdb->prefix . "eshop_option_names";
	$sql="ALTER TABLE $table CHANGE `description` `description` TEXT NULL DEFAULT ''";
	$wpdb->query($sql);
	
	$table = $wpdb->prefix . "eshop_emails";
	$sql="ALTER TABLE $table CHANGE `emailType` `emailType` VARCHAR( 50 ) NULL DEFAULT '',
	CHANGE `emailSubject` `emailSubject` VARCHAR( 255 ) NULL DEFAULT '',
	CHANGE `emailContent` `emailContent` TEXT NULL DEFAULT ''";
	$wpdb->query($sql);
}


//changed in 5.6.0
if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '5.5.9' ){
	$table = $wpdb->prefix ."eshop_stock";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	$add_field = TRUE;
	foreach ($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='option_id') {
			$add_field = FALSE;
		}
	}
	if ($add_field) {
		$sql="ALTER TABLE `".$table."` ADD option_id int(11) NOT NULL default '0'";
		$wpdb->query($sql);
	}
	$table = $wpdb->prefix ."eshop_order_items";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	$add_field = TRUE;
	foreach ($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='option_id') {
			$add_field = FALSE;
		}
	}
	if ($add_field) {
		$sql="ALTER TABLE `".$table."` ADD option_id int(11) NOT NULL default '0'";
		$wpdb->query($sql);
	}
}

//changed in 5.5.8
if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '5.5.82' ){
	$table = $wpdb->prefix . "eshop_discount_codes";
	$wpdb->query("ALTER TABLE ".$table." CHANGE `percent` `percent` float(5,2) NOT NULL DEFAULT '0'");
}
//added in 5.3
if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '5.3.1' ){
	$table = $wpdb->prefix . "eshop_order_items";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	$add_field = TRUE;
	foreach ($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='weight') {
			$add_field = FALSE;
		}
	}
	if ($add_field) {
		$sql="ALTER TABLE `".$table."` ADD `weight` float(16,2) NOT NULL default '0.00'";
		$wpdb->query($sql);
	}
	$table = $wpdb->prefix . "eshop_shipping_rates";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	$add_field = TRUE;
	foreach ($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='weight') {
			$add_field = FALSE;
		}
	}
	if ($add_field) {
		$sql="ALTER TABLE `".$table."` ADD `weight` float(16,2) NOT NULL default '0.00', ADD ship_type int(11) NOT NULL default '0'";
		$wpdb->query($sql);
	}
}


//added in 5.3
if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '5.3.1' ){
	$table = $wpdb->prefix . "eshop_orders";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	$add_field = TRUE;
	foreach ($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='user_id') {
			$add_field = FALSE;
		}
	}
	if ($add_field) {
		$sql="ALTER TABLE `".$table."` ADD `user_id` int(11) NOT NULL , ADD `user_notes` TEXT NOT NULL ";
		$wpdb->query($sql);
	}
}



//added in 5.1
if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '5.2.0' ){
	$table = $wpdb->prefix . "eshop_orders";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	$add_field = TRUE;
	foreach ($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='affiliate') {
			$add_field = FALSE;
		}
	}
	if ($add_field) {
		$sql="ALTER TABLE `".$table."` ADD `affiliate` varchar(255) NOT NULL default ''";
		$wpdb->query($sql);
	}
}

//added in 5.0

if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '5.0.0' ){
	$table = $wpdb->prefix . "eshop_option_names";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	$add_field = TRUE;
	foreach ($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='description') {
			$add_field = FALSE;
		}
	}
	if ($add_field) {
		$sql="ALTER TABLE `".$table."` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
		$wpdb->query($sql);
	}
}
//added in 4.2.5
if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '4.2.5' ){
	$table = $wpdb->prefix . "eshop_option_names";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	$add_field = TRUE;
	foreach ($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='description') {
			$add_field = FALSE;
		}
	}
	if ($add_field) {
		$sql="ALTER TABLE `".$table."` ADD `description` TEXT NOT NULL";
		$wpdb->query($sql);
	}
}

if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '4.1.9' ){
	$table = $wpdb->prefix . "eshop_discount_codes";
	$wpdb->query("ALTER TABLE ".$table." CHANGE `percent` `percent` float(4,2) NOT NULL DEFAULT '0'");
}

if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '3.9.0' ){
	$table = $wpdb->prefix . "eshop_order_items";
		$tablefields = $wpdb->get_results("DESCRIBE {$table}");
		$add_field = TRUE;
		foreach ($tablefields as $tablefield) {
			if(strtolower($tablefield->Field)=='optsets') {
				$add_field = FALSE;
			}
		}
		if ($add_field) {
			$sql="ALTER TABLE `".$table."` ADD `optsets` TEXT NOT NULL";
			$wpdb->query($sql);
	}
}

if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '3.5.0' ){
	$table = $wpdb->prefix . "eshop_order_items";
	$wpdb->query("ALTER TABLE ".$table." CHANGE `item_id` `item_id` VARCHAR( 255 ) NOT NULL DEFAULT''");
}
if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '3.0.2' ){
//prior to 3.1
//this portion removed due to problems.
/*
	$eshopurl=eshop_files_directory();
	$templateFile = $eshopurl['0'];
	$table = $wpdb->prefix ."eshop_emails";
	$files[]=array(0=>$templateFile.'order-recieved-email.tpl',1=>'Automatic default email',2=>__('Your order from ','eshop').get_bloginfo('name'),3=>'1');
	$files[]=array(0=>$templateFile.'customer-response-email.tpl',1=>'Admin Order Form email',2=>get_bloginfo('name').__(' Notification','eshop'),3=>'2');
	foreach($files as $foo=>$file){
		if(is_file($file[0]) && filesize($file[0]) > 0){
			$f = fopen($file[0], 'r');
			$wpdb->query("DELETE FROM $table WHERE id = $file[3] LIMIT 1");
			$emailfile = fread($f, filesize($file[0]));
			$wpdb->query("INSERT INTO ".$table." (id,emailUse,emailType, emailSubject,emailContent) VALUES ('$file[3]','1','$file[1]','$file[2]','$emailfile')"); 
			fclose($f);
			unlink ($file[0]);
		}
	}
*/
	$table = $wpdb->prefix . "eshop_orders";
	$wpdb->query("ALTER TABLE ".$table." CHANGE `status` `status` SET( 'Sent', 'Completed', 'Pending', 'Failed', 'Deleted', 'Waiting' ) DEFAULT 'Pending' NOT NULL");
}

if ( $eshopoptions['version']=='' || $eshopoptions['version'] < '2.13.9' ){
	// lumping all changes prior to 3.0.0
	/* db changes */
	$table = $wpdb->prefix ."eshop_base_products";
	$tablefields = $wpdb->get_results("DESCRIBE {$table};");
	foreach($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='condition') {
			$sql="ALTER TABLE ".$table." CHANGE `condition` thecondition VARCHAR(255) NOT NULL default ''";
			$wpdb->query($sql);
		}
	}
	/* db changes */
	$table = $wpdb->prefix . "eshop_orders";
	$tablefields = $wpdb->get_results("DESCRIBE {$table};");
	foreach($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='memo') {
			$sql="ALTER TABLE ".$table." CHANGE `memo` thememo TEXT NOT NULL";
			$wpdb->query($sql);
		}
	}

	/* db changes 2.10.1 */

	$table = $wpdb->prefix . "eshop_orders";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	$add_field = TRUE;
	foreach ($tablefields as $tablefield) {
		if(strtolower($tablefield->Field)=='admin_note') {
			$add_field = FALSE;
		}
	}
	if ($add_field) {
		$sql="ALTER TABLE `".$table."` ADD `admin_note` TEXT NOT NULL";
		$wpdb->query($sql);
	}

	/* db change 2.11.7 (2.12 release) */

	$table = $wpdb->prefix . "eshop_order_items";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	foreach ($tablefields as $tablefield) {
		 $add_field[]= $tablefield->Field;
	}
	if(!in_array('down_id',$add_field)) {
		$sql="ALTER TABLE `".$table."` ADD `down_id` int(11) NOT NULL default '0'";
		$wpdb->query($sql);
	}
	/* db changes 2.13.0 */
	/* state table recreated + */
	$table = $wpdb->prefix . "eshop_countries";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	foreach ($tablefields as $tablefield) {
		 $add_field[]= $tablefield->Field;
	}
	if(!in_array('list',$add_field)) {
		$sql="ALTER TABLE `".$table."` ADD `list` tinyint(1) NOT NULL default '1'";
		$wpdb->query($sql);
	}
	/*db change 3.0.0*/
	$table = $wpdb->prefix . "eshop_orders";
	$tablefields = $wpdb->get_results("DESCRIBE {$table}");
	foreach ($tablefields as $tablefield) {
		 $add_field[]= $tablefield->Field;
	}
	if(!in_array('paidvia',$add_field)) {
		$sql="ALTER TABLE `".$table."` ADD `paidvia` VARCHAR(255) NOT NULL default 'paypal'";
		$wpdb->query($sql);
	}

	$table = $wpdb->prefix . "eshop_orders";
	$sql="ALTER TABLE `".$table."` CHANGE `state` `state` VARCHAR(100) NOT NULL default ''";
	$sql="ALTER TABLE `".$table."` CHANGE `ship_state` `ship_state` VARCHAR(100) NOT NULL default ''";
	$wpdb->query($sql);
}



/* page insertion */
/*
 * This part creates the pages and automatically puts their URLs into the options page.
 * As you can probably see, it is very easily extendable, just pop in your page and the deafult content in the array and you are good to go.
 */

$post_date =date("Y-m-d H:i:s");
$post_date_gmt =gmdate("Y-m-d H:i:s");

$num=0;
$pages[$num]['post_name'] = 'shopping-cart';
$pages[$num]['post_title'] = 'Shopping Cart';
$pages[$num]['tag'] = '[eshop_show_cart';
$pages[$num]['option'] = 'cart';

$num++;
$pages[$num]['post_name'] = 'checkout';
$pages[$num]['post_title'] = 'Checkout';
$pages[$num]['tag'] = '[eshop_show_checkout';
$pages[$num]['option'] = 'checkout';

$num++;
$pages[$num]['post_name'] = 'thank-you';
$pages[$num]['post_title'] = 'Thank You for your order';
$pages[$num]['tag'] = '[eshop_show_success';
$pages[$num]['option'] = 'cart_success';

$num++;
$pages[$num]['post_name'] = 'cancelled-order';
$pages[$num]['post_title'] = 'Cancelled Order';
$pages[$num]['tag'] = '[eshop_show_cancel';
$pages[$num]['option'] = 'cart_cancel';

$num++;
$pages[$num]['post_name'] = 'shipping-rates';
$pages[$num]['post_title'] = 'Shipping Rates';
$pages[$num]['tag'] = '[eshop_show_shipping';
$pages[$num]['option'] = 'cart_shipping';
$pages[$num]['top'] = 'yes';

$num++;
$pages[$num]['post_name'] = 'downloads';
$pages[$num]['post_title'] = 'Downloads';
$pages[$num]['tag'] = '[eshop_show_downloads';
$pages[$num]['option'] = 'show_downloads';
$pages[$num]['top'] = 'yes';

$newpages = false;
$i = 0;
$post_parent = 0;
$qtable=$wpdb->prefix . "posts";
foreach($pages as $page) {
	$check_page = $wpdb->get_row("SELECT * FROM $qtable WHERE post_type='page' && (post_status='publish' OR post_status='draft' OR post_status='private') && `post_content` LIKE '%".$page['tag']."%' LIMIT 1",ARRAY_A);
	if($check_page == null){
		if($i == 0){
			$page['post_parent'] = 0;
		}else{
			$page['post_parent'] = $first_id;
		}
		if(isset($page['top']) && $page['top']=='yes'){
			$page['post_parent']=0;
		}
		$page['post_status']='publish';
		$page['ping_status'] ='closed';
		$page['comment_status'] ='closed'; 
		$page['post_content']=$page['tag'].']';
		$page['post_type'] = 'page';
		$post_id=wp_insert_post( $page );
		if($i == 0){
			$first_id = $post_id;
		}
		$eshopoptions[$page['option']]=$post_id;
		$newpages = true;
		$i++;
	}else{
		$eshopoptions[$page['option']]=$check_page['ID'];
	}
}
if($newpages == true){
	wp_cache_delete('all_page_ids', 'pages');
	$wp_rewrite->flush_rules();
}

/* directory creation, and file transfer */

function eshop_create_dirs(){
	$dirs=wp_upload_dir();
	$upload_dir=$dirs['basedir'];
	$eshopoptions = get_option('eshop_plugin_settings');
	if(wp_mkdir_p( $upload_dir )){
		$url_dir=$dirs['baseurl'];
		if(substr($url_dir, -1)!='/')$url_dir.='/';
		$plugin_dir=WP_PLUGIN_DIR;
		//files
		$eshop_goto=$upload_dir.'/eshop_files';
		$eshop_from=$plugin_dir.'/eshop/files';
		if(!file_exists($eshop_goto.'/eshop.css')){
			if(wp_mkdir_p( $eshop_goto )){
				if ($handle = opendir($eshop_from)) {
					/* This is the correct way to loop over the directory. */
					while (false !== ($file = readdir($handle))) {
						if($file!='' && $file!='.' && $file!='..'){
							copy($eshop_from.'/'.$file,$eshop_goto.'/'.$file);
							chmod($eshop_goto.'/'.$file,0666);
						}
					}
					closedir($handle);
				}
			}else{
				return false;
			}
		}
		if($eshopoptions['version']<='5.0.0'){
			copy($eshop_from.'/noimage.png',$eshop_goto.'/noimage.png');
			chmod($eshop_goto.'/noimage.png',0666);
			copy($eshop_from.'/eshop-onload.js',$eshop_goto.'/eshop-onload.js');
			chmod($eshop_goto.'/eshop-onload.js',0666);
		}
		//downloads
		$eshop_goto=$upload_dir.'/../eshop_downloads';
		$eshop_from=$plugin_dir.'/eshop/downloads';
		if(!file_exists($eshop_goto.'/.htaccess')){
			if(wp_mkdir_p( $eshop_goto )){
				if ($handle = opendir($eshop_from)) {
					/* This is the correct way to loop over the directory. */
					while (false !== ($file = readdir($handle))) {
						if($file!='' && $file!='.' && $file!='..'){
							copy($eshop_from.'/'.$file,$eshop_goto.'/'.$file);
							chmod($eshop_goto.'/'.$file,0666);
						}
					}
					closedir($handle);
				}
			}else{
				return false;
			}
		}
		//pay images
		$eshop_goto=$upload_dir.'/eshop_files';
		 //make sure directory exists
		if(wp_mkdir_p( $eshop_goto )){
			$files=array('paypal','payson','cash','epn','webtopay','authorizenet', 'ideallite','ogone','bank');
			foreach ($files as $file){
				if(!file_exists($eshop_goto.'/'.$file.'.png')){
					//copy the files
					copy($plugin_dir.'/eshop/'.$file.'/'.$file.'.png',$eshop_goto.'/'.$file.'.png');
					chmod($eshop_goto.'/'.$file.'.png',0666);
				}
			}
		}else{
			return false;
		}
		return true;
	}
	return false;
}
$eshoptime=mktime(0, 0, 0, date('n'), date('j'), date('Y'));
wp_schedule_event($eshoptime, 'daily', 'eshop_event');
//changes that need to be run just before version is updated:
if ( isset($eshopoptions['version']) && $eshopoptions['version'] < '5.5.9' 	&& isset($eshopoptions['stock_control']) && $eshopoptions['stock_control']=='yes'){
	eshop_updatestockcontrol();
}

/* version number store - add/update */
$eshopoptions['version']=ESHOP_VERSION;
update_option('eshop_plugin_settings', $eshopoptions);
?>