=== eShop ===
Contributors: elfin
Donate link: http://quirm.net/download/
Tags: eshop, ecommerce, shop, paypal, payson, eProcessingNetwork, Webtopay, ideal, stock control, cart, e-commerce, wpmu, multisite, authorize.net, cash
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 5.7.4

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

Version 5.7.5

* *added* admin short name for option sets
* *added* ability to sort featured/sale products in the shortcode randomly (use sortby='random')
* *fixed* issue with IIS and cannot redeclare add_user.
* *fixed* Item line too long error for authorize.net users
* *fixed* minor bugs
* *amended* support for older version of WordPress dropped.
* *amended* Products page, added in extra check for marking products as having stock available.
* *improved* upgrade routine - hopefully this will solve any issues when upgrading.
* *removed* eShortcodes file due to confusion - contact me if you want it back!


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

Version 5.4.2

* *fixed* CSS display issue for new users (others can add fieldset.eshoppayvia li{padding:5px;} to their css)
* *fixed* bug with CSS not being correctly parsed for multisite users
* *fixed?* possible bug with checkout

Version 5.4.1

* *fixed* bug with checkout State/County/Province sending a number to various merchant gateways
* *fixed* bug with shortcode  - eshop_cart_items

Version 5.4.0

* *added* enhancements for WP3.0, ability for buyer to sign up to the site at checkout stage. This gives the customer an order status page if they login, and ability to save address etc.
* *added* new shipping method - ship by weight, this also allows several different methods of shipping to be set.
* *changed* cart widget can now show the full cart
* *fixed* many small bugs

Version 5.3.0

* beta version not released

Version 5.2.3

* further fix for translations - this one works...

Version 5.2.3

* cleaned up install and uninstall, shouldn't be producing errors in the error log any more.
* cron notification now links to active orders page
* slight change for translations - please let me know asap if it causes an issue.

Version 5.2.2

* *fixed* major bug in checkout

Version 5.2.1

* *fixed* various small back end bugs

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

= 5.6.0 =
Please remember to backup your database before upgrading. 