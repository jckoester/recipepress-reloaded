<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}
/**
 * index-recipe.php - The Template for displaying all recipes.
 *
 * @package RecipePress
 * @subpackage templates
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 1.0
 */
global $RECIPEPRESSOBJ;
?>

<div id="nav-above" class="navigation">
     <div class="nav-previous"><?php the_next_recipe_link(); ?></div>
     <div class="nav-next"><?php the_previous_recipe_link(); ?></div>
</div><!-- #nav-above -->

<?php if ( $recipes->have_posts() ) : while ($recipes->have_posts()) : $recipes->the_post(); ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class('recipe'); ?>>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) : ?>
            <div class="recipe-press-image align-left">
                <a href="<?php the_permalink();?>"><?php the_post_thumbnail('recipe-press-image'); ?></a>
            </div>
        <?php endif; ?> 
        <div class="recipe-notes"><?php the_recipe_introduction(array('length' => '5000')); ?></div>
            <?php if ( use_recipe_categories() && get_the_terms(get_the_id(), 'recipe-category') ) :?>
                <span class="recipe-category">
                    <?php _e('Posted in: ', 'recipe-press-reloaded');
                    the_terms(get_the_id(), 'recipe-category');?>
                </span><br/>
            <?php endif; ?>
            <?php if ( use_recipe_cuisines() && get_the_terms(get_the_id(), 'recipe-cuisine') ): ?>
                <span class="recipe-cuisine">
                    <?php _e('from: ', 'recipe-press-reloaded');
                    the_terms(get_the_id(), 'recipe-cuisine');?>
                </span><br/>
            <?php endif; ?>
            <?php if ( use_recipe_seasons() && get_the_terms(get_the_id(), 'recipe-season') ): ?>
                <span class="recipe-season">
                    <?php _e('Season: ', 'recipe-press-reloaded');
                    the_terms(get_the_id(), 'recipe-season');?>
                </span><br/>
            <?php endif; ?>
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
            
         <div class="cleared"></div>
    </div><!-- #post-## -->

<?php endwhile; ?>
<?php else : ?>
   <?php  _e("Sorry, no recipes found.", "recipe-press-reloaded");?>
<?php endif; ?>

<div id="nav-below" class="cleared navigation">
    <div class="nav-previous"><?php the_next_recipe_link(); ?></div>
    <div class="nav-next"><?php the_previous_recipe_link(); ?></div>
</div><!-- #nav-below -->
