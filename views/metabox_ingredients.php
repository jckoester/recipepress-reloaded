<?php 
$ingredients = get_post_meta( $recipe->ID, "rpr_recipe_ingredients", true );


$ingredient_link = RPReloaded::get_option('recipe_ingredient_links');
$has_link=false;
?>
<script>
    function autoSuggestTag(id, type) {
        jQuery('#' + id).suggest("<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=" + type);
    }
    var plugin_url = "<?php echo $this->pluginUrl; ?>";
</script>
<input type="hidden" name="rpr_recipe_ingredients_meta_box_nonce" value="<?php echo wp_create_nonce('recipe-ingredients'); ?>" />

<table id="recipe-ingredients">
    <thead>
    <tr class="ingredient-group ingredient-group-first">
        <td>&nbsp;</td>
        <td><strong><?php _e( 'Group', $this->pluginName ); ?>:</strong></td>
        <td colspan="2">
            <span class="ingredient-groups-disabled"><?php echo __( 'Main Ingredients', $this->pluginName ) . ' ' . __( '(this label is not shown)', $this->pluginName ); ?></span>
            <?php
            $previous_group = '';
            if( isset($ingredients[0]) && isset( $ingredients[0]['group'] ) ) {
                $previous_group = $ingredients[0]['group'];
            }
            ?>
            <span class="ingredient-groups-enabled"><input type="text" class="ingredient-group-label" value="<?php echo $previous_group; ?>" /></span>
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    </thead>
    <tbody>
    <tr class="ingredient-group-stub">
        <td>&nbsp;</td>
        <td><strong><?php _e( 'Group', $this->pluginName ); ?>:</strong></td>
        <td colspan="2"><input type="text" class="ingredient-group-label" /></td>
        <td>&nbsp;</td>
        <td class="center-column"><span class="ingredient-group-delete"><img src="<?php echo $this->pluginUrl; ?>/img/minus.png" width="16" height="16"></span></td>
    </tr>
<?php
$i = 0;
if( $ingredients != '')
{
    foreach($ingredients as $ingredient) {

        if( isset( $ingredient['ingredient_id'] ) ) {
            $term = get_term($ingredient['ingredient_id'], 'ingredient');
            if ( $term !== null && !is_wp_error( $term ) ) {
                $ingredient['ingredient'] = $term->name;
            }
        }

        if( isset( $ingredient['group']) &&  $ingredient['group'] != $previous_group ) { ?>
            <tr class="ingredient-group">
                <td>&nbsp;</td>
                <td><strong><?php _e( 'Group', $this->pluginName ); ?>:</strong></td>
                <td colspan="2"><input type="text" class="ingredient-group-label" value="<?php echo $ingredient['group']; ?>" /></td>
                <td>&nbsp;</td>
                <td class="center-column"><span class="ingredient-group-delete"><img src="<?php echo $this->pluginUrl; ?>/img/minus.png" width="16" height="16"></span></td>
            </tr>
<?php
            $previous_group = $ingredient['group'];
        }
        if( isset($ingredient['link']) && $ingredient['link'] !="" )
        {
        	$has_link = true;
        }
        else
        {
        	$has_link = false;
        }
?>
    <tr class="ingredient">
        <td class="sort-handle fa fa-arrows-v"></td>
        <td><input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][amount]"     class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>" value="<?php echo $ingredient['amount']; ?>" /></td>
        <td><input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][unit]"       class="ingredients_unit" id="ingredients_unit_<?php echo $i; ?>" value="<?php echo $ingredient['unit']; ?>" /></td>
        <td><input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" onfocus="autoSuggestTag('ingredients_<?php echo $i; ?>', 'rpr_ingredient');"  value="<?php echo $ingredient['ingredient']; ?>" /></td>
        <td>
            <input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][notes]"      class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" value="<?php echo $ingredient['notes']; ?>" />
            <input type="hidden" name="rpr_recipe_ingredients[<?php echo $i; ?>][group]"      class="ingredients_group" id="ingredient_group_<?php echo $i; ?>" value="<?php echo $ingredient['group']; ?>" />
        </td>
        <td>
        <?php if($ingredient_link == 'archive_custom' OR $ingredient_link == 'custom'): ?>
        	<input name="rpr_recipe_ingredients[<?php echo $i; ?>][link]" class="rpr_recipe_ingredients_link" type="hidden" id="ingredient_link_<?php echo $i; ?>" value="<?php echo $ingredient['link']; ?>" />
            <span class="rpr_recipe_ingredients_add_link button mce-ico mce-i-link <?php if($has_link === true) { echo ' rpr-has-link'; } ?>">&nbsp;</span>
            <span class="rpr_recipe_ingredients_delete_link button mce-ico mce-i-unlink">&nbsp;</span>
        <?php else: ?>
        	&nbsp;
        <?php endif; ?>
        </td>
        <td><span class="ingredients-delete"><img src="<?php echo $this->pluginUrl; ?>/img/minus.png" width="16" height="16"></span></td>
    </tr>
<?php
        $i++;
    }

}
?>
    <tr class="ingredient">
        <td class="sort-handle fa fa-arrows-v">&nbsp;</td>
        <td><input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][amount]"     class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>" placeholder="1" /></td>
        <td><input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][unit]"       class="ingredients_unit" id="ingredients_unit_<?php echo $i; ?>" placeholder="<?php _e( 'tbsp', $this->pluginName ); ?>" /></td>
        <td><input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" onfocus="autoSuggestTag('ingredients_<?php echo $i; ?>', 'rpr_ingredient');" placeholder="<?php _e( 'olive oil', $this->pluginName ); ?>" /></td>
        <td>
            <input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][notes]"      class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" placeholder="<?php _e( 'extra virgin', $this->pluginName ); ?>" />
            <input type="hidden" name="rpr_recipe_ingredients[<?php echo $i; ?>][group]"    class="ingredients_group" id="ingredient_group_<?php echo $i; ?>" value="" />
        </td>
        <td>
        <?php if($ingredient_link == 'archive_custom' OR $ingredient_link == 'custom'): ?>
        	<input name="rpr_recipe_ingredients[<?php echo $i; ?>][link]" class="rpr_recipe_ingredients_link" type="hidden" id="ingredient_link_<?php echo $i; ?>"value="" />
            <span class="rpr_recipe_ingredients_add_link button mce-ico mce-i-link">&nbsp;</span>
            <span class="rpr_recipe_ingredients_delete_link button mce-ico mce-i-unlink">&nbsp;</span>
        <?php else: ?>
        	&nbsp;
       	<?php endif; ?>
        </td>
        <td><span class="ingredients-delete"><img src="<?php echo $this->pluginUrl; ?>/img/minus.png" width="16" height="16"></span></td>
    </tr>
    </tbody>
</table>

    <a href="#" id="ingredients-add-group" class="button button-primary"><?php _e( 'Add an ingredient group', $this->pluginName ); ?></a>
    <a href="#" id="ingredients-add" class="button button-primary"><?php _e( 'Add an ingredient', $this->pluginName ); ?></a>
<div class="recipe-form-notes">
    <?php _e( "EasyType edit: <strong>Use the TAB key</strong> while adding ingredients. New fields will be created automatically. <strong>Don't worry about empty lines</strong>, these won't be saved.", $this->pluginName ); ?>
</div>
