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

/**
 * Get all availble fields (defined in templat_tags.php
 */
require_once 'nutrition_data.php';
$nutridata = get_the_rpr_recipe_nutrition_fields();

// Load metadata already saved in post:
$per = get_post_meta($recipe->ID, "rpr_recipe_nutrition_per", true);

foreach ($nutridata as $key => $value ){
    $nutridata[$key]['value'] = get_post_meta($recipe->ID, $value['dbkey'], true);
}

/**
 * Create the meta box
 */return
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
        <label for="rpr_recipe_calorific_value"><?php echo $nutridata['calories']['label']; ?></label>
        <input type="number" min="0" name="rpr_recipe_calorific_value" id="rpr_recipe_calorific_value" value="<?php echo $nutridata['calories']['value']; ?>" />
        <span class="recipe-general-form-notes" style="margin-right:8px;">kcal</span>
        <input type="number" min="0" name="rpr_recipe_calorific_value_kj" id="rpr_recipe_calorific_value_kj" value="<?php echo round(4.18 * $nutridata['calories']['value']); ?>" />
        <span class="recipe-general-form-notes">kJ</span>
    </div>
    <?php } ?>
<?php
unset($nutridata['calories']);
/** 
 * Loop through all nutrition fields and create inputs
 */
foreach ($nutridata as $key => $value ){
    $out = '';
    if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata_nutrition', 'nutrition_use_'.$key ), true ) == true ){
        $out .= '<div class="recipe_details_row rpr_nutrition_row rpr_' . $key . '">';
        $out .= '<label for="rpr_recipe_' . $key . '">' . $value['label'] . '</label>';
        $out .= '<input type="number" min="0" name="rpr_recipe_' . $key . '" id="rpr_recipe_' . $key . '" value="' . $value['value'] . '" />';
        $out .= '<span class="recipe-general-form-notes">' . $value['unit'] . '</span>';
        $out .= '</div>';
    }
    echo $out;
}
?>  
</div>