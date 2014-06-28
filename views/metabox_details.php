<?php
$rating = get_post_meta( $recipe->ID, "rpr_recipe_rating", true );
$servings = get_post_meta( $recipe->ID, "rpr_recipe_servings", true );
$servings_type = get_post_meta( $recipe->ID, "rpr_recipe_servings_type", true );
$prep_time = get_post_meta( $recipe->ID, "rpr_recipe_prep_time", true );
$cook_time= get_post_meta( $recipe->ID, "rpr_recipe_cook_time", true );
$passive_time= get_post_meta( $recipe->ID, "rpr_recipe_passive_time", true );
$featured= get_post_meta( $recipe->ID, "rpr_recipe_featured", true );


?>
<script>
    function autoSuggestTag(id, type) {
        jQuery('#' + id).suggest("<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=" + type);
    }
    var plugin_url = "<?php echo $this->pluginUrl; ?>";
</script>

<div class="detailsbox">
     <!-- <div class="recipe_details_row">
     	<label for="rpr_recipe_rating"><?php _e( 'Rating', $this->pluginName ); ?></label>
     	<select name="rpr_recipe_rating" id="rpr_recipe_rating">
                <?php
                for ( $i = 0; $i <= 5; $i ++ ) {
                ?>
                <option value="<?php echo $i; ?>" <?php echo selected( $i, $rating ); ?>>
                    <?php echo $i == 1 ? $i .' '. __( 'star', $this->pluginName ) : $i .' '. __( 'stars', $this->pluginName ); ?>
                </option>
                <?php } ?>
            </select>
     </div>-->
     <div class="recipe_details_row rpr_servings">
     	<label for="rpr_recipe_servings"><?php _e( 'Servings', $this->pluginName ); ?></label>
     	<input type="number" min="0" name="rpr_recipe_servings" id="rpr_recipe_servings" value="<?php echo $servings; ?>" />
        <input type="text" name="rpr_recipe_servings_type" id="rpr_recipe_servings_type" value="<?php echo $servings_type; ?>" />
        <div class="recipe-general-form-notes" id="servings_note"> <?php _e( '(e.g. 2 people, 3 loafs, ...)', $this->pluginName ) ?></div>
     </div>
     <div class="recipe_details_row">
     	<label for="rpr_recipe_prep_time"><?php _e( 'Prep Time', $this->pluginName ); ?></label>
     	<input type="number" min="0"  name="rpr_recipe_prep_time" id="rpr_recipe_prep_time" value="<?php echo $prep_time; ?>" />
        <span class="recipe-general-form-notes"> <?php _e( 'min.', $this->pluginName ) ?></span>
     </div>
     <div class="recipe_details_row">
     	<label for="rpr_recipe_cook_time"><?php _e( 'Cook Time', $this->pluginName ); ?></label>
     	<input type="number" min="0"  name="rpr_recipe_cook_time" id="rpr_recipe_cook_time" value="<?php echo $cook_time; ?>" />
        <span class="recipe-general-form-notes"> <?php _e( 'min.', $this->pluginName ) ?></span>
     </div>
     <div class="recipe_details_row">
     	<label for="rpr_recipe_passive_time"><?php _e( 'Passive Time', $this->pluginName ); ?></label>
     	<input type="number" min="0"  name="rpr_recipe_passive_time" id="rpr_recipe_passive_time" value="<?php echo $passive_time; ?>" />
        <span class="recipe-general-form-notes"> <?php _e( 'min.', $this->pluginName ) ?></span>
     </div>
     <div class="recipe_details_row">
     	<label for="rpr_recipe_featured"><?php _e( 'Featured?', $this->pluginName ); ?></label>
     	<input type="checkbox" name="rpr_recipe_featured" id="rpr_recipe_featured" value="<?php echo $featured; ?>" />
     </div>
</div>
