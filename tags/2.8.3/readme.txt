=== eShop ===
Contributors: Rich Pedley
Donate link: http://quirm.net/download/
Tags: eshop, ecommerce, shop, paypal, stock control, cart, e-commerce
Requires at least: 2.5
Tested up to: 2.6.2
Stable tag: 2.8.3

An accessible Paypal Shopping Cart plugin.

== Description ==

Upgrading to 2.8. * ? please see the <a href="http://wordpress.org/extend/plugins/eshop/faq/">FAQ</a>

eShop has many features, these include:

* Use Wordpress pages, or posts, to create products
* Various methods now available for listing multiple products, with add to cart form,  on a single page
* Products can have multiple options
* Upload downloadable products
* Basic Statistics
* Download sales data
* 3 methods for calculating shipping charges, plus various zone settings via Country or US State
* Parent pages can list random products, featured products, or sub product pages (just like categories with posts)
* Admin has access to an Order handling section
* Automatic emails on successful purchase, with option to send one from the admin order handling section.
* User configurable email templates.
* Configurable Out of Stock message.
* Basic Stock Control
* Google Base Data creation
* Uninstall available within the plugin
* and more


== Latest Updates ==

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