<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * recipe-single.php - The Template for displaying all recipes.
 *
 * @package RecipePress Reloaded
 * @subpackage templates
 * @author dasmaeh
 * @copyright 2012
 * @access public
 * @since 0.1
 */
?>

<div id="post-<?php the_ID(); ?>">
    <ul class="recipe-controls">
        <?php the_recipe_controls(); ?>
    </ul>
    <div class="recipe-header">      
        <?php if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) : ?>
            <div class="rpr-image align-left">
                <?php the_post_thumbnail('rpr-image'); ?>
            </div>
        <?php endif; ?> 
        <div class="recipe-notes"><?php the_recipe_introduction(array('length' => '5000')); ?></div>
        <?php if ( use_recipe_categories() && get_the_terms(get_the_id(), 'recipe-category')) :?>
            <div class="recipe-category" >
                <?php _e('Posted in: ', 'recipe-press-reloaded');
                the_terms(get_the_id(), 'recipe-category');?>
            </div>
        <?php endif; ?>
        <?php if ( use_recipe_cuisines() && get_the_terms(get_the_id(), 'recipe-cuisine')): ?>
            <div class="recipe-cuisine">
                <?php _e('from: ', 'recipe-press-reloaded');
                the_terms(get_the_id(), 'recipe-cuisine');?>
            </div>
        <?php endif; ?>
        <?php if ( use_recipe_seasons() && get_the_terms(get_the_id(), 'recipe-season') ): ?>
            <div class="recipe-season">
                <?php _e('Season: ', 'recipe-press-reloaded');
                the_terms(get_the_id(), 'recipe-season');?>
            </div>
        <?php endif; ?>
        <div class="recipe-course-sevings">
        <?php if ( use_recipe_courses() && get_the_terms(get_the_id(), 'recipe-course') ): ?>
            <span class="recipe-course">
                <?php the_terms(get_the_id(), 'recipe-course');?>
            </span>&nbsp;
        <?php endif; ?>
        <?php if ( use_recipe_servings() && get_recipe_servings() ): ?>
            <span class="recipe-servings">
                <?php _e("for", "recipe-press-reloaded"); ?>
                <?php the_recipe_servings(); ?>
            </span>
        <?php endif; ?>
        </div>
    </div><!-- .recipe-header -->
    <?php if ( use_recipe_times ( ) && get_recipe_prep_time() != ""  ) : ?>
        <div id="recipe-details-<?php the_ID(); ?>" class="recipe-section recipe-section-<?php the_id(); ?>">
            <ul class="recipe-details">
                <?php the_recipe_prep_time(); ?>
                <?php the_recipe_cook_time(); ?>
                <?php the_recipe_ready_time(); ?>
            </ul>
        </div><!-- #recipe-details -->
    <?php endif; ?>

    <div class="recipe-content">
        <h2><?php _e('Ingredients', 'recipe-press-reloaded'); ?> </h2>
        <?php the_recipe_ingredients(); ?>

        <h2 ><?php _e('Directions', 'recipe-press-reloaded'); ?></h2>
        <?php the_content(); ?>


        <?php wp_link_pages(array('before' => '<div class="page-link">' . __('Pages:', 'recipe-press-reloaded'), 'after' => '</div>')); ?>

    </div><!-- .entry-content -->

        <?php do_action('after_recipe_content'); ?>

        <div class="entry-utility">
          <?php edit_post_link(__('Edit', 'recipe-press-reloaded'), '<span class="edit-link">', '</span>'); ?>
     </div><!-- .entry-utility -->
</div><!-- #post-## -->
