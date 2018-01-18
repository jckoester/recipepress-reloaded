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
?>

<?php
/**
 * JavaScript function for ingredient name autocompletion
 * @todo move to admin.js
 */
?>

<script type="text/javascript">
<?php
	$ingredients = get_terms( 'rpr_ingredient', array( 'orderby'=> 'name', 'order' => 'ASC' ) );
	$inglist = array();
	foreach( $ingredients as $ing ){
		array_push( $inglist, $ing->name );
	}
?>
    /*var ingredients = ;	*/
	var haystack = <?php echo json_encode( $inglist ); ?>;
jQuery(document).on("focusin", ".rpr-ing-name-input", function(){
	window.console.log(this.name);
	jQuery(this).autocomplete({
        source: haystack,
        minLength: 2,
		autoFocus: true
    });
});
jQuery(document).on("focusout", ".rpr-ing-name-input", function(){
		jQuery(this).autocomplete("destroy");

});
</script>

<table width="100%" cellspacing="5" class="rpr-metabox-table ingredients" id="recipe-ingredients">
    <thead>
        <tr>
            <th class="rpr-ing-sort">
                <div class="dashicons dashicons-sort"></div>
            </th>
            <th class="rpr-ing-amount"><?php _e( 'Amount', 'recipepress-reloaded' ); ?></th>
            <th class="rpr-ing-unit"><?php _e( 'Unit', 'recipepress-reloaded' ); ?></th>
            <th class="rpr-ing-ingredient"><?php _e( 'Ingredient', 'recipepress-reloaded' ); ?></th>
            <th class="rpr-ing-note"><?php _e( 'Note', 'recipepress-reloaded' ); ?></th>
            <th class="rpr-ing-link">
                <div class="fa fa-link"></div>
            </th>
            <th class=""rpr-ing-del></th>
        </tr>
    </thead>

    <tbody>
        <!-- hidden row to copy heading lines from -->
        <tr class="ingredient-group-stub rpr-hidden">
            <td class="rpr-ing-sort">
                <div class="sort-handle fa fa-sort"></div>
                <input type="hidden" name="rpr_recipe_ingredients[0][sort]" class="ingredients_sort" id="ingredients_sort_0" />
            </td>
            <td colspan="4" class="rpr-ing-group">
                <label for="rpr_recipe_ingredients[0][grouptitle]">
                    <?php _e( 'Group', 'recipepress-reloaded' ); ?>:
                </label>
                <input type="text" class="ingredient-group-label" name="rpr_recipe_ingredients[0][grouptitle]" class="ingredients_grouptitle" id="ingredients_grouptitle_0" />
            </td>
            <td>&nbsp;</td>
            <td class="rpr-ing-del">
                <a href="#" class="rpr-ing-remove-row dashicons dashicons-no" data-type="rpr_ingredient" title="<?php _e( 'Remove row', 'recipepress-reloaded' ) ?>"></a>
                <a href="#" class="rpr-ing-add-row dashicons dashicons-plus" data-type="rpr_ingredient" title="<?php _e( 'Add row', 'recipepress-reloaded' ) ?>"></a>
            </td>
        </tr>
        
        <!-- Existing ingredient rows -->
<?php
$ingredients = get_post_meta( $recipe->ID, "rpr_recipe_ingredients", true );
$i=1;
//var_dump($ingredients);
if( is_array($ingredients) ){
    foreach( $ingredients as $ing ){
        $has_link = "";
        
        // Check if we have a ingredients group or a ingredient
        if( isset( $ing['grouptitle'] ) ){
            // we have a ingredient group title line
            // Add a group heading line
            if(  $ing['grouptitle'] != "" ){
            ?>
        <tr class="ingredient-group">
            <td class="rpr-ing-sort">
                <div class="sort-handle fa fa-sort"></div>
                <input type="hidden" name="rpr_recipe_ingredients[<?php echo $i; ?>][sort]" class="ingredients_sort" id="ingredients_sort_<?php echo $i; ?>" />
            </td>
            <td colspan="4" class="rpr-ing-group">
                <label for="rpr_recipe_ingredients[<?php echo $i; ?>][grouptitle]">
                    <?php _e( 'Group', 'recipepress-reloaded' ); ?>:
                </label>
                <input type="text" class="ingredient-group-label" name="rpr_recipe_ingredients[<?php echo $i; ?>][grouptitle]" class="ingredients_grouptitle" id="ingredients_grouptitle_<?php echo $i; ?>" value="<?php echo $ing['grouptitle']; ?>" />
            </td>
            <td>&nbsp;</td>
            <td class="rpr-ing-del">
                <a href="#" class="rpr-ing-remove-row dashicons dashicons-no" data-type="rpr_ingredient" title="<?php _e( 'Remove row', 'recipepress-reloaded' ) ?>"></a>
                <a href="#" class="rpr-ing-add-row dashicons dashicons-plus" data-type="rpr_ingredient" title="<?php _e( 'Add row', 'recipepress-reloaded' ) ?>"></a>
            </td>
        </tr>
<?php
            }
        } else {
            // we have a single ingredient line
            // get the term name from the term_id just in case it has changed
            $term = get_term($ing['ingredient_id'], 'rpr_ingredient');
            if ( $term !== null && !is_wp_error( $term ) ) {
                $ing['ingredient'] = $term->name;
            }
            if( $ing['link'] != "" ){ $has_link = 'has-link'; }
            // Add single ingredient line
            ?>
        <tr class="rpr-ing-row">
            <td class="rpr-ing-sort">
                <div class="sort-handle fa fa-sort"></div>
                <input type="hidden" name="rpr_recipe_ingredients[<?php echo $i; ?>][sort]" class="ingredients_sort" id="ingredients_sort_<?php echo $i; ?>" value="<?php echo $i; ?>" />
            </td>
            <td class="rpr-ing-amount">
                <input type="text" name="rpr_recipe_ingredients[<?php echo $i; ?>][amount]" class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>"  value="<?php echo $ing['amount']; ?>" />
            </td>
            <td class="rpr-ing-unit">
                <?php // if ingredient list should be used: 
                if( AdminPageFramework::getOption( 'rpr_options', array( 'units', 'use_ingredient_units') , true ) ) {
                ?>
                    <select name="rpr_recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredient_unit_<?php echo $i; ?>">
                        <?php $this->the_ingredient_unit_selection(  $ing['unit'] ); ?>
                    </select>
                <?php 
                // if not we just use a standard input field 
                } else { ?>
                    <input type="text" name="rpr_recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredient_unit_<?php echo $i; ?>" value="<?php echo $ing['unit']; ?>" />
                <?php } ?>
            </td>
            <td class="rpr-ing-name">
                <input type="text" class="rpr-ing-name-input" name="rpr_recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" value="<?php echo $ing['ingredient']; ?>" />
            </td>
            <td class="rpr-ing-note">
                <input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][notes]"      class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>"  value="<?php echo $ing['notes']; ?>"  />
            </td>
            <td class="rpr-ing-link">
                <input name="rpr_recipe_ingredients[<?php echo $i; ?>][link]" class="rpr_recipe_ingredients_link" type="hidden" id="ingredient_link_<?php echo $i; ?>"  value="<?php echo $ing['link']; ?>"  />
                <span href="#" class="rpr-ing-add-link fa fa-link <?php echo $has_link ?>" data-type="rpr_ingredient" title="<?php _e( 'Add custom link', 'recipepress-reloaded' ) ?>"></span>
                <span href="#" class="rpr-ing-del-link fa fa-unlink <?php if($has_link === ""){ echo 'rpr-hidden'; }?> " data-type="rpr_ingredient" title="<?php _e( 'Remove custom link', 'recipepress-reloaded' ) ?>"></span>
            </td>
            <td class="rpr-ing-del">
                <a href="#" class="rpr-ing-remove-row dashicons dashicons-no" data-type="rpr_ingredient" title="<?php _e( 'Remove row', 'recipepress-reloaded' ) ?>"></a>
                <a href="#" class="rpr-ing-add-row dashicons dashicons-plus" data-type="rpr_ingredient" title="<?php _e( 'Add row', 'recipepress-reloaded' ) ?>"></a>
            </td>
        </tr>
<?php
        }
        $i++;
    }
}      
?>
        <!-- the last row is always empty, in case you want to add some -->
        <tr class="rpr-ing-row">
            <td class="rpr-ing-sort">
                <div class="sort-handle fa fa-sort"></div>
                <input type="hidden" name="rpr_recipe_ingredients[<?php echo $i; ?>][sort]" class="ingredients_sort" id="ingredients_sort_<?php echo $i; ?>" />
            </td>
            <td class="rpr-ing-amount">
                <input type="text" name="rpr_recipe_ingredients[<?php echo $i; ?>][amount]" class="ingredients_amount" id="ingredients_amount_<?php echo $i; ?>" placeholder="1" />
            </td>
            <td class="rpr-ing-unit">
                <?php // if ingredient list should be used: 
                if( AdminPageFramework::getOption( 'rpr_options', array( 'units', 'use_ingredient_units') , true ) ) {
                ?>
                    <select name="rpr_recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredient_unit_<?php echo $i; ?>">
                        <?php $this->the_ingredient_unit_selection(); ?>
                    </select>
                <?php 
                // if not we just use a standard input field 
                } else { ?>
                <input type="text" name="rpr_recipe_ingredients[<?php echo $i; ?>][unit]" class="ingredients_unit" id="ingredient_unit_<?php echo $i; ?>" value="" placeholder="<?php __('1 tsp', 'recipepress-reloaded' ); ?>" />
                <?php } ?>
            </td>
            <td class="rpr-ing-name">
                <input type="text" class="rpr-ing-name-input" name="rpr_recipe_ingredients[<?php echo $i; ?>][ingredient]" class="ingredients_name" id="ingredients_<?php echo $i; ?>" />
            </td>
            <td class="rpr-ing-note">
                <input type="text"   name="rpr_recipe_ingredients[<?php echo $i; ?>][notes]"      class="ingredients_notes" id="ingredient_notes_<?php echo $i; ?>" placeholder="<?php _e( 'extra virgin', 'recipepress-reloaded' ); ?>" />
            </td>
            <td class="rpr-ing-link">
                <input name="rpr_recipe_ingredients[<?php echo $i; ?>][link]" class="rpr_recipe_ingredients_link" type="hidden" id="ingredient_link_<?php echo $i; ?>" value="" />
                <span href="#" class="rpr-ing-add-link fa fa-link" data-type="rpr_ingredient" title="<?php _e( 'Add custom link', 'recipepress-reloaded' ) ?>"></span>
                <span href="#" class="rpr-ing-del-link fa fa-unlink rpr-hidden" data-type="rpr_ingredient" title="<?php _e( 'Remove custom link', 'recipepress-reloaded' ) ?>"></span>
            </td>
            <td class="rpr-ing-del">
                <a href="#" class="rpr-ing-remove-row dashicons dashicons-no" data-type="rpr_ingredient" title="<?php _e( 'Remove row', 'recipepress-reloaded' ) ?>"></a>
                <a href="#" class="rpr-ing-add-row dashicons dashicons-plus" data-type="rpr_ingredient" title="<?php _e( 'Add row', 'recipepress-reloaded' ) ?>"></a>
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7">
                <!--<a class="simmer-bulk-add-link hide-if-no-js" href="#" data-type="ingredient"><?php _e( '+ Add in Bulk', 'simmer' ); ?></a>-->
                <a id="rpr-ing-add-row-ing" class="rpr-ing-add-row button" data-type="rpr_ingredient" href="#">
                    <span class="dashicons dashicons-plus"></span>
                    <?php _e( 'Add an ingredient', 'recipepress-reloaded' ); ?>
                </a>
                <a id="rpr-ing-add-row-grp" class="rpr-ing-add-row button" data-type="heading" href="#">
                    <span class="dashicons dashicons-plus"></span>
                    <?php _e( 'Add a group', 'recipepress-reloaded' ); ?>
                </a>
            </td>
        </tr>
    </tfoot>
</table>