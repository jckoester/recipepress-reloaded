<?php

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}
/**
 * custom_walkers.php - Custom walkers for RecipePress
 *
 * @package RecipePress
 * @subpackage classes
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 2.2
 */

/**
 * Create HTML list of categories.
 *
 * @package WordPress
 * @since 2.2
 * @uses Walker
 */
class Walker_RPR_Taxonomy extends Walker_Category {

     /**
      * @see Walker::$tree_type
      * @since 2.2
      * @var string
      */
     var $tree_type = 'category';
     /**
      * @see Walker::$db_fields
      * @since 2.2
      * @todo Decouple this
      * @var array
      */
     var $db_fields = array('parent' => 'parent', 'id' => 'term_id');

     /**
      * @see Walker::start_lvl()
      * @since 2.2
      *
      * @param string $output Passed by reference. Used to append additional content.
      * @param int $depth Depth of category. Used for tab indentation.
      * @param array $args Will only append content if style argument value is 'list'.
      */
     function start_lvl(&$output, $depth, $args) {
          global $this_instance;

          if ( 'list' != $args['style'] ) {
               return;
          }

          $indent = str_repeat("\t", $depth);
          $output .= "$indent<ul class='children " . $this_instance['child-class'] . "'>\n";
     }

     /**
      * @see Walker::end_lvl()
      * @since 2.2
      *
      * @param string $output Passed by reference. Used to append additional content.
      * @param int $depth Depth of category. Used for tab indentation.
      * @param array $args Will only append content if style argument value is 'list'.
      */
     function end_lvl(&$output, $depth, $args) {
          switch ($args['style']) {
               case 'image':
                    break;
               case 'list':
                    $indent = str_repeat("\t", $depth);
                    $output .= "$indent</ul>\n";
                    break;
          }

          return;
     }

     /**
      * @see Walker::start_el()
      * @since 2.2
      *
      * @param string $output Passed by reference. Used to append additional content.
      * @param object $category Category data object.
      * @param int $depth Depth of category in reference to parents.
      * @param array $args
      */
     function start_el(&$output, $category, $depth, $args) {
          global $this_instance;
          extract($args);

          $cat_name = esc_attr($category->name);
          $cat_name = apply_filters('list_cats', $cat_name, $category);
          $link = '<a href="' . esc_attr(get_term_link($category)) . '" ';

          if ( $use_desc_for_title == 0 || empty($category->description) ) {
               $link .= 'title="' . esc_attr(sprintf(__('View all recipes filed under %s'), $cat_name)) . '"';
          } else {
               $link .= 'title="' . esc_attr(strip_tags(apply_filters('category_description', $category->description, $category))) . '"';
          }

          if ( $this_instance['target'] != 'none' ) {
               $link.= ' target="' . $this_instance['target'] . '"';
          }

          $link .= '>';

          /* Set up link count */
          if ( !empty($show_count) ) {
               $count = ' ' . $this_instance['before-count'] . intval($category->count) . $this_instance['after-count'];
          } else {
               $count = '';
          }

          /* Set up show date */
          if ( !empty($show_date) ) {
               $date = ' ' . gmdate('Y-m-d', $category->last_update_timestamp);
          } else {
               $date = '';
          }

          switch ($args['style']) {
               case 'image':
                    $link .= get_term_thumbnail($category, array('size' => $this_instance['thumbnail_size'], 'add_span' => false)) . '</a>';
                    $output .= "\t$link\n";
                    break;
               case'list':
                    $link .= $cat_name . '</a> ' . $count;
                    $output .= "\t<li";
                    $class = 'cat-item cat-item-' . $category->term_id;
                    $class.= ' ' . $this_instance['item-class'];
                    if ( !empty($current_category) ) {
                         $_current_category = get_term($current_category, $category->taxonomy);
                         if ( $category->term_id == $current_category )
                              $class .= ' current-cat';
                         elseif ( $category->term_id == $_current_category->parent )
                              $class .= ' current-cat-parent';
                    }
                    $output .= ' class="' . $class . '"';
                    $output .= ">$link\n";
                    break;
               default:
                    $link .= $cat_name . '</a> ' . $count;
                    $output .= "\t$link<br />\n";
          }
     }

     /**
      * @see Walker::end_el()
      * @since 2.2
      *
      * @param string $output Passed by reference. Used to append additional content.
      * @param object $page Not used.
      * @param int $depth Depth of category. Not used.
      * @param array $args Only uses 'list' for whether should append to output.
      */
     function end_el(&$output, $page, $depth, $args) {
          if ( 'list' != $args['style'] ) {
               return;
          }

          $output .= "</li>\n";
     }

}

