# ANDA.lu Woo Courses #
**Contributors:** TheWebist  
**Tags:** comments, spam  
**Requires at least:** 4.5  
**Tested up to:** 5.6  
**Stable tag:** 2.4.1  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

WooCommerce extension that adds a "Course" product type with "Class" CPTs.

# Instructions for Building Translations

1. Run `grunt makepot` to generate `languages/andalu_woo_courses.pot`.
2. Copy `languages/andalu_woo_courses.pot` with your desired language extension (e.g. `languages/andalu_woo_courses-es_ES.po`).
3. Update the translations in the file you copied in #2 (use [PoEdit](https://poedit.net/) if you prefer a GUI).
4. Run `grunt po2mo` to build `.mo` files in `languages/`.

Note: The first time I ran `grunt po2mo`, I got the error `Can not create sync-exec directory. To fix, I had to edit `node_modules\grunt-po2mo\tasks\po2mo.js` as follows:

- Line 11 Original: `var exec = require('sync-exec');`
- Line 11 Revised: `var exec = require('child_process').execSync;`

## Changelog ##

### 2.4.1 ###
* Updating styling for Course sidebar buttons.
* Adding translation strings for the Public Classes (i.e. `elementor_public_classes`) widget.

### 2.4.0 ###
* Adding "Print Friendly" button to Course Details.
* Removed "Course Language" field in Course editor.
* Refactored SCSS:
  * Renamed `class-calendar.scss` to `woo-courses.scss`.
  * Added `course.css` to build for `woo-courses.scss`.
* Moved inline styles inside `lib/templates/course_details.hbs` into `lib/scss/_course.scss`.

### 2.3.0 ###
* Refactoring translation setup.

### 2.2.3 ###
* Including `build/languages/` in repo.

### 2.2.2 ###
* Adding `show_in_rest` to Certification taxonomy so it will show in Gutenbery and the Quick Edit screen for posts.

### 2.2.1 ###
* Removing unused fields in Course editor (i.e. PDUs, Intended Audience, Prerequisites, PMI Endorsement, IIBA Endorsement, Course Outlines).

### 2.2.0 ###
* Adding "CIF/NIF" field for student registrations when the local is set to `es_ES`.

### 2.1.1 ###
* Updating Course Calendar "Register" button :hover styling.

### 2.1.0 ###
* Adding Location filters for the Class Calendar.

### 2.0.6 ###
* Multilingual CSS for the Class Calendar.

### 2.0.5 ###
* Updating Class Calendar CSS so rows will display properly when TranslatePress is activated.

### 2.0.4 ###
* BUGFIX: Checking for array to prevent error message in class edit metabox.

### 2.0.3 ###

* Moving `/assets/` to `/lib/`
* Adjusting date display format for classes.
* Adding class lanuage setting.
* Showing "General" tab for Course type products.
* Adding "Calendar Display" setting for classes.
* Initial translation setup.

### 2.0.2 ###

* Adding `border-radius` to Class Calendar "Register" buttons.

### 2.0.1 ###

* Checking for `$_REQUEST` variables before performing operations with them.

### 2.0.0 ###

* Updated for WooCommerce 3.0+ compatibility

### 1.0.0 ###

* Initial release
