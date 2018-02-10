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
class RPR_Module_Options_MB_Yield {
    
     
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
        $oFactory->addSettingFields(
            'units', 
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