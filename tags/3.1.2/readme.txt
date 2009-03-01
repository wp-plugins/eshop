=== eShop ===
Contributors: Rich Pedley
Donate link: http://quirm.net/download/
Tags: eshop, ecommerce, shop, paypal, payson, stock control, cart, e-commerce, wpmu
Requires at least: 2.5
Tested up to: 2.7
Stable tag: 3.1.2

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
* Several payment options
* and much much more

Tested, and working, in Wordpress 2.7 and is WPMU compatible.

= Latest Update =

Version 3.1.2

*fixed* errors when product details contained quotation marks - these are now stripped out - however existing users may need to update each product that contains quotation marks in the product details.

Version 3.1.1

* *NEW* shortcode [eshop_empty_cart] to show some text when the cart is empty - see help page for details
* *NEW* ability to show all States/Counties/Provinces on the checkout form
* *Fixed* major bug on eshop products screen
* *Fixed* minor bug on email templates (clashed with other plugins)


== Latest Updates ==

Version 3.1.2

* *fixed* errors when product details contained quotation marks - these are now stripped out

Version 3.1.1

* *NEW* shortcode [eshop_empty_cart] to show some text when the cart is empty - see help page for details
* *NEW* ability to show _all_ States/Counties/Provinces on the checkout form
* *Fixed* major bug on eshop products screen
* *Fixed* minor bug on email templates (clashed with other plugins)

Version 3.1.0

* New payment option added - cash.
* better email template handling, one available for each payment option
* various back end fixes 
* *Fixed* error if eShop directories not created on install (hopefully)


Version 3.0.0

* New payment option added - Payson.
* Settings page re-organised.
* New field in checkout form - State/County/Province(if not in the list).
* Checkout now features images for the different payment options.
* New setting on shipping page - default zone for unknown State/County/Province.
* New setting to specify 'Continue Shopping' link (ie main shop page).
* Now compatible with Role Manager plugin.
* New shortcode(and widget) to display the images for the payment options.
* New multi use widget, for displaying products in sidebars.
* Fixed -Cart now checks stock levels
* Fixed - shortcode for discounts.
* small bug fixes - most files changed.
* New stylesheet for fresh installs.

Version 2.13.2

* Hawaii (HI) and Alaska (AK) - added to list of US States (sorry)
* added note to settings page about setting the default State/County/Province
* added an extra <br class="pagfoot" /> when viewing all for panel listings 
* fixed wpmu issue with css url
* fixed discount conflicts under certain circumstances

Version 2.13.1

* changed method of adding download orders to db 

New for 2.13.0

* added ability to split countries by State/County/Province - US, UK and Canada are provided bt default.
* if no State/County/Province are available then it will not appear on the checkout form.
* *FIX* download items now automatically get free shipping

New for 2.12.5 

* added ability to add product images to the shopping cart
* product items in cart now link back to the relevant product page.

New for 2.12.4

* Required upgrade for WPMU (fixes cross site cart bug)
* for WP users uploading large files - upload via FTP to the correct directory with no need to upload a small file via eShop.

New for 2.12.3 

* eShop download products are now checked against allowed quota in WPMU

Version 2.12.2

* downloads are now part of WPMU quota settings.

Version 2.12.2

* fixed bug when upgrading
* added ID column to the product listing (long overdue)

Version 2.12.1

* fixes bug in cart when shop wide discount was set.

Version 2.12.0

* Discount codes can be offered, offering a percentage discount, or free shipping.
* Downloadable product options per product (e.g. all tracks off one album, or the same track in different formats).
* eShop Stats and Order pages tidied up.
* many many tweaks to various pages

Version 2.11.5

* finally fixed phantom cart errors 
* removed need to enter a shipping rate for download only stores
* removed shipping rate displayed per product for download only stores
* tidied up some scripts

Version 2.11.4

* fixed bug in add to cart
* removed shipping reference in the checkout for download only sites.
* amendment to session handling to try and fix a phantom error for some users

Version 2.11.3

* Fixed bug for currency code when single options are displayed.

Version 2.11.2

* Added an auto check to ensure plugin is deactivated/reactivated (useful for MU users as well)
* Changes for printing number currency, can now be localised.

Version 2.11.1

* fixed error in orders page.
* renamed submenu item from eshop to 'stats'.

Version 2.11.0 

all the fetaures of the unreleased version 2.10

Version 2.10.1 ((not a general release)

* Added ability for discounts - see the help page for more information
* Added ability to only show used shipping classes on the shipping rates page - see the help page for more information
* Added ability to change eShop From address for automated emails
* Added ability to add an admin note to an order.

Version 2.10.x 

* Not available - this release was not made available, but all features are in 2.11.x


Version 2.9.3

* _All_ users will need to deactivate and activate the plugin when upgrading.
* Help file tweaked, and English corrected.
* Conflict in MySQL table creation corrected.
* Uninstall tweaks - now deletes the data it says it was!
* Small updates for Wordpress 2.7 (not yet fully tested)
* plus various minor tweaks

Version 2.9.2

Add to cart form was appearing for password protected posts now only appears once password has been entered.

version 2.9.1

Tweaking the image appearance on the search page, and adding a class 'eshop_search_img' for those that need it.
Fixed small bug for eShop only pages/posts.

version 2.9.0

* <strong>New Setting</strong> - images can now be added to the search results page
* Settings page re-organised slightly
* admin css updated
* fixed bug in image code
* updated help

Version 2.8.4

Fixes a bug in the fold menus - now works on the 404, and other, pages.

Version 2.8.3

Fixes a bug when deleting an image, eShop now recognises and resets the image.

Version 2.8.2

Fix for the Google bse feed download only.

Version 2.8.1

Fixes for admin listing of Products, and Base products.

Version 2.8.0

Back end change - all users should deactivate and then re- activate. 


Version 2.7.6

New shortcode added to show a single product, or products. Fixed error in the add to cart form.


Version 2.7.5

An error when downloading laarge files has been fixed. Also fixing the auto redirect for the checkout.

Version 2.7.4

Correcting fault in stats page

Version 2.7.3

Fixed a major bug on installation - changed file cart-functions.php

Version 2.7.2

Fixed bugs in the product listing in the admin, where multiple product entries were occassionly showing


Version 2.7.1

minor bug fix in checkout.php


Version 2.7.0 New features including:

* Widget basic Cart (number of items, plus links to cart and checkout)
* Ability to increase a orders download allowance
* Improved paypal ipn - now integrated with the pages of wordpress
* added ability to show add to cart form on listing pages...
* Ability to amend the image shown on the listing pages 

Bug Fixes 

* deleting product info now deletes correctly!


Version 2.6.7 small fixes only - many files affected.

Version 2.6.6 fixes uninstall routines.

Version 2.6.5 attempts to fix the directory creation issue.

Version 2.6.4 added new functionality and fixed a few bugs.


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

= Does this work with Wordpress MU =

When WordpressMU utilises WP2.5, then hopefully yes.

= Upgrading to 2.11.0 (and above) from earlier versions =

You need to deactivate, and then reactivate the plugin. Product data won't be lost, there is a minor update to the database.

= Upgrading to 2.8.0 (and above) from earlier versions =

You need to deactivate, and then reactivate the plugin. Product data won't be lost, but will be un-usable unless you do this.

= Upgrading from eShop 2.5 and below =

For versions prior to eShop 2.5 follow these steps:

Disable old version, and delete. If you have amended any css, or email templates or you use the downloads - you might want to keep a copy first. Upload the new plugin, and re-upload the downloads, css and email templates as required.

Go to *settings - eshop base* and **reset** eshop base. This is required to reset the data due to wordpress 2.5 utilising a new image uploader.

Go to the following pages and amend:

* Shopping Cart: remove `<!--eshop_show_cart-->` replace with `[eshop_show_cart]`
* Checkout: remove `<!--eshop_show_checkout-->` replace with `[eshop_show_checkout]`
* Thank you for your order:remove `<!--eshop_show_success-->` replace with `[eshop_show_success]`
* Cancelled Order: remove `<!--eshop_show_cancel-->` replace with `[eshop_show_cancel]`
* Downloads: remove `<!--eshop_show_downloads-->` replace with `[eshop_show_downloads]`
* Shipping rates: remove `<!--eshop_show_shipping-->` replace with `[eshop_show_shipping]`

adjust other pages that use the code: 

* `<!--eshop_list_random-->` becomes `[eshop_random_products]`
* `<!--eshop_random_product-->` becomes `[eshop_random_products list='no']`
* `<!--eshop_list_featured-->` becomes `[eshop_list_featured]`
* `<!--eshop_list_subpages-->` becomes `[eshop_list_subpages]`

= Is eShop translatable =

Yes! the po file is available from quirm.net