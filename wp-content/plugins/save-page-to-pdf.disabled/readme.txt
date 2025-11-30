=== Save Page to PDF ===
Contributors: api2pdf
Donate link: http://www.api2pdf.com
Tags: pdf, wkhtmltopdf
Requires at least: 4.1
Tested up to: 4.9.8
Requires PHP: 5.2.4
License: MIT
License URI: https://opensource.org/licenses/MIT

Allows visitors of your website to download the current page as a PDF. 

== Description ==

WordPress Plugin Code for [Api2Pdf REST API](https://www.api2pdf.com/documentation)

Api2Pdf.com is a REST API for instantly generating PDF documents from HTML, URLs, Microsoft Office Documents (Word, Excel, PPT), and images. The API also supports merge / concatenation of two or more PDFs. Api2Pdf is a wrapper for popular libraries such as **wkhtmltopdf**, **Headless Chrome**, and **LibreOffice**.

This plugin adds a Save to PDF functionality to WordPress.  It will take the HTML contents of your page, convert it to PDF, and request the web browser to download it.

The plugin will add the shortcode **savepagetopdf** and also the javascript function **window.SavePageToPdf()**

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/savePageToPdf` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->PDF Settings to configure the plugin
4. You will need a valid Api2Pdf.com API Key.  You can create a key here: [api2pdf.com](https://portal.api2pdf.com/register)


== Frequently Asked Questions ==

= How do I include a link or button to generate a PDF? =

You can use the shortcode **[savepagetopdf]**, this will generate a link that will generate a PDF of the current page.  You can style this link by modifying the CSS class *savePageToPdf***

= What if I wish to include PDF Generation via my Theme, for example via a header widget? =

You can set the onclick event of any HTML elevent, for example <a href="javascript:void(0);" onclick="window.SavePageToPdf();">Make me a PDF</a>

== Changelog ==

= 1.1 =
* Fix naming convention