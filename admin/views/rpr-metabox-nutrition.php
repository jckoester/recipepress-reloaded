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
wp_nonce_field( 'rpr_save_recipe_meta', 'rpr_nonce' );

$calorific_value = get_post_meta( $recipe->ID, "rpr_recipe_calorific_value", true );
$protein = get_post_meta( $recipe->ID, "rpr_recipe_protein", true );
$fat = get_post_meta( $recipe->ID, "rpr_recipe_fat", true );
$carbohydrate = get_post_meta( $recipe->ID, "rpr_recipe_carbohydrate", true );
$per = get_post_meta( $recipe->ID, "rpr_recipe_nutrition_per", true );
?>

<div class="nutritionsbox">
	<div class="recipe_details_row">
		<label for="rpr_recipe_calorific_value"><?php _e( 'Calorific value', 'recipepress-reloaded' ); ?>:</label>
		<input type="number" min="0" name="rpr_recipe_calorific_value" id="rpr_recipe_calorific_value" value="<?php echo $calorific_value; ?>" />
		<span class="recipe-general-form-notes">kcal</span>
		<input type="number" min="0" name="rpr_recipe_calorific_value_kj" id="rpr_recipe_calorific_value_kj" value="<?php echo round( 4.18 * $calorific_value ); ?>" />
		<span class="recipe-general-form-notes">kJ</span>
	</div>

	<div class="recipe_details_row rpr_nutrition_row rpr_protein">
		<label for="rpr_recipe_protein"><?php _e( 'Protein', 'recipepress-reloaded' ); ?>:</label>
		<input type="number" min="0" name="rpr_recipe_protein" id="rpr_recipe_protein" value="<?php echo $protein; ?>" />
		<span class="recipe-general-form-notes">g</span>
	</div>
	<div class="recipe_details_row rpr_nutrition_row rpr_fat">
		<label for="rpr_recipe_fat"><?php _e( 'Fat', 'recipepress-reloaded' ); ?>:</label>
		<input type="number" min="0" name="rpr_recipe_fat" id="rpr_recipe_fat" value="<?php echo $fat; ?>" />
		<span class="recipe-general-form-notes">g</span>
	</div>
	<div class="recipe_details_row rpr_nutrition_row rpr_carbohydrate">
		<label for="rpr_recipe_carbohydrate"><?php _e( 'Carbohydrate', 'recipepress-reloaded' ); ?>:</label>
		<input type="number" min="0" name="rpr_recipe_carbohydrate" id="rpr_recipe_carbohydrate" value="<?php echo $carbohydrate; ?>" />
		<span class="recipe-general-form-notes">g</span>
	</div>

	<div class="recipe_details_row rpr_nutrition_row rpr_nutrition_per">
		<label for="rpr_recipe_nutrition_per"><?php _e( 'Per', 'recipepress-reloaded' ); ?>:</label>
		<select name="rpr_recipe_nutrition_per" id="rpr_recipe_nutrition_per">
			<option value="per_100g" <?php if( $per == 'per_100g' ){ echo 'selected';}?>><?php _e( '100g', 'recipepress-reloaded' ); ?></option>
			<option value="per_portion" <?php if( $per == 'per_portion' ){ echo 'selected';}?>><?php _e( 'portion', 'recipepress-reloaded' ); ?></option>
			<option value="per_recipe" <?php if( $per == 'per_recipe' ){ echo 'selected';}?>><?php _e( 'recipe', 'recipepress-reloaded' ); ?></option>
		</select>
		
	</div>
</div>