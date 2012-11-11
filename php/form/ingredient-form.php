<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * ingredient-form.php - Create the ingredient entry form on the admin side.
 *
 * @package RecipePress Reloaded
 * @subpackage includes
 * @author dasmaeh
 * @copyright 2012
 * @access public
 * @since 0.1
 */
global $ingredientID, $ingredient, $RECIPEPRESSOBJ;



do_action('rpr_form_before_ingredients');
?>
<div class="table">
	<table id="rpr_ingredients" class="form-table editrecipe">
		<thead>
			<tr class="form-field">
            	<th class="recipe-press-header rpr-header-sort">&nbsp;</th>
                <th class="recipe-press-header rpr-header-quantity"><?php _e('Quantity', 'recipe-press-reloaded'); ?></th>
                <th class="recipe-press-header rpr-header-size"><?php _e('Size', 'recipe-press-reloaded'); ?></th>
                <th class="recipe-press-header rpr-header-ingredient"><?php _e('Ingredient', 'recipe-press-reloaded'); ?></th>
                <th class="recipe-press-header rpr-header-link"><?php _e('Link to page', 'recipe-press-reloaded'); ?></th>
			</tr>
		</thead>
		<tbody id="rpr_ingredients_body">
			<tr id="rpr_ingredient_null" style="display:none">
				<th class="rpr-header rpr-header-sort" id="rpr_drag_icon">
               		<img alt="<?php _e('Drag Ingredient', 'recipe-press-reloaded'); ?>" src="<?php echo RPR_URL . 'images/icons/drag-icon.png'; ?>" style="cursor:pointer" />
                    <img alt="<?php _e('Delete Ingredient', 'recipe-press-reloaded'); ?>" src="<?php echo RPR_URL . 'images/icons/delete.gif'; ?>" style="cursor:pointer" onclick="rpr_delete_row('rpr_ingredient_NULL');" />
				</th>
				<td id="rpr_size_column">
					<?php wp_dropdown_categories(array('hierarchical' => false, 'taxonomy' => 'recipe-size', 'hide_empty' => false, 'name' => 'ingredientsCOPY[NULL][size]', 'orderby' => 'name', 'echo' => true, 'show_option_none' => __('No Size', 'recipe-press-reloaded'))); ?>
                </td>
				<td id="rpr_item_column">
				<?php
					$ingredientItem = '<input type="hidden" id="recipe_ingredient_NULL" name = "ingredients[NULL][item]" value="' . $ingredient['item'] . '" />';
					$ingredientBox = '<input id="ingname_NULL" type="text" class="recipe-item-lookup rpr-ingredients" name="ingredients[NULL][new-ingredient]" value="" onkeypress="clear_ingredient_id(NULL)" placeholder="'.__("Ingredient", "recipe-press-reloaded").'"/>';
					echo apply_filters('rpr_ingredient_form_item', $ingredientItem);
					echo apply_filters('rpr_ingredient_form_name', $ingredientBox);
					echo '<input class="rpr-ingredients-notes" type="text" name="ingredients[NULL][notes]" value="" placeholder="'.__("Notes", "recipe-press-reloaded").'"/>';
                ?>
				</td>
				<td id="rpr_page_column">
					<?php recipe_dropdown_pages(array('name' => 'ingredientsCOPY[NULL][page-link]', 'show_option_none' => 'None')); ?><br />
					<input type="text" class="rpr-ingredient-url" name="ingredientsCOPY[NULL][url]" value="" placeholder="<?php _e("URL", "recipe-press-reloaded") ?>" />
				</td>
			</tr>
            <?php
				$ingredientID = 1;
				if ( !isset($ingredients) ):
					$ingredients = $RECIPEPRESSOBJ->getIngredients();
				endif; ?>

				<?php foreach ( $ingredients as $id => $ingredient ) : ?>
				<tr id="rpr_ingredient_<?php echo $ingredientID; ?>" class="rpr_size_type_<?php echo $ingredient['size']; ?>" valign="top">
					<th class="rpr-header rpr-header-sort">
						<img alt="<?php _e('Drag Ingredient', 'recipe-press-reloaded'); ?>" src="<?php echo RPR_URL . 'images/icons/drag-icon.png'; ?>" style="cursor:pointer" />
						<img alt="<?php _e('Delete Ingredient', 'recipe-press-reloaded'); ?>" src="<?php echo RPR_URL . 'images/icons/delete.gif'; ?>" style="cursor:pointer" onclick="rpr_delete_row('rpr_ingredient_<?php echo $ingredientID; ?>');" />
					</th>
					<td>
						<?php if ( $ingredient['size'] != 'divider' and $ingredient['size'] ):
							$value = isset($ingredient['quantity']) ? $ingredient['quantity'] : '';
							$quantityBox = '<input class="recipe-press-quantity" type="text" name="ingredients[' . $ingredientID . '][quantity]" value="' . $value . '" />';
							echo apply_filters('rpr_ingredient_form_quantity', $quantityBox);
						endif; ?>
					</td>
					<td>
						<?php if ( $ingredient['size'] != 'divider' and $ingredient['size'] ) :
							/* If size is text, convert to ID */
							if ( (int) $ingredient['size'] == 0 ) {
								$size = get_term_by('name', $ingredient['size'], 'recipe-size');
								if ( is_object($size) ) {
									$ingredient['size'] = $size->term_id;
								}
							}
							/* Display size drop down */
							wp_dropdown_categories(array('selected' => $ingredient['size'], 'hierarchical' => false, 'taxonomy' => 'recipe-size', 'hide_empty' => false, 'name' => 'ingredients[' . $ingredientID . '][size]', 'id' => 'ingredient_' . $ingredientID . '_size', 'orderby' => 'name', 'echo' => true, 'show_option_none' => __('No Size', 'recipe-press-reloaded'))); ?>     
                         <?php else : ?>
							<input type="text" class="rpr-ingredient-url" name="ingredients[<?php echo $ingredientID; ?>][size]" value="divider" readonly="readonly" />
                         <?php endif; ?>
					</td>
					<td>
                        <?php if ( isset($ingredient['item']) and $ingredient['size'] != 'divider'):
							$value = $ingredient['item'];
							$term = get_term($value, 'recipe-ingredient');
							if ( isset($term->name) ) {
								$value = $term->name;
							} else {
								$value = '';
							}
						elseif ($ingredient['size'] == 'divider'):
							$term = false;
							$value = $ingredient['item'];
						else:
							$value = '';
							$term = '';
						endif;
						
						$ingredientItem = '<input type="hidden" id="recipe_ingredient_' . $ingredientID . '" name = "ingredients[' . $ingredientID . '][item]" value="' . $ingredient['item'] . '" />';
						$ingredientBox = '<input id="ingname_' . $ingredientID . '" type="text" class="recipe-item-lookup rpr-ingredients" name="ingredients[' . $ingredientID . '][new-ingredient]" value="' . $value . '" onkeypress="clear_ingredient_id(' . $ingredientID . ')" placeholder="'.__("Ingredient", "recipe-press-reloaded").'" />';
						echo apply_filters('rpr_ingredient_form_item', $ingredientItem);
						echo apply_filters('rpr_ingredient_form_name', $ingredientBox);
                        ?>

                        <?php
							$value = isset($ingredient['notes']) ? $ingredient['notes'] : '';
							if ( $ingredient['size'] != 'divider' and $ingredient['size'] ) : ?>
								<input class="rpr-ingredients-notes" type="text" name="ingredients[<?php echo $ingredientID; ?>][notes]" value="<?php echo stripslashes_deep(trim($value)); ?>" placeholder="<?php _e("Notes", "recipe-press-reloaded") ?>" />
							<?php endif; ?>
					</td>
					<td>
						<?php if ( $ingredient['size'] != 'divider' ) :
                        	recipe_dropdown_pages(array('name' => 'ingredients[' . $ingredientID . '][page-link]', 'selected' => isset($ingredient['page-link']) ? $ingredient['page-link'] : false, 'show_option_none' => 'None')); ?><br />
                            
                            <?php
								if ( isset($ingredient['url']) ) {
									$value = $ingredient['url'];
								} else {
									$value = '';
								}
							?>
							<input type="text" class="rpr-ingredient-url" name="ingredients[<?php echo $ingredientID; ?>][url]" value="<?php echo stripslashes_deep(trim($value)); ?>" placeholder="<?php _e("URL", "recipe-press-reloaded") ?>" />
                         <?php endif; ?>
					</td>
				</tr>
               <?php ++$ingredientID; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p><a onclick="rpr_add_ingredient('admin')" style="cursor:pointer"><?php _e('Add Ingredient', 'recipe-press-reloaded'); ?></a> | <a onclick="rpr_add_divider('admin')" style="cursor:pointer"><?php _e('Add Divider', 'recipe-press-reloaded'); ?></a></p>
	<?php do_action('rpr_form_after_ingredients'); ?>
</div>