=== eShop ===
Contributors: elfin
Donate link: http://quirm.net/download/
Tags: eshop, ecommerce, shop, store, estore, stock control, cart, e-commerce, wpmu, multisite, authorize.net, paypal, payson, eProcessingNetwork, Webtopay, ideal, cash, bank, tax, sale
Requires at least: 3.4
Tested up to: 3.4.1
Version: 6.2.14
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

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
* Merchant gateways: Paypal and Cash/Cheque!
* Merchant gateways included but no longer supported: Authorize.net, Payson, eProcessingNetwork, Webtopay, iDEAL
* Sales tax!
* Now compatible with WP Affiliate for affiliates - see [wiki](http://quirm.net/wiki/eshop/).
* able to be used as a product catalogue with no sales.
* and much much more

Documentation is available via [Quirm.net](http://quirm.net/wiki/eshop/)

== Screenshots ==

Videos and screenshots available on [Quirm.net](http://quirm.net/)

== Changelog == 

Version 6.2.14

* *fixed* shipping address not always saving data correctly. 

Version 6.2.13

* *fixed* trailing space removed from checkout.php line 771
* *fixed* small typo in paypal.php
* *fixed* error in cart when adjusting quantity of 2nd item
* *fixed* issue displaying products with no price.
* *fixed* eShop tax issue when taxing 1 state in the USA
* *added* eshop_add_username filter for the username when creating accounts.
* *added* maxlength to checkout fields.
* *added* missed an action hook
* *added* extra CSS hooks to the continue proceed links.
* *removed* Support for other payment options withdrawn, as I am unable to test them.

Version 6.2.12

If you are having trouble switching eShop between Test and Live, please see:
http://quirm.net/wiki/eshop/troubleshooting/cant-switch-eshop-to-live/

* *fixed* contextual help error, help tab should now be created
* *fixed* Options Sets page, minor display issue
* *added* more action hooks added

Version 6.2.11

* *fixed* 	Paypal test mode fixed.
* *added* 	eshoppaypalextra filter to add hdden fields to be sent to paypal
* *added* 	classes to shipping details table.
* *amended* 	settings page, small tweaks, 2 columns are preferable, 1 column may not work as intended.

Version 6.2.10

* *fixed* 	small issue with Paypal.

Version 6.2.9

* *NEW* 	CSS file updated and tweaked for new users.
* *fixed* 	Several minor XSS - as advised by High-Tech Bridge SA Security Research Lab
* *fixed*  	sort by sku issue on products and base page.
* *fixed*  	small error with authorize.net 
* *fixed* 	admin bar showing eShop status for non admins.
* *fixed* 	minor translation issues.
* *fixed* 	issue on admin Shipping page that caused the countries to go missing
* *fixed* 	Option sets prices not being used correctly for discount purposes
* *added* 	CSS classes
* *added*	Filter added for Ogone location: ogone-location
* *changed* 	paypal.class renamed to avoid conflicts
* *changed* 	Turkish Lira changed from TL to TRY


Version 6.2.8

* *fixed*  	Activation issue, rogue space :(

Version 6.2.7

* *fixed* 	tax not being applied when shipping fields were hidden
* *added* 	signup field can now be a required field - add to the required fields using signup as the value in the filter eshopCheckoutReqd

Version 6.2.6

* *fixed* 	cart issue with deleting an item in error
* *fixed* 	small fix for authorize.net when using discount codes.
* *fixed* 	bug on checkout when not using tax.
* *amended* 	When using Shipping method 4, the first option is now automatically the default. the filer eshop_default_shipping can amend which one is the default.
* *added* 	a filter eshop_use_cookie which can be set to false if sites are having an issue with the checkout after first purchase

Version 6.2.5

* *fixed* 	now possible set an overall tax rate if using the download only form (may be extended in the future)
* *fixed* 	tax notification in emails
* *fixed* 	missing translation string for single price

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

= 6.2.9 =

Please remember to backup your database before upgrading. Please update asap.