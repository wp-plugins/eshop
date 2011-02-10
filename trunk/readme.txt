=== eShop ===
Contributors: elfin
Donate link: http://quirm.net/download/
Tags: eshop, ecommerce, shop, store, estore, stock control, cart, e-commerce, wpmu, multisite, authorize.net, paypal, payson, eProcessingNetwork, Webtopay, ideal, cash, bank, tax
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 6.2.2

An accessible Shopping Cart plugin.

== Description ==

eShop is an accessible shopping cart plugin for WordPress, packed with various features. Including:

* Utilises WordPress pages or posts, and compatible with custom post types, to create products
* Customers can sign up to your site (settable option)
* Various methods available for listing products
* Products can have multiple options
* Upload downloadable products
* Basic Statistics
* Download sales data
* Various shipping options, including by weight.
* Admin has access to an Order handling section
* Automatic emails on successful purchase
* User configurable email templates.
* Configurable Out of Stock message.
* Basic Stock Control
* Google Base Data creation
* Uninstall available within the plugin
* Various discount options
* WPMU, Multisite compatible.
* Merchant gateways:Authorize.net, Paypal, Payson, eProcessingNetwork, Webtopay, iDEAL and Cash/Cheque!
* Now compatible with WP Affiliate for affiliates - see [wiki](http://quirm.net/wiki/eshop/).
* able to be used as a product catalogue with no sales.
* and much much more

* *NEW* for 6.2.0 - Sales tax!

Documentation is available via [Quirm.net](http://quirm.net/wiki/eshop/)

== Screenshots ==

Videos and screenshots available on [Quirm.net](http://quirm.net/)

== Changelog == 

Version 6.2.2

* *fixed* 	Tax not calculating correctly for shipping methods 1-3 - __if you are using Sales Tax, please upgrade asap__
* *fixed* 	Shipping method 4, not saving correct shipping on error in checkout
* *fixed* 	fixed eshop details, class name appeared twice + missing information for shipping by weight
* *fixed* 	missing information for shipping by weight
* *fixed* 	apply_filters for the_title 
* *fixed* 	error in add cart
* *fixed* 	scalar value as an array error
* *fixed* 	product widget issue not showing correct amount.
* *fixed* 	checkout nor picking up saved State/County/Province
* *fixed* 	minor error with new dashboard stats
* *fixed* 	dates now hopefully picking up correct timezone.
* *updated* 	CSS when using the image button for the add to cart.
* *updated* 	Dashboard widgets.
* *updated* 	Link to order in admin email
* *added*  	Link to user on the orders page (if they signed up/signed in at time of order)
* *added* 	extras to the downloads shortcode - ability to set content and image type icons(via images='yes') 
* *added* 	filter to discounts.
* *added* 	Hide Appearance > eShop page if theme has an eshop.css

Version 6.2.1

* *fixed* 	some orders were not saved to the database correctly, this fixes it, but is not backwards compatible. (only affect those whose table prefix was not the default)
* *fixed* 	issue with discount codes on the checkout
* *fixed* 	issue with checkout and fields losing their value on error
* *fixed* 	checkout required fields for shipping method 4
* *added* 	missing spans to checkout fields for styling

Version 6.2.0

* *WARNING* 	__Back up your database before upgrading__
* *NEW* 	__Sales Tax__ can now be added. (settable per product option after 'tax bands' has been entered).
* *NEW* 	Extra Stats.
* *NEW* 	Downloads can now have 'collections'.
* *NEW* 	Sale Prices for main options, shortcodes adapted to take price = yes, sale or both.
* *FIXED/Added* Secondary address box for Paypal, finally allowing non main account email addresses to be used!
* *added* 	generic eshop-widget class to eShop widgets
* *added* 	filter for the eshop_files_directory
* *added* 	check on checkout page, if memebersonly is set as an attribute, then you can use this format to display a message: [eshop_show_checkout membersonly='yes']please show me[/eshop_show_checkout]
* *added* 	number of decimals is now translatable.
* *added* 	extra CSS hooks to checkout confirmation page.
* *added* 	action eshop_sold_product which is sent the post id of the product that was sold.
* *added* 	Shipping by weight now allows you to choose between states/counties/province and countries per mode, and add in a max weight for each.
* *added* 	missing strings for translations
* *added* 	classes for styling in various places
* *fixed* 	small bug with listing on the downloads page.
* *fixed* 	slashes issue with option sets
* *fixed* 	eshop_welcome shortcode now picks up loged in users display name if set.
* *fixed* 	slashes appearing in emails.(hopefully)
* *fixed* 	eshop_details shortcode now functions correctly.
* *fixed* 	checkout losing chosen State/County/Province, props VK.
* *fixed* 	many small bugs.
* *changed*	renaming of eshop.css to eshop-admin.css to avoid confusion, and many styles tweaked.
* *changed*	About page changed.
* *changed* 	Discount Codes are no longer case sensitive
* *changed* 	removed _ from filenames and replaced with -, easier for developers.
* *changed* 	eShop stats are now on the main WP Dashboard, only available for eShop admins.
* *Note* 	after upgrading you may need to refresh the page due to the change above.

__Note__ This version has been tested with Paypal, Cash, Bank, Webtopay and Authorize.net merchant gateways, and all seemed to work OK in test mode. The other gateways have not had a full test, but should work without issue.

Version 6.1.0

* *Development Version only*

Version 6.0.2

* *fixed* 	issue with Shipping Method's clearing all values.
* *fixed* 	errors when allowing user to sign up to your site from the checkout.
* *added* 	version 6 broke the renaming of shipping zones, a new method has therefore been created, see the [wiki](http://quirm.net/wiki/eshop/changing-displayed-text/#Renaming-Shipping-Zones)

Version 6.0.1

* *added/fixed* 	eship_list_subpages shortcode, new attribute depts, set to yes by default to show department pages - if a featured image is chosen for that page it will show that as well. To hide them set it to no.

Version 6.0.0

* *NEW* 	shipping zones, number utilised can now be set between 1 & 9.
* *NEW* 	added outofstock attribute to some listing shortcodes - you can now decide whether to show out of stock items in the listings or not
* *added*  	action, eshop_copy_admin_order_email, for cc'ing that email.
* *added*	filter, eshop_add_ref_feed, for adding a reference to links in feed for affiliates. 
* *added* 	extra classes for styling
* *fixed* 	for users of WP Replicator who were unable to access eShop menu's - hopefully.
* *fixed* 	issue with some shortcodes and widgets showing incorrect number of products.
* *fixed* 	small fix for cancelled orders and discount codes.
* *fixed* 	small fix in admin for product tables.

Version 5.9.3

* *FIXED* 	Issue with Paypal and failed orders - at long last! (hopefully...)
* *fixed* 	changes for multisite users who were unable to use some admin functionality
* *fixed* 	issue with a-z shortcode

Version 5.9.2

* *amended* 	if you have are _Allowing users to sign up to your site_ via eShop settings, then you need to upgrade - apologies.
* *amended* 	small change to cart, adding in hooks for styling

Version 5.9.1

* *fixed*  	upgrade routine causing failed upgrades...

Version 5.9.0

* *AMENDED* 	labels and code on checkout page, to reset things as they appeared before, add this to your style: .fld2 label, .fld4 label{display:block;} May affect customised style as well.
* *amended* 	security fix for Users orders page.
* *amended* 	eshop_details removed stockqty from _what to show_, and added to _what to hide_. Also removed _option_ from what to hide, as it caused errors.
* *amended* 	small tweaks to Paypal error notifications.
* *fixed* 	issue with option sets.
* *fixed* 	product listing with forms when set to 'yesqty'
* *fixed* 	number of results returned by cat tags shortcode.
* *fixed* 	issue with eshop_details shortcode causing details to appear twice.
* *fixed* 	Webtopay, now works solely for Webtopay verion 1.3 - please check the settings page.
* *fixed* 	Small bug with shortcodes.
* *fixed* 	more validation issues.
* *added* 	filter for amending the option set display order.
* *added* 	filters and actions required for merchant gateway plugins.
* *added* 	option set names for all now show in the cart, not just text and textareas.
* *added* 	classes to shipping table to enable hiding of unused shipping zones
* *added* 	filter for eShop Shipping Classes (eshop_shipping_rate_class).
* *added* 	ability to hide reference/po number from checkout via class eshopreference
* *added* 	to the cart widget - ability to show image, text, or both. Image is also resizeable.

Version 5.8.2

* *fixed*   eShop > Base page error.
* *changed* handling of products with 0 cost.

Version 5.8.1

* *fixed*    incorrect back link appearing on checkout page
* *fixed*    option set prices not being correctly identified for some configurations.
* *fixed*    character set and collation on database tables.
* *updated*  portion of the paypal validation script that may have affected a small proportion of users.
* *updated*  shortcodes, when using panels changing form value to 'yesqty' will now also show the qty field.
* *updated*  add to cart, drop down select/radio buttons now only show if there are more than 1 option.
* *added*    filter eshop_states_na, to remove the not applicable from the state/county/province drop down box.
* *added*    class per product to enable targetting of specific products for styling.
* *added*    filters to the list cat tags shortcode.
* *added*    improved cart widget, old cart widget remains for now. This also features a total price for the cart.
* *added*    filter for the ajax timings.
* *improved* activation routine, only affected a few people.
* *improved* upgrade routine for those upgrading manually.
* *amended*  default listing for the admin order pages.


== Installation ==

Download the plugin, upload to your Wordpress plugins directory and activate. Further instructions and help are included. the plugin automatically creates certain pages for you:
The plugin automatically creates 6 pages for you, all of which are editable.

* Shopping Cart
* Checkout
* Thank you for your order
* Cancelled Order
* Downloads
* Shipping rates

You then need to create a top level shop page, and start creating departments and entering products!

== Frequently Asked Questions ==

= Yet Another Related Posts Plugin =
If you are using this plugin I recommend you disable it before upgrading or deactivating/reactivating eShop, as it may be the cause of some incompatibility issues.


= Does this work with Wordpress MultiSite =

Yes - but do not activate the plugin via the network activate link. eShop needs to run the activation process for each and every site within your network. This is currently not done when you activate a plugin for the entire network. Enable the plugin for the sites to activate themselves instead.

= I updated and now things don't look right = 

There is always a possibility that necessary CSS changes have been made, always remember to check the _Appearance > eShop_ page where you will be notified if they do not match, and be given an opportunity to view the differences.

= Is eShop translatable =

Yes! the po file is available from Quirm.net [eshop.po download](http://quirm.net/download/26/)

= Support =

Available via the WordPress forums (please tag the post eshop) or via [Quirm.net forums](http://quirm.net/forum/forum.php?id=14)

Due to increasing demands we no longer offer free CSS support.

== Upgrade Notice ==

= 6.2.2 =

Please remember to backup your database before upgrading - If you are using Sales Tax please upgrade asap.