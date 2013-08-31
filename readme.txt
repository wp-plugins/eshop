=== eShop ===
Contributors: elfin
Donate link: http://quirm.net/download/
Tags: eshop, ecommerce, shop, store, estore, stock control, cart, e-commerce, wpmu, multisite, authorize.net, paypal, payson, eProcessingNetwork, Webtopay, ideal, cash, bank, tax, sale
Requires at least: 3.4
Tested up to: 3.6
Stable tag: 6.3.11
Version: 6.3.11
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
* Merchant gateways still included but no longer supported: Authorize.net, Payson, eProcessingNetwork, Webtopay, iDEAL
* Sales tax!
* Several companion plugins are now available please see [eShop wiki](http://quirm.net/wiki/eshop/).
* able to be used as a product catalogue with no sales.
* and much much more

Documentation is available via [Quirm.net](http://quirm.net/wiki/eshop/)

== Screenshots ==

Videos and screenshots available on [Quirm.net](http://quirm.net/)

== Changelog ==


Version 6.3.11

* *amended* name of a function that caused a issue with Zotpress
* *added* action eshop_order_delete has been added for use when orders are deleted
* *added* link to product on the order details page
* *FIXED* adding items to cart without going to cart page, display issue only
* *FIXED* debug errors
* *update* updated install routine for email templates - experimental.

Version 6.3.10

* *fixed* zero cost orders should now work again.
* *fixed* slashes no longer appear for download item names
* *added* warning for max weight ussage in Shipping method 4
* *added* filter to allow for larger number of top sellers to show on the dashboard
* *added* filters to change default order listing, check bottom of eshop-orders.php for the various filter names
* *added* filter to eshop_mg_process_product function
* *added* filter for subject of the customer emails, eshop_customer_email_subject
* *NOTE* Due to changes at paypal, I have been unable to test this release. If there are issues, please roll back to a previous version.

Version 6.3.9

* *fixed* issue with the unsupported gateways, thanks to iDeal these might now work.
* *fixed* issue with eShop style
* *fixed* eshop_best_sellers shortcode and widget, now only shows a product once, and not for each option
* *added* further link to the wiki in the FAQ section.

Version 6.3.8

* *removed* filter eshopproddetails, causing issues.
* *fixed* issue in multisite


Version 6.3.7

* *fixed* recopying javascript to eshop_files
* *fixed* small issue when in testmode, only the redirect page was affected for 'all' merchant gateways.
* *fixed* small issue on appearance/eshop
* *added* new filter added eshopproddetails to the eshop_rtn_order_details function.


Version 6.3.6

* *TEST* release for better jetpack fix.


Version 6.3.5

* *reverted* Fix for jetpack users was too buggy and has been removed - however, if you are using jetpack and 6.3.4 works for you - you do not need to upgrade.

Version 6.3.4

* *Fixed* Pagination issues for the shortcode: eshop_list_cat_tags
* *Possible fix* for Jetpack and Simple Facebook Connect users. __Warning__ if you have a custom merchant gateway, please see the [FAQ](http://wordpress.org/extend/plugins/eshop/faq/) before you upgrade. This is a temporary fix until a more permanent solution can be found.
* *Added* The filter `eshopaddtocheckoutlast` has been added for adding info at the bottom of the checkout.
* *amended* many files, to remove old constants.

Version 6.3.3

* *Fixed* add to cart javascript filter.
* *Fixed* wpdb>prepare issues.
* *amendment* minor change to checkout page that may help translation plugins
* *amendment* small change to Paypal, possible fix for phone number not being sent
* *possibe fix* for discounts and authorize.net
* *added* for new installs only, Berkshire was missing from the UK counties list. For existing installs, add it via the normal method.

Version 6.3.2

* *Fixed* listing issue on admin products and base pages.
* *Added* set eshopshowcartoncart filter to true to show cart widget again on cart and checkout pages
* *Fixed* names with apostrophes being sent to Paypal (note: apostrophe is currently removed)
* *Fixed* link in cron email is now correct.
* *Fixed* deprectaed notices
* *Amended* Link to new WordPress.org support forum for eShop
* *Fixed* accessibility issue on the checkout form.

Version 6.3.1

* *Added* Download allowance can now be decreased as well as increased on the order details page.
* *Fixed* discount code issue.
* *Fixed* all payments - bug with discount codes not working.
* *Fixed* deleting an order from the system now removes permission to download an item.
* *Fixed* quick fix for slashes appearing in names - a more robust solution will be in a future release.
* *Amended* cart in widget no longer shows on cart or checkout pages.
* *Removed* old cart widget.

Version 6.3.0

* *Fixed* Bug with stock quantity
* *Fixed* Widget doubling issue
* *Added* Ability to set a default Country, and State/County/Province to the shipping page.
* *Added* new filter eshop_is_shipfree added
* *Amended* Maxlength on checkout address fields, now match what the db allows.
* *removed* Summary from tables
* *Possible fix* For an edge issue with shipping not being charged
* *Added* Problem themes can are now highlighted if in use


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

= Got a question? = 

Please see the see the [eShop wiki](http://quirm.net/wiki/eshop/) before posting on the forums.

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