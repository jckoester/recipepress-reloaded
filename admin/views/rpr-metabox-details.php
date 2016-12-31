<?php
/**
 * The general metabox view of the plugin.
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
wp_nonce_field( 'rpr_save_recipe_meta', 'rpr_nonce' );

$servings = get_post_meta( $recipe->ID, "rpr_recipe_servings", true );
$servings_type = get_post_meta( $recipe->ID, "rpr_recipe_servings_type", true );
$prep_time = get_post_meta( $recipe->ID, "rpr_recipe_prep_time", true );
$cook_time= get_post_meta( $recipe->ID, "rpr_recipe_cook_time", true );
$passive_time= get_post_meta( $recipe->ID, "rpr_recipe_passive_time", true );
?>

<?php
/**
 * @todo: use separate fields for servings and yield?
 * like: servings: 4 portions
 * yield: 12 muffins
 */
?>

<div class="detailsbox">
	<div class="recipe_details_row rpr_servings">
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

	<div class="recipe_details_row">
     	<label for="rpr_recipe_prep_time"><?php _e( 'Prep Time', 'recipepress-reloaded' ); ?>:</label>
		<input type="number" min="0"  name="rpr_recipe_prep_time" class="rpr_time" id="rpr_recipe_prep_time" value="<?php echo $prep_time; ?>" placeholder="10" />
        <span class="recipe-general-form-notes"> <?php _e( 'min.', 'recipepress-reloaded' ) ?></span>
     </div>
     <div class="recipe_details_row">
     	<label for="rpr_recipe_cook_time"><?php _e( 'Cook Time', 'recipepress-reloaded' ); ?>:</label>
     	<input type="number" min="0"  name="rpr_recipe_cook_time" class="rpr_time" id="rpr_recipe_cook_time" value="<?php echo $cook_time; ?>" placeholder="10" />
        <span class="recipe-general-form-notes"> <?php _e( 'min.', 'recipepress-reloaded' ) ?></span>
     </div>
     <div class="recipe_details_row">
     	<label for="rpr_recipe_passive_time"><?php _e( 'Passive Time', 'recipepress-reloaded' ); ?>:</label>
     	<input type="number" min="0"  name="rpr_recipe_passive_time" class="rpr_time" id="rpr_recipe_passive_time" value="<?php echo $passive_time; ?>" placeholder="10" />
        <span class="recipe-general-form-notes"> <?php _e( 'min.', 'recipepress-reloaded' ) ?></span>
     </div>
</div>