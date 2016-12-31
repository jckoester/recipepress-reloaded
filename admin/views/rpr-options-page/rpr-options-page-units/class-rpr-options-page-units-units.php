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
class RPR_Options_Page_Units_Units {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'rpr_options';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'units';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'units';
	        
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
                'title'         => '<i class="fa fa-balance-scale"></i>&nbsp;' . __( 'Units' , 'recipepress-reloaded' )
                )
            );
      
        /**
        * Add settings fields for nutritional metadata
        */
        $oFactory->addSettingFields(
            $this->sSectionID, 
            array(
                'field_id'          => 'use_ingredient_units',
                'type'              => 'checkbox',
                'title'             => __( 'Use ingredient unit list?', 'recipepress-reloaded' ),
                'tip'               => __( 'Check this to use a list of units for entering ingredients. You can define the list below. I recommend using a well defined list of units as this will make your recipes more consistent and readable.', 'recipepress-reloaded'),
                'default'			=> true,
            ),
            array(
                'field_id'          => 'ingredient_units',
                'type'              => 'text',
                'title'             => __( 'Unit list', 'recipepress-reloaded' ),
                'description'       => __( 'Unit list for ingredients.<br/> I recommend using a well defined list of units as this will make your recipes more consistent and readable.', 'recipepress-reloaded' ),
                'repeatable'        => true,
                'sortable'          => true,
            ),
            array(
                'field_id'          => 'use_serving_units',
                'type'              => 'checkbox',
                'title'             => __( 'Use servings size unit list?', 'recipepress-reloaded' ),
                'tip'               => __( 'Check this to use a list of units for entering serving sizes. You can define the list below. I recommend using a well defined list of units as this will make your recipes more consistent and readable.', 'recipepress-reloaded'),
                'default'           => true,
            ),
            array(
                'field_id'          => 'serving_units',
                'type'              => 'text',
                'title'             => __( 'Unit list', 'recipepress-reloaded' ),
                'description'       => __( 'Unit list for serving sizes.<br/> I recommend using a well defined list of units as this will make your recipes more consistent and readable.', 'recipepress-reloaded' ),
                'repeatable'        => true,
                'sortable'          => true,
            )
        );
    }

}