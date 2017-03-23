<?php
/*
Author: Jan Köster
Author Mail: dasmaeh@cbjck.de
Author URL: www.cbjck.de
Layout Name: RPR default
Version: 0.2
Description: The default layout. Despite being default this layout provides all the bits you need to display proper recipes. Structured meta data for search engine come standard. </br> Use the options below to finetune the look and feel of your recipes.
*/

$printlink_class  = AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'printlink_class' ), '.rpr_recipe' );
$icon_display     = AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'icon_display' ), false );
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

<div class="rpr-terms-container">
  <?php
  the_recipe_print_link(); // Display the printlink if set to do so.

  /**
  * I think it's always nice to have an overview of the taxonomies a recipe is 
  * filed under at the top:
  */
  the_rpr_taxonomy_list( $icon_display, true, '/' );
  ?>
</div>

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
    * Display source / citation information if available
    */
    the_rpr_recipe_source(); 
    ?>
  </div>
<?php } ?>

<?php if ( get_the_rpr_recipe_nutrition() !== null ) { ?>
  <div class="rpr-nutrition-container">
    <?php
    /**
    * display nutritional information if available:
    */
    the_rpr_recipe_nutrition();
    ?>
  </div>
  
<?php } ?>


<div class="rpr-ingredients-container">
  <?php
  /**
  * Ingredients section of the recipe
  * First: The headline
  */
  the_rpr_recipe_ingredients_headline( $icon_display );
  /* Second: Serving size / yield
  */
  the_rpr_recipe_servings( $icon_display );
  /*
  * Third: The ingredient list
  */
  the_rpr_recipe_ingredients();
  ?>
</div>

<?php if ( get_the_rpr_recipe_times() !== null ) { ?>
<div class="rpr-times-container">
  <?php
  /**
   * Display the recipe times bar
   */
  the_rpr_recipe_times( $icon_display );
  ?>
</div>
<?php } ?>

<div class="rpr-instruction-container">
  <?php
  /**
  * Instructions section of the recipe
  * First: the headline
  */
  the_rpr_recipe_instructions_headline( $icon_display );

  /**
   *Second: the instructions list
   */
  the_rpr_recipe_instructions();
  ?>
</div>

<?php if ( get_the_rpr_recipe_notes() !== null ) { ?>
  <div class="rpr-notes-container">
    <?php
    /**
    * Notes section of the recipe
    * First: the headline
    */
    the_rpr_recipe_notes_headline( $icon_display );
    /**
    * Second: the actual notes
    */
    the_rpr_recipe_notes( $icon_display );
    ?>
  </div>
<?php } ?>

<?php //the_rpr_recipe_notes( $icon_display ); ?>

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

