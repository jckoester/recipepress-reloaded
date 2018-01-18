<?php
/**
 * The ingredient metabox view of the plugin.
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

$source = get_post_meta( $recipe->ID, "rpr_recipe_source", true );
$source_link = get_post_meta( $recipe->ID, "rpr_recipe_source_link", true );
$has_link='';
if( $source_link != '' ){ $has_link='has-link'; }
?>

<div class="sourcebox">
    <!--<label for="rpr_recipe_source"><?php _e( 'Source', 'recipepress-reloaded' ); ?>:</label>-->
    <input type="text" name="rpr_recipe_source" id="rpr_recipe_source" value="<?php echo $source; ?>" />
    <input name="rpr_recipe_source_link" class="rpr_recipe_source_link" type="hidden" id="rpr_recipe_source_link"  value="<?php echo $source_link; ?>"  />           
    <span href="#" class="rpr-source-add-link fa fa-link <?php echo $has_link ?>" title="<?php _e( 'Add link', 'recipepress-reloaded' ) ?>"></span>
    <span href="#" class="rpr-source-del-link fa fa-unlink <?php if($has_link === ""){ echo 'rpr-hidden'; }?> " title="<?php _e( 'Remove link', 'recipepress-reloaded' ) ?>"></span>
</div>