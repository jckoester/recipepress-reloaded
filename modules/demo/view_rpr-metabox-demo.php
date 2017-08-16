<?php
/**
 * The nutritions metabox view of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin/views
 */

/**
 * @todo create and check nonce in core!
 */
wp_nonce_field( 'rpr_save_recipe_demo', 'rpr_nonce' );

$demo_value = get_post_meta( $recipe->ID, "rpr_recipe_demo_value", true );
?>

<div class="demo_box">
    <div class="recipe_details_row">
	<label for="rpr_recipe_demo_value"><?php _e( 'Demo value', 'recipepress-reloaded' ); ?>:</label>
	<input type="text" name="rpr_recipe_demo_value" id="rpr_recipe_demo_value" value="<?php echo $demo_value; ?>" />
	<span class="recipe-general-form-notes"><?php _e( 'This is for demonstration only', 'recipepress-reloaded'); ?></span>
    </div>
</div>