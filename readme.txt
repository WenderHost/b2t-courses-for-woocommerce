=== ANDA.lu Woo Courses ===
Contributors: TheWebist
Tags: comments, spam
Requires at least: 4.5
Tested up to: 5.5.1
Stable tag: 2.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

WooCommerce extension that adds a "Course" product type with "Class" CPTs.

# Instructions for Building Translations

1. Run `grunt makepot` to generate `build/languages/andalu_woo_courses.pot`.
2. Copy `andalu_woo_courses.pot` to `src/languages/` with your desired language extension (e.g. `andalu_woo_courses-es_ES.po`).
3. Update the translations in the file you copied in #2 (might want to use PoEdit.app).
4. Run `grunt po2mo` to build `.mo` files in `build/languages/`.

Note: The first time I ran `grunt po2mo`, I got the error `Can not create sync-exec directory. To fix, I had to edit `node_modules\grunt-po2mo\tasks\po2mo.js` as follows:

- Line 11 Original: `var exec = require('sync-exec');`
- Line 11 Revised: `var exec = require('child_process').execSync;`

== Changelog ==

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
