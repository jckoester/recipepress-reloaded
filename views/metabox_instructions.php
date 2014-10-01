<?php 
$instructions = get_post_meta( $recipe->ID, "rpr_recipe_instructions", true );
$images = RPReloaded::get_option('recipe_instruction_image');
?>

<table id="recipe-instructions" class=" <?php if($images){ echo 'images'; }?>">
    <thead>
    <tr class="instruction-group instruction-group-first">
        <td>&nbsp;</td>
        <td <?php if($images){?>colspan="2"<?php }?>>
            <strong><?php _e( 'Group', $this->pluginName ); ?>:</strong>
            <span class="instruction-groups-disabled"><?php echo __( 'Main Instructions', $this->pluginName ) . ' ' . __( '(this label is not shown)', $this->pluginName ); ?></span>
            <?php
            $previous_group = '';
            if( isset($instructions[0]) && isset($instructions[0]['group'] ) ) {
                $previous_group = $instructions[0]['group'];
            }
            ?>
            <span class="instruction-groups-enabled"><input type="text" class="instruction-group-label" value="<?php echo $previous_group; ?>"/></span>
        </td>
        <td>&nbsp;</td>
    </tr>
    </thead>
    <tbody>
    <tr class="instruction-group-stub">
        <td>&nbsp;</td>
        <td <?php if($images){?>colspan="2"<?php }?>>
            <strong><?php _e( 'Group', $this->pluginName ); ?>:</strong>
            <input type="text" class="instruction-group-label" />
        </td>
        <td class="center-column"><span class="instruction-group-delete"><img src="<?php echo $this->pluginUrl; ?>/img/minus.png" width="16" height="16"></span></td>
    </tr>
<?php
$i = 0;

if( $instructions != '')
{
    foreach($instructions as $instruction) {
        if( isset( $instruction['group'] ) && $instruction['group'] != $previous_group)
        { ?>
            <tr class="instruction-group">
                <td>&nbsp;</td>
                <td <?php if($images){?>colspan="2"<?php }?>>
                    <strong><?php _e( 'Group', $this->pluginName ); ?>:</strong>
                    <input type="text" class="instruction-group-label" value="<?php echo $instruction['group']; ?>"/>
                </td>
                <td class="center-column"><span class="instruction-group-delete"><img src="<?php echo $this->pluginUrl; ?>/img/minus.png" width="16" height="16"></span></td>
            </tr>
<?php
            $previous_group = $instruction['group'];
        }

        if( isset($instruction['image']) )
        {
            $image = wp_get_attachment_image_src($instruction['image'], 'thumbnail');
            $image = $image[0];
            $has_image = true;
        }
        else
        {
            $image = $this->pluginUrl . '/img/image_placeholder.png';
            $has_image = false;
        }
        ?>
        <tr class="instruction">
            <td class="sort-handle fa fa-arrows-v"></td>
            <td>
                <textarea name="rpr_recipe_instructions[<?php echo $i; ?>][description]" rows="4" id="ingredient_description_<?php echo $i; ?>"><?php echo $instruction['description']; ?></textarea>
                <input type="hidden" name="rpr_recipe_instructions[<?php echo $i; ?>][group]"    class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="<?php echo $instruction['group']; ?>" />
            </td>
           <?php if($images):?>
            <td>
                <input name="rpr_recipe_instructions[<?php echo $i; ?>][image]" class="rpr_recipe_instructions_image" type="hidden" value="<?php echo $instruction['image']; ?>" />
                <input class="rpr_recipe_instructions_add_image button<?php if($has_image) { echo ' rpr-hide'; } ?>" rel="<?php echo $recipe->ID; ?>" type="button" value="<?php _e( 'Add Image', $this->pluginName ) ?>" />
                <input class="rpr_recipe_instructions_remove_image button<?php if(!$has_image) { echo ' rpr-hide'; } ?>" type="button" value="<?php _e( 'Remove Image', $this->pluginName ) ?>" />
                <br /><img src="<?php echo $image; ?>" class="rpr_recipe_instructions_thumbnail" />
            </td>
            <?php endif;?>
            <td><span class="instructions-delete"><img src="<?php echo $this->pluginUrl; ?>/img/minus.png" width="16" height="16"></span></td>
        </tr>
        <?php
        $i++;
    }

}

$image = $this->pluginUrl . '/img/image_placeholder.png';
?>
        <tr class="instruction">
            <td class="sort-handle fa fa-arrows-v"></td>
            <td>
                <textarea name="rpr_recipe_instructions[<?php echo $i; ?>][description]" rows="4" id="ingredient_description_<?php echo $i; ?>"></textarea>
                <input type="hidden" name="rpr_recipe_instructions[<?php echo $i; ?>][group]"    class="instructions_group" id="instruction_group_<?php echo $i; ?>" value="" />
            <?php if($images):?>
                <?php //if( !is_user_logged_in() ) { ?>
                <?php if ( !current_user_can( 'manage_options' ) ) { ?>
                    <?php _e( 'Add Image', $this->pluginName ); ?>:<br/>
                    <input class="rpr_recipe_instructions_image button" type="file" id="recipe_thumbnail" value="" size="50" name="recipe_thumbnail_<?php echo $i; ?>" />
                    </td>
                <?php } else { ?>
            </td>
            
            <td>

                <input name="rpr_recipe_instructions[<?php echo $i; ?>][image]" class="rpr_recipe_instructions_image" type="hidden" value="" />
                <input class="rpr_recipe_instructions_add_image button" rel="<?php echo $recipe->ID; ?>" type="button" value="<?php _e('Add Image', $this->pluginName ) ?>" />
                <input class="rpr_recipe_instructions_remove_image button rpr-hide" type="button" value="<?php _e( 'Remove Image', $this->pluginName ) ?>" />
                <br /><img src="<?php echo $image; ?>" class="rpr_recipe_instructions_thumbnail" />
                <?php } ?>
            </td>
            <?php endif; ?>
            <td><span class="instructions-delete"><img src="<?php echo $this->pluginUrl; ?>/img/minus.png" width="16" height="16"></span></td>
        </tr>
    </tbody>
</table>

    <a href="#" id="instructions-add" class="button button-primary"><?php _e( 'Add an instruction', $this->pluginName ); ?></a>
    <a href="#" id="instructions-add-group" class="button button-primary"><?php _e( 'Add an instruction group', $this->pluginName ); ?></a>
<div class="recipe-form-notes">
    <?php _e( "EasyType edit: <strong>Use the TAB key</strong> while adding ingredients. New fields will be created automatically. <strong>Don't worry about empty lines</strong>, these won't be saved.", $this->pluginName ); ?>
</div>
