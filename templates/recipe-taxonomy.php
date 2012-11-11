<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * recipe-taxonomy.php - The Template for displaying all recipe categories.
 *
 * @package RecipePress Reloaded
 * @subpackage templates
 * @author dasmaeh
 * @copyright 2012
 * @access public
 * @since 1.0
 */
/* Make sure we have some terms to list */
if ( !is_array($terms) ) {
     foreach ( $this->options['taxonomies'] as $key => $taxonomy ) {
          if ( $post->ID == $taxonomy['page'] ) {
               $tax = $key;
          }
     }

     $terms = get_terms($tax, array('parent' => 0));
}

?>

<?php if ( $pagination['pages'] > 1 ) : ?>
    <div id="nav-above" class="navigation recipe-navigation cleared">
        <div class="nav-previous"><?php previous_taxonomies_link(__('<span class="meta-nav">&larr;</span>More Taxonomies', 'recipe-press-reloaded')); ?></div>
        <div class="nav-next"><?php next_taxonomies_link(__('More Taxonomies <span class="meta-nav">&rarr;</span>', 'recipe-press-reloaded')); ?></div>
    </div><!-- #nav-above -->
<?php endif; ?>

<ul>
    <?php foreach ( $terms as $id => $term ) : ?>
        <li id="recipe_taxonomy_<?php the_term_id($term); ?>">
            <h2><a href="<?php echo get_term_link($term, $taxonomy); ?>"><?php the_term_name($term); ?></a></h2>
            <p class="recipe-category-description"><?php the_term_description($term); ?></p>
            <div class="recipe-sample-list attach-bottom"><?php _e('Sample recipes', 'recipe-press-reloaded'); ?>: <?php the_random_posts_list($term, array('after-link' => ', ')); ?></div>
        </li>
    <?php endforeach; ?>
</ul>

<?php if ( $pagination['pages'] > 1 ) : ?>
    <div id="nav-below" class="navigation recipe-navigation cleared">
        <div class="nav-previous"><?php previous_taxonomies_link(__('<span class="meta-nav">&larr;</span>More Taxonomies', 'recipe-press-reloaded')); ?></div>
        <div class="nav-next"><?php next_taxonomies_link(__('More Taxonomies <span class="meta-nav">&rarr;</span>', 'recipe-press-reloaded')); ?></div>
    </div><!-- #nav-below -->
<?php endif; ?>

<div class="cleared"></div>
