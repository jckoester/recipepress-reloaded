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
class RPR_Options_Module_Ingredients {
    
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
    public $sSectionID  = 'metadata_demo';
	
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Add an option section to an existing tab
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug   
            array(
                'section_id'    => 'metadata_demo',
                'tab_slug'      => 'metadata',
                'title'         => '<i class="fa fa-tags"></i> ' . __( 'Demo Settings' , 'recipepress-reloaded' )
                )
            );
      
        /**
        * Add option fields to the new secion
        */
        $oFactory->addSettingFields(
            $this->sSectionID, 
            array(
                'field_id'          => 'demo_use_nutritional_data',
                'type'              => 'checkbox',
                'title'             => __( 'Demo module checkbox option in foreign tab.', 'recipepress-reloaded' ),
                //'tip'               => __( 'Check this to enable the use of nutritional information like calorific value, protein, fat and carbohydrates.', 'recipepress-reloaded'),
                'default'           => false,
            ),
            array(
		'field_id'			=> 'demo_structured_data_format',
		'type'				=> 'select',
		'title'				=> __( 'Demo module select option in foreign tab', 'recipepress-reloaded' ),
		//'description'			=> sprintf( __( 'Structured data help search engines understand your content. Find more information on structured data formats at <a href="%1s" target="_blank">schema.org</a>', 'recipepress-reloaded' ), 'http://1.schemaorgae.appspot.com/Recipe' ),
		//'tip'				=> __( 'Structured data help search engines understand your content. Find more information on structured data formats at schema.org.', 'recipepress-reloaded' ),
		'label'				=> array(
                    'option1'	=> __( 'Option 1', 'recipepress-reloaded' ),
                    'option2'	=> __( 'Option 2', 'reciperess-reloaded' ),
                    'option3'	=> __( 'Option 3', 'reciperess-reloaded' )
		),
		'default'			=> 'option2'
            )
        );
        

        // Add settings to existing section:
        $oFactory->addSettingFields(
            'general', 
            array(
                'field_id'          => 'test_demo_option',
                'type'              => 'checkbox',
                'title'             => __( 'This is a demo option', 'recipepress-reloaded' ),
                'description'       => __( 'This is an option from the demo module. If this option appears, dynamic loading of module option does work!', 'recipepress-reloaded'),
                'tip'               => __( 'If this works, dynamic loading of module option does work!', 'recipepress-reloaded'),
                'default'           => false,
            )
        );
        
        // it should also be possible to create a new options tab:
        // You will need to create a new class for this
        // Anyhow it is not recommended to create new tabs for modules as this crowds the options page
    }

}