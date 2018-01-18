<?php
/*
Author: Jan Köster
Author Mail: dasmaeh@cbjck.de
Author URL: www.cbjck.de
Layout Name: RPR 2column
Version: 0.2
Description: A basic layoutin two columns. This layout provides all the bits you need to display proper recipes. Structured meta data for search engine come standard. </br> Use the options below to finetune the look and feel of your recipes.
*/

$printlink_class = AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'printlink_class' ), '.rpr_recipe' );
$icon_display    = AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_2column', 'icon_display' ), false );
$use_categories  = AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'category', 'use' ), false );
$display_cats    = AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'display_categories' ), false );
$use_tags        = AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'post_tag', 'use' ), false );
$display_tags    = AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'display_tags' ), false );
$custom_tax      = AdminPageFramework::getOption( 'rpr_options', array( 'tax_custom' ) );

/** 
 * Checking to see.if we are using and displaying default WP taxonomies.
 */
if ( $use_categories && $display_cats ) {
	$categories = get_the_category();
}

if ( $use_tags && $display_tags ) {
	$post_tags = get_the_tags();
}

?>

<div class="<?php echo sanitize_html_class( $printlink_class ); ?> rpr-recipe-container">
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

<div class="rpr-description-container">
	<?php
	/**
	* Display a description / excerpt /summary / abstract of the recipe 
	* if there is one
	*/
	the_rpr_recipe_description();
	?>
</div>

<?php if ( get_the_rpr_recipe_source() !== null ) { ?>
<div class="rpr-source-container">
	<?php
	/**
	* display source / citation information if available
	*/
	the_rpr_recipe_source();
	?>
</div>
<?php } ?>

<div class="columns-container">
	<div class="rpr_col1"> <!--- Start the first column -->
		<div class="rpr-ingredients-container">
			<?php
				the_rpr_recipe_ingredients_headline( $icon_display );
				the_rpr_recipe_servings( $icon_display );
				the_rpr_recipe_ingredients();
			?>
		</div>
	</div>

	<div class="rpr_col2"> <!--- Start the second column -->
		<?php if ( get_the_rpr_recipe_times() !== null ) { ?>
			<div class="rpr-times-container">
				<?php
					the_rpr_recipe_times_headline( $icon_display );
					the_rpr_recipe_times( $icon_display );
				?>
			</div>
		<?php	} ?>

		<?php if ( get_the_rpr_recipe_nutrition() !== null ) { ?>
			<div class="rpr-nutrition-container">
				<?php 
					the_rpr_recipe_nutrition_headline( $icon_display );
					the_rpr_recipe_nutrition( $icon_display );
				?>
			</div>
		<?php	}	?>

		<?php if ( $use_categories && $categories ) { ?>
			<div class="rpr-category-container">
				<?php
					if ( $display_cats ) {
					the_rpr_taxonomy_headline( 'category', $icon_display );
					the_rpr_taxonomy_terms( 'category', $icon_display, false, '/' );
					}
				?>
			</div>
		<?php	}	?>

		<?php if ( $use_tags && $post_tags ) { ?>
			<div class="rpr-tags-container">
				<?php
					if( $display_tags ) {
						the_rpr_taxonomy_headline( 'post_tag', $icon_display );
						the_rpr_taxonomy_terms( 'post_tag', $icon_display, false, '/' );
					}
				?>
			</div>
		<?php	} ?>

		<?php foreach ( $custom_tax as $tax ) { ?>
			<div class="rpr-taxonomy-container">
				<?php if( $tax[ 'slug' ] !== 'rpr_ingredient' && get_the_rpr_taxonomy_terms( $tax[ 'slug' ] ) !== '' ) {
						the_rpr_taxonomy_headline( $tax[ 'slug' ], $icon_display );
						the_rpr_taxonomy_terms( $tax[ 'slug' ], $icon_display, false, '/' );
					}
				?>
			</div>
		<?php	}	?>
	</div>
</div>

<div class="rpr-instructions-container">
	<?php
	/**
	* Instructions go below the columns.
	*/
	the_rpr_recipe_instructions_headline( $icon_display );
	the_rpr_recipe_instructions();
	?>
</div>

<?php if ( get_the_rpr_recipe_notes() !== null ) { ?>
	<div class="rpr-notes-container">
		<?php
			/**
			* Instructions notes.
			*/
		the_rpr_recipe_notes_headline( $icon_display );
		the_rpr_recipe_notes();
		?>
	</div>
<?php } ?>

<?php
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

