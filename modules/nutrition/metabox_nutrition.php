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
// Nonce field
wp_nonce_field('rpr_save_recipe_nutrition', 'rpr_nonce_nutrition');

// Load metadata already saved in post:
$per = get_post_meta($recipe->ID, "rpr_recipe_nutrition_per", true);

$calories = get_post_meta($recipe->ID, "rpr_recipe_calorific_value", true);

$carbohydrate = get_post_meta($recipe->ID, "rpr_recipe_carbohydrate", true);
$sugar = $carbohydrate = get_post_meta($recipe->ID, "rpr_recipe_sugar", true);

$protein = get_post_meta($recipe->ID, "rpr_recipe_protein", true);

$fat = get_post_meta($recipe->ID, "rpr_recipe_fat", true);
$fat_unsaturated = get_post_meta($recipe->ID, "rpr_recipe_fat_unsaturated", true);
$fat_saturated = get_post_meta($recipe->ID, "rpr_recipe_fat_saturated", true);
$fat_trans = get_post_meta($recipe->ID, "rpr_recipe_fat_trans", true);
$cholesterol = get_post_meta($recipe->ID, "rpr_recipe_cholesterol", true);

$sodium = get_post_meta($recipe->ID, "rpr_recipe_nutrition_sodium", true);
$fiber = get_post_meta($recipe->ID, "rpr_recipe_nutrition_fiber", true);
?>

<div class="nutritionsbox">
    <div class="recipe_details_row rpr_nutrition_row rpr_nutrition_per">
        <label for="rpr_recipe_nutrition_per"><?php _e('Per', 'recipepress-reloaded'); ?>:</label>
        <select name="rpr_recipe_nutrition_per" id="rpr_recipe_nutrition_per">
            <option value="per_100g" <?php if ($per == 'per_100g') { echo 'selected'; } ?>>
                <?php _e('100g', 'recipepress-reloaded'); ?>
            </option>
            <option value="per_portion" <?php if ($per == 'per_portion') { echo 'selected'; } ?>>
                <?php _e('portion', 'recipepress-reloaded'); ?>
            </option>
            <option value="per_recipe" <?php if ($per == 'per_recipe') { echo 'selected'; } ?>>
                <?php _e('recipe', 'recipepress-reloaded'); ?>
            </option>
        </select>
    </div>
    
    
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_calories' ), true ) == true ){ ?>
    <div class="recipe_details_row">
        <label for="rpr_recipe_calorific_value"><?php _e('Calorific value:', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_calorific_value" id="rpr_recipe_calorific_value" value="<?php echo $calorific_value; ?>" />
        <span class="recipe-general-form-notes" style="margin-right:8px;">kcal</span>
        <input type="number" min="0" name="rpr_recipe_calorific_value_kj" id="rpr_recipe_calorific_value_kj" value="<?php echo round(4.18 * $calorific_value); ?>" />
        <span class="recipe-general-form-notes">kJ</span>
    </div>
    <?php } ?>
    
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_carbohydrates' ), true ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_carbohydrate">
        <label for="rpr_recipe_carbohydrate"><?php _e('Carbohydrate:', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_carbohydrate" id="rpr_recipe_carbohydrate" value="<?php echo $carbohydrate; ?>" />
        <span class="recipe-general-form-notes">g</span>
    </div>
    <?php } ?>
    
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_sugar' ), false ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_sugar">
        <label for="rpr_recipe_sugar"><?php _e('Sugar:', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_sugar" id="rpr_recipe_sugar" value="<?php echo $sugar; ?>" />
        <span class="recipe-general-form-notes">g</span>
    </div>
    <?php } ?>
    
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_protein' ), true ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_protein">
        <label for="rpr_recipe_protein"><?php _e('Protein:', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_protein" id="rpr_recipe_protein" value="<?php echo $protein; ?>" />
        <span class="recipe-general-form-notes">g</span>
    </div>
    <?php } ?>
    
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_fat' ), true ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_fat">
        <label for="rpr_recipe_fat"><?php _e('Fat:', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_fat" id="rpr_recipe_fat" value="<?php echo $fat; ?>" />
        <span class="recipe-general-form-notes">g</span>
    </div>
    <?php } ?>
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_fat_unsaturated' ), false ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_fat_unsaturated">
        <label for="rpr_recipe_fat_unsaturated"><?php _e('Fat (unsaturated):', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_fat_unsaturated" id="rpr_recipe_fat_unsaturated" value="<?php echo $fat_unsaturated; ?>" />
        <span class="recipe-general-form-notes">g</span>
    </div>
    <?php } ?>
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_fat_saturated' ), false ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_fat_saturated">
        <label for="rpr_recipe_fat_saturated"><?php _e('Fat (saturated):', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_fat_saturated" id="rpr_recipe_fat_saturated" value="<?php echo $fat_saturated; ?>" />
        <span class="recipe-general-form-notes">g</span>
    </div>
    <?php } ?>
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_fat_trans' ), false ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_fat_trans">
        <label for="rpr_recipe_fat_trans"><?php _e('Trans fat:', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_fat_trans" id="rpr_recipe_fat_trans" value="<?php echo $fat_trans; ?>" />
        <span class="recipe-general-form-notes">g</span>
    </div>
    <?php } ?>
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_cholesterol' ), false ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_cholesterol">
        <label for="rpr_recipe_cholesterol"><?php _e('Cholesterol:', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_cholesterol" id="rpr_recipe_cholesterol" value="<?php echo $cholesterol; ?>" />
        <span class="recipe-general-form-notes">mg</span>
    </div>
    <?php } ?>
    
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_sodium' ), false ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_sodium">
        <label for="rpr_recipe_sodium"><?php _e('Sodium:', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_sodium" id="rpr_recipe_fat" value="<?php echo $sodium; ?>" />
        <span class="recipe-general-form-notes">g</span>
    </div>
    <?php } ?>
    
    <?php if(AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'nutrition', 'use_fibre' ), false ) == true ){ ?>
    <div class="recipe_details_row rpr_nutrition_row rpr_fibre">
        <label for="rpr_recipe_fibre"><?php _e('Fibre:', 'recipepress-reloaded'); ?></label>
        <input type="number" min="0" name="rpr_recipe_fibre" id="rpr_recipe_fibre" value="<?php echo $fibre; ?>" />
        <span class="recipe-general-form-notes">g</span>
    </div>
    <?php } ?>
    
</div>