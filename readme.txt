=== Projects by WooThemes ===
Contributors: woothemes, mattyza, jameskoster, tiagonoronha, jeffikus
Donate link: http://woothemes.com/
Tags: portfolio, projects, project, showcase, artwork, work, creative, photography, art, images, woocommerce
Requires at least: 4.0
Tested up to: 4.3.1
Stable tag: 1.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Hi, I'm your projects showcase plugin for WordPress. Use me to show off a portfolio of your latest work.

== Description ==

= Create and display a portfolio of projects =

"Projects" by WooThemes is a clean and easy-to-use portfolio showcase management system for WordPress. Load in your recent projects, and display them on a specified page using our template system, or via a shortcode, widget or template tag.

Projects integrates with our [testimonials plugin](http://wordpress.org/plugins/testimonials-by-woothemes/) allowing you to associate a testimonial with a project and display it on the single project page.

You can even assign a project to a product you've created in [WooCommerce](http://wordpress.org/plugins/woocommerce/) and let customers add it to their cart from the project page.

= Easy to use =

Publish and categorise your portfolio of projects using the familiar WordPress interface.

= Detailed project information =

Include full project details such as cover images, galleries, categories, client details and projects URLs.

= Fully integrated with WordPress =

Display your portfolio of projects using native WordPress architecture (project archives & single pages).

= Widgets & Shortcodes included =

Use Widgets & Shortcodes for situational project display.

= Mobile friendly =

Projects includes a responsive layout to integrate with any theme.

= Customisable =

Get your hands dirty! Customise & extend Projects via a vast array of hooks, filters & templates.

= WooCommerce =
Assign projects to products and give visitors an easy way to add products to their cart using [WooCommerce](http://wordpress.org/plugins/woocommerce/) functionality.

= Testimonials =

Integrated with our [testimonials plugin](http://wordpress.org/plugins/testimonials-by-woothemes/) allowing you to assign a client testimonial to a project and display it on the single project page.

= Documentation =

Extensive [documentation](http://docs.woothemes.com/documentation/plugins/projects/) to help you out if you get stuck.

== Usage ==

Once installed you can begin adding projects to your portfolio right away via the 'Projects' item in the main menu. But before you do that you should go ahead and create a page for your projects (via the Pages menu item) and specify that page as your projects base page in the projects settings. The interface for adding projects is very similar to adding posts so you should feel right at home.

For more detailed usage instructions take a look at the [Projects Documentation](http://docs.woothemes.com/documentation/plugins/projects/).

== Installation ==

Installing "Projects" can be done either by searching for "Projects by WooThemes" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org.
2. Upload the ZIP file through the "Plugins > Add New > Upload" screen in your WordPress dashboard.
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= My project images are blurry / distorted, how do I fix that? =

It's possible that your theme is scaling images to fit their container by setting `width: 100%;`. If this is the case, and your project thumbnails are too small they will become distored / blurred as they are scaled up. To fix this change the project thumbnail size options in the Projects settings then regenerate your thumbnails using the [Regenerate Thumbnails plugin](http://wordpress.org/plugins/regenerate-thumbnails/).

= How do I integrate projects into my theme? =

If you run into layout issues on Projects pages then you will need to peruse one of two methods to make your theme compatible. You can read all about this in the [documentation](http://docs.woothemes.com/document/third-party-theme-compatibility/).

= I want to change X =

Projects uses template files to control the display of it's various components. These templates can be safely editied so that your customisations are not lost during updates as described in the [documentation](http://docs.woothemes.com/document/editing-projects-templates-safely/).

= Is it possible to add cusstom fields to the Project details meta box? =

Yes! You can follow this tutorial to [add custom fields to projects](http://docs.woothemes.com/document/add-a-project-meta-field/) and display them.

= How do I contribute? =

We encourage everyone to contribute their ideas, thoughts and code snippets. This can be done by forking the [repository over at GitHub](http://github.com/woothemes/projects/).

= Can I display a tesitmonial on a project page? =

You sure can! Read about how in the [documentation](http://docs.woothemes.com/document/adding-testimonials-to-projects/).

== Screenshots ==

1. The projects base page
2. A single project page
3. The projects management screen within the WordPress dashboard.

== Upgrade Notice ==

= 1.5.0 =
* Fixes projects page not loading - please resave permalinks after updating!

= 1.4.2 =
* Removes deprecated constructor call for WP_Widget

= 1.4.0 =
* The default permalink structure has been tweaked to remove the project category. So http://localhost/network/project/landscapes/the-barrow-downs/ becomes http://localhost/network/project/the-barrow-downs/. Any old links will automatically redirect to their newer counterparts. You may have to re-save your permalink settings after performing this update. You can use the `projects_post_type_rewrite` filter to adjust this.

= 1.3.0 =
* The project category urls have been improved to use the base page title as the slug opposed to the `project-category` default. So, previously your project cateogry urls will have looked like: yoursite.com/project-category/illustration. Now, `project-category` is replaced with the slug of your projects base page. So if you're using a page called 'Portfolio' that url will now look like: yoursite.com/portfolio/illustration. You will have to create custom redirects if you want the old urls work.
* This version adds admin tabs to the settings screens, and will run an update to your existing settings data. If your data is not retained, simply resave your settings.

= 1.2.0 =
* The project archive slug now mirrors your projects base page slug isntead of being fixed as 'projects'.  If you're using a base page with a slug other than 'projects' you may want to check any static links to your projects page. For example if your base page is 'portfolio' your projects post type archive will now exist at http://yoursite.com/portfolio rather than http://yoursite.com/projects.

= 1.0.0 =
* Initial release. Woo!

== Changelog ==

= 1.5.0 =
* 2015.12.07
* New/Fix - Adds query class to modify the query to fix the Project page not loading.

= 1.4.2 =
* 2015.07.07
* Tweak - Allow attribute for include_children inside taxonomy query.
* Removes deprecated constructor call for WP_Widget

= 1.4.1 =
* 2014.12.05
* Fix - Redirect issue when setting projects page as homepage.

= 1.4.0 =
* 2014-11-11
* Fix - Project gallery thumbnail sizes in admin. (Props @corvannoorloos).
* Fix - Undefined index notice when trashing/untrashing projects. (Props @johnbuck).
* Tweak - Improvements to `projects_template_redirect()`.
* Tweak - Added `rel="lightbox"` to project galleries for compatibility with lightbox plugins.
* Tweak - Project post type default url structure no longer includes project category (resolves attachment page 404's.

= 1.3.0 =
* 2014-08-26
* New - Adds admin tabs to the settings screens for better UX.
* New - Category permalinks use base page for structure.

= 1.2.2 =
* 2014-06-06
* Tweak - Product linking label now includes sku / id.
* Tweak - Widgets now called via a single function and other minor refactoring.
* Tweak - Project Category taxonomy names filterable. (Props @abouolia)
* Fix - Reset post data in projects widget.

= 1.2.1 =
* 2014-04-24
* Fix - Javascript error in admin
* Fix - Select box save method. (Props @anija).

= 1.2.0 =
* 2014-04-09
* New - Replaced .less files with .scss.
* New - Integrated with WooCommerce.
* New - Added config.codekit.
* New - Project images can now be cropped. (Props @helgatheviking).
* New - Project archive slug is now retreived from base page slug.
* Tweak - Added css for multi column layouts when using the shortcode to display projects.
* Tweak - Project categories can now be added to navigation menus.
* Tweak - Various new input types can now be easily added using projects_custom_fields filter. (Props @helgatheviking).
* Tweak - Menu icon added in register_post_type() and now filterable. (Props @helgatheviking).
* Tweak - Added an edit media link to the project gallery meta box.

= 1.1.0 =
* 2014-03-25
* New - Integration with Testimonials plugin.
* New - dummy_data.xml containing dummy projects content.
* New - Project excerpt replaced with new short description meta box with tinymce support.
* New - Projects can now be filtered by category on the project management screen.
* Fix - Project category widget list items are now wrapped in a ul.
* Tweak - Shortcodes in project descriptions now work as expected. Shortcodes in excerpts will need to be enabled.
* Tweak - Gallery images link to full size versions. Disable with projects_gallery_link_images filter.
* Tweak - Projects Post Type / Taxonomy args are now filterable. (Props @helgatheviking).
* Tweak - Projects post type single/plural names are now filterable. (Props @helgatheviking).
* Tweak - Several UI tweaks and improvements.
* Tweak - It's now possible to add select and radio custom fields using the projects_custom_fields filter (Props @helgatheviking).

= 1.0.1 =
* 2014-02-24
* Localization - Replaces incorrect language files.
* Tweak - Localized some strings (props Vaclad)

= 1.0.0 =
* 2014-02-17
* Initial release. Woo!
