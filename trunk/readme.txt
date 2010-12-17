=== eShop ===
Contributors: elfin
Donate link: http://quirm.net/download/
Tags: eshop, ecommerce, shop, paypal, payson, eProcessingNetwork, Webtopay, ideal, stock control, cart, e-commerce, wpmu, multisite, authorize.net, cash
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 6.0.2

An accessible Shopping Cart plugin.

== Description ==

eShop is an accessible shopping cart plugin for WordPress, packed with various features. Including:

* Utilises WordPress pages or posts, and custom post types, to create products
* enhanced for WP3.0 users - customers can sign up to your site (settable option)
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

Documentation is available via [Quirm.net](http://quirm.net/wiki/eshop/)

== Screenshots ==

Videos and screenshots available on [Quirm.net](http://quirm.net/)

== Changelog == 

Version next

* *fixed* 	small bug with listing on the downloads page.
* *fixed* 	slashes issue with option sets
* *fixed* 	eshop_welcome shortcode now picks up loged in users display name if set.
* *added* 	generic eshop-widget class to eShop widgets
* *added* 	filter for the eshop_files_directory
* *added* 	check on checkout page, if memebersonly is set as an attribute, then you can use this format to display a message: [eshop_show_checkout membersonly='yes']please show me[/eshop_show_checkout]
* *added* 	number of decimals is now translatable.

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

 
Version 5.7.9

* *Paypal Bug fixed* If using Paypal please upgrade to fix a potential security issue.
* *added* filter eshop_options_order set this to false to show option sets after the main options, but before the quantity field.

Version 5.7.8

* *fixed* authorize.net - should now work correctly?
* *fixed* missing quantity field for shortcode listings
* *added* more styling hooks
* *added* function get_eshop_product which returns an array containing product info, an page id is not passed it will try to use the current post->ID
* *added* notifications of change before upgrading for future releases.
* *added* filter for eshop_download_directory (requires full path with trailing slash)

Version 5.7.7

* *fixed* item/products in cart count was incorrect.
* *fixed* option set creation
* *fixed* option sets markup
* *fixed* upgrading from older versions - please upgrade to version 5.7.4 first if you are upgrading from 4.x
* *fixed* Sent renamed Shipping on customer order pages.
* *fixed* min/max bug in cart when readding same item

Version 5.7.6

* *added* admin short name for option sets
* *added* ability to sort featured/sale products in the shortcode randomly (use sortby='random')
* *fixed* issue with IIS and cannot redeclare add_user.
* *fixed* Item line too long error for authorize.net users
* *fixed* minor bugs
* *fixed* min/max bug in cart
* *amended* support for older version of WordPress dropped.
* *amended* Products page, added in extra check for marking products as having stock available.
* *improved* upgrade routine - hopefully this will solve any issues when upgrading.
* *improved* option set listings
* *removed* eShortcodes file due to confusion - contact me if you want it back!

Version 5.7.5

* developer release only.

Version 5.7.4

* *fixed* Authorize.net issues for PHP5 users.
* *fixed* checkout issues for some users.(mainly those hosting on IIS)
* *changed* You are now provided with a link for Google Base to pull your feed automatically.
* *changed* For new users only, the display of the 'panels' - existing users will need to amend the CSS (use the link provided on the Apperance > eShop page).
* *added* hooks for copy emails (admin and customer)
* *fixed* minor hidden bugs
* *fixed* missing translation strings

Version 5.7.3

* *fixed* product search...

Version 5.7.1 / 2

* *fixed* bug with excerpts when using listing shortcodes. The Continue reading link was pointing to wrong page.
* *fixed* admin product listing per author on multisite
* *fixed* widgets for featured and sale
* *fixed* adding thumbnails to search results.
* *fixed* minor issues
* *added* delete per item for the cart page(products can still be deleted by quantity 0)
* *added* search widget, search for products (both in stock, and out of stock) comes with an optional random product link.
* *added* filter for language files.
* *added* text to show when full cart widget is used, but nothing is in cart.
* *added/fixed* messages generated when orders refunded via paypal. message still sent, but hopefully marked as a refund.


Version 5.7.0

* *NEW* Ajax'ed add to cart with filters for success message and error messages.
* *NEW* filter added for add to cart image (eshop_theme_addtocartimg) for theme developers.
* *NEW* Products can now be marked as being On Sale, various sale CSS hooks added for theme developers.
* *NEW* Shortcode and widget option for listing products on sale.
* *NEW* Option sets can now have text and textareas.
* *NEW* filters for merchant gateway images
* *NEW* filter for default noimage
* *fixed* products admin page - fix for options+prices wrapping.
* *fixed* panel display for cat_tags shortcode.
* *fixed* State name when user sign up on checkout was not saving.
* *fixed* option quantity changes in cart.
* *fixed* bug when using the add details with incorrect information.
* *added* Some extra CSS classes for customisation.
* *amended* category and tags, shortcode and widget now also use the eshop_post_types array - useful for custom post types.

Version 5.6.6

* not released.

Version 5.6.5

* *fixed* long term issue with some people unable to install - hopefully now fixed
* *fixed* issue with single option and stock control where it was hiding details in the add to cart form
* *fixed* error with shipping not being added to cart under some circumstances.
* *updated* number formatting for other languages
* *updated* Settings page over hauled, new interface to match other WordPress pages.

Version 5.6.4

* not released.

Version 5.6.3

* *fixed* URGENT fix for shipping rates table not being created, and causing other issues.
* *fixed* missing email templates.

Version 5.6.2

* *fixed* URGENT fix for option sets, price not transferred/recorded correctly.

Version 5.6.1

* *fixed* if number of options set to 1, cart was not working - hopefully this is fixed in this release.
* *fixed* continue shopping link will by default not link to the last item added to the basket - correctly!
* *fixed* issue with product options not displaying correctly in the cart/orders/emails.
* *added* filters for custom post types. eshop_sub_page_type changs shortcode to list subpages (page by default), and eshop_post_types allows you to amend what edit screens the product entry appears on. (post and page by default)
* *amended* a few database settings (only affecting a very small proportion of users)

version 5.6.0

* *added* ability to hide the addtocart shortcode, so that it is only available to site members.
* *added* ability to set a min/max purchase quantity (affects all products)
* *added* ability for developers to test authorize.net with their test account.
* *added* New merchant gateway, duplicate of Cash, called Bank. As with Cash this can be renamed to whatever you would like.
* *added* South African Rand & Bulgarian Lev to the available currencies.
* *added* More strings that can be translated (email templates specific)
* *fixed* ability to display and use more than one add to cart form on a single product.
* *fixed* Major bug in checkout shipping calculation when shipping is set per State/County/Province
* *UPDATED* stock control has been updated and now is available per option (will not be available for option sets)
* *changed* eshop_details shortcode - stockqty is now part of options, and not a separate item, but can be hidden.
* *added* message for eShop admins only when in test mode.
* *amended* when in test mode sales are now restricted to eShop admins only.

version 5.5.8

* *added* ability to add a single price (from option 1) to every display shortcode
* *fixed* bug with some carts having 2 added instead of 1 - specifically when using Simple Facebook Connect
* *fixed* cart now updates before page is displayed which will hopefully allow the cart widget to be displayed correctly at all times.
* *changed* method of handling some strings to allow ease of amending text see [Changing Displayed Text](http://quirm.net/wiki/eshop/changing-displayed-text/)
* *amended* Discount codes reconfigured to allow use of 100% - use with care. Additionally you will need to set Allow zero cost orders.to yes.

Version 5.5.7

* *fixed* bug for all merchant gateways for values over 1000.

Version 5.5.6

* *fixed* bug for authorize.net users only.

Version 5.5.5

* *fixed* bug with add to cart form not appearing in listings.
* *fixed* Cash payment - debug info left in in last release - oops
* *fixed* minor errors for shortcodes
* *fixed* various errors for fresh installs
* *fixed* errors with eShop Base, listing and feed.
* *added* back end hooks for customisation

Version 5.5.4

* *fixed* bug in shortcode eshop_list_new
* *fixed* bug in stats for number of purchases (count may still be inaccurate for older installations)

Version 5.5.3

* *fixed* upgrades were resetting email templates - please accept my apologies.

Version 5.5.2 

* *added* ability to accept 0.00 cost orders.
* *added* link toorder detail page in the transaction email
* *fixed* several small errors
* *fixed* error with base feed.
* *changed* orders page will now show number of orders correctly 
* *amended* creation of automatic pages to use native WordPress function.
* *amended* default for visible credits is now set to no.
* *removed* help file from plugin, now resides on it's own wiki (link on the help tab for all eShop admin pages)

Version 5.5.1

* *fixed* bug with ogone merchant gateway
* *fixed* bug with eshop_details when the shortcode eshop_addtocart is used.
* *added* facility to compare your current eShop style with the version shipped within the plugin
* *added* a warning if email templates are messed up.


Version 5.5.0

* *added* ogone merchant gateway (in final stages of testing)
* *added* ability to display any and all product details in any order. Either shop wide, or on a per product basis.
* *removed* the sku, shipping rate and stock quantity from the settings page, and replaced with above. This affects everyone, but will be easy to reset up (see video on quirm).
* *amended* CSS for new installs.
* *amended* merchant gateway images can now be replaced with others of any size (warning, this includes the wdiget).
* *fixed* bug with notes for the customer not saving
* *fixed* bug with entering a 0 into the cart


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

= 5.8.0 =
Please remember to backup your database before upgrading. 