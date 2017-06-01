<?php

/**
 * The admin-specific core functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The admin-specific core functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Admin {

    /**
     * The version of this plugin.
     *
     * @since    0.8.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    
     /**
     * The version of the database of this plugin.
     *
     * @since    0.8.0
     * @access   private
     * @var      string    $version    The current version of the database of this plugin.
     */
    private $dbversion;
    
	/**
     * instance of the general meta class handling all general information related functions
     * 
     * @since 0.8.0
     * @access public
     */
    public $generalmeta;
	
    /**
     * instance of the ingredients class handling all ingredient related functions
     * 
     * @since 0.8.0
     * @access public
     */
    public $ingredients;

    /**
     * instance of the instructions class handling all instruction related functions
     * 
     * @since 0.8.0
     * @access public
     */
    public $instructions;

    /**
     * instance of the nutritional meta class handling all nutritional information related functions
     * 
     * @since 0.8.0
     * @access public
     */
    public $nutrition;
	
    /**
     * instance of the shortcode class handling all shortcode insertion related functions and scripts
     * 
     * @since 0.8.0
     * @access public
     */
    public $shortcodes;
    
    /**
     * instance of the migration class handling all migration and database update tasks
     * 
     * @since 0.8.0
     * @access public
     */
    public $migration;
    
    /**
     * instance of the demo class to install demo data
     * 
     * @since 0.8.0
     * @access public
     */
    public $demo;
    
    /**
     * instance of class source to save recipe source
     * 
     * @since 0.9.0
     * @access public
     */
    public $source;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    0.8.0
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $version, $dbversion ) {

        $this->version = $version;
        $this->dbversion = $dbversion;
                
        require_once 'class-rpr-admin-generalmeta.php';
        $this->generalmeta = new RPR_Admin_GeneralMeta( $this->version );
		
        require_once 'class-rpr-admin-ingredients.php';
        $this->ingredients = new RPR_Admin_Ingredients( $this->version );
        
        require_once 'class-rpr-admin-instructions.php';
        $this->instructions = new RPR_Admin_Instructions( $this->version );
		
        require_once 'class-rpr-admin-nutrition.php';
        $this->nutrition = new RPR_Admin_NutritionMeta( $this->version );
		
        require_once 'class-rpr-admin-shortcodes.php';
        $this->shortcodes = new RPR_Admin_Shortcodes( $this->version );
        
        require_once 'class-rpr-admin-migration.php';
        $this->migration = new RPR_Admin_Migration( $this->version, $this->dbversion );
        
        require_once 'class-rpr-admin-demo.php';
        $this->demo = new RPR_Admin_Demo( $this->version, $this->dbversion );
        
        require_once 'class-rpr-admin-source.php';
        $this->source = new RPR_Admin_Source( $this->version );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.8.0
     */
    public function enqueue_styles() {
        /* General styles */
        wp_enqueue_style( 'recipepress-reloaded', plugin_dir_url( __FILE__ ) . 'css/rpr-admin.css', array (), $this->version, 'all' );
        /* Font Awesome style */
        wp_enqueue_style( 'recipepress-reloaded' . '-fa', plugin_dir_url( dirname( __FILE__ ) ) . 'libraries/font-awesome/css/font-awesome.min.css', array (), $this->version, 'all' );
        /* Styles for modal overlays */
        wp_enqueue_style('rpr_modal', plugin_dir_url( __FILE__ ) . '/css/rpr-modal.css');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.8.0
     */
    public function enqueue_scripts($hook) {
        /**
         * @todo minify the JavaScript! Or at least put all in one file
         */
		//wp_enqueue_script( 'recipepress-reloaded', plugin_dir_url( __FILE__ ) . 'js/rpr-admin.js', array ( 'jquery' ), $this->version, false );
        global $post;

        if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
            if ( 'rpr_recipe' === $post->post_type ) {
                
                wp_enqueue_script( 'recipepress-reloaded' . '_meta_ing_table', plugin_dir_url( __FILE__ ) . 'js/rpr-admin-ing-meta-table.js', array ( 'jquery' ), $this->version, false );
                wp_enqueue_script( 'recipepress-reloaded' . '_meta_ing_link', plugin_dir_url( __FILE__ ) . 'js/rpr-admin-ing-meta-link.js', array ( 'jquery' ), $this->version, false );
                wp_enqueue_script( 'recipepress-reloaded' . '_meta_ins_table', plugin_dir_url( __FILE__ ) . 'js/rpr-admin-ins-meta-table.js', array ( 'jquery' ), $this->version, false );
				// Load jQuery suggest script to add autocomplete to ingredients
				//wp_enqueue_script( 'suggest' );
            }
        }

        $translations = array(
            'ins_img_upload_title'  => __( 'Insert instruction image', 'recipepress-reloaded' ),
            'ins_img_upload_text'   => __( 'Insert image', 'recipepress-reloaded' )
        );

        wp_localize_script( 'recipepress-reloaded' . '_meta_ins_table', 'ins_trnsl', $translations);
		
		if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'use_nutritional_data') , false ) ) {
			wp_enqueue_script( 'recipepress-reloaded' . '_meta_nutrition', plugin_dir_url( __FILE__ ) . 'js/rpr-admin-nutrition.js', array ( 'jquery' ), $this->version, false );
		}
                if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'use_source') , false ) ) {
			wp_enqueue_script( 'recipepress-reloaded' . '_meta_source', plugin_dir_url( __FILE__ ) . 'js/rpr-admin-source-meta-link.js', array ( 'jquery' ), $this->version, false );
		}
                
		
        // Load jQuery Link script to add links to ingredients
        wp_enqueue_script( 'wp-link' );
		

		
    }

    /**
     * Load Admin Page Framework and create the options page
     * 
     * @since 0.8.0
     */
    public function create_options() {
        /**
         * The class creating the options page
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/views/class-rpr-options.php';
        
        // Instantiate Admin Page Framework
        new RPR_Options( 'recipepress-reloaded', $this->version );
    }

    /**
     * Save the recipe data to database.
     * This function does all the preparation and handling to combine taxonomies
     * and metadata to a complete recipe
     * @param int   $recipe_id  Post-Id of recipe to save
     * @param mixed $recipe     The $recipe post object
     * @since 0.8.0
     */
    public function save_recipe( $recipe_id, $recipe = NULL ){
        remove_action('save_post', array($this, 'save_recipe'));

    	$data=$_POST;

        /**
         *  This is done for testing! REMOVE WHEN DONE!
		 */
        //var_dump( $_POST);
        //die;
        //var_dump( $recipe );
			//die;
		if( $recipe !== NULL && $recipe->post_type == 'rpr_recipe' ) {
    		$errors = false;
    		// verify if this is an auto save routine.
    		// If it is our form has not been submitted, so we dont want to do anything
    		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    			$errors = "There was an error doing autosave";

    		//Verify the nonces for the metaboxes
    		if ( isset( $data['rpr_save_recipe_meta_field'] ) &&  !wp_verify_nonce( $data['rpr_save_recipe_meta_field'], 'rpr_save_recipe_meta' ) ){
    			$errors = "There was an error saving the recipe. Description nonce not verified";
    		}
			
			// Check permissions
    		if ( !current_user_can( 'edit_post', $recipe_id ) ){
    			$errors = "There was an error saving the recipe. No sufficient rights.";
    		}

    		//If we have an error update the error_option and return
    		if( $errors ) {
    			update_option('rpr_admin_errors', $errors);
    			return $recipe_id;
    		}
			
			//if(!isset($data)||$data==""){$data=$_POST;}
			if( $recipe !== NULL && $recipe->post_type == 'rpr_recipe' )
			{
                            /**
                             * This is for testing! REMOVE WHEN DONE!
				echo "<pre>";
				foreach( $data as $key => $value){
					echo $key . "</br>";
				}
				//die;
                             */
				$this->generalmeta->save_generalmeta( $recipe_id, $data, $recipe );

				if( isset( $data['rpr_recipe_ingredients'] ) ) {
					$this->ingredients->save_ingredients( $recipe_id, $data['rpr_recipe_ingredients'] );
				}
				if( isset( $data['rpr_recipe_instructions'] ) ) {
					$this->instructions->save_instructions( $recipe_id, $data['rpr_recipe_instructions']);
				}

				if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'use_nutritional_data') , false ) ) {
					$this->nutrition->save_nutritionalmeta($recipe_id, $data, $recipe);
				}
                if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'use_source') , false ) ) {
					$this->source->save_sourcemeta($recipe_id, $data, $recipe);
				}
				//die;
				add_action('save_post', array($this, 'save_recipe'));
			}
		}
    }
	
	/**
	 * Function to display any errors in the backend
	 * @since 0.8.0
	 */
	// Display any errors
	public function admin_notice_handler() {

		$errors = get_option('rpr_admin_errors');

		if($errors) {
			echo '<div class="error"><p>' . $errors . '</p></div>';
		}
		
		// Reset the error option for the next error
		update_option('rpr_admin_errors', false);
	}

	/**
	 * Adds recipes to the 'Recent Activity' Dashboard widget
	 * 
	 * @since 0.8.3
	 * @param array $query_args
	 */
	public function add_to_dashboard_recent_posts_widget( $query_args ) {
		$query_args =  array_merge( $query_args, array( 'post_type' => array( 'post', 'rpr_recipe' ) ));
		return $query_args;
	}

	/**
	 * Adds recipes to the 'At a Glance' Dashboard widget
	 * 
	 * @since 0.8.3
	 * @param array $items
	 */
	public function add_recipes_glance_items( $items = array() ) {
			$num_recipes = wp_count_posts( 'rpr_recipe' );
			
			if( $num_recipes ) {
				$published = intval( $num_recipes->publish );
				$post_type = get_post_type_object( 'rpr_recipe' );
				
				$text = _n( '%s ' . $post_type->labels->singular_name, '%s ' . $post_type->labels->name, $published, 'recipepress-reloaded' );
				$text = sprintf( $text, number_format_i18n( $published ) );
				
				if ( current_user_can( $post_type->cap->edit_posts ) ) {
					$items[] = sprintf( '<a class="%1$s-count" href="edit.php?post_type=%1$s">%2$s</a>', 'rpr_recipe', $text ) . "\n";
				} else {
					$items[] = sprintf( '<span class="%1$s-count">%2$s</span>', 'rpr_recipe', $text ) . "\n";
				}
			}
    
		return $items;
	}

    /**
    * Recipe update messages.
    * See /wp-admin/edit-form-advanced.php
    * @param array $messages Existing post update messages.
    * @return array Amended post update messages with new recipe update messages.
    */
    function updated_rpr_messages( $messages ) {
    $post             = get_post();
    $post_type        = get_post_type( $post );
    $post_type_object = get_post_type_object( $post_type );

    $messages['rpr_recipe'] = array(
        0  => '', // Unused. Messages start at index 1.
        1  => __( 'Recipe updated.', 'recipepress-reloaded' ),
        2  => __( 'Custom field updated.', 'recipepress-reloaded' ),
        3  => __( 'Custom field deleted.', 'recipepress-reloaded' ),
        4  => __( 'Recipe updated.', 'recipepress-reloaded' ),
        /* translators: %s: date and time of the revision */
        5  => isset( $_GET['revision'] ) ? sprintf( __( 'Recipe restored to revision from %s', 'recipepress-reloaded' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6  => __( 'Recipe published.', 'recipepress-reloaded' ),
        7  => __( 'Recipe saved.', 'recipepress-reloaded' ),
        8  => __( 'Recipe submitted.', 'recipepress-reloaded' ),
        9  => sprintf(
            __( 'Recipe scheduled for: <strong>%1$s</strong>.', 'recipepress-reloaded' ),
            // translators: Publish box date format, see http://php.net/date
            date_i18n( __( 'M j, Y @ G:i', 'recipepress-reloaded' ), strtotime( $post->post_date ) )
        ),
        10 => __( 'Recipe draft updated.', 'recipepress-reloaded' )
    );

    if ( $post_type_object->publicly_queryable && 'rpr_recipe' === $post_type ) {
        $permalink = get_permalink( $post->ID );

        $view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View recipe', 'recipepress-reloaded' ) );
        $messages[ $post_type ][1] .= $view_link;
        $messages[ $post_type ][6] .= $view_link;
        $messages[ $post_type ][9] .= $view_link;

        $preview_permalink = add_query_arg( 'preview', 'true', $permalink );
        $preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview recipe', 'recipepress-reloaded' ) );
        $messages[ $post_type ][8]  .= $preview_link;
        $messages[ $post_type ][10] .= $preview_link;
    }
        return $messages;
    }
}
