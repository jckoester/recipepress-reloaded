<?php

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * form_tags.php - Additional form tags for RecipePress
 *
 * @package RecipePress
 * @subpackage includes
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 2.0
 */


function recipe_dropdown_pages($args = '') {

     $args = apply_filters('before_recipe_dropdown_pages', $args);

     $defaults = array(
          'depth' => 0, 'child_of' => 0,
          'selected' => 0, 'echo' => 1,
          'name' => 'page_id', 'id' => '',
          'show_option_none' => '',
          'show_option_no_change' => '',
          'option_none_value' => '',
          'post_type' => array('page', 'post', 'recipe')
     );

     $r = wp_parse_args($args, $defaults);

     extract($r, EXTR_SKIP);

     $pages = array();

     if ( !is_array($post_type) ) {
          $post_type = array($post_type);
     }

     foreach ( $post_type as $type ) {
          $r = array(
               'post_type' => $type,
               'numberposts' => -1,
               'selected' => null,
          );
          $results[$type] = get_posts($r);
     }

     $output = '';
     $name = esc_attr($name);
     // Back-compat with old system where both id and name were based on $name argument
     if ( empty($id) )
          $id = $name;

     if ( !empty($results) ) {
          $output = "<select name=\"$name\" id=\"$id\">\n";
          if ( $show_option_no_change )
               $output .= "\t<option value=\"-1\">$show_option_no_change</option>";
          if ( $show_option_none )
               $output .= "\t<option value=\"" . esc_attr($option_none_value) . "\">$show_option_none</option>\n";

          // $output .= walk_page_dropdown_tree($pages, $depth, $r);

          foreach ( $results as $ptype => $result ) {
               $output .= '<optgroup label="' . ucfirst($ptype) . '">';
               $output .= walk_page_dropdown_tree($result, $depth, $r);
               $output .= '</optgroup>';
          }

          $output .= "</select>\n";
     }

     $output = apply_filters('recipe_dropdown_pages', $output);

     if ( $echo )
          echo $output;

     return $output;
}