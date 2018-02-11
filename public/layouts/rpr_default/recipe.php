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
 * Usually the theme will display the recipe image. We only do it here, when
 * the recipe is embedded  into something else
 */
if (recipe_is_embedded() ){
 the_rpr_recipe_image();
}
?>

<div class="rpr-terms-container">
  <?php
  if( function_exists( 'the_recipe_print_link' ) ){
    the_recipe_print_link(); // Display the printlink if set to do so.
  }
  
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
  * (this template tag should always be accessible)
  */
  if(function_exists( 'the_recipe_description' ) && get_the_rpr_recipe_description() != null ){
    the_rpr_recipe_description();
  }
  ?>
</div>

<?php if (function_exists( 'the_rpr_recipe_credit' ) && get_the_rpr_recipe_credit() != null ) { ?>
  <div class="rpr-source-container">
    <?php
    /**
    * Display source / citation information if available
    */
    the_rpr_recipe_credit(); 
    ?>
  </div>
<?php } ?>

<?php if ( function_exists( 'the_rpr_recipe_nutrition' ) && get_the_rpr_recipe_nutrition() != null ) { ?>
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
   * we do rely on the ingredients module being active as there can't be
   * recipes without ingredients
  * First: The headline
  */
  the_rpr_recipe_ingredients_headline( $icon_display );
  /* Second: Serving size / yield
  */
  if(function_exists( 'the_rpr_recipe_servings' ) && get_the_rpr_recipe_servings() != null ){
    the_rpr_recipe_servings( $icon_display );
  }
  /*
  * Third: The ingredient list
  */
  the_rpr_recipe_ingredients();
  ?>
</div>

<?php if ( function_exists( 'the_rpr_recipe_times' ) && get_the_rpr_recipe_times() != null ) { ?>
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
   *we do rely on the instructions module being active as there can't be
   * recipes without instructions
  * First: the headline
  */
  the_rpr_recipe_instructions_headline( $icon_display );

  /**
   *Second: the instructions list
   */
  the_rpr_recipe_instructions();
  ?>
</div>

<?php if( function_exists( 'the_rpr_recipe_notes' ) && get_the_rpr_recipe_notes() != null ) { ?>
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

</div>	
<!-- 
________________________________________________________________________________
THIS IS OLD STUFF															  || 
                                        \/
-->
  
<!--<script>
  /**
   * 
   * @todo: What is this needed for?
   */
var rpr_pluginUrl = '<?php //echo $this->pluginUrl; ?>';
var rpr_template = '<?php echo AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'layout' ), 'rpr_default' );  ?>';
</script>
-->
