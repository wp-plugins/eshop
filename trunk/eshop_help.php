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
<div id="eshoppage" class="eshophelp">
<div class="wrap">
<div id="eshopicon" class="icon32"></div><h2>Help</h2>
<p>This is some basic helpful information about the shopping cart admin.</p>
<h3>Sections</h3>
<ul>
<li><a href="#crpr">Creating Products</a></li>
<li><a href="#dept">Creating Departments</a></li>
<li><a href="#short">Shortcodes</a></li>
<li><a href="#test">eShop testing</a></li>
<li><a href="#glive">Going Live with eShop</a></li>
<li><a href="#payliv">eShop &amp; Paypal</a></li>

<li><a href="#bover">Configuration</a></li>
<li><a href="#img">Product Images</a></li>
<li><a href="#optset">Option Sets</a></li>

<li><a href="#aover">eShop Admin Pages</a></li>
<li><a href="#pend">Why is an Order Still Pending?</a></li>

<li><a href="#base">eShop Base</a></li>
<li><a href="#extr">Cart Operations</a></li>
<li><a href="#conf">Conflicting plugins</a></li>
<li><a href="#comp">Compatability</a> (including - affiliates)</li>
<li><a href="#actnote">Notes on activation</a></li>
<li><a href="#del">Deactivating and Uninstalling</a></li>

</ul>
</div>
<div class="wrap">
<h2 id="crpr">Creating Products</h2>
<p>Adding a product is as easy as creating a page. Just fill in the required fields within the <strong>Product Entry</strong> section when creating or editing a page.</p>
<p>The <strong>Sku</strong> should be a unique identification reference for your product eg.abc001.</p>
<p>The <strong>Product Description</strong> is a short description of the product. This is used in the customers cart, and will appear on their invoice from Paypal.</p>
<p><strong>Option x</strong>, <strong>Price x</strong> and <strong>Download x</strong> are the individual item and price, thus allowing several options for each product eg. Small, Medium, Large. If there is only 1 option for a product, then please use the <strong>Option 1</strong> and <strong>Price 1</strong> input fields.</p>
<p>The <strong>Download</strong> selection boxes only appear if you have uploaded a file via eShop for download. To link to a file, select it from the dropdown list. Downloadable options now default to free shipping in the cart - even if you set the shipping rate as something different. This allows for online and offline options for the same product (e.g. book and ebook).</p>
<p>Choose your <strong>Shipping Rate</strong> carefully, <strong>F</strong> is set aside for any Free shipping (obviously downloadable products should use this).</p>
<p>The <strong>Featured Product</strong> product selection chooses whether that product can be listed as a featured product.</p>
<p>A product is unavailable for sale until <strong>Stock Available</strong> is set.</p>
<p><strong>Stock Quantity</strong> - sets the quantity available for this product. A quantity needs to be entered for download products. I suggest you enter a 1.</p>

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
</div>
<div class="wrap">
<h2 id="short">Shortcodes</h2>
<p>To then list the products on the Online Shop and Department pages there are various options available. You will need to add to those pages one of the following codes:</p>
<table class="widefat eshopatt">
<thead>
<tr><th>Shotrtcodes</th><th colspan="12" class="eshopdefault">Attributes: Defaults shown(where applicable)</th></tr>
<tr class="center"><th></th>
<th>class</th>
<th>panels</th>
<th>form</th>
<th>show</th>
<th>records</th>
<th>sortby</th>
<th>order</th>
<th>list</th>
<th>id</th>
<th>imgsize</th>
<th>excludes</th>
<th>links</th>
</tr>
</thead>
<tbody>
<tr><th>[eshop_list_subpages]</th>	<td>eshopsubpages</td><td>no</td><td>no</td><td>100</td><td>10</td><td>post_title</td><td>ASC</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>no</td><td>100</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>yes</td></tr>
<tr class="alternate"><th>[eshop_list_cat_tags]</th>	<td>eshopcats</td><td>no</td><td>no</td><td>100</td><td>10</td><td>post_title</td><td>ASC</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>100</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>yes</td></tr>
<tr><th>[eshop_list_featured]</th>	<td>eshopfeatured</td><td>no</td><td>no</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>post_title</td><td>ASC</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>100</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>yes</td></tr>
<tr class="alternate"><th>[eshop_list_new]</th>		<td>eshopsubpages</td><td>no</td><td>no</td><td>100</td><td>10</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>100</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>yes</td></tr> 
<tr><th>[eshop_random_products]</th><td>eshoprandomlist</td><td>no</td><td>no</td><td>6</td><td>6</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>yes</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>100</td><td>0</td><td>yes</td></tr> 
<tr class="alternate"><th>[eshop_show_product]</th>	<td>eshopshowproduct</td><td>no</td><td>no</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>0</td><td>100</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>yes</td></tr> 
<tr><th>[eshop_best_sellers]</th>	<td>eshopbestsellers</td><td>no</td><td>no</td><td>100</td><td>10</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>100</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>yes</td></tr> 
<tr class="alternate"><th>[eshop_list_alpha]</th>		<td>eshopalpha</td><td>no</td><td>no</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>25</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>100</td><td><img src="<?php echo WP_PLUGIN_URL; ?>/eshop/no.png" alt="Not available" height="16px" width="16px" /></td><td>yes</td></tr> 
</tbody>
</table>
<dl class="eshop-def">
<dt><code>[eshop_list_subpages]</code></dt>
<dd>This displays a list of pages with products and is ideal for use on a Department page.</dd>
<dt><code>[eshop_list_cat_tags]</code></dt>
<dd>Displays a list of products with tags or categories. This has 2 extra attributes <em>type</em> where you can have values of 'cat','category_name','tag' &amp; 'tag_id'. <em>find</em> is where you add what you want to find, multiple values can be added separated by a comma.</dd>
<dt><code>[eshop_list_featured]</code></dt>
<dd>This displays products that have been as set as a Featured product. Suggested use for this is on the main Online Shop page.</dd>
<dt><code>[eshop_list_new]</code></dt>
<dd>This displays latest products. Suggested use for this is on a separate Latest Products page.</dd>
<dt><code>[eshop_random_products]</code></dt>
<dd>This displays a random selection of products. This could be used on the Online Shop page, or on other pages within your site.</dd>
<dt><code>[eshop_show_product]</code></dt>
<dd>Can be used to display a specific product, or products.</dd>
<dt><code>[eshop_best_sellers]</code></dt>
<dd>Can be used to display the best selling products.</dd>
<dt><code>[eshop_list_alpha]</code></dt>
<dd><strong>Experimental</strong> Displays products alphabetically split by alphabet and 0-9. A selection list of A to Z plus 0-9 is included.</dd>
</dl>
<p><strong>Remember</strong> you do not have to add in the defaults. You should only add the attributes when you want to change from the defaults.</p>
<h3>Examples</h3>
<ul class="eshop-shortcodes">
<li><code>[eshop_list_subpages class="myclass"]</code> changes the default class to 'myclass'</li>
<li><code>[eshop_show_product id='9' class='hilite' panels='yes' form='yes']</code> shows product '9' only. Changes the default class to 'hilite', shows the product as a panel and the add to cart form is shown.</li>
</ul>
<p>Details for attributes:</p>
<ul class="eshop-shortcodes">
<li><code>class</code> example: <em>class="myclass"</em> to change the default class.</li>
<li><code>panels</code> example: <em>panels="yes"</em> to show 'panels'</li>
<li><code>form</code> example: <em>form="yes"</em> to add the shortened add to cart form.</li>
<li><code>show</code> example: <em>show="10"</em> limits the display to 10 products.</li>
<li><code>records</code> example: <em>records="5"</em> limits the number of products shown 'per page' to 5.</li>
<li><code>sortby</code> example: <em>sortby="post_title"</em> shows the pages in alphabetical order. Possible values: post_date, post_title or menu_order</li>
<li><code>order</code> example: <em>order="ASC"</em> shows the results in ascending, or descending order. Possible values: ASC or DESC</li>
<li><code>list</code> example: <em>list="no"</em> limits the display to 1 random product.</li>
<li><code>id</code> example: <em>id="25"</em> or <em>id="25,29,52"</em> shows specific products only. For subpages, only one id can be used.</li>
<li><code>imgsize</code> example: <em>imgsize="50"</em> would resize the image to 50% of its original width and height.</li>
<li><code>excludes</code> example: <em>excludes="5,14"</em> allows you to exclude items from the list.</li>
<li><code>links</code> example: <em>links="no"</em> stops all the links from being generated, useful when combined with <code>form</code>.</li>

</ul>
<p><strong>sortby</strong> and <strong>order</strong> replace the following settings from earlier versions:</p>
<ul>
<li>Featured and department product sort order</li>
<li>Random products to display</li>
<li>Department Products to display</li>
</ul>


<h3>Extra</h3>
<ol class="eshop-shortcodes">

<li><code>[eshop_show_discounts]</code> This displays a table of discounts and a paragraph for the free shipping discount. This will only show if set.</li>
<li><code>[eshop_show_shipping]</code> (automatically added to the Shipping Rates page) can now be amended via the attribute 
<code>shipclass</code>. example <em>shipclass='A,B,F'</em> would only display shipping classes A, B and F (dependant on the shipping rate calculation used).</li>
<li><code>[eshop_show_payments]</code> Displays a list of images with the current payment methods allowed.</li>
<li><code>[eshop_empty_cart]</code>Message<code>[/eshop_empty_cart]</code> Specifically designed for the cart page, any <em>Message</em> you enter will only be displayed if the cart is empty.</li>
<li><code>[eshop_cart_items]</code> A simple shortcode for use in templates via the <code>do_shortcode</code> function. In its simplest form it displays the number of items in the cart. It can be adjusted with the following attributes <code>before</code> and <code>after</code>, which can be used to insert text before and after the cart size.
<code>hide</code> is also available, setting this to yes will stop the shortcode from displaying anything if the cart is empty. <code>showwhat</code> is also available to show either number of <em>items</em>, <em>qty</em> total or <em>both</em></li>
<li><code>[eshop_addtocart]</code> will enable the add to cart form to appear anywhere on a product page. By default the form appears after the content. (not fully tested - please let me know if it causes problems).</li>
<li><code>[eshop_welcome]</code> print a simple name of the customer. You can use the following attributes: <code>before</code> - which could be used to add mark up, <code>returning</code> - perhaps use a phrase like welcome back, <code>guest</code> - the phrase you would like to use for a guest, <code>after</code> - again could be used to close the markup.<br />
This has also been written for use in templates, add something like:
<code>&lt;?php 
echo do_shortcode("[eshop_welcome before='&lt;span style=\"color:red;\"&gt;' returning='Welcome back' guest='Hello Guest' after='&lt;/span&gt;']");
?&gt;</code></li>
</ol>
<h3>Notes</h3>
<p>By default eShop will not display a form, even if specified via a shortcode, on WordPress post listing pages - category, search etc. To enable this you need to change the setting in the Product Listings section of the settings page.</p>
</div>
<div class="wrap">
<h2 id="test">eShop Testing</h2>
<p>To test eShop with Paypal you need to have an account on <a href="https://developer.paypal.com/">Paypal Sandbox</a>. You will need to create and utilise email addresses for the 'seller' and 'buyer' within the sandbox when you test the cart. To make test purchases whilst in test mode you have to be logged into Wordpress <strong>and</strong> Paypal Sandbox.</p>
<p>The redirect page when in testing mode does not automatically redirect, this is to give you chance to style that page if needed.</p>
<p>Testing with <a href="https://www.payson.se/Default.aspx">Payson</a> just requires a standard Payson account.</p>
</div>
<div class="wrap">
<h2 id="glive">Going Live with eShop</h2>
<p>On the <strong>Settings - eShop - Merchant Gateway</strong> page you then need to change the following.</p>
<ul>
<li><strong>Business Location</strong> &#8212; Your 2 letter country code.</li>
<li><strong>Currency Code</strong> &#8212; your 3 letter currency code.</li>
</ul>
<p>If using Paypal then those setting must match those at Paypal.</p>
<p>You then need to enter various settings depending on the payment option you choose to use.</p>
<p>On the main <strong>Settings - eShop</strong> you will need to change these settings:</p>
<ul>
<li><strong>Currency Symbol</strong> &#8212; The symbol for your currency e.g. <em>&pound;</em>.</li>
<li><strong>eShop Status</strong> needs to be set to <em>Live</em>.</li>
</ul>

<p>It would also be advisable to visit the <strong>eShop - Shipping</strong> page.</p>

<p>Everything should then be set up and working!</p>
</div>
<div class="wrap">
<h2 id="payliv">eShop &amp; Paypal</h2>


<p>If you are about to open or have just opened a new PayPal account, jump straight to the section <strong>New paypal business account</strong> but if you want to use an account that has been used and configured before, please read <strong>Existing paypal account</strong> first.</p>


<h3>Existing paypal account</h3>

<p>You might have an account that has been used for other shopping cart solutions or just with settings changed from default and you are unsure if those will prevent eShop from working properly. Below, you will see a list of settings that HAVE to be set as indicated for proper operation of eShop. After you have done those, read the recommendations given under "New paypal business account" for best customer experience.</p>
<ol>
<li>If you have a PERSONAL or PREMIER ACOUNT it is recommended to upgrade to a BUSINESS account.</li>

<li><p>Check your account options and change them if different from the (default) settings below.</p>

<p>Go to My Account > Profile ( > More Options ) and go through each of these sections:</p>
<dl>
<dt>Account Information</dt>

   <dd><strong>Email</strong> - Make sure you are using the same email address in eShop as the one that is set as Primary here! If not, change either your eShop settings or Add and make that email address Primary here.
</dd>

<dt>Financial Information</dt>

    <dd><strong>Currency Balances</strong> -  Make sure the currency you are going to use in eShop is present here and is set as Primary. If not, pick the currency from the dropdown list under Select new currency , hit Add Currency and then select the new currency and hit Make Primary.</dd>


<dt>Selling Preferences</dt>
<dd>
<dl>
    <dt>Tax/Value Added Tax</dt>
	<dd>If you are a business supplier -for example- and have Sales Tax rules set up here, be sure you define prices in eShop WITHOUT tax. Do not forget to mention prices are without tax on your website to avoid an unpleasant surprise during checkout. Currently, eShop does not have an option to display such a message automatically.</dd>

    <dt>Shipping Calculations</dt>

    <dd>It is recommended to let eShop handle shipping calculation. If you have set up any rules here, copy them over to your eShop > Shipping admin page in WordPress before you hit Clear Settings to clear them from your PayPal account. If you insist on letting PayPal handle shipping calculation instead of eShop's Shippping Rates feature, please be sure to set all shipping rates in eShop to 0 (zero) and warn customers on your site they will face additional shipment costs during checkout to avoid canceled transactions due to an unpleasant surprise. Currently, eShop does not have an option to disable shipping calculation and display such a message automatically.</dd>

    <dt>Instant Payment Notification Preferences</dt>

    <dd>IPN needs to be OFF ! If active, hit Turn Off IPN and confirm to get rid of all settings.</dd>

    <dt>Website Payment Preferences</dt>

    <dd>The following option need to be set to OFF or transactions will NOT be handled properly by eShop!

          <ul>
		  <li>Auto Return: OFF</li>
          <li>Payment Data Transfer: OFF</li>
          <li>Encrypted Website Payments: OFF</li>
		  </ul></dd>

    <dt>Language Encoding </dt>
<dd>If in doubt set this to UTF-8 same as WP</dd>
</dl></dd></dl>
</li>
</ol>
<p>All other settings will not interfere with proper operation of eShop.</p>

<p>Read the instructions under "New paypal business account" for some recommendations to improve customer experience on your web shop.</p>
<h2>New paypal business account</h2>

<p>If you haven't done so already, head over to https://www.paypal.com/ and Sign Up for a new Business account in the same country with the same currency and with the same e-mail address as you will be using in eShop. After that, there is nothing more that needs to be done to make eShop handle PayPal transactions successfully.</p>

<p>However, you might want to configure and tweak your account to improve the customer experience. The following instructions should guide you through that without doing things that will break proper communication between eShop and PayPal during checkout.</p>

<p>On your new Acount Overview you will see a section My account tools with a link My Business Setup. Follow that and you will land on a page that starts with:</p>

<p style="font-size:1.4em;">    First steps with PayPal<br />

    You've signed up for a PayPal Business account. Now what?</p>


<p>Below that, in the section My payment solution you will see Set up my payment solution and Customize my buyer's experience. Just leave that first part alone, eShop will take care of that for you. If you like, you can set stuff like your company logo under the second part. Hit the Start Now button right after "Customize PayPal payment pages, adjust customer service settings, and much more."</p>

<p>You are now on the Customize my buyer's experience page. Here you can :</p>
<dl>
<dt>Add your business logo</dt>
<dd><ul>
<li>Make the PayPal checkout pages recognizable as your business. Recommended.</li>
<li>Create a new checkout page style here and give it colors and logo to match your website. Then set it to Primary to make it visible for </li>
<li>customers that check out with your eShop cart. Currently, eShop is not able to use non-primary styles during checkout.	</li>
</ul></dd>

<dt>Sales tax</dt>
<dd>Set this ONLY if you are a business supplier -for example- and specify prices WITHOUT tax on your website! Do not forget to mention this on your site to avoid unpleasant surprises during checkout. Currently, eShop does not have an option to display such a message automatically.
</dd>
<dt>Shipping calculations</dt>
<dd>Do NOT set these but let eShop handle shipping charges. Go to your eShop > Shipping admin page in WordPress and define shipping cost rules there.</dd>
<dt>Auto Return</dt>
<dd><p>Careful: Some settings must be left OFF for proper operation of eShop.</p>

    <ul>
	<li>Auto Return: leave OFF !</li>
    <li>Payment Data Transfer: leave OFF !</li>
    <li>Encrypted Website Payments: leave OFF</li> 
    <li>PayPal Account Optional: switch to ON if you want buyers to be able to use their Credit Card without opening a PayPal account first. Recommended!</li>
    <li>Contact Telephone Number: to show your phone number to those that are about to check out. Setting your private number here is NOT recommended!</li>
    <li>Express Checkout Settings - not required.</li>
	</ul>
</dd>

<dt>Card Statement Name</dt>
<dd>Settings here are fine when left like they are but it is RECOMMENDED that you make sure the Credit Card Statement Name is recognizable as that of your online shop. Customers will see this name later on their Credit Card statement and might get confused -or even charge back the money- if they do not recognize it as a purchase from your shop.</dd>
</dl>
<p>By now, you will have you account set up to receive transactions from your website using eShop. Go ahead and put it LIVE in WordPress on Settings > eShop > General !</p>

<p>Later, will probably link your business or personal bank account to your new PayPal account to be able to transfer revenue and maybe your Credit Card. Then I recommend to get your status VERIFIED for increased credibility among potential customers. See the PayPal Help pages and Tutorials (under Resolution Center) for more info.</p>

</div>
<div class="wrap">
<h2 id="bover">Configuration</h2>
<p>There are a number of settings that should be configured <strong>before</strong> you can start selling.</p>
<p>Go to <strong>Settings - eShop</strong>. This page features the main options for the plugin. Defaults have been added for your convenience, where possible.</p>

<h3>General</h3> 
<h4>eShop Admin</h4>
<p><strong>eShop status</strong> - is the cart live or in test mode?</p>
<p><strong>Orders per page</strong> - this value sets how many items (orders, products etc.) per page are displayed on the various admin pages.</p>

<h4>Business Details</h4>
<p><strong>eShop from email address</strong> - eShop will use this as the 'From' address in the automated emails.</p>
<p><strong>Available business email addresses</strong> - extra business email addresses for use when contacting customers via their order details.</p>

<h4>Product options </h4>
<p><strong>Options per product</strong> - how many different options values can you add to a product.</p>
<p><strong>Out of Stock message</strong> - when you run out of stock you may want to temporarily display an out of stock message.</p>
<p><strong>Stock Control</strong> - If checked, when a products stock level falls to 0 or below, the product will automatically be marked as 'not available' However stock levels are <strong>not</strong> checked during the order process, and stock reduction is only processed <strong>after</strong> a successful purchase. Therefore it is possible to sell more items than you have in stock.</p>
<p><strong>Show stock available</strong> - if using stock control this allows you to display the stock avaialble.</p>


<h4>Currency</h4> 
<p><strong>Symbol</strong> - whether it be $, &pound;, etc.</p>

<h4>Product Listings</h4>
<p><strong>Show add to cart forms on WordPress post listings</strong> -  <strong>Warning</strong> activating this can invalidate your site!. By default eShop will not add forms, even if specified by a shortcode, to WordPress post listing pages. By enabling this option the add to cart form will be added, where possible to all posts, in category listings, search results, etc.</p>


<h4>Cart Options</h4>
<p><strong>Percentage size of thumbnail image shown in cart - leave blank to not show the image</strong> - takes the standard thumbnail produced by wordpress and reduces it by the value entered to fit into the shopping cart.</p>
<p><strong>Use an add to cart image or button? </strong> - a default image is supplied for us, but this can be changed.</p>

<h4>Checkout Options</h4>
<p><strong>Hide the Additional information form fields</strong> - hides checkout form fields.</p>
<p><strong>Hide the shipping address form fields</strong> - hides checkout form fields.</p>

<p><strong>Add a required checkbox to the checkout.</strong></p>
<p><strong>Text for the required checkbox.</strong></p>
<p><strong>Page id (transforms text above into a link).</strong></p>
<p><strong>Allow users to sign up. (You should ensure that {LOGIN_DETAILS} is in your email templates.)</strong> - enables customers to sogn up to be able to view their past orders. <strong>Note</strong> will not sign anyone up if the email addres is already in use.</p>
<p><strong>Change sign up text.</strong> - default text is provided, but you can change that here.</p>

<h4>Sub Pages</h4>
<p>This option, sometimes referred to as 'fold menus', can automatically hide sub pages until their parent page is viewed. (hides links to shop pages until you go into the shop). <strong>Warning</strong> this affects all sub page listings on your site.</p>

<h4>Search Results</h4>
<p><strong>Add image to search results</strong> - if used eShop will add an image to the search results page for any post or page or product pages only.</p>

<h4>Credits</h4> 
<p><strong>Display eShop credits</strong> allows you to hide the '<em>Powered by eShop</em>' credit that appears on various pages in your shop. Disabling this will still add a hidden <abbr title="Hypertext MarkUp Language">HTML</abbr> comment to the page.</p>

<h4>Cron</h4>
<p>Cron automatically sends out a daily email to the specified address if there are any outstanding, or pending, orders.
This is only triggered when someone visits the site.</p>
<p>To stop this feature, simply delete the Cron Email address.</p>

<h3>Merchant Gateway</h3>
<h4>General Settings</h4>
<p>Both of these settings must match those at paypal, if used, but should be set for all merchant gateways.</p>
<p><strong>Business Location</strong> - which country you are in.</p>
<p><strong>Code</strong> -  a 3 letter currency code that matches your country.</p>

<h4>Paypal</h4>
<p><strong>Email address:</strong> the email address associated with your Paypal business account. Ensure that you enter it correctly!</p>
<p><strong>Send buyers email address to paypal?:</strong>(<em>experimental</em>) unsetting this may change the login screen at Paypal to highlight ability to pay without joining Paypal.</p>

<h4>Payson</h4>
<p><strong>Warning</strong> - all Payson transactions are in SEK irrespective of settings within eShop. there is also a minimum order value, which is not reflected in the shopping cart on the site. However it will be shown to the buyer when they go to pay.</p>
<p>The <strong>Email address</strong>, <strong>Agent ID</strong> and <strong>Secret Key</strong> must match those at Payson.</p>
<p>You need to set a <strong>Cart Description</strong> and this will be used in place of the shopping cart at Payson.</p>
<p>Payson has a minimum order value which, at time of writing, is 4 SEK. The <strong>Min. Cart value</strong> alters the amount that Payson recieves to ensure the minimum value is met. Payson refuses values less than this.</p>

<h4>eProcessingNetwork</h4>
<p><strong>Warning</strong> - test mode does not look any different on the payment side.</p>

<h4>Cash</h4>
<p>Orders placed here are automatically added to the awaiting payment page.</p>


<h4>webtopay</h4>
<p>For help with webtopay please contact <a href="mailto:integrate@mokejimai.lt">Markas Krasovskis</a></p>

<h4>Authorize.net</h4>
<p>Settings must match those set at Authorize.net exactly.</p>


<h3>Discounts</h3>
<p><strong>Spend</strong> - how much needs to be spent before <strong>% Discount</strong> is applied.</p>
<p><strong>Spend over to get free shipping</strong> - how much needs to be spent before the order qualifies for free shipping.</p>
<p>In both cases deleting the amount will cancel the discount.</p>

<h3>Downloads</h3>
<h4>Downloadables</h4>
<p><strong>Download attempts</strong> - the number of download attempts that you will allow per purchase, per file. If set to 0 you will stop all downloads! The default value is 3.</p>
<h4>Downloads Only</h4>
<p>If using eShop for a downloads-only store, then setting this to <em>'yes'</em> will hide the shipping rates link, and provide a shorter checkout form.</p>


<h3>Special pages</h3>
<h4>Continue Shopping Link</h4>
<p>If you enter the page id of your main Shop page, then eShop will use that for the Continue Shopping link. Leave this blank and eShop will either link to the last product, or to the main page of your site automatically.</p>

<h4>Link to extra pages</h4>
<p>Here you can add 2 extra links to appear in various places in the cart/checkout procedure that link to a <strong>Privacy Policy</strong>, or <strong>Help page</strong>. The page id number should be used.</p>
<p>The Shipping Rates page id should be available by default.</p>

<h4 id="auotp">Automatically created pages</h4>
<p>These are automatically generated when eShop is first activated. Changing these could affect how eShop works, but are available for amendment should you delete a page in error. All of these pages require shortcodes to work correctly.</p>

<h3>Other Settings</h3>	
<p>The eShop <strong>Shipping Rates</strong> menu item also allows you to amend the shipping calculations and set whether you would like to use the zones set up by country, or by country specific State/County/Province. The <strong>Show Shipping Zones on Shipping Page</strong> option allows you to automatically show the correct table for the zones. Be warned that these tables are <em>large</em>. </p>
<p>The <strong>Appearance</strong> eShop page allows you to amend the default style, or disable it completely. <strong>Note:</strong> If your theme has an eshop.css then that will be used.</p>


<h3>Settings - eShop Base</h3>
<p>This section sets up defaults that may be useful if you want to use Google Base. Anything entered here is applied to <em>all</em> products. You don't have to enter anything but the more information you supply the better. You are able to override these settings per product via the eShop - Base page.</p>
<p><strong>Brand</strong> - if you sell one particular brand, set it here.</p>
<p><strong>Condition</strong> - choose from one of the options available.</p>
<p><strong>Product expiry in days</strong> - Google base automatically expires products listed after 30 days, but yo can set a different value here.</p>
<p><strong>Product type</strong> - if you sell one type of product eg. figurines, set it here.</p>
<p><strong>Payment Accepted</strong> - comma delimited list of payment methods available in addition to Paypal.</p>
<h4>Reset eShop Base</h4>
<p>This resets all data that has been set using the eShop - Base page.</p>

<h3>Appearance - eShop</h3>
<p>Some default style has been included with the eShop plugin to allow you to get up and running as quickly as possible. On this page you are able to disable the default styling if you would rather use style associated with a particular theme. If the CSS file is editable you can also edit it directly via this page.</p>
<p>Should your theme already have an eshop.css file then it will be used by default.</p>

</div>
<div class="wrap">
<h2 id="img">Product Images</h2>
<p>You can add and use any image to a product page. Just use the thumbnail feature from within WordPress, this is found on the edit post/page screen.</p>
</div>

<div class="wrap">
<h2 id="optset">Option Sets</h2>
<p>Create any number of option sets, which can then be added to any number of products. There is no restriction on how many can be added to a product, or how many item you can have in an option set.</p>
<p>Each option set can be a dropdown select box, or a series of checkboxes. Each option within a set can have additional price, which is added onto the default price for that item.</p>
<p>Deleting an option set removes it from all products.</p>
</div>

<div class="wrap">
<h2 id="aover">eShop Admin Pages</h2>

<h3>Orders</h3>

<ul>
<li><strong>eShop</strong> &amp; <strong>Stats</strong> - a quick statistics page showing how many orders are in the system.</li>
<li><strong><a href="#pend" title="Why is an Order Still Pending?">Pending</a></strong> - not yet processed - orders are automatically removed from here after 4 days (Paypal can take that long to interact with your system)</li>
<li><strong>Active</strong> - these orders have been paid for and a successful transaction has been completed.</li>
<li><strong>Shipped</strong> - you may want to move an order here after you ship it.</li>
<li><strong>Failed</strong> - hopefully never used but, if there is a problem with a payment for an order, it will show here.</li>
<li><strong>Deleted</strong> - when you delete an order, it is initially moved to here. You then have the facility to delete *all* orders over a certain age - x hours. Once deleted from this section, the order is completely removed from the database.</li>
</ul>

<p>Each page lists orders (except statistics). Select an individual order to view the full order details. The customer's email address is highlighted and can be used to contact the customer. Selecting the Customer's email address will take you to a Customer Contact page containing a pre-filled 'customer response' email template for your use.</p>

<h3>Shipping</h3>
<h4>Shipping Rates</h4>
<p>Shipping rate calculations:</p>
<ul>
<li><strong>Method 1</strong> ( per quantity of 1, prices reduced for additional items ) may take a while to calculate, but is possibly of greatest use.</li>
<li><strong>Method 2</strong> ( once per shipping class no matter what quantity is ordered ) </li>
<li><strong>Method 3</strong> ( one overall charge no matter what quantity is ordered )</li>
<li><strong>Method 4</strong> (  by weight & zone )</li>

</ul>
<p>Each of these methods still allow for a 'per zone' price.</p>

<p><strong>Shipping Zones</strong> - choose between US States <strong>or</strong> Countries to be used.</p>
<p>The <strong>Show Shipping Zones on Shipping Page</strong> option allows you to automatically show the correct table for the zones. Be warned that these tables are <em>large</em>.</p>
<p>The table for the shipping charges is fairly complex, but hopefully easy to follow.</p>

<p>Shipping Rate F is preset for 'FREE' delivery and cannot be amended.</p>
<h5>Method 4 notes</h5>
<p>This will automatically set all weights to zero, so be careful when using. An extra field is added to the product entry section. Additionally a new section on the checkout page is added to allow the customer to choose the shipping method.</p>
<p><strong>Shipping Modes (by weight) - 1 mode per line</strong> - required, enables you to specify different methods of shipping.</p>
<p><strong>Weight units</strong> - specify what weight unit you will be using.</p>
<p><strong>Note:</strong> - currently weight is <em>not</em> automatically shown on product pages.</p>
<p>The table for entering the prices features a starting weight, and prices per zone.</p>

<h4>Countries and State/County/Province</h4>
<p>Default zones are pre-configured for both 'Countries' and 'State/County/Province' but can be amended if necessary.</p>
<p>Paypal doesn't list all countries, so you may need to check the list to ensure it is correct. Obviously you should delete any that you feel you don't want to deliver to. At the bottom of each form there is a blank field to allow you to add to these lists.</p>

<h3>Products</h3>
<p>This page lists all the products you have entered, along with a few statistics.</p>

<h3>Downloads</h3>
<p>Providing the downloads directory is writable, you can upload files here. These will become available for sale within your eShop.The page lists all available downloadable products (that you have previously uploaded), along with a few statistics.</p>
<p>As a security measure you are not able to delete a file that is currently available for sale within your eShop.</p>
<h4>Uploading large files</h4>
<p>Any files that you FTP to the correct eshop_downloads directory can be added by visiting the <em>Unknown Download Files</em> section on the eshop Downloads page.</p>
<p>An alternative method is to amend your main wordpress .htaccess file, or amend your sites php.ini. The following are an example set of directives to be added to the htaccess file.</p>
<pre><code># BEGIN eShop
php_value upload_max_filesize 100M
php_value post_max_size 200M
php_value memory_limit 400M
php_value max_execution_time 10800
php_value max_input_time 10800
php_value session.gc_maxlifetime 10800
# END eShop</code></pre>
<p>This is <strong>not</strong> done automatically as which method you can use may be reliant on your hosting company.</p>

<h3>Discount Codes</h3>
<p>Various options have been created to give a wide variety of discount codes, from single use to unlimited. This can be a set discount, or for free shipping.</p>

<h3>Base</h3>
<p>Manage your products for Google Base.</p>
<p>The details for each product can be tweaked by following the <em>Sku</em> link.</p>
<p>For images to be used they <strong>must</strong> be uploaded via the page the product is allied to.</p>

<h3>Emails</h3>
<p>This allows you to edit the email templates:</p>
<ul>
<li>The <em>Automatic order email</em> is sent out automatically when a successful transaction is recorded on your system.</li>
<li>The <em>Customer response email</em> can be sent at any time from you order details screen.</li>
</ul>

<p>The other templates are empty by default, make sure you add some content to them before activating them!</p>

<h3>About</h3>
<p>List initial installation and configuration help, along with eShop credits.</p>
<h3>Help</h3>
<p>This page!</p>

</div>

<div class="wrap">
<h2 id="pend">Why is an Order Still Pending?</h2>
<p>Pending orders should be automatically moved to <strong>Active</strong> once a successful transaction has taken place. However there are a few circumstances where this might not happen. This mainly relates to orders from Paypal.</p>
<ul>
<li>A customer cancelled the transaction.</li>
<li>Part of the Paypal transaction was invalid.</li>
<li>Paypal's server is down, possibly for maintenance.</li>
</ul>
<p>In all cases before you decide to move an order from pending, check the following.</p>
<h3>Paypal service status</h3>
<p><a href="http://www.pdncommunity.com/blog?blog.id=mts_updates">Paypal Live Site Status</a> : it is always worth keeping an eye on this page for outages and planned maintenance.</p>
<h3>Has 4 days passed?</h3>
<p>Paypal can take up to 4 days to interact with your web site with regard to any transaction. Orders are automatically sent to <em>Deleted</em> orders after 4 days via eShop. However no orders are fully deleted unless you specifically request it, so the order can be retrieved at a later date.</p>
<h3>Did you recieve a transaction notification?</h3>
<p>All successful, and most unsuccessful, orders generate a system email to your Paypal address. This email should be kept as a permanent record of the transaction. These are useful for checking outstanding transactions.</p>
<h3>Check your Paypal account</h3>
<p>Check your account at Paypal to see if there are any sales that don't tie up with anything.</p>
</div>

<div class="wrap">
<h2 id="base">eShop Base</h2>
<p>eShop Base creates a data file for upload to Google Base.</p>
<h3>Manage - eShop base Feed</h3>
<p>Download or view online your product feed for uploading to Google Base.</p>
</div>

<div id="delete-info" class="wrap">
<h2 id="extr">Cart Operations</h2>
<p>After items are added to the Cart, the customer can complete a form on the Checkout page before being  redirected to the merchant gateway for payment. A successful payment will auto generate an email to the customer <strong>and</strong> yourself. The email sent to you will have a subject containing the phrase <strong>{merchant gateway} IPN</strong>. This is a quick record of the transaction that should be kept as a permanent record. The email to the customer details their order and includes a download link, along with their login details when necessary. This download information is comprised of their email address, and a unique code. They have 'x' attempts to download a file, as set in the eShop settings.</p>
<p>If using Paypal, and providing Paypal has successfully accepted payment, a download form will be available should the customer come back to the site.</p>
<p>If the order just contains downloads it should be automatically moved to the <strong>Sent</strong> orders page, otherwise they will be sent to the <strong>Active</strong> page for delivery.</p>
</div>

<div class="wrap">
<h2 id="conf">Conflicting plugins</h2>
<p>The eShop plugin may conflict with others you have installed. If you encounter any conflicting plugins, please post their details on the <a href="http://www.quirm.net/punbb/">support forum</a>.</p>
<h3>Bad Behaviour plugin</h3>
<p>It is recommended that this plugin is not used with eShop. I am unable to test this plugin effectively as it slowed the test site down dramatically, causing it to become unusable.</p>
<p>However for those that still wish to use it, so far as I can tell there are no conflicts. But you will have to visit the <a href="https://www.Paypal.com/IntegrationCenter/ic_go-live.html">Paypal Go Live Checklist</a> and add all of the <abbr title="Internet protocol">IP</abbr> addresses to Bad Behaviour's whitelist.</p>
<h3>Sociable plugin</h3>
<p>If you use this plugin and you experience problems with eShop functionality, you may have to deactivate it. Sorry.</p>
<h3>Maintenance Mode plugin</h3>
<p>Maintenance Mode would have to be deactivated when testing eShop, otherwise Paypal can not interact with the site.</p>
<h3>WP Supercahe</h3>
<p>eShop has been amended to try and work with this cache plugin, however it will not work correctly when using widgets.</p>
<h3>Yet Another Related Posts Plugin</h3>
<p>Suggest you disable this plugin before upgrading or deactivating/reactivating eShop, as it may be the cause of some incompatibility issues.</p>
</div>

<div class="wrap">
<h2 id="comp">Compatability</h2>
<p>eShop has been written for Wordpress 2.9 and up, and is not compatible with earlier versions.</p>
<h3>Affiliates</h3>
<p>Now compatible with <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/">WP Affiliate</a> for affiliates.</p>
<h3>Shortcodes</h3>
<p>Compatible with <a href="http://wordpress.org/extend/plugins/eshortcodes/">eShortcodes</a>. (WP version 3 specific)</p>
</div>

<div class="wrap">
<h2 id="actnote">Notes on activation</h2>
<p>When eShop is activated it adds database tables, and adds data where necessary to those, and native wordpress, tables. Additionally there are some <a href="#autop">automatically created pages</a>.</p>
<p>To ensure updates to the plugin don't over write changes you have made certain directories and files are copied to new locations <strong>on the first activation only</strong>.</p>
<ul>
<li><strong>wp-content/eshop_downloads</strong> is created from plugins/eshop/downloads</li>
<li><strong>wp-content/uploads/eshop_files</strong> is created from plugins/eshop/files</li>
</ul>
<p>So, for example, when you edit your style, the actual eshop.css file that is being amended is located in <strong>wp-content/uploads/eshop_files</strong>.</p>
<p>The default pages for eShop are:</p>
<ul>
<li><strong>Shipping Rates</strong>: [eshop_show_shipping]</li>
<li><strong>Shopping Cart</strong> : <code>[eshop_show_cart]</code></li>
<li><strong>Checkout</strong> : <code>[eshop_show_checkout]</code></li>
<li><strong>Thank you for your order</strong>:  <code>[eshop_show_success]</code></li>
<li><strong>Cancelled order</strong> : <code>[eshop_show_cancel]</code></li>
<li><strong>Downloads</strong> : <code>[eshop_show_downloads]</code></li>
</ul>
<p>If you are experiencing an issue with an eShop notice telling you to deactivate/reactivate your plugin, try deactivating it, uploading an image via the WP media library, and then reactivating eShop.</p> 
</div>

<div class="wrap">
<h2 id="del">Deactivating and Uninstalling</h2>
<p>To deactivate the plugin without losing data use the <em>deactivate</em> link on the plugins page. If you want to completely uninstall the plugin, delete all associated data and files use the <strong>eShop Uninstall</strong> link from the plugins page.</p>
</div>
</div>
<?php eshop_show_credits(); ?>
