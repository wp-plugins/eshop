<?php
if ('eshop-install.php' == basename($_SERVER['SCRIPT_FILENAME']))
     die ('<h2>Direct File Access Prohibited</h2>');

if (file_exists(ABSPATH . 'wp-admin/includes/upgrade.php')) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
} else {
    require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
}

/***
* default options(mainly for settings) go here
*/
add_option('eshop_style', 'yes');
add_option('eshop_method','paypal');
add_option('eshop_records','10');
add_option('eshop_options_num','3');
add_option('eshop_downloads_num','3');
add_option('eshop_random_num','5');
add_option('eshop_pagelist_num','5');
add_option('eshop_cart_nostock','Out of Stock');
add_option('eshop_status', 'testing');
add_option('eshop_currency_symbol','&pound;');
add_option('eshop_currency','GBP');
add_option('eshop_location','GB');
add_option('eshop_sudo_cat','1');
add_option('eshop_shipping', '1');
add_option('eshop_shipping_zone', 'country');
add_option('eshop_shipping_state', 'GB');

add_option('eshop_show_zones','no');
add_option('eshop_credits', 'yes');
add_option('eshop_stock_control','no');
add_option('eshop_show_stock','no');
add_option('eshop_first_time', 'yes');
add_option('eshop_downloads_only', 'no');
add_option('eshop_search_img', 'no');
add_option('eshop_fold_menu', 'yes');

$table = $wpdb->prefix . "eshop_states";
//new for 2.13.0
if ( get_option('eshop_version')=='' || get_option('eshop_version') < '2.12.9' )
	$wpdb->query("DROP TABLE $table");
	
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
   			id INT NOT NULL AUTO_INCREMENT,
		  	code char(4) NOT NULL default '',
			stateName varchar(30) NOT NULL default '',
			zone tinyint(1) NOT NULL default '0',
			list char(2) NOT NULL default '',
			  PRIMARY KEY  (id),
			KEY zone (zone)
			);";
	error_log("creating table $table");
	dbDelta($sql);
	$wpdb->query("INSERT INTO ".$table." (code,stateName,zone,list) VALUES  
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
	zone1 float(6,2) NOT NULL default '0.00',
	zone2 float(6,2) NOT NULL default '0.00',
	zone3 float(6,2) NOT NULL default '0.00',
	zone4 float(6,2) NOT NULL default '0.00',
	zone5 float(6,2) NOT NULL default '0.00',
	  PRIMARY KEY  (id)
	);";
	error_log("creating table $table");
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
	item_id varchar(30) NOT NULL default '0',
	item_qty int(11) NOT NULL default '0',
	item_amt float(8,2) NOT NULL default '0.00',
	optname varchar(255) NOT NULL default '',
	post_id int(11) NOT NULL default '0',
	down_id int(11) NOT NULL default '0',
	  PRIMARY KEY  (id),
	KEY custom_field (checkid)
	);";
	error_log("creating table $table");
	dbDelta($sql);
}

$table = $wpdb->prefix . "eshop_orders";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	id int(11) NOT NULL auto_increment,
	checkid varchar(255) NOT NULL default '',
	status set('Sent','Completed','Pending','Failed','Deleted') NOT NULL default 'Pending',
	first_name varchar(50) NOT NULL default '',
	last_name varchar(50) NOT NULL default '',
	company varchar(255) NOT NULL default '',
	email varchar(100) NOT NULL default '',
	phone varchar(30) NOT NULL default '',
	address1 varchar(255) NOT NULL default '',
	address2 varchar(255) NOT NULL default '',
	city varchar(100) NOT NULL default '',
	state varchar(3) NOT NULL default '',
	zip varchar(20) NOT NULL default '',
	country varchar(3) NOT NULL default '',
	reference varchar(255) NOT NULL default '',
	ship_name varchar(100) NOT NULL default '',
	ship_company varchar(255) NOT NULL default '',
	ship_phone varchar(30) NOT NULL default '',
	ship_address varchar(255) NOT NULL default '',
	ship_city varchar(100) NOT NULL default '',
	ship_state varchar(3) NOT NULL default '',
	ship_postcode varchar(20) NOT NULL default '',
	ship_country varchar(3) NOT NULL default '',
	custom_field varchar(15) NOT NULL default '',
	transid varchar(255) NOT NULL default '',
	comments text NOT NULL,
	thememo text NOT NULL,
	edited datetime NOT NULL default '0000-00-00 00:00:00',
	downloads set('yes','no') NOT NULL default 'no',
	admin_note TEXT NOT NULL,
	  PRIMARY KEY  (id),
	KEY custom_field (checkid),
	KEY status (status)
	);";
	error_log("creating table $table");
	dbDelta($sql);
}

$table = $wpdb->prefix ."eshop_stock";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	  id int(11) NOT NULL auto_increment,
	  post_id int(11) NOT NULL default '0',
	  available int(11) NOT NULL default '0',
	  purchases int(11) NOT NULL default '0',
	    PRIMARY KEY  (id),
	  KEY post_id (post_id,available,purchases)
	);";
	error_log("creating table $table");
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
			);";
	error_log("creating table $table");
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
		);";
	error_log("creating table $table");
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
		);";
	error_log("creating table $table");
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
	  img text NOT NULL,
	  brand varchar(255) NOT NULL default '',
	  ptype varchar(255) NOT NULL default '',
	  thecondition varchar(255) NOT NULL default '',
	  expiry date NOT NULL default '0000-00-00',
	  ean varchar(255) NOT NULL default '',
	  isbn varchar(255) NOT NULL default '',
	  mpn varchar(255) NOT NULL default '',
	  qty int(5) NOT NULL default '0',
	  xtra text NOT NULL,
	  PRIMARY KEY  (post_id)
	);";
	error_log("creating table $table");
	dbDelta($sql);
}
$table = $wpdb->prefix ."eshop_discount_codes";
if ($wpdb->get_var("show tables like '$table'") != $table) {
	$sql = "CREATE TABLE ".$table." (
	  id int(11) NOT NULL auto_increment,
	  dtype tinyint(1) NOT NULL default '0',
	  disccode varchar(255) NOT NULL default '',
	  percent mediumint(5) NOT NULL default '0',
	  remain varchar(11) NOT NULL default '',
	  used int(11) NOT NULL default '0',
	  enddate date NOT NULL default '0000-00-00',
	  live char(3) NOT NULL default 'no',
	  PRIMARY KEY  (id),
	  UNIQUE KEY disccode (disccode)
	);";
	error_log("creating table $table");
	dbDelta($sql);
}


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


/*update all post meta to new post meta */
$eshop_old_postmeta=array('Sku','Product Description','Product Download','Shipping Rate','Featured Product','Stock Available','Stock Quantity');
//add on options and prices into the mix

$numoptions=get_option('eshop_options_num');
if(!is_numeric($numoptions)) $numoptions='3';
for($i=1;$i<=$numoptions;$i++){
	$eshop_old_postmeta[]='Option '.$i;
	$eshop_old_postmeta[]='Price '.$i;
}
//go through every page and post
$args = array(
	'post_type' => 'any',
	'numberposts' => -1,
	); 
//add in transfer from prod download to _download here
$allposts = get_posts($args);
foreach( $allposts as $postinfo) {
	foreach($eshop_old_postmeta as $field){
		$eshopvalue=get_post_meta($postinfo->ID, $field,true);
		if($eshopvalue!=''){
			add_post_meta( $postinfo->ID, '_'.$field, $eshopvalue);
	    	delete_post_meta($postinfo->ID, $field);
	 	}
	 }
	if(get_post_meta($postinfo->ID, '_Product Download',true)!=''){
		$eshopvalue=get_post_meta($postinfo->ID, '_Product Download',true);
		add_post_meta( $postinfo->ID, '_Download 1', $eshopvalue);
	}
	delete_post_meta($postinfo->ID, '_Product Download');
}



/* post meta end */

/* page insertion */
/*
 * This part creates the pages and automatically puts their URLs into the options page.
 * As you can probably see, it is very easily extendable, just pop in your page and the deafult content in the array and you are good to go.
 */

$post_date =date("Y-m-d H:i:s");
$post_date_gmt =gmdate("Y-m-d H:i:s");

$num=0;
$pages[$num]['name'] = 'shopping-cart';
$pages[$num]['title'] = 'Shopping Cart';
$pages[$num]['tag'] = '[eshop_show_cart';
$pages[$num]['option'] = 'eshop_cart';

$num++;
$pages[$num]['name'] = 'checkout';
$pages[$num]['title'] = 'Checkout';
$pages[$num]['tag'] = '[eshop_show_checkout';
$pages[$num]['option'] = 'eshop_checkout';

$num++;
$pages[$num]['name'] = 'thank-you';
$pages[$num]['title'] = 'Thank You for your order';
$pages[$num]['tag'] = '[eshop_show_success';
$pages[$num]['option'] = 'eshop_cart_success';

$num++;
$pages[$num]['name'] = 'cancelled-order';
$pages[$num]['title'] = 'Cancelled Order';
$pages[$num]['tag'] = '[eshop_show_cancel';
$pages[$num]['option'] = 'eshop_cart_cancel';

$num++;
$pages[$num]['name'] = 'shipping-rates';
$pages[$num]['title'] = 'Shipping Rates';
$pages[$num]['tag'] = '[eshop_show_shipping';
$pages[$num]['option'] = 'eshop_cart_shipping';
$pages[$num]['top'] = 'yes';

$num++;
$pages[$num]['name'] = 'downloads';
$pages[$num]['title'] = 'Downloads';
$pages[$num]['tag'] = '[eshop_show_downloads';
$pages[$num]['option'] = 'eshop_show_downloads';
$pages[$num]['top'] = 'yes';

$newpages = false;
$i = 0;
$post_parent = 0;
$qtable=$wpdb->prefix . "posts";
foreach($pages as $page) {
	$check_page = $wpdb->get_row("SELECT * FROM $qtable WHERE post_type='page' && `post_content` LIKE '%".$page['tag']."%' LIMIT 1",ARRAY_A);
	if($check_page == null){
		if($i == 0){
			$post_parent = 0;
		}else{
			$post_parent = $first_id;
		}
		if($page['top']=='yes'){
			$post_parent=0;
		}
		
		global $wp_version;
		if($wp_version >= 2.1){
			$pagepublish='publish';
		}else{
			$pagepublish='static';
		}
		$page['tag']=$page['tag'].']';
		$sql ="INSERT INTO $qtable
		(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_type)
		VALUES
		('1', '$post_date', '$post_date_gmt', '".$page['tag']."', '', '".$page['title']."', '', '".$pagepublish."', 'closed', 'closed', '', '".$page['name']."', '', '', '$post_date', '$post_date_gmt', '$post_parent', '0', 'page')";
		
		$wpdb->query($sql);
		$post_id = $wpdb->insert_id;
		if($i == 0){
			$first_id = $post_id;
		}
		$wpdb->query("UPDATE $qtable SET guid = '" . get_permalink($post_id) . "' WHERE ID = '$post_id'");
		update_option($page['option'],  $post_id);
		$newpages = true;
		$i++;
	}else{
	  update_option($page['option'],  $check_page['ID']);
	}
}
if($newpages == true){
	wp_cache_delete('all_page_ids', 'pages');
	$wp_rewrite->flush_rules();
}
/* version number store - add/update */
update_option('eshop_version', ESHOP_VERSION);
?>