=== eShop ===
Contributors: Rich Pedley
Donate link: http://www.quirm.net/page.php?id=39
Tags: eshop, ecommerce, shop
Requires at least: 2.5
Tested up to: 2.5.1
Stable tag: 2.6.1

An accessible Paypal Shopping Cart plugin.

== Description ==

eShop has many features, these include:

* 1 product per page
* Products entered via page editing
* Products can have multiple options
* Upload downloadable products
* Basic Statistics
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