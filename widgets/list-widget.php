<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * list-widget.php - sidebar widget for listing recipes.
 *
 * @package RecipePress
 * @subpackage widgets
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 1.0
 */
class RPR_Widget_List_Recipes extends WP_Widget {

     var $options = array();

     /**
      * Constructor
      */
     function RPR_Widget_List_Recipes() {
          global $RECIPEPRESSOBJ;

          load_plugin_textdomain('recipe-press', false, dirname(dirname(plugin_basename(__FILE__))) . '/language');

          /* translators: The description of the Recpipe List widget on the Appearance->Widgets page. */
          $widget_ops = array('description' => __('List recipes on your sidebar. By dasmaeh.', 'recipe-press-reloaded'));
          /* translators: The title for the Recipe List widget. */
          $this->WP_Widget('recipe_press_list_widget', __('RPR :: List', 'recipe-press-reloaded'), $widget_ops);

          $this->options = $RECIPEPRESSOBJ->loadSettings();
     }

     function defaults($args = array()) {
          $defaults = array(
               'title' => '',
               'items' => $this->options['widget-items'],
               'type' => $this->options['widget-type'],
               'sort_order' => $this->options['widget-sort'],
               'category' => false,
               'submit_link' => false,
               'linktarget' => $this->options['widget-target'],
               'show-icon' => isset($args['items']) ? isset($args['show-icon']) : isset($this->options['widget-show-icon']),
               'icon-size' => $this->options['widget-icon-size'],
               'li-class' => 'recipe-press-list-class',
          );

          return wp_parse_args($args, $defaults);
     }

     /**
      * Widget code
      */
     function widget($args, $instance) {
          if ( isset($instance['error']) && $instance['error'] ) {
               return;
          }

          extract($args, EXTR_SKIP);

	      echo $before_widget;

          if ( $instance['title'] ) {
               echo $before_title . $instance['title'] . $after_title;
          }

          echo '<ul class="rpr-widget-list">';

          $options = array(
               'post_type' => 'recipe',
               'order' => $instance['sort_order'],
               'numberposts' => $instance['items'],
          );

          switch ($instance['type']) {
               case 'newest':
                    $options['ordeby'] = 'date';
                    $options['order'] = 'desc';
                    break;
               case 'random':
                    $options['orderby'] = 'rand';
                    break;
               case 'featured':
                    $options['meta_key'] = '_recipe_featured_value';
                    $options['meta_value'] = 1;
                    break;
               case 'updated':
                    $options['orderby'] = 'modified';
                    break;
               default:
                    break;
          }

          $recipes = get_posts($options);

          foreach ( $recipes as $recipe ) :
               echo '<li class="' . $instance['li-class'] .'">';
               
               if ( $instance['show_icon'] && function_exists('has_post_thumbnail') && has_post_thumbnail($recipe->ID) ) {
                    echo get_the_post_thumbnail($recipe->ID, array($instance['icon_size'], $instance['icon_size']));
               }

               echo '<a href="' . get_post_permalink($recipe->ID) .'" >' . apply_filters('the_title', $recipe->post_title) .'</a>';
          	   echo '</li>';
		  endforeach;

          echo '</ul>';
          
          echo $after_widget;
          }

          /** @see WP_Widget::form */
          function form($instance) {
               global $RECIPEPRESSOBJ;
               
               $default = array(
               		'title' => '',
               		'items' => 10,
               		'type' => 'newest',
               		'sort_order' => 'asc',
               		'show_icon' => true,
               		'icon_size' => 50,
               		'li-class' => 'rpr-list-class',
          		);
			   $instance = wp_parse_args( $instance, $default );
 
			   $field_id = $this->get_field_id('title');
			   $field_name = $this->get_field_name('title');
		       echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title (optional)', 'recipe-press-reloaded').': </label><input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['title'] ).'" /></p>';
        	   $field_id = $this->get_field_id('items');
        	   $field_name = $this->get_field_name('items');
        	   echo "\r\n".'<p><label for="'.$field_id.'">'. __('How many items would you like to display?', 'recipe-press-reloaded').': </label><input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['items'] ).'" style="width:50px;" /></p>';
        	   $field_id = $this->get_field_id('type');
        	   $field_name = $this->get_field_name('type');
        	   echo "\r\n <p><label for=\"$field_id\">". __('Display type:', 'recipe-press-reloaded')." </label><select id=\"$field_id\" name=\"$field_name\" class=\"widefat\">";
			   echo "<option value=\"newest\" ".selected( esc_attr( $instance['type'] ) , 'newest', false) .">". __('Newest Recipes', 'recipe-press-reloaded') ."</option>";
			   echo "<option value=\"random\" ".selected( esc_attr( $instance['type'] ) , 'random', false) .">". __('Random Recipes', 'recipe-press-reloaded') ."</option>";
			   echo "<option value=\"featured\" ".selected( esc_attr( $instance['type'] ) , 'featured', false) .">". __('Featured', 'recipe-press-reloaded') ."</option>";
			   echo "<option value=\"updated\" ".selected( esc_attr( $instance['type'] ) , 'updated', false) .">". __('Recently Updated', 'recipe-press-reloaded') ."</option>";
          	   echo "</select></p>";
			   $field_id = $this->get_field_id('sort_order');
        	   $field_name = $this->get_field_name('sort_order');
        	   echo "\r\n <p><label for=\"$field_id\">". __('Sort order:', 'recipe-press-reloaded')." </label><select id=\"$field_id\" name=\"$field_name\" class=\"widefat\">";
        	   echo "<option value=\"asc\" ".selected( esc_attr( $instance['sort_order'] ) , 'asc', false) .">". __('Ascending', 'recipe-press-reloaded') ."</option>";
        	   echo "<option value=\"desc\" ".selected( esc_attr( $instance['sort_order'] ) , 'desc', false) .">". __('Descending', 'recipe-press-reloaded') ."</option>";
        	   echo "</select></p>";
        	   $field_id = $this->get_field_id('show_icon');
        	   $field_name = $this->get_field_name('show_icon');
        	   echo "\r\n".'<p><label for="'.$field_id.'">'. __('Show icon?', 'recipe-press-reloaded').': </label><input type="checkbox" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="1" '.esc_attr(  checked($instance['show_icon'], true, false) ).'"  /></p>';
        	   $field_id = $this->get_field_id('icon_size');
        	   $field_name = $this->get_field_name('icon_size');
        	   echo "\r\n".'<p><label for="'.$field_id.'">'. __('Icon size (square)', 'recipe-press-reloaded').': </label><input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['icon_size'] ).'" style="width:50px;" /></p>';
          }
          
          /** @see WP_Widget::update */
		function update($new_instance, $old_instance) {
			// fill current state with old data to be sure we not loose anything
			$instance = $old_instance;
			// for example we want title always have capitalized first letter
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['items'] = absint($new_instance['items']);
			$instance['type'] = strip_tags($new_instance['limit']);
        	$instance['sort_order'] = strip_tags($new_instance['sort_order']);
        	$instance['show_icon'] = strip_tags($new_instance['show_icon']);
        	$instance['icon_size'] = absint($new_instance['icon_size']);
        	$instance['li-class'] = 'rpr-list-class'; //absint($new_instance['li-class']);
			// and now we return new values and wordpress do all work for you
			return $instance;
		}

     }

     add_action('widgets_init', create_function('', 'return register_widget("RPR_Widget_List_Recipes");'));