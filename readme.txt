=== B2T Courses for WooCommerce ===
Contributors: TheWebist
Tags: comments, spam
Requires at least: 6.3
Tested up to: 6.7
Stable tag: 3.6.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommerce extension that adds a "Course" product type with "Class" CPTs.

# HowTo: Show Class Registration Forms

To show the registration form for a class, the following conditions must be met in your setup:

1. Your Elementor WooCommerce Product template must use the "Product Content" widget to display the course description.
2. You must add `product-content` as the ID for the "Product Content" widget.

# Instructions for Building Translations

1. Run `grunt makepot` to generate `languages/andalu_woo_courses.pot`.
2. Copy `languages/andalu_woo_courses.pot` with your desired language extension (e.g. `languages/andalu_woo_courses-es_ES.po`).
3. Update the translations in the file you copied in #2 (use [PoEdit](https://poedit.net/) if you prefer a GUI).
4. Run `grunt po2mo` to build `.mo` files in `languages/`.

Note: The first time I ran `grunt po2mo`, I got the error `Can not create sync-exec directory`. To fix, I had to edit `node_modules\grunt-po2mo\tasks\po2mo.js` as follows:

- Line 11 Original: `var exec = require('sync-exec');`
- Line 11 Revised: `var exec = require('child_process').execSync;`

# Handlebars templating

Many of the functions in `lib/fns/shortcodes.php` use the `render_template()` function to render their HTML. That function utilizes handlebars templates stored inside `lib/templates/`. For details on how `render_template()` works, please see the DocBlock for that function inside `lib/fns/handlebars.php`.

== Changelog ==

= 3.6.0 =
* Showing "IIBA Endorsed Course" logo for any Course Product CPTs in the "IIBA" Product Category.

= 3.5.3 =
* Moving "Course Materials Shipping Note" to ACF Options Page Field.

= 3.5.2 =
* Additional styling for Course Materials note.

= 3.5.1 =
* Adding "Course Materials Shipping Address" note.

= 3.5.0 =
* Adding "Class Full Message" option. Allows admins to create an Elementor template that replaces the output shown when a class is full.
* `[class_dates_from_url]` shortcode for displaying the dates of a `course_class` when on a page that has the `course_class->post_name` as the last parameter.
* Gravity Forms merge tag for `{class_dates}`.

= 3.4.0 =
* Adding new Certification ACF Repeater Field to "Additional Class Details" meta box.

= 3.3.1 =
* Lowercasing remote repository URL.

= 3.3.0 =
* Renaming plugin to "B2T Courses for WooCommerce".

= 3.2.5 =
* Updating address labels for Class Registration.

= 3.2.4 =
* Updating links to `/course-calendar/` to point to `/services/public-class-schedule/`.

= 3.2.3 =
* Updating text above Class Registration form to include link back to the Public Class Schedule.

= 3.2.2.7 =
* Restoring use of `__()` in add to cart message.
* Removing `esc_html()` for added to cart message.

= 3.2.2.6 =
* Adding link back to Public Class Schedule without using `__()` in add to cart message.

= 3.2.2.5 =
* BUGFIX: Correcting variable spelling (`$add_text` changed to `$added_text`).

= 3.2.2.4 =
* BUGFIX: Properly escaping link in cart message.

= 3.2.2.3 =
* BUGFIX: Rewriting `Andalu_Woo_Courses_Single::add_to_cart_message()` to correctly work with the `wc_add_to_cart_message_html` filter.

= 3.2.2.2 =
* BUGFIX: Checking for product_type = 'course'.

= 3.2.2.1 =
* BUGFIX: Declaring `Andalu_Woo_Courses_Order::reduce_order_seats()` as a `static` method for PHP 8.0 compatiblity.

= 3.2.2 =
* Updating filter hook used by `Andalu_Woo_Courses_Single::add_to_cart_message()` to utilize `wc_add_to_cart_message_html`.
* Adding link to "Public Class Schedule" in the WooCommerce Message shown after adding a class registration to the cart.

= 3.2.1 =
* Vertically centering "Register" buttons in Public Class Calendar.
* Changing "Time/Duration" to "Course Length" label in Public Class Calendar.

= 3.2.0 =
* Adding "Dates" and "Location" to Course Info for class registration form.
* Updating class pricing on class registration form to display parent course pricing when no class pricing is set.

= 3.1.9 =
* Updating `add_cart_item()` to expire a product in 30 minutes instead of 15.

= 3.1.8 =
* Filtering `woocommerce_cart_item_quantity` to show "1" for the quantity value for class registrations in the WC Shopping Cart.

= 3.1.7 =
* Adding `.registration-form` to `<body>` on class registration form views.

= 3.1.6 =
* Updating styling/layout for Course Calendar display.

= 3.1.5.2 =
* BUGFIX: Checking for object before using `method_exists()`.

= 3.1.5.1 =
* Fixing required after optional param.

= 3.1.5 =
* Updating functions so that required parameters don't follow optional parameters.

= 3.1.4 =
* Updating styling for class registration form.

= 3.1.3 =
* Updating Course Calendar "Register" buttons to have `border-radius: 0`.

= 3.1.2 =
* Updating Course Calendar colors to match B2T Training branding.
* Hiding "Language" column in Course Calendar.

= 3.1.1 =
* Removing `the_content` filter from Location description because it was outputing the wrong content.
* Setting Class Calendar height to `auto` when showing a Location description.

= 3.1.0 =
* Setting `$class_price` whilst adding class to the WooCommerce Cart.
* Updating Course Info display to show Course price when `$class_price` is $0.00.
* Setting `ANDALU_DEV_ENV` and not expiring the cart whilst in dev env.

= 3.0.2 =
* BUGFIX: Reverting Course Order hook from `woocommerce_new_order_item` back to `woocommerce_add_order_item_meta` because the newer hook does not add the class data to the order.

= 3.0.1 =
* BUGFIX: Verifying `$class_price` is not empty in `get_class_pricing()`. Otherwise, class

= 3.0.0 =
* Adding per class pricing option.

= 2.9.0 =
* Saving Public Class Shortcode query in a WordPress transient.

= 2.8.7 =
* Restoring a space in the gettext string for "No Public Classes" so that the Spanish translation will show.

= 2.8.6 =
* Adding documentation for `render_template()` inside `lib/fns/handlebars.php`.

= 2.8.5 =
* Updating Handlebars `cost` variable to output HTML and reference the properties of the parent.

= 2.8.4 =
* Adding Price label to public classes.

= 2.8.3 =
* Adding `.confirmed` to Class Calendar rows.

= 2.8.2 =
* Translate View cart to ver carrito. Only ES version.

= 2.8.1 =
* Adding `show_if_simple` to `Andalu_Woo_Courses_Admin::data_tabs()` so that the "General" tab will show for WooCommerce simple products.

= 2.8.0 =
* Recompiling Spanish translations to get the "Currently, we don't have any classes..." message into `languages/andalu_woo_courses-es_ES.mo`.

= 2.7.9 =
* Checking to see if `$product` instance has an `has_classes` method inside the `[elementor_public_classes]` shortcode.

= 2.7.8 =
* ES translation error.

= 2.7.7 =
* Translate Inscribirse por Inscribir. Only ES version.

= 2.7.6 =
* Translate Register and Request Info. Only ES version.

= 2.7.5 =
* Translate CIF/NIF to DNI/NIF. Only ES version.

= 2.7.4 =
* Add slash end url 'Inscribir' button. Only ES version.

= 2.7.3 =
* Add woocommerce addon after the form.

= 2.7.2 =
* Updating zebra striping for Class Calendar rows.

= 2.7.1 =
* Removing `msgstr` from translation string.
* Adding "Certificación" to ES translation.

= 2.7.0 =
* ES translations for `[elementor_public_classes]` and `[course_details]`.

= 2.6.9.1 =
* BUGFIX: `[public_class_calendar]` was showing classes without checking the parent course's `post_status`. Fixed code to check for `publish` status.

= 2.6.9 =
* Translation strings for dates in `[public_class_calendar]` and `[elementor_public_classes]`.
* Translation strings for class registration form.
* Removing "Metro Area" from `templates/single-product/course-info.php`.

= 2.6.8 =
* BUGFIX: Checking for existence of product class methods when getting multilingual prices.

= 2.6.7 =
* Adding "Idioma", "Sub Categorías" and "Certificaciones" to Spanish translation.
* Restoring "Inglés" and "Español" to Spanish translation.

= 2.6.6 =
* Spanish translations.

= 2.6.5 =
* Adding additional Spanish text strings.

= 2.6.4 =
* Bugfix: Checking for float in `get_class_pricing()`.

= 2.6.3 =
* Left aligning "Price" column in Class Calendar.

= 2.6.2 =
* Adding line wrap for class duration listing in `[elementor_public_classes]`.
* Ensuring CSS loads for `[elementor_public_classes]` when editing in Elementor.

= 2.6.1 =
* Adding "Confirmed" check to mobile listing on Course Calendar.

= 2.6.0 =
* Adding "Confirmed" check for confirmed classes.

= 2.5.0 =
* Adding "Duration" field for Course Class.

= 2.4.3 =
* Adding Netmind Blue border around "Register" buttons in Course Calendar.

= 2.4.2 =
* Moving Course widget CSS from Instant CSS plugin to `lib/scss/_widgets.scss`.

= 2.4.1 =
* Updating styling for Course sidebar buttons.
* Adding translation strings for the Public Classes (i.e. `elementor_public_classes`) widget.

= 2.4.0 =
* Adding "Print Friendly" button to Course Details.
* Removed "Course Language" field in Course editor.
* Refactored SCSS:
  * Renamed `class-calendar.scss` to `woo-courses.scss`.
  * Added `course.css` to build for `woo-courses.scss`.
* Moved inline styles inside `lib/templates/course_details.hbs` into `lib/scss/_course.scss`.

= 2.3.0 =
* Refactoring translation setup.

= 2.2.3 =
* Including `build/languages/` in repo.

= 2.2.2 =
* Adding `show_in_rest` to Certification taxonomy so it will show in Gutenbery and the Quick Edit screen for posts.

= 2.2.1 =
* Removing unused fields in Course editor (i.e. PDUs, Intended Audience, Prerequisites, PMI Endorsement, IIBA Endorsement, Course Outlines).

= 2.2.0 =
* Adding "CIF/NIF" field for student registrations when the local is set to `es_ES`.

= 2.1.1 =
* Updating Course Calendar "Register" button :hover styling.

= 2.1.0 =
* Adding Location filters for the Class Calendar.

= 2.0.6 =
* Multilingual CSS for the Class Calendar.

= 2.0.5 =
* Updating Class Calendar CSS so rows will display properly when TranslatePress is activated.

= 2.0.4 =
* BUGFIX: Checking for array to prevent error message in class edit metabox.

= 2.0.3 =

* Moving `/assets/` to `/lib/`
* Adjusting date display format for classes.
* Adding class lanuage setting.
* Showing "General" tab for Course type products.
* Adding "Calendar Display" setting for classes.
* Initial translation setup.

= 2.0.2 =

* Adding `border-radius` to Class Calendar "Register" buttons.

= 2.0.1 =

* Checking for `$_REQUEST` variables before performing operations with them.

= 2.0.0 =

* Updated for WooCommerce 3.0+ compatibility

= 1.0.0 =

* Initial release
