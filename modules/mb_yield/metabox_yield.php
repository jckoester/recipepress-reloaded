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

wp_nonce_field( 'rpr_save_recipe_yield', 'rpr_nonce_yield' );

$servings = get_post_meta( $recipe->ID, "rpr_recipe_servings", true );
$servings_type = get_post_meta( $recipe->ID, "rpr_recipe_servings_type", true );
?>

<div class="yieldbox">
    <label for="rpr_recipe_servings"><?php _e( 'Servings / yield', 'recipepress-reloaded' ); ?>:</label>
    <input type="number" min="1" name="rpr_recipe_servings" id="rpr_recipe_servings" value="<?php echo $servings; ?>" placeholder="4"/>
    <?php //if unit list is to be used:
        if( AdminPageFramework::getOption( 'rpr_options', array( 'units', 'use_serving_units') , true ) ) {?>
            <select name="rpr_recipe_servings_type" id="rpr_recipe_servings_type">
                <?php $this->the_serving_unit_selection(  $servings_type ); ?>
            </select>
        <?php // if no unit list is to be used:
        } else { ?>
            <input type="text" name="rpr_recipe_servings_type" id="rpr_recipe_servings_type" value="<?php echo $servings_type; ?>" placeholder="<?php _e( 'Portions', 'recipepress-reloaded' ); ?> "/>
        <?php } ?>
    <div class="recipe-general-form-notes" id="rpr_recipe_servings_note"> <?php _e( '(e.g. 2 people, 3 loafs, ...)', 'recipepress-reloaded' ) ?></div>
    
</div>