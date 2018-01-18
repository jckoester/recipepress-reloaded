<?php
/**
 * The instructions metabox view of the plugin.
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
?>
   
<table width="100%" cellspacing="5" class="rpr-metabox-table instructions" id="recipe-instructions">
    <thead>
        <tr>
            <th class="rpr-ins-sort">
                <div class="dashicons dashicons-sort"></div>
            </th>
            <th class="rpr-ins-instruction"><?php _e( 'Description', 'recipepress-reloaded' ); ?></th>
            <th class="rpr-ins-image"><?php _e( 'Image', 'recipepress-reloaded' ); ?></th>
            <th class=""rpr-ing-del></th>
        </tr>
    </thead>
    <tbody>
        <!-- hidden row to copy heading lines from -->
        <tr class="instruction-group-stub rpr-hidden">
            <td class="rpr-ins-sort">
                <div class="sort-handle fa fa-sort"></div>
                <input type="hidden" name="rpr_recipe_instructions[0][sort]" class="instructions_sort" id="instructions_sort_0" />
            </td>
            <td colspan="2" class="rpr-ins-group">
                <label for="rpr_recipe_instructions[0][grouptitle]"><?php _e( 'Group', 'recipepress-reloaded' ); ?>:</label>
                <input type="text" class="instructions-group-label" name="rpr_recipe_instructions[0][grouptitle]" class="instructions_grouptitle" id="instructions_grouptitle_0" />
            </td>
            <td class="rpr-ins-del">
                <a href="#" class="rpr-ins-remove-row dashicons dashicons-no" data-type="rpr_instructions" title="<?php _e( 'Remove row', 'recipepress-reloaded' ) ?>"></a>
                <a href="#" class="rpr-ins-add-row dashicons dashicons-plus" data-type="rpr_instructions" title="<?php _e( 'Add row', 'recipepress-reloaded' ) ?>"></a>
            </td>
        </tr>
        
        <!-- Existing ingredient rows -->
<?php
$instructions = get_post_meta( $recipe->ID, "rpr_recipe_instructions", true );
$i=1;

if( is_array($instructions) ){
    foreach ($instructions as $ins ){
        $has_image = "";
        
        // Check if we have a instructions group or a instruction line
        if( isset( $ins['grouptitle'] ) ){
            // we have a instruction group title line
            // Add a group heading line
            if(  $ins['grouptitle'] != "" ){
?>
        <tr class="instruction-group">
            <td class="rpr-ins-sort">
                <div class="sort-handle fa fa-sort"></div>
                <input type="hidden" name="rpr_recipe_instructions[<?php echo $i; ?>][sort]" class="instructions_sort" id="instructions_sort_<?php echo $i; ?>" value="<?php echo $ins['sort']; ?>" />
            </td>
            <td colspan="2" class="rpr-ins-group">
                <label for="rpr_recipe_instructions[<?php echo $i; ?>][grouptitle]"><?php _e( 'Group', 'recipepress-reloaded' ); ?>:</label>
                <input type="text" class="instructions-group-label" name="rpr_recipe_instructions[<?php echo $i; ?>][grouptitle]" class="instructions_grouptitle" id="instructions_grouptitle_<?php echo $i; ?>" value="<?php echo $ins['grouptitle']; ?>" />
            </td>
            <td class="rpr-ins-del">
                <a href="#" class="rpr-ins-remove-row dashicons dashicons-no" data-type="rpr_instructions" title="<?php _e( 'Remove row', 'recipepress-reloaded' ) ?>"></a>
                <a href="#" class="rpr-ins-add-row dashicons dashicons-plus" data-type="rpr_instructions" title="<?php _e( 'Add row', 'recipepress-reloaded' ) ?>"></a>
            </td>
        </tr>
<?php
            }
        } else {
            // we have a instruction line
            $image ="";
            if( isset($ins['image']) && $ins['image'] != "")
            {
                $image = wp_get_attachment_image_src($ins['image'], 'recipe-thumbnail');
                $image = $image[0];
                $has_image = true;
            }
?>
        <tr class="rpr-ins-row">
            <td class="rpr-ins-sort">
                <div class="sort-handle fa fa-sort"></div>
                <input type="hidden" name="rpr_recipe_instructions[<?php echo $i; ?>][sort]" class="instructions_sort" id="instructions_sort_<?php echo $i; ?>" value="<?php echo $ins['sort']; ?>" />
            </td>
            <td class="rpr-ins-instruction">
                <textarea name="rpr_recipe_instructions[<?php echo $i; ?>][description]" rows="4" id="instruction_description_<?php echo $i; ?>"><?php echo $ins['description']; ?></textarea>
            </td>
            <td class="rpr-ins-image">
                <img class="rpr_recipe_instructions_thumbnail" id="rpr_recipe_instructions_thumbnail_<?php echo $i; ?>" src="<?php echo $image; ?>" <?php echo ( ! $has_image ? 'style="display:none;"' : '' ); ?>/>
                <input name="rpr_recipe_instructions[<?php echo $i; ?>][image]" class="rpr_recipe_instructions_image" type="hidden" value="<?php echo $ins['image']; ?>" />
                <div>
                    <a title="<?php esc_attr_e( 'Set instruction image', 'recipepress-reloaded' ) ?>" href="#" id="rpr_recipe_instructions_image_set_<?php echo $i; ?>" class="rpr-ins-image-set fa fa-image" rel="<?php echo $recipe->ID; ?>"></a>
                    <a title="<?php esc_attr_e( 'Remove instruction image' ) ?>" href="#" id="rpr_recipe_instructions_image_del_<?php echo $i; ?>" <?php echo ( ! $has_image ? 'style="display:none;"' : '' ); ?> class="rpr-ins-image-del fa fa-trash-o" ></a>
                </div>
            </td>
            <td class="rpr-ins-del">
                <a href="#" class="rpr-ins-remove-row dashicons dashicons-no" data-type="rpr_instruction" title="<?php _e( 'Remove row', 'recipepress-reloaded' ) ?>"></a>
                <a href="#" class="rpr-ins-add-row dashicons dashicons-plus" data-type="rpr_instructions" title="<?php _e( 'Add row', 'recipepress-reloaded' ) ?>"></a>
            </td>
        </tr>
<?php
        }
        $i++;
    }
}
?>
        <!-- the last row is always empty, in case you want to add some -->
        <tr class="rpr-ins-row">
            <td class="rpr-ins-sort">
                <div class="sort-handle fa fa-sort"></div>
                <input type="hidden" name="rpr_recipe_instructions[<?php echo $i; ?>][sort]" class="instructions_sort" id="instructions_sort_<?php echo $i; ?>" />
            </td>
            <td class="rpr-ins-instruction">
                <textarea name="rpr_recipe_instructions[<?php echo $i; ?>][description]" rows="4" id="instruction_description_<?php echo $i; ?>"></textarea>
            </td>
            <td class="rpr-ins-image">
                <img class="rpr_recipe_instructions_thumbnail" id="rpr_recipe_instructions_thumbnail_<?php echo $i; ?>" src="" style="display:none;"/>
                <input name="rpr_recipe_instructions[<?php echo $i; ?>][image]" class="rpr_recipe_instructions_image" type="hidden" value="" />
                <div>
                    <a title="<?php esc_attr_e( 'Set instruction image', 'recipepress-reloaded' ) ?>" href="#" id="rpr_recipe_instructions_image_set_<?php echo $i; ?>" class="rpr-ins-image-set fa fa-image" rel="<?php echo $recipe->ID; ?>"></a>
                    <a title="<?php esc_attr_e( 'Remove instruction image' ) ?>" href="#" id="rpr_recipe_instructions_image_del_<?php echo $i; ?>" style="display:none" class="rpr-ins-image-del fa fa-trash-o" ></a>
                </div>
            </td>
            <td class="rpr-ins-del">
                <a href="#" class="rpr-ins-remove-row dashicons dashicons-no" data-type="rpr_instruction" title="<?php _e( 'Remove row', 'recipepress-reloaded' ) ?>"></a>
                <a href="#" class="rpr-ins-add-row dashicons dashicons-plus" data-type="rpr_instructions" title="<?php _e( 'Add row', 'recipepress-reloaded' ) ?>"></a>
            </td>
        </tr>
    </tbody>
    
    <tfoot>
        <tr>
            <td colspan="7">
                <!--<a class="simmer-bulk-add-link hide-if-no-js" href="#" data-type="ingredient"><?php _e( '+ Add in Bulk', 'simmer' ); ?></a>-->
                <a id="rpr-ins-add-row-ins" class="rpr-ins-add-row button" data-type="rpr_instruction" href="#">
                    <span class="dashicons dashicons-plus"></span>
                    <?php _e( 'Add an instruction', 'recipepress-reloaded' ); ?>
                </a>
                <a id="rpr-ins-add-row-grp" class="rpr-ins-add-row button" data-type="heading" href="#">
                    <span class="dashicons dashicons-plus"></span>
                    <?php _e( 'Add a group', 'recipepress-reloaded' ); ?>
                </a>
            </td>
        </tr>
    </tfoot>
</table>