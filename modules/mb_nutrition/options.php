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
class RPR_Module_Options_MB_Nutrition {
    
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
    public $sSectionID  = 'metadata_nutrition';
	
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
    
        // Add an option section to an existing tab
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug   
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => 'metadata',
                'title'         => '<i class="fa fa-tags"></i> ' . __( 'Nutritional information' , 'recipepress-reloaded' ),
                'description'   => '<i style="float:left; margin-right:6px; color:green;" class="fa fa-exclamation-circle fa-3x"></i> ' . __( 'Decide here which nutritional information you want to provide. <br/>If you leave a field empty in the recipe, it won\'t be displayed in the front end.' , 'recipepress-reloaded' )

                )
            );
      
        /**
        * Add option fields to the new secion
        */
        $oFactory->addSettingFields(
            $this->sSectionID, 
            array(
                'field_id'          => 'nutrition_use_calories',
                'type'              => 'checkbox',
                'title'             => __( 'Use calories', 'recipepress-reloaded' ),
                'default'           => true,
            ),
            array(
                'field_id'          => 'nutrition_use_carbohydrates',
                'type'              => 'checkbox',
                'title'             => __( 'Use carbohydrate', 'recipepress-reloaded' ),
                'default'           => true,
            ),
            array(
                'field_id'          => 'nutrition_use_sugar',
                'type'              => 'checkbox',
                'title'             => __( 'Use sugar', 'recipepress-reloaded' ),
                'default'           => false,
            ),
            array(
                'field_id'          => 'nutrition_use_protein',
                'type'              => 'checkbox',
                'title'             => __( 'Use protein', 'recipepress-reloaded' ),
                'default'           => true,
            ),
            array(
                'field_id'          => 'nutrition_use_fat',
                'type'              => 'checkbox',
                'title'             => __( 'Use fat', 'recipepress-reloaded' ),
                'default'           => true,
            ),
            array(
                'field_id'          => 'nutrition_use_fat_unsaturated',
                'type'              => 'checkbox',
                'title'             => __( 'Use fat (unsaturated)', 'recipepress-reloaded' ),
                'default'           => false,
            ),
            array(
                'field_id'          => 'nutrition_use_fat_saturated',
                'type'              => 'checkbox',
                'title'             => __( 'Use fat (saturated)', 'recipepress-reloaded' ),
                'default'           => false,
            ),
            array(
                'field_id'          => 'nutrition_use_fat_trans',
                'type'              => 'checkbox',
                'title'             => __( 'Use trans fat', 'recipepress-reloaded' ),
                'default'           => false,
            ),
            array(
                'field_id'          => 'nutrition_use_cholesterol',
                'type'              => 'checkbox',
                'title'             => __( 'Use cholesterol', 'recipepress-reloaded' ),
                'default'           => false,
            ),
            array(
                'field_id'          => 'nutrition_use_sodium',
                'type'              => 'checkbox',
                'title'             => __( 'Use sodium', 'recipepress-reloaded' ),
                'default'           => false,
            ),
            array(
                'field_id'          => 'nutrition_use_fibre',
                'type'              => 'checkbox',
                'title'             => __( 'Use fibre', 'recipepress-reloaded' ),
                'default'           => false,
            )
        );
    }

}