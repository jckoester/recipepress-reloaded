<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * recipe-loop.php - The Template for looping through recipes.
 *
 * @package RecipePress
 * @subpackage templates
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 1.0
 */
/*
 * This template is used to display the each recipe in a list. You can copy this file to your
 * Theme folder and make changes if you wish. You can use standard template tags to display the
 * recipe information.
 */
?>
<div id="post-<?php the_ID(); ?>" <?php post_class('recipe'); ?>>
    <?php if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) : ?>
        <div class="recipe-press-image align-left">
            <a href="<?php the_permalink();?>"><?php the_post_thumbnail('rpr-image'); ?></a>
        </div>
    <?php endif; ?> 
    <p class="recipe-notes"><?php the_recipe_introduction(array('length' => '5000')); ?></p>
        <?php if ( use_recipe_categories() ) :?>
            <div class="recipe-category">
                <?php _e('Posted in: ', 'recipe-press-reloaded');
                the_terms(get_the_id(), 'recipe-category');?>
            </div>
        <?php endif; ?>
        <?php if ( use_recipe_cuisines() ): ?>
            <div class="recipe-cuisine">
                <?php _e('from: ', 'recipe-press-reloaded');
                the_terms(get_the_id(), 'recipe-cuisine');?>
            </div>
        <?php endif; ?>
        <?php if ( use_recipe_seasons() ): ?>
            <div class="recipe-season">
                <?php _e('Season: ', 'recipe-press-reloaded');
                the_terms(get_the_id(), 'recipe-season');?>
            </div>
        <?php endif; ?>
        <div class="recipe-course-servings">
        <?php if ( use_recipe_courses() ): ?>
            <span class="recipe-course">
                <?php the_terms(get_the_id(), 'recipe-course');?>
            </span>&nbsp;
        <?php endif; ?>
        <?php if ( use_recipe_servings() ): ?>
            <span class="recipe-servings">
                <?php _e("for", "recipe-press-reloaded"); ?>
                <?php the_recipe_servings(); ?>
            </span>
        <?php endif; ?>
        </div>
     <div class="cleared"></div>
</div><!-- #post-## -->
