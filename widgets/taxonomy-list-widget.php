<?php

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * taxonomy-widget.php - sidebar widget for displaying recipe taxonomies.
 *
 * @package RecipePress
 * @subpackage widgets
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 2.2
 */
class RPR_Taxonomy_List_Widget extends WP_Widget {

     var $options = array();

     /**
      * Constructor
      */
     function RPR_Taxonomy_List_Widget() {
          load_plugin_textdomain('recipe-press', false, dirname(dirname(plugin_basename(__FILE__))) . '/language');

          /* translators: The description of the Category List widget on the Appearance->Widgets page. */
          $widget_ops = array('description' => __('List recipe taxonomies on your sidebar.', 'recipe-press-reloaded'));
          $control_ops = array('width' => 400, 'height' => 350);
          /* translators: The title for the Taxonomy List widget. */
          $this->WP_Widget('recipe_press_taxonomy_widget', __('RPR :: Taxonomy List', 'recipe-press-reloaded'), $widget_ops, $control_ops);

          /* Plugin Folders */
/*          $this->pluginPath = WP_PLUGIN_DIR . '/' . basename(dirname(dirname(__FILE__))) . '/';
          $this->pluginURL = WP_PLUGIN_URL . '/' . basename(dirname(dirname(__FILE__))) . '/';
          $this->templatesPath = WP_PLUGIN_DIR . '/' . basename(dirname(dirname(__FILE__))) . '/templates/';
          $this->templatesURL = WP_PLUGIN_URL . '/' . basename(dirname(dirname(__FILE__))) . '/templates/';*/

          include (RPR_PATH . 'php/class/custom-walkers.php');
          /*require_once (RPR_Path . 'php/class/rpr_core.php');
          $this->recipePress = new RPR_Core();*/
          global $RECIPEPRESSOBJ;
          $this->options = $RECIPEPRESSOBJ->loadSettings();
     }

     /**
      * Widget code
      */
     function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		//Prepare exclude string:
		$excludes=explode(',', $instance['exclude']);
        $excludestring="0";
        foreach($excludes as $ex):
            $excludestring.=",".$this->get_tag_id_by_name($ex);
        endforeach;
        
        if ( isset($instance['error']) && $instance['error'] ) {
        	return;
       	}

        echo $before_widget;
          
        if ( $instance['title'] ) {
        	echo $before_title . $instance['title'] . $after_title;
        }

	    echo '<ul id="the_' . $args['widget_id'] . '" class="rpr_taxonomy_list">';

          $taxArgs = array(
               'orderby' => $instance['order_by'],
               'order' => $instance['order'],
               'style' => 'list',
               'show_count' => $instance['show_count'],
               'hide_empty' => $instance['hide_empty'],
               'use_desc_for_title' => 1,
               'child_of' => 0,
               'exclude' => $instance['exclude'],
               'include' => get_published_categories($instance['taxonomy']),
               'hierarchical' => ($instance['taxonomy'] == 'recipe-ingredient' ) ? false : $this->options['taxonomies'][$instance['taxonomy']]['hierarchical'],
               'title_li' => '',
               'show_option_none' => __('No categories'),
               'number' => $instance['items'],
               'echo' => 1,
               'depth' => 0,
               'current_category' => 0,
               'pad_counts' => false,
               'taxonomy' => $instance['taxonomy'],
               'walker' => new Walker_RPR_Taxonomy
          );

          wp_list_categories($taxArgs);

         
      	echo '</ul>';
      	
          echo '<div class="cleared" style="clear:both"></div>';


          echo $after_widget;
     }
     
     /** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		// fill current state with old data to be sure we not loose anything
		$instance = $old_instance;
		// for example we want title always have capitalized first letter
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
		$instance['items'] = absint($new_instance['items']);
		$instance['order_by'] = strip_tags($new_instance['order_by']);
		$instance['order'] = strip_tags($new_instance['order']);
		$instance['show_count'] = absint($new_instance['show_count']);
		$instance['before_count'] = strip_tags($new_instance['before_count']);
		$instance['after_count'] = strip_tags($new_instance['after_count']);
		$instance['hide_empty'] = absint($new_instance['hide_empty']);
        $instance['exclude'] = trim($new_instance['exclude']);
		// and now we return new values and wordpress do all work for you
		return $instance;
	}

     /** @see WP_Widget::form */
     function form($instance) {
     	   $default = array(
				'title' => '',
			   	'taxonomy' => 'recipe-category', 
			   	'items' => $this->options['widget-items'],
			   	'order_by' => $this->options['widget-orderby'],	
               	'order' => $this->options['widget-order'],
               	'show_count' => false,
               	'before_count' => ' ( ',
               	'after_count' => ' ) ',
               	'hide_empty' => $this->options['widget-hide-empty'],
	            'exclude' => NULL,
          );
          
          $instance = wp_parse_args( $instance, $default );
          
           	$field_id = $this->get_field_id('title');
			$field_name = $this->get_field_name('title');
		    echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title (optional)', 'recipe-press-reloaded').': </label><input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['title'] ).'" /></p>';
		    
		    $field_id = $this->get_field_id('taxonomy');
			$field_name = $this->get_field_name('taxonomy');
		    echo "\r\n".'<p><label for="'.$field_id.'">'.__('Taxonomy to display', 'recipe-press-reloaded').': </label><select id="' . $field_id . '" name="' . $field_name . '" class="widefat">';
		    foreach(get_taxonomies() as $tax):
				echo '<option value="'.$tax.'" ' . selected( esc_attr( $instance['taxonomy'] ) , $tax, false) . '>' . $tax. '</option>';
     		endforeach;
          	echo "</select>";
          	
          	$field_id = $this->get_field_id('items');
        	$field_name = $this->get_field_name('items');
        	echo "\r\n".'<p><label for="'.$field_id.'">'. __('How many items would you like to display?', 'recipe-press-reloaded').' </label><input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['items'] ).'" style="width:50px;" /></p>';
          	
          	$field_id = $this->get_field_id('order_by');
        	$field_name = $this->get_field_name('order_by');
        	echo "\r\n".'<p><label for="'.$field_id.'">'. __('Order items by', 'recipe-press-reloaded').' </label><select id="' . $field_id . '" name="' . $field_name . '" class="widefat" style="width:100px;">';
        	echo '<option value="name" ' . selected($instance['order_by'], 'name', false) . '>' . __('Name', 'recipe-press-reloaded') .'</option>';
          	echo '<option value="count" ' . selected($instance['order_by'], 'count', false) . '>' .  __('Count', 'recipe-press-reloaded') . '</option>';
        	echo '</select>';
        	$field_id = $this->get_field_id('order');
        	$field_name = $this->get_field_name('order');
        	echo '<label for="'.$field_id.'"></label><select id="' . $field_id . '" name="' . $field_name . '" class="widefat" style="width:60px;" >';
        	echo '<option value="name" ' . selected($instance['order'], 'asc', false) . '>' . __('ASC', 'recipe-press-reloaded') .'</option>';
          	echo '<option value="count" ' . selected($instance['order'], 'desc', false) . '>' .  __('DESC', 'recipe-press-reloaded') . '</option>';
        	echo '</select>';
        	
        	$field_id = $this->get_field_id('show_count');
        	$field_name = $this->get_field_name('show_count');
        	echo "\r\n".'<p><input type="checkbox" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['title'] ).'" /><label for="'.$field_id.'">'.__('Show count with', 'recipe-press-reloaded') . '</label>';
        	$field_id = $this->get_field_id('before_count');
        	$field_name = $this->get_field_name('before_count');
        	echo "\r\n".'<input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['before_count'] ).'" style="width:20px;" /><label for="'.$field_id.'">' . __('before and', 'recipe-press-reloaded') . '</label>';
        	$field_id = $this->get_field_id('after_count');
        	$field_name = $this->get_field_name('after_count');
        	echo "\r\n".'<input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['after_count'] ).'" style="width:20px;" /><label for="'.$field_id.'">' . __('behind', 'recipe-press-reloaded') . '</label></p>';
        	
        	$field_id = $this->get_field_id('hide_empty');
        	$field_name = $this->get_field_name('hide_empty');
        	echo "\r\n".'<p><label for="'.$field_id.'">'.__('Hide empty terms?', 'recipe-press-reloaded') . '</label><input type="checkbox" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['hide_empty'] ).'" /></p>';
        	
        	$field_id = $this->get_field_id('exclude');
			$field_name = $this->get_field_name('exclude');
			echo "\r\n".'<p><label for="'.$field_id.'">'.__('Exclude terms', 'recipe-press-reloaded').': </label><textarea class="widefat" id="'.$field_id.'" name="'.$field_name.'">'.esc_attr( $instance['exclude'] ).'</textarea></p>';
     }
     
     	//From:  http://cfpg.me/post/WordPress%3A+Get+Tag+ID+using+only+the+Tag+Name/
	private function get_tag_id_by_name($tag_name) {
		global $wpdb;
		$tag_ID = $wpdb->get_var("SELECT * FROM ".$wpdb->terms." WHERE  `name` =  '".$tag_name."'");

		return $tag_ID;
	}

}

add_action('widgets_init', create_function('', 'return register_widget("RPR_Taxonomy_List_Widget");'));