=== RecipePress reloaded===
Contributors: Jan Köster
Tags: recipes, cooking, food, recipe share
Requires at least: 3.4
Tested up to: 3.9
Stable tag: 0.3
License: GPLv2

A simple recipe plugin. It does all you need for your food blog. Plus: there these nifty recipe previews in Google's search - automagically.

== Description ==

It basically adds a post type for recipes to your site. You can publish recipes as standalone posts or include in your normal posts and pages. 
Organize your recipes in categories, cuisines, courses, seasons, ... It'S up to your choice how many taxonomies you are creating. Of course there are post images and all the normal wordpress post goodies for your recipes as well.
The backend is designed to allow fast, keyboard-based input and not to bother you with to many clicks and choices.
The frontend is using schema.org's recipe microformat to allow search engines like google to display our recipes nicely.

= Features =

* update from the famous but discontinued plugin "RecipePress" by grandslambert
* custom post type "recipe", made to be used alongside with other recipe plugins
* a bunch of taxonomies like cateogories, cuisines, seasons, course. You can add as many custom taxonomies as you like
* options to also include categories and tags used from standard posts.
* user comments and pingbacks
* recipe photo using featured thumbnail tools.
* easy-type backend
* schema.org's recipe microformat

= Languages =

This plugin includes the following translations:

* English
* German
If you want a translation of another language included please help translating!

= Future Features =

* shortcodes for displaying an alphabetic index of recipes, ingredients, ...


== Installation ==

1. Upload `recipe-press-reloaded` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin on the Recipes menu screen.

== Changelog ==

= 0.5.3 May 4th, 2014
* Bugfix release

= 0.5.2 April 30th, 2014
* improved the migration scripts

= 0.5.1 April 29th, 2014
* Bugfix release

= 0.5.0 April 18th, 2014
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
