<?php
$calorific_value = get_post_meta( $recipe->ID, "rpr_recipe_calorific_value", true );
$protein = get_post_meta( $recipe->ID, "rpr_recipe_protein", true );
$fat = get_post_meta( $recipe->ID, "rpr_recipe_fat", true );
$carbohydrate = get_post_meta( $recipe->ID, "rpr_recipe_carbohydrate", true );
$per = get_post_meta( $recipe->ID, "rpr_recipe_nutrition_per", true );

?>

<div class="detailsbox">
	<div class="rpr_details_row">
		<label for="rpr_recipe_calorific_value"><?php _e( 'Calorific value', $this->pluginName ); ?></label>
		<input type="number" min="0" name="rpr_recipe_calorific_value" id="rpr_recipe_calorific_value" value="<?php echo $calorific_value; ?>" />
		<span class="recipe-general-form-notes">kcal</span>
	</div>
	<div class="rpr_details_row rpr_nutrition_row rpr_joule">
		<label for="rpr_recipe_calorific_value_kj">&nbsp;</label>
		<input type="number" min="0" name="rpr_recipe_calorific_value_kj" id="rpr_recipe_calorific_value_kj" value="<?php echo round( 4.18 * $calorific_value ); ?>" />
		<span class="recipe-general-form-notes">kJ</span>
	</div>
	<span class="clear">&nbsp;</span>
	<div class="rpr_details_row rpr_nutrition_row rpr_protein">
		<label for="rpr_recipe_protein"><?php _e( 'Protein', $this->pluginName ); ?></label>
		<input type="number" min="0" name="rpr_recipe_protein" id="rpr_recipe_protein" value="<?php echo $protein; ?>" />
		<span class="recipe-general-form-notes">g</span>
	</div>
	<div class="rpr_details_row rpr_nutrition_row rpr_fat">
		<label for="rpr_recipe_fat"><?php _e( 'Fat', $this->pluginName ); ?></label>
		<input type="number" min="0" name="rpr_recipe_fat" id="rpr_recipe_fat" value="<?php echo $fat; ?>" />
		<span class="recipe-general-form-notes">g</span>
	</div>
	<div class="rpr_details_row rpr_nutrition_row rpr_carbohydrate">
		<label for="rpr_recipe_carbohydrate"><?php _e( 'Carbohydrate', $this->pluginName ); ?></label>
		<input type="number" min="0" name="rpr_recipe_carbohydrate" id="rpr_recipe_carbohydrate" value="<?php echo $carbohydrate; ?>" />
		<span class="recipe-general-form-notes">g</span>
	</div>
	<span class="clear">&nbsp;</span>
	<div class="rpr_details_row rpr_nutrition_row rpr_nutrition_per">
		<label for="rpr_recipe_nutrition_per"><?php _e( 'Per', $this->pluginName ); ?></label>
		<select name="rpr_recipe_nutrition_per" id="rpr_recipe_nutrition_per">
			<option value="per_100g" <?php if( $per == 'per_100g' ){ echo 'selected';}?>><?php _e( '100g', $this->pluginName ); ?></option>
			<option value="per_portion" <?php if( $per == 'per_portion' ){ echo 'selected';}?>><?php _e( 'portion', $this->pluginName ); ?></option>
			<option value="per_recipe" <?php if( $per == 'per_recipe' ){ echo 'selected';}?>><?php _e( 'recipe', $this->pluginName ); ?></option>
		</select>
		
	</div>
</div>
