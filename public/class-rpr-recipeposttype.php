<?php

/**
 * The recipe post type
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/public
 */

// Include the library file. Set your file path here.
include( dirname( dirname( __FILE__ ) ) . '/libraries/apf/admin-page-framework.php' );

/**
 * The recipe post type.
 *
 * Defines the custom post type, the related taxonomies.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/public
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_RecipePostType extends AdminPageFramework_PostType{
    
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
        
        parent::__construct( 'rpr_recipe' );
    }

    /**
     * function to actually create the post type, similar to register_post_type()
     * A little different though
     * 
     * @since 0.8.0
     */
     public function setUp() {
        /**
         * Check which of wp core's taxonomies to use;
         */
        $taxonomies = array();
        if( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'category', 'use' ) , false ) ){
            array_push($taxonomies, 'category' );
        }
        if( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'post_tag', 'use' ) , false ) ){
            array_push($taxonomies, 'post_tag' );
        }
        
        /** 
         * Setup the post type
         */
        $this->setArguments(
            array( // argument - for the array structure, refer to http://codex.wordpress.org/Function_Reference/register_post_type#Arguments
                'labels'        => array(
                    'name'                  => __( 'Recipes', 'recipepress-reloaded' ),
                    'singular_name'         => __( 'Recipe', 'recipepress-reloaded' ),
                    'add_new'               => __( 'Add new', 'recipepress-reloaded' ),
                    'add_new_item'          => __( 'Add new recipe', 'recipepress-reloaded' ),
                    'edit'                  => __( 'Edit', 'recipepress-reloaded' ),
                    'edit_item'             => __( 'Edit recipe', 'recipepress-reloaded' ),
                    'new_item'              => __( 'New recipe', 'recipepress-reloaded' ),
                    'view'                  => __( 'View', 'recipepress-reloaded' ),
                    'view_item'             => __( 'View recipe', 'recipepress-reloaded' ),
                    'search_items'          => __( 'Search recipes', 'recipepress-reloaded' ),
                    'not_found'             => __( 'No recipes found.', 'recipepress-reloaded' ),
                    'not_found_in_trash'    => __( 'No recipes found in trash.', 'recipepress-reloaded' ),
                    'plugin_listing_table_title_cell_link' => __( 'RPR recipe post type', 'recipepress-reloaded' ), // (framework specific key). [3.0.6+]
                ),
                'supports'      => array( 'title', 'comments', 'thumbnail', 'excerpt', 'featured', 'author' ),
                //'supports'      => array( 'title', 'editor', 'comments', 'thumbnail', 'excerpt' ),
                'public'        => true,
                'menu_icon'     => plugins_url( 'img/logo_16x16.png', dirname( __FILE__ ) ),
                'screen_icon'   => dirname( __FILE__  ) . '/img/logo_32x32.png', // a file path can be passed instead of a url, plugins_url( 'asset/image/wp-logo_32x32.png', APFDEMO_FILE )
                'menu_position' => 5, 
                'has_archive'   => true, 
                'taxonomies'    => $taxonomies,
                'rewrite'       => array(
                    'slug' => sanitize_title( AdminPageFramework::getOption( 'rpr_options', array( 'general', 'slug') , 'recipe' ) )
                ),
 
            )    
        );
        
        /** 
         * Setup the ingredient taxonomy
         */
        $name = AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'singular') , __('ingredient', 'recipepress-reloaded') );
        $this->addTaxonomy( 
            'rpr_ingredient',  // taxonomy slug
            array(                  // argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
                'labels'                => array(
                    'name'          => AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'plural') , __('Ingredients', 'recipepress-reloaded') ),
                    'singular'      => $name,
                    'add_new_item'  => sprintf( __( 'Add New %s', 'recipepress-reloaded' ), $name ),
                    'new_item_name' => sprintf( __( 'New %s', 'recipepress-reloaded' ), $name ),
                ),
                'show_ui'               => true,
                'show_tagcloud'         => false,
                'hierarchical'          => false,
                'show_admin_column'     => false,
                'show_in_nav_menus'     => false,
                'show_table_filter'     => false,    // framework specific key
                'show_in_sidebar_menus' => true,    // framework specific key
                'rewrite'               => array(
                    'slug'  => sanitize_title( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'slug') , 'ingredient' ) )
                ),
            )
        );
        // Add aditional fields:
        require_once 'class-rpr-ingredientmeta.php';
        new RPR_IngredientMeta( 'rpr_ingredient' );
       
        /**
         * add custom taxonomies
         * We need to be very carefule as we can't be sure the user has set all 
         * fields in the correct way!
         * However verifaction is done at input. Still, never trust ...
         * 
         * @since 0.8.0
         */
        foreach( AdminPageFramework::getOption( 'rpr_options', 'tax_custom' , array() ) as $taxonomy ){
                        
            // Only add if singular and slug are set:
            if( isset( $taxonomy['singular'] ) && 
                '' != (string) $taxonomy['singular'] &&
                isset( $taxonomy['slug'] ) && 
                '' != (string) $taxonomy['slug']){
           
                $name = sanitize_text_field( $taxonomy['singular'] );
                
                if( isset( $taxonomy['plural'] ) && 
                '' != (string) $taxonomy['plural']) {
                    $plural = sanitize_text_field( $taxonomy['plural'] );
                } else {
                    $plural = $name . __( 's', 'recipepress-reloaded' );
                }
                
                $this->addTaxonomy( 
                    sanitize_title( $taxonomy['slug'] ),  // taxonomy slug
                    array(                  // argument - for the argument array keys, refer to : http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
                    'labels'            => array(
                        'name'          => $plural,
                        'singular'      => $name,
                        'add_new_item'  => sprintf( __( 'Add New %s', 'recipepress-reloaded' ), $name ),
                        'new_item_name' => sprintf( __( 'New %s', 'recipepress-reloaded' ), $name ),
                    ),
                    'show_ui'               => true,
                    'show_tagcloud'         => true,
                    'hierarchical'          => $taxonomy['hierarchical'],
                    'show_admin_column'     => $taxonomy['table'],
                    'show_in_nav_menus'     => true,
                    'show_table_filter'     => $taxonomy['filter'],    // framework specific key
                    'show_in_sidebar_menus' => true,    // framework specific key
                    'rewrite'               => array(
                        'slug'      => sanitize_title( $taxonomy['slug'])
                        ),
                    )
                );
            }
        }
    }
}