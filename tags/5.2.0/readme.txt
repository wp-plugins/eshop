=== eShop ===
Contributors: Rich Pedley
Donate link: http://quirm.net/download/
Tags: eshop, ecommerce, shop, paypal, payson, eProcessingNetwork, Webtopay, stock control, cart, e-commerce, wpmu, authorize.net
Requires at least: 2.9
Tested up to: 3.0
Stable tag: 5.2.0

An accessible Shopping Cart plugin.

== Description ==

eShop is an accessible shopping cart plugin for WordPress, packed with various features. Including:

* Utilises WordPress pages, or posts, to create products
* Various methods available for listing products
* Products can have multiple options
* Upload downloadable products
* Basic Statistics
* Download sales data
* Various shipping options
* Admin has access to an Order handling section
* Automatic emails on successful purchase
* User configurable email templates.
* Configurable Out of Stock message.
* Basic Stock Control
* Google Base Data creation
* Uninstall available within the plugin
* Various discount options
* WPMU compatible.
* Merchant gateways:Authorize.net, Paypal, Payson, eProcessingNetwork, Webtopay, iDEAL and Cash/Cheque!
* Now compatible with WP Affiliate for affiliates (see help page).
* Compatible with eShortcodes. (WP verion 3 specific)
* and much much more

== Screenshots ==

Not currently available.

== Changelog == 

Version 5.2.0

* *added* integration with WP Affiliate.
* *added* extra help for Paypal users on the help page.
* *added* integration with eShortcodes
* *fixed* error on eShop products page
* *fixed* error with pagination for cats and tags shortcode
* *fixed* error with shipping (one price per rate)


Version 5.1.0

* *fixed* style page not updating correctly
* *added* ability to set featured product, stock and stock availability on the products quick reference page
* *added* new shortcode attribute - links, useful when used with form


Version 5.0.4

* *fixed* featured product count on stats page
* *fixed* featured products on products page
* *fixed* eshop downloads page on editing information, now stays on that page.
* *fixed* issue with deleting option sets
* *fixed* downloads can only be deleted when download is not assocaited with a product.

Version 5.0.3

* *fixed* hopefully... downloadables in orders
* *fixed* widget for featured products
* *amended* widget text for eShop cart
* *fixed* mis-spelling in help.
* *fixed* small eShop downloads page error.

Version 5.0.2

* *fixed* pagination issue when viewing all products

Version 5.0.1

* *all* anyone who installed/upgraded to version 5.0.0 will need to revist the eShop Settings page to check that things are ok
* *fixed* product image in shopping cart
* *fixed* spelling error on help page
* *fixed* options page
* *fixed* product options select box in the add to cart form
* *fixed* multiple addto cart forms on one page
* *fixed* quantity error when updating cart

Version 5.0.0

* *all* files amended and many small bug fixes implemented.
* *all* data is now stored differently in the database - please ensure you backup your database before upgrading to this version.
* *changed* method for thumbnails, this affects all users upgrading. Please use the thumbnail option on the post/page edit screen. Previous thumbnail image are _not_ used.
* *added* to help with the above a default image has been included for products with no thumbnail.
* *added* eshop_list_cat_tags shortcode, listing for products by category and tags. See help page for more details. The functionality has also been added into the the eShop product widget.

Version 4.3.2

* *fixed* 'Array' appearing in blank product entry fields..
* *added* eshop_welcome shortcode - see help page for details.

Version 4.3.1

* *fixed* admin downloads only showing one per page (oops my bad).

Version 4.3.0

**Last major update before WordPress 3 is released**

* *added* eshop hide sub pages - off by default not on (for new users only)
* *added* Option sets can now have descriptions.
* *added* optional hide shopping cart link until items in cart (does not affect widget)
* *fixed* ideallite.class - small alteration to solve some purchaseId 's being created that were too long.
* *fixed* Malaysian Ringgit changed from from "RM " to "MYR" 
* *added* note about turning stock control on - could cause all items to have 0 stock
* *added* ability to add a donotcache for WP Supercahe - should only affect eShop pages. Does not play nicely for widgets though.
* *fixed* download pagination issues
* *fixed* delete stats should now work correctly.

Version 4.2.4

* *fixed* bug with carts over SEK1000 with Payson payment gateway.
* *added* small updates for compatability with wp super cache (not fully tested)

Version 4.2.3

* *fixed* bug on stats page.

Version 4.2.1 & Version 4.2.2

* *fixed* bug in ability to create Discount Codes

Version 4.2.0

* *added* notes about auto created pages to the help page
* *added* another unique identifier for multipe option sets
* *added* {DOWNLOADS} now hiden if no downloads are present.
* *added* hide price if 0.00 for standard options.
* *added* ability to delete all orders to reset stats from the stats page.
* *added* ability to list sub page products from elsewhere for [eshop_list_subpages] only
* *amended* csv download data now separates address/city/county/zip/country
* *updated* discounts can now have decimal point values - only partially tested, use with caution.
* *updated* webtopay functionality
* *fixed* option set data fof customer emails and csv download.
* *fixed* issue with zeroing and readding same product
* *fixed* possible code injection bug in checkout.
* *fixed* csv download data
* *fixed* state/county/province in emails.

Version 4.1.1

* *updated* cart widget - ability to hide when empty.
* *updated* customer email link to highlight it more.
* *fixed* product listing image can now be chosen - in any language...
* *fixed* large downloads - may solve some time out issues.

Version 4.1.0

* *added* ability to rename cash payment option
* *added* Dutch iDEAL payment gateway
* *added* Turkish Lira, and tidied up code for that section.
* *amended* eshop install to add in $charset_collate - this may help solve some language issues (for fresh installs only).
* *amended* update/empty cart button changed - update now comes first.
* *fixed* attempted to fix a double order entry issue.
* *fixed* minor errors  - affects several files.

Version 4.0.2

* *fixed* minor bug with major issues for new option sets.

Version 4.0.1 

* *fixed* CSS class for shipping alt state.
* *fixed* State short code duplicates.

Version 4.0.0 

* *added* Option sets for all products.


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


= Does this work with Wordpress MU =

Yes.


= Is eShop translatable =

Yes! the po file is available from quirm.net

= Support =

Available via the WordPress forums (please tag the post eshop) or via http://www.quirm.net/punbb/viewforum.php?id=14

Due to increasing demands we no longer offer free CSS support.

== Upgrade Notice ==

= 5.2.0 =
Please remember to backup your database before upgrading. 