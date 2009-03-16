<?php
if ('eshop_help.php' == basename($_SERVER['SCRIPT_FILENAME']))
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

?>
<div id="eshoppage">
<div class="wrap">
<h2>Help</h2>
<p>This is some basic helpful information about the shopping cart admin.</p>
<h3>Sections</h3>
<ul>
<li><a href="#crpr">Creating Products</a></li>
<li><a href="#dept">Creating Departments</a></li>
<li><a href="#test">eShop testing</a></li>
<li><a href="#glive">Going Live with eShop</a></li>
<li><a href="#bover">Configuration</a></li>

<li><a href="#aover">eShop Admin Pages</a></li>
<li><a href="#pend">Why is an Order Still Pending?</a></li>

<li><a href="#base">eShop Base</a></li>
<li><a href="#extr">Cart Operations</a></li>
<li><a href="#conf">Conflicting plugins</a></li>
<li><a href="#comp">Compatability</a></li>
<li><a href="#del">Deactivating and Uninstalling</a></li>

</ul>
</div>
<div class="wrap">
<h2 id="crpr">Creating Products</h2>
<p>Adding a product is as easy as creating a page, just fill in the required fields within the <strong>'Product Entry'</strong> section when creating or editing a page.</p>
<p>The <strong>sku</strong> should be a unique identification reference for your product eg.abc001.</p>
<p>The <strong>Product Description</strong> is a short description of the product. This is used in the customers cart, and will appear on their invoice from paypal.</p>
<p><strong>Option x</strong> and <strong>Price x</strong>, are the individual item and price, thus allowing several options for each product eg. Small, Medium, Large. If there is only 1 option for a product, then please use the Option 1 and Price 1 input fields.</p>
<p>The <strong>Product Download</strong> selection box only appears if you have uploaded a file via eShop for download. To link to a file, select it from the dropdown list.</p>
<p>Choose your <strong>Shipping Rate</strong> carefully, <strong>F</strong> is set aside for any Free shipping (obviously downloadable products should use this).</p>
<p>The <strong>Featured Product</strong> product selection chooses whether that product can be listed as a featured product.</p>
<p>A product is unavailble for sale until <strong>Stock Available</strong> is set to <em>Yes</em>.</p>
<p><strong>Stock Quantity</strong> - sets the quantity available for this product. A quantity needs to be entered for download products, I suggest you enter a 1.</p>

</div>
<div class="wrap">
<h2 id="dept">Creating Departments</h2>
<p>There are many options, but a suggested layout would be:</p>
<ul>
<li><strong>Online Shop</strong> <em>Page Parent: Main Page</em>
<ul>
<li><strong>Department A</strong> <em>Page Parent: Online Shop</em>
<ul>
<li><strong>Product 1</strong> <em>Page Parent: Department A</em></li>
<li><strong>Product 2</strong> <em>Page Parent: Department A</em></li>
</ul>
</li>
</ul>
<ul><li><strong>Department B</strong> <em>Page Parent: Online Shop</em>
<ul><li><strong>Product 2</strong> <em>Page Parent: Department B</em></li>
<li><strong>Product 3</strong> <em>Page Parent: Department B</em></li></ul>
</li>
</ul>
</li>
</ul>
<p>To then list the products on the Online Shop and Department pages there are various options available. You will need to add to those pages one of the following codes:</p>

<ol>
<li><code>[eshop_list_subpages]</code> This displays a list of pages with products and is ideal for use on a Department page.</li>
<li><code>[eshop_list_featured]</code> This displays products that have been as set as a Featured product. Suggested use for this is on the main Online Shop page.</li>
<li><code>[eshop_list_new]</code> This displays latest products. Suggested use for this is on a separate Latest Products page.</li>
<li><code>[eshop_random_products]</code> This displays a random selection of products. This could be used on the Online Shop page, or on othe rpages within your site.</li>
</ol>

<p>However there are a few optional extras for those codes. You can amend the CSS class, which is useful if you want to tailor the style for a specific page. You can also show items in a grid view.</p>
<p>To change the class add <em>class="myclass"</em> to the shortcode eg.<code>[eshop_list_subpages class="myclass"]</code>. To change to a grid layout then add <em>panels="yes"</em> eg.<code>[eshop_list_subpages panels="yes"]</code>. You can of course use them together eg. <code>[eshop_list_subpages panels="yes" class="myclass"]</code></p>
<p>The random products has one additional option <em>list="no"</em> which can be added to show just one random product.</p>
<p>The 'list new' has two additional options <em>show="6"</em> which sets how many to show, and <em>records="6"</em> sets how many to display on a page.</p>

</div>
<div class="wrap">
<h2 id="test">eShop Testing</h2>
<p>To test the shop you need to have an account on <a href="https://developer.paypal.com/">paypal sandbox</a>. You will need to create and utilise email addresses for the 'seller' and 'buyer' within the sandbox when you test the cart. To make test purchases whilst in test mode you have to be logged into Wordpress <strong>and</strong> paypal sandbox.</p>
</div>
<div class="wrap">
<h2 id="glive">Going Live with eShop</h2>
<p>In the <strong>Settings - eShop</strong> page you then need to change the following.</p>
<ul>
<li><strong>eShop Status</strong> needs to be set to <em>Live</em>.</li>
<li><strong>Email Address</strong> &#8212; Needs to be your PayPal account email address.</li>
<li><strong>Business Location</strong> &#8212; Your 2 letter country code.</li>
<li><strong>Currency Symbol</strong> &#8212; The symbol for your currency e.g. <strong>&pound;</strong>.</li>
<li><strong>Currency Code</strong> &#8212; your 3 letter currency code.</li>

</ul>
<p>Everything should then be set up and working!</p>
</div>

<div class="wrap">
<h2 id="bover">Configuration</h2>
<p>There are numerous settings that need to be set up <strong>before</strong> you can start selling.</p>
<p>Go to Settings - <strong>eShop</strong>. This page features the main options for the plugin. Defaults have been added for your convienance, where possible.</p>

<h3>eShop Admin</h3> 
<p><strong>eShop status</strong> - is the cart live or in test mode? To test the shop you need to have an account on <a href="https://developer.paypal.com/">paypal sandbox</a>. You will need to create and utilise email addresses for the 'seller' and 'buyer' within the sandbox when you test the cart.</p>
<p><strong>Items per page</strong> - this value sets how many items(orders, products etc.) per page are displayed on the various admin pages.</p>

<h3>Merchant Gateway</h3>
<p><strong>Payment method:</strong> only paypal is available at present.</p>
<p><strong>Email address:</strong> this is your paypal business accounts email address.</p>

<h3>Business Details </h3>
<p><strong>Available business email addresses:</strong> when sending an email to a customer via their order details you can add in here extra email addresses you wish to make available to 'send from'.</p>
<p><strong>Business Location</strong> - which country you are in, or registered as being in when setting up your paypal account.</p>

<h3>Product options </h3>
<p><strong>Options per product</strong> - how many different options values can you add to a product.</p>
<p><strong>Out of Stock message</strong> - when you run out of stock you may want to temporarily display an out of stock message.</p>
<p><strong>Stock Control</strong> - If this is used when a stock level falls to 0 or below then the stock is automatically made not available. However stock levels are <strong>not</strong> checked during the order process, and stock reduction is only processed <strong>after</strong> a successful purchase. Therefore it is possible to sell more items than you have in stock.</p>
<p><strong>Show stock available</strong> - if using stock control this allows you to display the stock avaialble.</p>
<p><strong>Download attempts</strong> - the number of download attempts that you will allow per purchase, per file. If set to 0 you will stop all downloads! The default value is 3.</p>
<h3>Currency</h3> 
<p><strong>Symbol</strong> - whether it be $, &pound;, etc.</p>
<p><strong>Code</strong> -  a 3 letter currency code that matches your country.</p>

<h3>Product Listings</h3>
<p><strong>Featured and department product sort order</strong> - newest, oldest, alphabetically - these are the values available to sort products when they are listed on a parent page.</p>

<p><strong>Random products to display</strong> - this is the number of random products to display if you utilise the <code>[eshop_random_products]</code> shortcode.</p>
<p><strong>Department Products to display</strong> - this sets the number of producs to appear per page when you use the <code>[eshop_list_subpages]</code> shortcode.</p>


<h3>Credits</h3> 
<p><strong>Display eShop credit</strong> allows you to hide the 'Powered by eShop' credit that appears on various pages in your shop. Disabling this will still add a hidden <abbr title="Hypertext MarkUp Language">HTML</abbr> comment to the page.</p>
<h3>Link to extra pages</h3>
<p>Here you can add 2 extra links to appear in various places in the cart/checkout procedure that link to a <strong>Privacy Policy</strong>, or <strong>Help page</strong>. The page id number should be used.</p>

<h3>Other Settings</h3>	
<p>The eShop <strong>Shipping Rates</strong> menu item also allows you to amend the shipping calculations and set whether you would like to use the zones set up by country, or by <abbr title="United States">US</abbr> States. The <strong>Show Shipping Zones on Shipping Page</strong> option allows you to automatically show the correct table for the zones. Be warned that these tables are <em>large</em>.</p>
<p>The eShop <strong>Style</strong> page allows you to amend the default style, or disable it completely.</p>
<h3>Settings - eShop Base</h3>
<p>This section sets up defaults that may be useful if you want to use Google Base. Anything entered here is applied to <em>all</em> products. You don't have to enter anything but the more information you supply the better. You are able to override these settings per product via the eShop - Base page.</p>
<p><strong>Brand</strong> - if you sell one particular brand, set it here.</p>
<p><strong>Condition</strong> - choose from one of the options available.</p>
<p><strong>Product expiry in days</strong> - Google base automatically expires products listed after 30 days, but yo can set a different value here.</p>
<p><strong>Product type</strong> - if you sell one type of product eg. figurines, set it here.</p>
<p><strong>Payment Accepted</strong> - comma delimited list of payment methods available in addition to paypal.</p>
<h4>Reset eShop Base</h4>
<p>This resets all data that has been set using the eShop - Base page.</p>
</div>

<div class="wrap">
<h2 id="aover">eShop Admin Pages</h2>

<h3>Orders</h3>

<ul>
<li><strong>eShop</strong> &amp; <strong>Stats</strong> - a quick statistics page showing how many orders are in the system.</li>
<li><strong><a href="#pend" title="Why is an Order Still Pending?">Pending</a></strong> - not yet processed - orders are automatically removed from here after 4 days (paypal can take that long to interact with your system)</li>
<li><strong>Active </strong>- these orders have been paid for, and a successful transaction has been made.</li>
<li><strong>Shipped</strong> - you may want to move an order here after you ship it.</li>
<li><strong>Failed</strong> - hopefully will never get used! but if there is a problem with a payment for an order it will show here.</li>
<li><strong>Deleted pages</strong> - when you delete a page initially it goes here. However you then have the facility to delete *all* orders over a certain age - x hours. Once deleted from here the order is completely wiped from the database.</li>
</ul>

<p>Each page lists orders(except statistics), where you can choose to view a specific one. The customers order is shown in full for you to see. Their email address is highlighted, and can be used to email the customer. It actually takes you to a form on your site with a form prefilled in for you.(this form utilises the <em>'customer responce email'</em> template)</p>

<h3>Shipping</h3>
<h4>Shipping rates</h4>
<p>Shipping rate calculations, 3 methods are offered: </p>
<ul>
<li><strong>Method 1</strong> ( per quantity of 1, prices reduced for additional items ) may take a while to calculate, but is possibly of greatest use.</li>
<li><strong>Method 2</strong> ( once per shipping class no matter what quantity is ordered ) </li>
<li><strong>Method 3</strong> ( one overall charge no matter what quantity is ordered )</li>
</ul>
<p>each of these methods still allow for a per zone price.</p>

<p><strong>Shipping Zones</strong> - this is where you choose between US States, <strong>OR</strong> countries to be used.</p>
<p>The <strong>Show Shipping Zones on Shipping Page</strong> option allows you to automatically show the correct table for the zones. Be warned that these tables are <em>large</em>.</p>
<p>The table for the shipping charges is fairly complex, but hopefully easy to follow.</p>

<p>Shipping rate F is an additional option available, but is set for 'FREE' delivery.</p>

<h4>Countries and US States</h4>
<p>On both a set of default zones have been added for you, but can be alteredif necessary.</p>
<p>Paypal doesn't list all countries, so you may need to check the list to ensure it is correct. Obviously you should delete any that you feel you don't want to deliver to. At the bottom of each form there is a blank field to allow you to add to these lists.</p>

<h3>Products</h3>
<p>This page lists all the products you have entered, along with a few statistics.</p>


<h3>Downloads</h3>
<p>Providing the downloads directory is writable, you can upload files here. These will become available for sale within your eShop.The page list all available downloadable products (that you have previously uploaded), along with a few statistics.</p>
<p>As a security measure you are not able to delete a file that is currently available for sale within your eShop.</p>

<h3>Base</h3>
<p>Manage your products for use in your data file for Google Base.</p>
<p>The details for each product can be tweaked by following the <em>sku</em> link.</p>
<p>For images to be used they <strong>must</strong> be uploaded via the page the product is allied to.</p>

<h3>Style</h3>
<p>Some default style has been included with the eShop to allow you to get up and running as quickly as possible. On this page you are able to turn that off if you would rather use style associated with a particular theme. If the CSS file is editable you can also edit it directly via this page.</p>


<h3>Templates</h3>
<p>This allows you to edit 2 email templates:</p>
<ul>
<li>The <strong>automatic order email</strong> is sent out automatically when a successful transaction is recorded on your system.</li>
<li>The <strong>customer responce email</strong> can be sent at any time from you order details screen.</li>
</ul>

<h3>About</h3>
<p>List initial installation and configuration help, along with eShop credits.</p>
<h3>Help</h3>
<p>This page!</p>

</div>

<div class="wrap">
<h2 id="pend">Why is an Order Still Pending?</h2>
<p>Pending orders should be automatically moved to <strong>Active</strong> orders once a successful transaction has taken place. However there are a few circumstances where this might not happen.</p>
<ul>
<li>A customer cancelled the transaction.</li>
<li>Part of the paypal transaction was invalid.</li>
<li>Paypal's server is down, possibly for maintenance.</li>
</ul>
<p>In all cases before you decide to move an order from pending, check the following.</p>
<h3>Paypal service status</h3>
<p><a href="http://www.pdncommunity.com/blog?blog.id=mts_updates">Paypal Live Site Status</a> : it is always worth keeping an eye on this page for outages and planned maintenance.</p>
<h3>Has 4 days passed?</h3>
<p>Paypal can take upto 4 days to interact with your web site with regard to any transaction. Orders are automatically sent to <em>Deleted</em> orders after 4 days via eShop. However no orders are fully deleted unless you specifically request it, so the order can be retrieved at a later date.</p>
<h3>Did you recieve a transaction notification?</h3>
<p>All successful, and most unsuccessful, orders generate a system email to your paypal address, this should be kept as a back up copy of the transaction. These are useful for checking outstanding transactions.</p>
<h3>Check your paypal account</h3>
<p>Check your account at paypal to see if there are any sales that don't tie up with anything.</p>

</div>



<div class="wrap">
<h2 id="base">eShop Base</h2>
<p>eShop Base creates a data file for upload to Google Base.</p>
<h3>Manage - eShop base Feed</h3>
<p>Download or view online your product feed for uploading to Google Base.</p>
</div>
<div id="delete-info" class="wrap">
<h2 id="extr">Cart Operations</h2>
<p>After items are added to the Cart the customer can then fill in a form on the checkout page. After which they are redirected to payapl for payment. A successful payment will auto generate an email to the customer <strong>and</strong> yourself. The email sent to you will have a subject containing <strong>Paypal IPN</strong>. This is a quick record of the transaction that should be kept as a backup. The email to the customer deatils their order, and includes a download link, along with their login details when necessary. This download information is comprised of their email address, and a unique code. They have 'x' attempts to download a file, as set in the eShop settings. Providing Paypal has successfully accepted payment, a download form will be available should the customer come back to the site.</p>
<p>If the order just contains downloads it should be automatically moved to the <strong>Sent</strong> orders page, otherwise they will be sent to the <strong>Active</strong> page for delivery.</p>
</div>

<div class="wrap">
<h2 id="conf">Conflicting plugins</h2>
<p>The eShop plugin may conflict with others you have installed. If you find any please post on the <a href="http://www.quirm.net/punbb/">support forums</a>.</p>
<h3>Bad Behaviour plugin</h3>
<p>It is recommended that this plugin is not used with eShop. I am unable to test this plugin effectively as it slowed the test site down dramatically, causing it to become unusable.</p>
<p>However for those that still wish to use it, so far as I can tell there are no conflicts. But you will have to visit the <a href="https://www.paypal.com/IntegrationCenter/ic_go-live.html">Paypal Go Live Checklist</a> and add all of the <abbr title="Internet protocol">IP</abbr> addresses to Bad Behaviour's whitelist.</p>
</div>

<div class="wrap">
<h2 id="comp">Compatability</h2>
<p>eShop has been written for Wordpress 2.5 and up, and is not compatible with earlier versions.</p>
</div>

<div class="wrap">
<h2 id="del">Deactivating and Uninstalling</h2>
<p>To deactivate the plugin without losing data use the <strong>deactivate</strong> link on the plugins page. If you want to completely uninstall the plugin, delete all associated data and files use the <strong>eShop Uninstall</strong> link from the plugins page.</p>
</div>
</div>
<?php eshop_show_credits(); ?>
