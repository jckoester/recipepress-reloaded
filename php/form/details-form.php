<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * details-form.php - Create the details entry form on the admin side.
 *
 * @package RecipePress
 * @subpackage includes
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 1.0
 */
global $RECIPEPRESSOBJ;

/* Set up initial data for filters */
$rpFilters['servings'] = '<input name="recipe_details[recipe_servings]" type="text" id="recipe_servings" value="' . get_post_meta($post->ID, '_recipe_servings_value', true) . '" style="width:25px;" />';

$sizeSelected = get_post_meta($post->ID, '_recipe_serving_size_value', true);
if ( (int) $sizeSelected == 0 ) {
     $size = get_term_by('name', $sizeSelected, 'recipe-serving');
     if ( is_object($size) ) {
          $sizeSelected = $size->term_id;
     }
}
$courseSelected = get_post_meta($post->ID, '_recipe_course_value', true);
if ( (int) $courseSelected == 0 ) {
     $course = get_term_by('name', $courseSelected, 'recipe-serving');
     if ( is_object($course) ) {
          $courseSelected = $course->term_id;
     }
}

$rpFilters['serving_sizes'] = wp_dropdown_categories(array('selected' => $sizeSelected, 'hierarchical' => false, 'taxonomy' => 'recipe-serving', 'hide_empty' => false, 'name' => 'recipe_details[recipe_serving_size]', 'id' => 'recipe_serving_size', 'orderby' => 'name', 'echo' => false, 'show_option_none' => __('No Size', 'recipe-press-reloaded')));
$rpFilters['prep_time'] = '<input name="recipe_details[recipe_prep_time]" type="text" id="recipe_prep_time" value="' . get_post_meta($post->ID, '_recipe_prep_time_value', true) . '" style="width:25px;" /> minutes';
$rpFilters['cook_time'] = '<input name="recipe_details[recipe_cook_time]" type="text" id="recipe_cook_time" value="' . get_post_meta($post->ID, '_recipe_cook_time_value', true) . '" style="width:25px;" /> minutes';
$rpFilters['featured'] = '<input type="checkbox" class="checkbox" name="recipe_details[recipe_featured]" id="recipe_featured" value="1" ' . checked(get_post_meta($post->ID, '_recipe_featured_value', true), 1, false) . ' />';
$rpFilters['link'] = '<input type="checkbox" class="checkbox" name="recipe_details[recipe_link_ingredients]" id="recipe_link_ingredients" value="1" ' . checked(get_post_meta($post->ID, '_recipe_link_ingredients_value', true), 1, false) . ' />';
?>

<div class="detailsbox">
     <div class="details-minor">
          <?php if ( $this->rpr_options['use_servings'] ) : ?>

               <div class="recipe-details recipe-details-servings">
                    <label for="recipe_servings"><?php _e('Servings', 'recipe-press-reloaded'); ?></label>
               <?php echo apply_filters('rp_details_form_servings', $rpFilters['servings']); ?>
               <?php echo apply_filters('rp_details_form_serving_sizes', $rpFilters['serving_sizes']); ?>
          </div>
          <?php endif; ?>

          <?php if ( $this->rpr_options['use_times'] ) : ?>
                    <div class="recipe-details recipe-details-prep-time">
                         <label for="recipe_prep_time"><?php _e('Prep Time', 'recipe-press-reloaded'); ?></label>:
               <?php echo apply_filters('rp_details_form_prep_time', $rpFilters['prep_time']); ?>
               </div>
               <div class="recipe-details recipe-details-cook-time">
                    <label for="recipe_cook_time"><?php _e('Cook Time', 'recipe-press-reloaded'); ?></label>:
               <?php echo apply_filters('rp_details_form_cook_time', $rpFilters['cook_time']); ?>
               </div>

          <?php if ( $ready_time = get_post_meta($post->ID, '_recipe_ready_time_value', true) ) : ?>
                         <div class="recipe-details">
                              <label for="recipe_ready_time"><?php _e('Ready in', 'recipe-press-reloaded'); ?></label>:
               <?php echo apply_filters('rp_details_form_ready_time', $ready_time); ?>
                    </div>
          <?php endif; ?>

          <?php endif; ?>

          <?php if ( $this->rpr_options['use_featured'] ) : ?>
                              <div class="recipe-details">
                                   <label for="recipe_featured"><?PHP _e('Featured?', 'recipe-press-reloaded'); ?></label>
               <?php echo apply_filters('rp_details_form_featured', $rpFilters['featured']); ?>
                         </div>
          <?php
                              endif;
                              do_action('rp_details_form_after_table');
          ?>
           <!--                   <div class="recipe-details recipe-details-link">
                                   <label for="recipe_details_form_link_ingredient"><?php _e('Link ingredients?', 'recipe-press-reloaded'); ?></label>
               <?php echo apply_filters('rp_details_form_link_ingredient', $rpFilters['link']); ?>
          </div>-->
     </div>
</div>