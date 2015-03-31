<?php

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * taxonomy-widget.php - sidebar widget for displaying recipe taxonomies.
*/

class RPR_Widget_Taxonomy_List extends WP_Widget {

     var $options = array();

     /**
      * Constructor
      */
	function RPR_Widget_Taxonomy_List() {
		// use parent constructor to re-write standard class properties
		parent::WP_Widget(
			'RPR_Widget_Taxonomy_List_base', 
			__('Taxonomy List', 'recipe-press-reloaded'), 
			array(
				'description' => __('Allows you to create tag lists not only from tags but from every taxonomy. Good to know: You can use this widget for any type of taxonomy, not only recipe related.', 'recipe-press-reloaded'),
				'class' => 'rpr-widget-taxonomy-cloud'
				)
			);	
	}

     /**
      * Widget code
      */
     function widget($args, $instance) {
     	global $rpr_option;
		
		$excludestring='';
		
		if($instance['taxonomy']=='rpr_ingredient'){
			$excludestring = $rpr_option['ingredients_exclude_list'];
		}
        
        if ( isset($instance['error']) && $instance['error'] ) {
        	return;
       	}

         extract($args, EXTR_SKIP);
        //collect arguments for wp_tag_cloud
		$args = array(
			'smallest'                  => 10, 
    		'largest'                   => 22,
    		'unit'                      => 'px', 
    		//'number'                    => $instance['limit'],  
    		'format'                    => 'flat',
    		'separator'                 => "\n",
    		'orderby'                   => 'name', 
    		'order'                     => 'ASC',
    		'exclude'                   => $excludestring, 
    		'include'                   => null, 
    		'topic_count_text_callback' => 'default_topic_count_text',
    		'link'                      => 'view', 
    		'taxonomy'                  => $instance['taxonomy'], 
    		'echo'                      => true 
    		);
    		
		echo $before_widget;
		
		if ( $instance['title'] ) {
               echo $before_title . $instance['title'] . $after_title;
         }
        
        
        $terms = get_terms( $instance['taxonomy'], $args );
		if( count( $terms ) > 0 ){
			echo '<ul class="taglist">';
			foreach( $terms as $term ){
				echo '<li>';
				//var_dump($term);
				echo '<a href="' . get_term_link( $term, $instance['taxonomy'] ) . '">' . $term->name ;
				//var_dump($instance['show_count']);
				if( $instance['show_count'] == true ){
					var_dump($term);
				}
				echo '</a>';
				echo '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p class="taglist taglist-warning">' . __('No terms found', 'recipepress-reloaded' ) .'</p>';
		}
        		

		echo $after_widget;
     }
     
     /** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		// fill current state with old data to be sure we not loose anything
		$instance = $old_instance;
		
		//var_dump($new_instance); die;
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
			   	//'items' => $this->options['widget-items'],
			   	//'order_by' => $this->options['widget-orderby'],	
               	//'order' => $this->options['widget-order'],
               	'show_count' => false,
               	'before_count' => ' ( ',
               	'after_count' => ' ) ',
               	//'hide_empty' => $this->options['widget-hide-empty'],
	            'exclude' => NULL,
          );
          
          $instance = wp_parse_args( $instance, $default );
          
           	$field_id = $this->get_field_id('title');
			$field_name = $this->get_field_name('title');
		    echo "\r\n".'<p><label for="'.$field_id.'"><code>'.__('Title (optional)', 'recipe-press-reloaded').': </code></label><input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['title'] ).'" /></p>';
		    
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