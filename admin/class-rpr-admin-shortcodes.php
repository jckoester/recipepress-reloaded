<?php
/**
 * The admin-specific shortcode functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The admin-specific shortcode functionality of the plugin.
 *
 * Loads and controls the dialogues, js and css for the shortcode insertion dialogues
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Admin_Shortcodes {

    /**
     * The version of this plugin.
     *
     * @since    0.8.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
	
	/**
     * Initialize the class and set its properties.
     *
     * @since    0.8.0
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $version ) {

        $this->version = $version;
    }
	
	/**
	 ************************ SHORTCODE FOR RECIPE *****************************
	 */
	/**
	 * Add a button for the shortcode dialog above the editor just as "Add Media"
	 * @param type $editor_id
	 * @return type
	 */
	public function add_button_scr( $editor_id = 'content' ) {
		global $post_type;
		if(!in_array($post_type, array( 'page', 'post' ) ) )
		   return;
		
		printf( '<a href="#" id="rpr-add-recipe-button" class="rpr-icon button" data-editor="%s" title="%s">%s</a>',
			esc_attr( $editor_id ),
			esc_attr__( 'Add Recipe', 'recipepress-reloaded' ),
			esc_html__( 'Add Recipe', 'recipepress-reloaded' )
		);
	}
	/**
	 * Function to load the modal overlay in the footer
	 * @global type $post_type
	 * @return type
	 */
	public function load_in_admin_footer_scr(){
		global $post_type;
		if(!in_array($post_type, array( 'page', 'post' ) ) )
		   return;

		include dirname( __FILE__ ) . '/views/rpr-modal-recipe.php';
	}
	/**
	 * Function to load the scripts needed for the ajax part in shortcode dialog
	 * @global type $post_type
	 * @param type $hook
	 * @return type
	 */
	public function load_ajax_scripts_scr( $hook ){
		global $post_type;
		
		// Only load on pages where it is necessary:
		if(!in_array($post_type,array( 'page', 'post' ) ) )
			return;

		wp_enqueue_script('rpr_ajax_scr', plugin_dir_url( __FILE__ ) . 'js/rpr_ajax_scr.js', array('jquery') );
		wp_localize_script('rpr_ajax_scr', 'rpr_vars', array(
				'rpr_ajax_nonce' => wp_create_nonce( 'rpr-ajax-nonce' )
			)
		);
		wp_localize_script( 'rpr_ajax_scr', 'rprRecipeScL10n', array(
			'noTitle' => __( 'No title', 'recipepress-reloaded' ),
			'recipe' => __( 'Recipe', 'recipepress-reloaded' ),
			'save' => __( 'Insert', 'recipepress-reloaded' ),
			'update' => __( 'Insert', 'recipepress-reloaded' ),
		) );
	}
	/**
	 * Process the data from the shortcode include dialog
	 * 
	 */
	public function process_ajax_scr() {
		check_ajax_referer( 'rpr-ajax-nonce', 'rpr_ajax_nonce' );

		$args = array();

		if ( isset( $_POST['search'] ) ){
			$args['s'] = wp_unslash( $_POST['search'] );
		} else {
			$args['s'] = '';
		}

		$args['pagenum'] = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

		$query=array(
			'posts_per_page' => 10,
		);
		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

		$recipes = get_posts(array('s'=> $args['s'], 'post_type' => 'rpr_recipe', 'posts_per_page' => $query['posts_per_page'], 'offset'=> $query['offset'], 'orderby'=> 'post_date'));

		$json = array();

		foreach($recipes as $recipe){
			array_push($json, array('id'=>$recipe->ID, 'title'=>$recipe->post_title));
		}

		wp_send_json($json);
		die();
	}
	
	
	/**
	 *********************** SHORTCODE FOR LISTINGS ****************************
	 */
	/**
	 * Add a button for the shortcode dialog above the editor just as "Add Media"
	 * @param type $editor_id
	 * @return type
	 */
	public function add_button_scl( $editor_id = 'content' ) {
		global $post_type;
		if(!in_array($post_type, array( 'page' ) ) )
		   return;
		
		printf( '<a href="#" id="rpr-add-listings-button" class="rpr-icon button" data-editor="%s" title="%s">%s</a>',
			esc_attr( $editor_id ),
			esc_attr__( 'Add Listing', 'recipepress-reloaded' ),
			esc_html__( 'Add Listing', 'recipepress-reloaded' )
		);
	}
	/**
	 * Function to load the modal overlay in the footer
	 * @global type $post_type
	 * @return type
	 */
	public function load_in_admin_footer_scl(){
		global $post_type;
		if(!in_array($post_type, array( 'page' ) ) )
		   return;

		include dirname( __FILE__ ) . '/views/rpr-modal-listings.php';
	}
	/**
	 * Function to load the scripts needed for the ajax part in shortcode dialog
	 * @global type $post_type
	 * @param type $hook
	 * @return type
	 */
	public function load_ajax_scripts_scl( $hook ){
		global $post_type;
		
		// Only load on pages where it is necessary:
		if(!in_array($post_type,array( 'page' ) ) )
			return;

		wp_enqueue_script('rpr_ajax_scl', plugin_dir_url( __FILE__ ) . 'js/rpr_ajax_scl.js', array('jquery') );
		wp_localize_script('rpr_ajax_scl', 'rpr_vars', array(
				'rpr_ajax_nonce' => wp_create_nonce( 'rpr-ajax-nonce' )
			)
		);
		wp_localize_script( 'rpr_ajax_scl', 'rprListingsScL10n', array(
			'noTitle' => __( 'No title', 'recipepress-reloaded' ),
			'recipe' => __( 'Recipe', 'recipepress-reloaded' ),
			'save' => __( 'Insert', 'recipepress-reloaded' ),
			'update' => __( 'Insert', 'recipepress-reloaded' ),
		) );
	}
}