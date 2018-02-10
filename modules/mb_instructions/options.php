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
class RPR_Module_Options_MB_Instructions {
    
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {

        // Add settings to existing section:
        $oFactory->addSettingFields(
            'advanced', 
            array(
                'field_id'          => 'instructions_headline',
                'type'              => 'text',
                'title'             => __( 'Instructions headline', 'recipepress-reloaded' ),
                'description'       => __( 'Use this to change the headline of the instructions section in the frontend', 'recipepress-reloaded'),
                'tip'               => __( 'If this works, dynamic loading of module option does work!', 'recipepress-reloaded'),
                'default'           => __( 'Instructions', 'recipepress-reloaded' ),
            )
        );
    }

}