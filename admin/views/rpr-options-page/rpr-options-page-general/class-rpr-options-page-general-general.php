<?php
/**
 * The options page functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The general options tab of the options page, display saved data
 *
 * Adds a section to the tab, displaying the data
 *
 * @since		0.8.0
 * @package		recipepress-reloaded
 * @subpackage  	recipepress-reloaded/admin/views
 * @author		Jan Köster <rpr@cbjck.de>
 */
class RPR_Options_Page_General_General {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'rpr_options';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'general';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'general';
	
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Section
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug   
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => '<i class="fa fa-cog"></i> ' . __( 'General Options' , 'recipepress-reloaded' )
                )
            );
      
        /**
        * Add settings fields for ingredients
        */
        $oFactory->addSettingFields(
            $this->sSectionID, 
            array(
                'field_id'          => 'slug',
                'type'              => 'text',
                'title'             => __( 'Slug', 'recipepress-reloaded' ),
                'tip'               => __( 'The slug is the url part of the taxonomy name. It should only contain letters, numbers und hyphens.', 'recipepress-reloaded'),
                 'description'      => sprintf( __( 'Recipe archives will be accessible at <u>%s<b>slug</b>/a-recipe</u>', 'recipepress-reloaded' ), site_url( '/' ) ),
                 'default'          => strtolower( __( 'recipe', 'recipepress-reloaded' ) ),
            ),
            array(
                'field_id'          => 'error404',
                'type'              => 'none',
                'title'             => '<i class="fa fa-exclamation-circle"></i>' . __( ' Error 404?', 'recipepress-reloaded' ),
                'content'           => __( 'You\'ve set up everything correctly here but now wordpress is giving you an <b>Error 404</b> (not found) ? Try flushing you\'re permalink settings.</br>Visit <i>Settings</i> -> <i>Permalinks</i> and just save without changing anything.', 'recipepress-reloaded' )
            ),
            array(
                'field_id'          => 'homepage_display',
                'type'              => 'checkbox',
                'title'             => __( 'Recipes on home page', 'recipepress-reloaded' ),
                'tip'               => __( 'Defines if recipes should be displayed on the homepage like \'normal\' posts.', 'recipepress-reloaded' ) ,
                'default'           => 1,
            ),
            array(
                'field_id'          => 'archive_display',
                'type'              => 'select',
                'title'             => __( 'Archive Page', 'recipepress-reloaded' ),
                'tip'               => __( 'Defines what to show of your recipes on the archive page', 'recipepress-reloaded') ,
                'label'             => array(
                    'excerpt' => __( 'Only the excerpt', 'recipepress-reloaded' ),
                    'full' => __('The entire recipe', 'recipepress-reloaded'),
                ),
                'default' => 'excerpt',
            ),
            array(
                'field_id'          => 'use_taxcloud_widget',
                'type'              => 'checkbox',
                'title'             => __( 'Taxonomy cloud widget', 'recipepress-reloaded' ),
                //'description'       => __( 'The taxonomy cloud widget replaces the default tag cloud widget. It allows you to display a tag cloud for terms of any taxonomy and any post type. ', 'recipepress-reloaded' ),
                'tip'               => __( 'The taxonomy cloud widget replaces the default tag cloud widget. It allows you to display a tag cloud for terms of any taxonomy and any post type. ', 'recipepress-reloaded' ),
                'default'           => true
            ),
            array(
                'field_id'          => 'use_taxlist_widget',
                'type'              => 'checkbox',
                'title'             => __( 'Taxonomy list widget', 'recipepress-reloaded' ),
                //'description'       => __( 'The taxonomy list widget can display a list of terms of any of any taxonomy and any post type. Use it to create lists of your top ten categories, terms, ...', 'recipepress-reloaded' ),
                'tip'               => __( 'The taxonomy list widget can display a list of terms of any taxonomy and any post type. Use it to create lists of for example your top ten categories, terms, ...', 'recipepress-reloaded' ),
                'default'           => true
            )
        );
        
        add_filter( 
            'validation_' . $oFactory->oProp->sClassName . '_' . 'general' . '_' . 'slug', 
            array( $this, 'replyToValidateSlug' ), 
            10, // priority
            4   // number of parameters
        );
        
    }
    
    /**
     * Validates the 'slug' field in the 'general' section of the 'RPR_Options' class.
     *
     * @callback        filter      validation_{instantiated class name}_{section id}_{field id}
     */
    public function replyToValidateSlug( $sNewInput, $sOldInput, $oAdmin ) { 
    
        $_bVerified = true;
        $_aErrors = array();

        
        if ( '' === (string) trim( $sNewInput ) ) {
            $_aErrors[ $this->sSectionID ][ 'slug' ] = __( 'The slug must not be empty!', 'recipepress-reloaded' ) . ' ' . $sNewInput;
            $_bVerified = false;            
        } else {
            // sanitize the slug
            $sNewInput = sanitize_title( $sNewInput );
        }
        
        if ( ! $_bVerified ) {
            /* 4-1. Set the error array for the input fields. */
            $oAdmin->setFieldErrors( $_aErrors );     
            $oAdmin->setSettingNotice( __( 'There was an error in a form field.', 'admin-page-framework-loader' ) );

            return $sOldInput;
            
        }
                
        return $sNewInput;     
        
    }    

}