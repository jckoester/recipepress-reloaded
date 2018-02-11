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
 * The debug tab of the options page, display saved data
 *
 * Adds a section to the tab, displaying the data
 *
 * @since		0.8.0
 * @package		recipepress-reloaded
 * @subpackage	recipepress-reloaded/admin
 * @author		Jan Köster <rpr@cbjck.de>
 */
class RPR_Options_Page_Advanced_Advanced {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'rpr_options';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'advanced';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'advanced';
	
    /**
     * A list of available layouts with their paths
     */
    private $layouts = array();
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        /* Create the sections for these options */
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug   
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => '<i class="fa fa-cogs"></i>&nbsp;' . __( 'Advanced Options', 'recipepress-reloaded' ),
                'description'   => '<i style="float:left; margin-right:6px; color:orange;" class="fa fa-exclamation-triangle fa-3x"></i>&nbsp;' . __( 'In this section you can make some advanced settings on the look and feel of your recipes. For the standard food blog it should not be necessary to change any of these.' , 'recipepress-reloaded' )
            )   
        );
        
        /**
         * This should be moved to a module for taxonomies ... 
         */
        $oFactory->addSettingFields(
            'advanced',
            array(
                'field_id'      => 'display_categories',
                'type'          => 'checkbox',
                'title'         => __('Display categories', 'recipepress-reloaded' ),
                'tip'           => __('Display WP Categories in the recipe part of the theme instead the default one.', 'recipepress-reloaded'),
                'default'       => 0
            ),
            array(
                'field_id'      => 'display_tags',
                'type'          => 'checkbox',
                'title'         => __('Display tags', 'recipepress-reloaded' ),
                'tip'           => __('Display WP Tags in the recipe part of the theme instead the default one.', 'recipepress-reloaded'),
                'default'       => 0
            )
        );
    }
}