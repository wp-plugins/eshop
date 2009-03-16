=== eShop ===
Contributors: Rich Pedley
Donate link: http://www.quirm.net/page.php?id=39
Tags: eshop, ecommerce, shop, paypal, stock control, cart, e-commerce
Requires at least: 2.5
Tested up to: 2.6.2
Stable tag: 2.6.7

An accessible Paypal Shopping Cart plugin.

== Description ==

eShop has many features, these include:

* 1 product per page or post
* Products entered via page editing
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
* and more

Latest version now has an uninstall, allowing all eShop data to be removed from the database, and files deleted where necessary.

== Latest Updates ==

Version 2.6.7 small fixes only - many files affected.

Version 2.6.6 fixes uninstall routines.

Version 2.6.5 attempts to fix the directory creation issue.

Version 2.6.4 added new functionality and fixed a few bugs.

 = Added =
 
* CSV download for all sales.
* Ability to 'fold menus' (hide sub pages)
* Added a download only site option - this changes the checkout form to bare minimum.
* Added ability to hide shipping links
* Added ability to change automaticlly created page ids
* Products can now be added to pages or *posts*

= Fixed =

* Temporary fixed issue with image heights and widths (will not add to img tag if the image can't be found)
* bug in eshop_products/eshop_base for pages with no title.
* removed references to pluggable.php
* various minor bugs

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

= I'm upgrading from an earlier version of eShop =

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

Yes! the po file is available via quirm.net