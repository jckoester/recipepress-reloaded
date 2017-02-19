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
class RPR_Options_Page_Metadata_Metadata {
    
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'rpr_options';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'metadata';
    
    /**
     * The section slug to add to the tab.
     */
    public $sSectionID  = 'metadata';
	
        
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
                'title'         => '<i class="fa fa-tags"></i> ' . __( 'Metadata' , 'recipepress-reloaded' )
                )
            );
      
        /**
        * Add settings fields for nutritional metadata
        */
        $oFactory->addSettingFields(
            $this->sSectionID, 
                array(
                'field_id'          => 'use_source',
                'type'              => 'checkbox',
                'title'             => __( 'Use source field', 'recipepress-reloaded' ),
                'tip'               => __( 'Check this to enable the use of a source field allowing you to enter restaurants, books or websites as source of your recipe.', 'recipepress-reloaded'),
                'default'			=> false,
            ),
            array(
                'field_id'          => 'use_nutritional_data',
                'type'              => 'checkbox',
                'title'             => __( 'Use nutritional meta data', 'recipepress-reloaded' ),
                'tip'               => __( 'Check this to enable the use of nutritional information like calorific value, protein, fat and carbohydrates.', 'recipepress-reloaded'),
                'default'			=> false,
            ),
			array(
				'field_id'			=> 'structured_data_format',
				'type'				=> 'select',
				'title'				=> __( 'Structured data format', 'recipepress-reloaded' ),
				'description'				=> sprintf( __( 'Structured data help search engines understand your content. Find more information on structured data formats at <a href="%1s" target="_blank">schema.org</a>', 'recipepress-reloaded' ), 'http://1.schemaorgae.appspot.com/Recipe' ),
				'tip'				=> __( 'Structured data help search engines understand your content. Find more information on structured data formats at schema.org.', 'recipepress-reloaded' ),
				'label'				=> array(
					'microdata'	=> __( 'Microdata', 'recipepress-reloaded' ),
					'rdfa'		=> __( 'RDFa', 'reciperess-reloaded' ),
					'json-ld'	=> __( 'JSON-LD', 'reciperess-reloaded' )
				),
				'default'			=> 'microdata'
			)
        );
    }

}