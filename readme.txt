=== RecipePress reloaded===
Contributors: Jan Köster
Tags: recipes, cooking, food, recipe share
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 0.7.7
License: GPLv2

A simple recipe plugin. It does all you need for your food blog. Plus: there these nifty recipe previews in Google's search - automagically.

== Description ==

It basically adds a post type for recipes to your site. You can publish recipes as standalone posts or include in your normal posts and pages. 
Organize your recipes in categories, cuisines, courses, seasons, ... It's up to your choice how many taxonomies you are creating. Of course there are post images and all the normal wordpress post goodies for your recipes as well.
The backend is designed to allow fast, keyboard-based input and not to bother you with to many clicks and choices.
The frontend is using schema.org's recipe microformat to allow search engines like google to display our recipes nicely.

= Features =


* custom post type "recipe", made to be used alongside with other recipe plugins
* a bunch of taxonomies like cateogories, cuisines, seasons, course. You can add as many custom taxonomies as you like
* display nutritional information alongside your recipes
* options to also include categories and tags used from standard posts.
* easily include recipes into posts and pages
* shortcodes for displaying an alphabetic index of recipes, ingredients
* user comments and pingbacks
* recipe photo using featured thumbnail tools.
* easy-type backend
* schema.org's recipe microformat
* choose between templates to determine how your recipes should look like or create e template yourself
* update from the famous but discontinued plugin "RecipePress" by grandslambert

= Languages =

This plugin includes the following translations:

* English
* German
* Italian (partly)
* Hungarian (partly)

If you want a translation of another language included please help translating!



== Installation ==

1. Upload `recipe-press-reloaded` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin on the Recipes menu screen.

== Changelog ==

= 0.7.8 =
* Recipes now also appear in your main RSS feed
* Improved taxonomy management. You can now decide if a taxonomy should be category like (hierarchical) or term like.
* Improved recipe index, now dealing well with special characters
* Several bug fixes 
= 0.7.7 =
* New taxonomy 'Season'
* New taxonomy 'Difficulty' (still to be improved)
* Easier management of taxonomies
* Taxonomy initialisation improved. Now faster.
= 0.7.6 =
* Taxonomy cloud widget
* Redux-Framework is now pulled in as a dependency. Keeping it up to date is much easier this way.
* Fixed a bug in excerpt view.
= 0.7.5 =
* improved layout engine
* several bugfixes
= 0.7.4 =
* added an option to hide recipes from homepage
* fixed a problem of RPR interfering with other plugins
= 0.7.3 =
* button for the editor to include shortcodes
* exclude ingredients like 'salt' and 'pepper' from ingredient listings
* completely refurbished settings page
* media buttons for description and notes sections
* fixing a bug affecting attachment pages
= 0.7.2 =
* fixed a bug affecting foreign post types
= 0.7.1 =
* completly recoded function for querying recipes 
= 0.7.0 =
* proper excerpts for recipes
* templates can define the look & feel of excerpts
* option for placement of instruction images
* several bugfixes

= 0.6.1 =
* Bugfix release
= 0.6.0 July 19th, 2014 =
* added support for nutritional information
* choose between two templates
* rearranged settings for a better overview
* added Italian translation, thanks to [link](https://wordpress.org/support/profile/bonecruncher"Bonecruncher")

= 0.5.6 May 17th, 2014 =
* bugfix release, fixing several bugs introduced with 0.5.5

= 0.5.5 May 15th. 2014 =
* bugfix release

= 0.5.4 May 11th. 2014 =
* fixed a bug in display of times
* improved template tags
* improved display of embedded recipes

= 0.5.3 May 4th, 2014 =
* Bugfix release

= 0.5.2 April 30th, 2014 =
* improved the migration scripts

= 0.5.1 April 29th, 2014 =
* Bugfix release

= 0.5.0 April 18th, 2014 =
* completely remade the codebase, backend and frontend for better maintenance
* lighter backend for easy writing
* simpler options page
* added support for schema.org's recipe microformat
* relabeled the post-type to avoid interference with other recipe-plugins (now RPR creates posts of type "rpr-recipe" instead of "recipe")

= 0.2 - November 18th, 2012 =
* Fixed issues #1 and #2 on the RPR bugtracker (http://www.rp-reloaded.net/report-a-bug/)

= 0.1 - November 12th, 2012 =
* Initial release


== Frequently Asked Questions ==

= I've been using RecipePress by grandslambert. How can I migrate?
Just install RecipePress Reloaded. Deactivate RecipePress and activate RecipePress Reloaded. RecipePress Reloaded will have to upgrade youre database. Pleas MAKE A BACKUP before you proceed.

= I found a bug. What's next? =
Please open a thread in trhe wordpress.org support area.

= Where can I get support? =
I'm currently building up http://www.rp-reloaded.net/ as the home of RecipePress reloaded. Please be aware that I'm working on this plugin only in my rare spare time and I do it mainly for my own website. I'll do what I can to help you out but it might take some time.

== Screenshots ==

1. Simple and clean interface to type your recipes easily.
2. Sample recipe output. Find more at <a href="http://www.rp-reloaded.net/demo">http://www.rp-reloaded.net/demo</a>
3. Output is using Schema.org's microformat for recipes to allow Google to create RichSnippets
4. Details of the backend interface (ingredients). Easily add number, unit, ingredient name, comment and link. Ingredients will automatically be created as taxonomy items. You can also group ingredients.  
5. Details of the backend interface (instructions). Add instructions step by step. You can even illustrate each step with a picture. Of course istructions can be grouped as well.
6. Easily embed recipes into posts or pages using the shortcode button in the editor