<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}
/**
 * acknowledgements.php - View for the Settings page.
 *
 * @package RecipePress Reloaded
 * @subpackage includes
 * @author dasmaeh
 * @copyright 2012
 * @access public
 * @since 1.0
 */
/* Flush the rewrite rules */
global $wp_rewrite;
$wp_rewrite->flush_rules();

global $RECIPEPRESSOBJ;
//var_dump($RECIPEPRESSOBJ);

?>
<div class="wrap">
	<div class="icon32" id="icon-edit"><br/></div>
	<h2><?php echo $this->pluginName; ?> &raquo; <?php _e('Acknowledgements', 'recipe-press-reloaded'); ?> </h2> 
	
	<h3><?php _e("Thanks to GrandSlambert!", "recipe-press-reloaded");?></h3>
	<p><?php _e("This plugin was originally created to replace the plugin &quot;RecipePress&quot; by GrandSlambert. This was discontinued for some time. That's where the name of this plugin is coming from and it's also the reason why this plugin is using a good deal of code written by GrandSlambert.", "recipe-press-reloaded");?></p>
	<p><?php _e("However, RecipePress is going to be continued now. RecipePress reloaded however is meant to become a more simple alternative.", "recipe-press-reloaded");?></p>
	<p><?php _e("Anyhow: Thanks to GrandSlambert for his great work!", "recipe-press-reloaded");?></p>
	
	<h3><?php _e("Thanks to all translators", "recipe-press-reloaded");?></h3>
	<p><?php _e("Help translate RecipePress reloaded and find your name here!", "recipe-press-reloaded");?></p>
	
	<h3><?php _e("A copyright note on the logo", "recipe-press-reloaded");?></h3>
	<p><?php _e("Please note: The logo is taken from a German traffic sign (Zeichen 376, VzKat). Therefore it is public domain and its use is not limited by the GPL.", "recipe-press-reloaded");?></p>
</div>

