<?php
/*
Author: Jan Köster
Author Mail: dasmaeh@cbjck.de
Author URL: www.cbjck.de
Layout Name: RPR 2column
Version: 0.2
Description: A basic layoutin two columns. This layout provides all the bits you need to display proper recipes. Structured meta data for search engine come standard. </br> Use the options below to finetune the look and feel of your recipes.
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
 * Start the first column
 */
?>
	<div class="rpr_col1">
		<?php
		the_rpr_recipe_ingredients_headline( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) ); 
		the_rpr_recipe_servings( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
		the_rpr_recipe_ingredients();
		?>
	</div>
<?php 
/**
 * Start the second column
 */
?>
	<div class="rpr_col2">
		<?php
			if( get_the_rpr_recipe_times() != '' ) {
				the_rpr_recipe_times_headline( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
				the_rpr_recipe_times( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
			}
		?>
		
		<?php
			if( get_the_rpr_recipe_nutrition() != '' ) {
				the_rpr_recipe_nutrition_headline( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
				the_rpr_recipe_nutrition( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
			}
		?>
	
		<?php
			if( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'categories', 'use' ), false ) ){
				if( AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'display_categories' ), false ) ){
					the_rpr_taxonomy_headline( 'category', AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
					the_rpr_taxonomy_terms( 'category', AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false, false ) );
				}
			}
		?>
		<?php
			if( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'post_tag', 'use' ), false ) && $tags ){
				if( AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'display_tags' ), false ) ){
					the_rpr_taxonomy_headline( 'post_tag', AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
					the_rpr_taxonomy_terms( 'post_tag', AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false, false ) );
				}
			}
		?>
		<?php foreach( AdminPageFramework::getOption( 'rpr_options', array( 'tax_custom' ) ) as $tax ){
			if( $tax['slug'] != 'rpr_ingredient' && get_the_rpr_taxonomy_terms( $tax['slug'] ) != '' ){
				the_rpr_taxonomy_headline( $tax['slug'], AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
				the_rpr_taxonomy_terms( $tax['slug'], AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false, false ) );
			}
		}
		?>
	</div>
	<div class="rpr-clear"></div>
<?php
/**
 * Instructions go below the columns
 */

the_rpr_recipe_instructions_headline( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
the_rpr_recipe_instructions();

the_rpr_recipe_notes_headline( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false ) );
the_rpr_recipe_notes();

/**
 * Close the structurted data container
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
var rpr_pluginUrl = '<?php //echo $this->pluginUrl; ?>';
var rpr_template = '<?php //echo $this->option( 'rpr_template', 'rpr_default' ); ?>';
</script>

