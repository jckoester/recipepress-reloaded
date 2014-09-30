<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/*
 * Created on 30.10.2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class RPR_Widget_Taxonomy_Cloud extends WP_Widget {
	// constructor
	function RPR_Widget_Taxonomy_Cloud() {
		// use parent constructor to re-write standard class properties
		parent::WP_Widget('RPR_Widget_Taxonomy_Cloud_base', __('RPR :: Taxonomy Cloud', 'recipe-press-reloaded'), array('description' => __('Advanced tagcloud widget for to display any taxonomy with excludes', 'recipe-press-reloaded'), 'class' => 'rpr-widget-taxonomy-cloud'));	
	}

/**
	 * display widget
	 */	 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		//Prepare exclude string:
		$excludes=explode(',', $instance['exclude']);
        $excludestring="0";
        foreach($excludes as $ex):
            $excludestring.=",".$this->get_tag_id_by_name($ex);
        endforeach;
        //collect arguments for wp_tag_cloud
		$args = array(
			'smallest'                  => 10, 
    		'largest'                   => 22,
    		'unit'                      => 'px', 
    		'number'                    => $instance['limit'],  
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
        
        echo '<p class="tagcloud">';
        wp_tag_cloud($args);
        echo '</p>';		

		echo $after_widget;
	}
	
	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		// fill current state with old data to be sure we not loose anything
		$instance = $old_instance;
		// for example we want title always have capitalized first letter
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
		$instance['limit'] = intval($new_instance['limit']);
        $instance['exclude'] = trim($new_instance['exclude']);
		// and now we return new values and wordpress do all work for you
		return $instance;
	}
 
	/** @see WP_Widget::form */
	function form($instance) {
		$default = 	array( 
					'title' => '',
					'taxonomy'=>'category',
					'limit'=>45,
					'exclude'=>'',
		);
		$instance = wp_parse_args( (array) $instance, $default );
 
		$field_id = $this->get_field_id('title');
		$field_name = $this->get_field_name('title');
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title (optional)', 'recipe-press-reloaded').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['title'] ).'" /><label></p>';
        $field_id = $this->get_field_id('taxonomy');
		$field_name = $this->get_field_name('taxonomy');
        echo "\r\n <p><label for=\"$field_id\">". __('Taxonomy:', 'recipe-press-reloaded')." </label><select id=\"$field_id\" name=\"$field_name\" class=\"widefat\">";
		foreach(get_taxonomies() as $tax):
			echo '<option value="'.$tax.'" ' . selected( esc_attr( $instance['taxonomy'] ) , $tax, false) . '>' . $tax. '</option>';
     	endforeach;
		echo "</select></p>";
		
		$field_id = $this->get_field_id('limit');
		$field_name = $this->get_field_name('limit');
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Number auf items to display', 'recipe-press-reloaded').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['limit'] ).'" /><label></p>';
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

 add_action('widgets_init', create_function('', 'return register_widget("RPR_Widget_Taxonomy_Cloud");'));
