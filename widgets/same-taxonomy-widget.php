<?php
/*
 * Created on 31.10.2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
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
class RPR_Same_Taxonomy_Widget extends WP_Widget {
	// constructor
	function RPR_Same_Taxonomy_Widget() {
		// use parent constructor to re-write standard class properties
		parent::WP_Widget('SameTaxonomy_Widget_base', __('RPR :: Same Taxonomy', 'recipe-press-reloaded'), array('description' => __('Lists posts from the same taxonomy on single-post view. Will not display elsewhere', 'recipe-press-reloaded'), 'class' => 'rpr-same-taxonomy-widget'));	
	}

/**
	 * display widget
	 */	 
	function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		
		if ( isset($instance['error']) && $instance['error'] ) {
        	return;
       	}

        $post_id = get_the_id();
		$terms= get_the_terms($post_id, $instance['taxonomy']);

		if($terms):
			$id=array_pop(array_values($terms))->term_id;
            $name=array_pop(array_values($terms))->name;

 		   	echo $before_widget;
          
       		if ( $instance['title'] ) {
        		echo $before_title . $instance['title'] . $after_title;
        	}
 		   	
 		   	$the_query= null;
			
			$the_query = new WP_Query(array(
					'post_type'=>array('post', 'recipe'), 
					'tax_query' => array( array(
											'taxonomy' => $instance['taxonomy'],
											'field' => 'term_id',
											'terms' => $id
									)),
					'posts_per_page'=>$instance['limit'],
					'paged'=>1
			));

    		echo '<ul class="rpr-same-taxonomy-list">';
			while ( $the_query->have_posts() ):
				$the_query->the_post();
				if(get_the_id() != $post_id):
        			echo "<li><a href=\"".get_permalink()."\">".get_the_title()."</a></li>";
        		endif;
			endwhile;
    		echo '</ul>';
		endif;
		
		echo $after_widget;
	}
	
	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		// fill current state with old data to be sure we not loose anything
		$instance = $old_instance;
		// for example we want title always have capitalized first letter
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
        $instance['limit'] = strip_tags($new_instance['limit']);
		// and now we return new values and wordpress do all work for you
		return $instance;
	}
 
	/** @see WP_Widget::form */
	function form($instance) {
		$default = 	array( 
			'title' => '',
			'taxonomy'=>'recipe-category',
			'limit'=>5 
		);
		
		$instance = wp_parse_args( (array) $instance, $default );
 
		$field_id = $this->get_field_id('title');
		$field_name = $this->get_field_name('title');
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Title (optional)', 'recipe-press-reloaded').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['title'] ).'" /><label></p>';
        
        $field_id = $this->get_field_id('taxonomy');
		$field_name = $this->get_field_name('taxonomy');
        echo "\r\n <p><label for=\"$field_id\">". __('Taxonomy to display', 'recipe-press-reloaded').": </label><select id=\"$field_id\" name=\"$field_name\" class=\"widefat\">";
		foreach(get_taxonomies() as $tax):
			$selected = ($instance['taxonomy'] == $tax) ? "selected=\"selected\"": "";
			echo"<option value=\"".$tax."\" $selected>".$tax."</option>";
     	endforeach;
		echo "</select></p>";
		
		$field_id = $this->get_field_id('limit');
		$field_name = $this->get_field_name('limit');
		echo "\r\n".'<p><label for="'.$field_id.'">'.__('Number of posts', 'recipe-press-reloaded').': <input type="text" class="widefat" id="'.$field_id.'" name="'.$field_name.'" value="'.esc_attr( $instance['limit'] ).'" /><label></p>';
	}
}

add_action('widgets_init', create_function('', 'return register_widget("RPR_Same_Taxonomy_Widget");'));
?>
