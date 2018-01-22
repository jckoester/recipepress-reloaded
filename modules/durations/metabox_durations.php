<?php
/**
 * The credits metabox view of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin/views
 */

wp_nonce_field( 'rpr_save_recipe_durations', 'rpr_nonce' );

$prep_time = get_post_meta( $recipe->ID, "rpr_recipe_prep_time", true );
$performance_time = get_post_meta( $recipe->ID, "rpr_recipe_perform_time", true );
$cook_time= get_post_meta( $recipe->ID, "rpr_recipe_cook_time", true );
$passive_time= get_post_meta( $recipe->ID, "rpr_recipe_passive_time", true );
?>

<div class="durationsbox">
    <label for="rpr_recipe_prep_time"><?php _e( 'Preparation time:', 'recipepress-reloaded' ); ?>:</label>
    <input type="number" min="0"  name="rpr_recipe_prep_time" class="rpr_time" id="rpr_recipe_prep_time" value="<?php echo $prep_time; ?>" placeholder="10" />
    <span class="recipe-general-form-notes"> <?php _e( 'min.', 'recipepress-reloaded' ) ?></span>
    
    <label for="rpr_recipe_cook_time"><?php _e( 'Cooking time:', 'recipepress-reloaded' ); ?></label>
    <input type="number" min="0"  name="rpr_recipe_cook_time" class="rpr_time" id="rpr_recipe_cook_time" value="<?php echo $cook_time; ?>" placeholder="10" />
    <span class="recipe-general-form-notes"> <?php _e( 'min.', 'recipepress-reloaded' ) ?></span>
     
    <label for="rpr_recipe_passive_time"><?php _e( 'Passive time:', 'recipepress-reloaded' ); ?></label>
    <input type="number" min="0"  name="rpr_recipe_passive_time" class="rpr_time" id="rpr_recipe_passive_time" value="<?php echo $passive_time; ?>" placeholder="10" />
    <span class="recipe-general-form-notes"> <?php _e( 'min.', 'recipepress-reloaded' ) ?></span>
    
</div>