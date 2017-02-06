<?php
/*
Author: Jan Köster
Author Mail: dasmaeh@cbjck.de
Author URL: www.cbjck.de
Layout Name: RPR default
Version: 0.2
Description: The default layout. Despite being default this layout provides all the bits you need to display proper recipes. Structured meta data for search engine come standard. </br> Use the options below to finetune the look and feel of your recipes.
*/
?>
<div class="<?php echo sanitize_html_class( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'printlink_class' ), '.rpr_recipe') ); ?>">
<?php
/** 
 * Displaying the recipe title is normally done by the theme as post_title().
 * However, if the recipe is embedded, we need to do it here.
 */
if( recipe_is_embedded() ){ ?>
	<h2 class="rpr_title"><?php echo get_the_title( $recipe_post ); ?></h2>
<?php } ?>

<?php
/**
 * Display the printlink if set to do so
 */
the_recipe_print_link();
?>
<?php
/**
 * First thing we 'display' is the structured data header, so search engines
 * know this is a recipe:
 */
the_rpr_structured_data_header();
?>

<?php
/**
 * Usually the theme will display the recipe image. If it doesn't we should do 
 * it here, either set by option, or because we have a recipe embedded 
 * somewhere else
 */
the_rpr_recipe_image();
?>
	
<?php
/**
 * Also the author and the date should be displayed by the theme. But we can 
 * also do it here if necessary and if we want to. After all this is design!
 */
the_rpr_recipe_author();
the_rpr_recipe_date();
?>

<?php
/**
 * I think it's always nice to have an overview of the taxonomies a recipe is 
 * filed under at the top:
 */
the_rpr_taxonomy_list( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'icon_display' ), false ) );
?>
	<span class="rpr-clear">&nbsp;</span>
<?php
/**
 * Display a description / excerpt /summary / abstract of the recipe 
 * if there is one
 */
the_rpr_recipe_description();
?>

<?php
/**
 * display nutritional information if available:
 */
the_rpr_recipe_nutrition();
?>
<?php
/**
 * Ingredients section of the recipe
 * First: The headline
 */
the_rpr_recipe_ingredients_headline( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'icon_display' ), false ) );
/* Second: Serving size / yield
 */
the_rpr_recipe_servings( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'icon_display' ), false ) );
/*
 * Third: The ingredient list
 */
the_rpr_recipe_ingredients();
?>

<?php
/**
 * Instructions section of the recipe
 * First: the headline
 */
the_rpr_recipe_instructions_headline( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'icon_display' ), false ) );
/* Second: the times
 */
the_rpr_recipe_times( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'icon_display' ), false ) );
/* Third: the instructions list
 */
the_rpr_recipe_instructions();
?>
	
<?php
/**
 * Notes section of the recipe
 * First: the headline
 */
the_rpr_recipe_notes_headline( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'icon_display' ), false ) );
/**
 * Second: the actual notes
 */
the_rpr_recipe_notes( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'icon_display' ), false ) );
?>

<?php
/**
 * Last thing to render is the structured data footer, the end of the recipe
 * not only for search engines:
 */
the_rpr_structured_data_footer();
?>

</div>	
<!-- 
________________________________________________________________________________
THIS IS OLD STUFF															  || 
																			  \/
-->
	
<script>
	/**
	 * 
	 * @todo: What is this needed for?
	 */
var rpr_pluginUrl = '<?php //echo $this->pluginUrl; ?>';
var rpr_template = '<?php echo AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'layout' ), 'rpr_default' );  ?>';
</script>

