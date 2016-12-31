<?php
/**
 * The Taxonomy list widget allows you to create tag lists not only from tags 
 * but from every taxonomy. Good to know: You can use this widget for any type 
 * of taxonomy, not only recipe related. 
 * 
 * @package    RecipePress reloaded
 * @subpackage Widgets
 * @author     Jan Köster <dasmaeh@cbjck.de>
 * @copyright  Copyright (c) 2016 Jan Köster
 * @link       http://tech.cbjck.de/wp/rpr
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Taxonomy List Widget Class
 *
 * @since 0.8.0
 */
class RPR_Widget_Taxonomy_List extends WP_Widget {
    
    /**
     * Set up the widget's unique name, ID, class, description, and other options.
     */
    function __construct() {
        /* Set up the widget options. */
        $widget_options = array(
            'classname'   => 'taxonomy_list',
            'description' => esc_html__( 'An advanced widget that gives you total control over the output of your taxonomies.', 'recipepress-reloaded' )
        );

        /* Set up the widget control options. */
        $control_options = array(
            'width'  => 800,
            'height' => 350
	);

        /* Create the widget. */
        parent::__construct(
            'rpr-taxonomy-list',
            __( 'Taxonomy List', 'recipepress-reloaded' )
        );
    }

    /**
     * Outputs the widget based on the arguments input through the widget controls.
     *
     * @since 0.6.0
     */
    function widget( $sidebar, $instance ) {
        extract( $sidebar );

        /* Set the $args for wp_tag_cloud() to the $instance array. */
        $args = $instance;

        /**
         *  Get and parse the arguments, defaults have been set during saving (hopefully)
         */
        extract($args, EXTR_SKIP);
        
        /** 
         * If there is an error, stop and return
         */
        if ( isset($instance['error']) && $instance['error'] ) {
            return;
       	}

        
         /* Output the theme's $before_widget wrapper. */
        echo $before_widget;
        
        /**
         * Output the title (if we have any)
         */
        if ( $instance['title'] ) {
            echo $before_title . sanitize_text_field( $instance['title'] ) . $after_title;
        }
        
        /**
         * Put together the list of terms
         */
        $terms = get_terms( $instance['taxonomy'], $args );
            if( count( $terms ) > 0 ){
                echo '<ul class="taglist">';
                foreach( $terms as $term ){
                    echo '<li>';
                        echo '<a href="' . esc_url_raw( get_term_link( $term, $instance['taxonomy'] ) ) . '">';
                        echo $term->name;

                        if( $instance['show_count'] == true ){
                            echo '&nbsp;';
                            echo sanitize_text_field( $instance['before_count'] );
                            echo sanitize_text_field( $term->count );
                            echo sanitize_text_field( $instance['after_count'] );
                        }
                        echo '</a>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="taglist taglist-warning">' . __('No terms found', 'recipepress-reloaded' ) .'</p>';
            }
            
        /**
         *  Close the theme's widget wrapper. 
         */
        echo $after_widget;         
     }
     
    /**
     * Updates the widget control options for the particular instance of the widget.
     *
     * @since 0.8.0
     */
    function update( $new_instance, $old_instance ) {
        // fill current state with old data to be sure we not loose anything
        $instance = $old_instance;
        
         /* Set the instance to the new instance. */
        //$instance = $new_instance;
		
        // Check and sanitize all inputs
        $instance['title']          = strip_tags( $new_instance['title'] );
        $instance['taxonomy']       = strip_tags( $new_instance['taxonomy'] );
        $instance['items']          = absint( $new_instance['items'] );
        $instance['order_by']       = strip_tags( $new_instance['order_by'] );
        $instance['order']          = strip_tags( $new_instance['order'] );
        $instance['show_count']     = boolval( $new_instance['show_count'] );
        $instance['before_count']   = strip_tags( $new_instance['before_count'] );
        $instance['after_count']    = strip_tags( $new_instance['after_count'] );
        $instance['hide_empty']     = boolval( $new_instance['hide_empty'] );
        //$instance['exclude']        = trim( $new_instance['exclude'] );
	
        // and now we return new values and wordpress do all work for you
        return $instance;
    }

    /**
     * Displays the widget control options in the Widgets admin screen.
     *
     * @since 0.8.0
     */
    function form( $instance ) {
        /* Set up the default form values. */
        $defaults = array(
            'title'         => '',
            'taxonomy'      => 'category', 
            'items'         => 0,
            'order_by'      => 'name',
            'order'         => 'ASC',
            'show_count'    => false,
            'before_count'  => ' ( ',
            'after_count'   => ' ) ',
            'hide_empty'    => false,
            //'exclude'       => NULL,
        );
          
        /* Merge the user-selected arguments with the defaults. */
        $instance = wp_parse_args( (array) $instance, $defaults );

        /* element options. */
        $title          = sanitize_text_field( $instance['title'] );
        $taxonomy       = sanitize_key( $instance['taxonomy'] );
        $items          = sanitize_text_field( $instance['items'] );
        $order_by       = sanitize_key( $instance['order_by'] );
        $order          = sanitize_sql_orderby( $instance['order'] );
        $show_count     = isset($instance['show_count']) ? (bool) $instance['show_count'] :false;
        $before_count   = sanitize_text_field( $instance['before_count'] );
        $after_count    = sanitize_text_field( $instance['after_count'] );
        $hide_empty     = isset($instance['hide_empty']) ? (bool) $instance['hide_empty'] :false;
        echo $show_count;
        $taxonomies = get_taxonomies( array( 'show_tagcloud' => true ), 'objects' );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?> ">
                <?php _e( 'Title (optional)', 'recipepress-reloaded' ); ?>
            </label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ) ?>" />
        </p>
		    
	<p>
            <label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>">
                <?php _e( 'Taxonomy to display', 'recipepress-reloaded' ); ?>
            </label>
             <select class="widefat" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" size="4" multiple="false">
                    <?php foreach ( $taxonomies as $tax ) { ?>
                        <option value="<?php echo $tax->name; ?>" <?php selected( in_array( $tax->name, (array) $taxonomy ) ); ?>><?php echo $tax->labels->singular_name; ?></option>
                    <?php } ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('items'); ?>">
                <?php _e( 'How many items would you like to display?', 'recipepress-reloaded' ); ?>
            </label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php $this->get_field_name('items'); ?>" value="<?php echo esc_attr( $items ); ?>" style="width:50px;" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'order_by' ); ?>">
                <?php _e( 'Order items by', 'recipepress-reloaded' ); ?>
            </label>
            <select id="<?php echo $this->get_field_id( 'order_by' ); ?>" name="<?php echo $this->get_field_name( 'order_by' ); ?>" class="widefat" style="width:100px;">
                <option value="name" <?php selected( $order_by, 'name', false ) ?> >
                    <?php _e( 'Name', 'recipepress-reloaded' ); ?>
                </option>
          	<option value="count" <?php selected( $order_by, 'count', false ); ?> >
                    <?php _e( 'Count', 'recipepress-reloaded' ); ?>
                </option>
            </select>
            <label for="<?php echo $this->get_field_id('order'); ?>">
            </label>
            <select id="<?php $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>" class="widefat" style="width:100px;" >'
                <option value="name" <?php selected( $order, 'asc', false ); ?> >
                    <?php _e( 'ASC', 'recipepress-reloaded' ); ?>
                </option>
          	<option value="count" <?php selected( $order, 'desc', false ); ?> >
                    <?php _e( 'DESC', 'recipepress-reloaded' ); ?>
                </option>
            </select>
        </p>
        <p>
            <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" <?php checked( $show_count ); ?> />
            <label for="<?php echo $this->get_field_id('show_count'); ?>">
                <?php _e( 'Show count with', 'recipepress-reloaded' ); ?>
            </label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('before_count'); ?>" name="<?php echo $this->get_field_name('before_count'); ?>" value="<?php echo esc_attr( $before_count ); ?>" style="width:20px;" />
            <label for="<?php echo $this->get_field_id('before_count'); ?>">
                <?php _e( 'before and', 'recipepress-reloaded' ); ?>
            </label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('after_count'); ?>" name="<?php echo $this->get_field_name('after_count'); ?>" value="<?php echo esc_attr( $after_count ); ?>" style="width:20px;" />
            <label for="<?php echo $this->get_field_id('after_count'); ?>">
                <?php _e( 'behind', 'recipepress-reloaded' ); ?>
            </label>
        </p>
        <p>
             <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('hide_empty'); ?>" name="<?php echo $this->get_field_name('hide_empty');?>" <?php checked( $hide_empty ); ?> />
            <label for="<?php echo $this->get_field_id('hide_empty'); ?>">
                <?php _e( 'Hide empty terms?', 'recipepress-reloaded' ); ?>
            </label>
        </p>
                <?php
//
//        	
//        	$field_id = $this->get_field_id('exclude');
//			$field_name = $this->get_field_name('exclude');
//			echo "\r\n".'<p><label for="'.$field_id.'">'.__('Exclude terms', 'recipe-press-reloaded').': </label><textarea class="widefat" id="'.$field_id.'" name="'.$field_name.'">'.esc_attr( $instance['exclude'] ).'</textarea></p>';
//     }
//     
//     	//From:  http://cfpg.me/post/WordPress%3A+Get+Tag+ID+using+only+the+Tag+Name/
//	private function get_tag_id_by_name($tag_name) {
//		global $wpdb;
//		$tag_ID = $wpdb->get_var("SELECT * FROM ".$wpdb->terms." WHERE  `name` =  '".$tag_name."'");
//
//		return $tag_ID;
//	}

    }
}
//add_action('widgets_init', create_function('', 'return register_widget("RPR_Taxonomy_List_Widget");'));