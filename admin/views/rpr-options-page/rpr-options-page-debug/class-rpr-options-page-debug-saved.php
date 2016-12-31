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
class RPR_Options_Page_Debug_Saved {
    
	private $oFactory;
	/**
     * The page slug to add the tab and form elements.
     */
    private $sPageSlug   = 'rpr_options';
    
    /**
     * The tab slug to add to the page.
     */
    private $sTabSlug    = 'debug';
    
    /**
     * The section slug to add to the tab.
     */
    private $sSectionID  = 'saved';
	
        
    /**
     * Sets up a form section.
     */
    public function __construct( $oFactory ) {
		$this->oFactory     = $oFactory;
        // Section
        $oFactory->addSettingSections(    
            $this->sPageSlug, // the target page slug                
            array(
                'section_id'    => $this->sSectionID,
                'tab_slug'      => $this->sTabSlug,
                'title'         => '<i class="fa fa-bug"></i> ' . __( 'Debugging information', 'recipepress-reloaded' ),
                //'description'   => __( 'Displays the saved options.', 'recipepress-reloaded' ),     
            )
        );   
      
        $oFactory->addSettingFields(
            $this->sSectionID, // the target section ID       
            /*array(
                'field_id'      => 'saved_options',
                'type'          => 'system',     
                'title'         => __( 'Saved Options', 'recipepress-reloaded' ),
                'data'          => array(
                    // Removes the default data by passing an empty value below.
                    'Admin Page Framework'  => '', 
                    'WordPress'             => '', 
                    'PHP'                   => '', 
                    'Server'                => '',
                    'PHP Error Log'         => '',
                    'MySQL'                 => '', 
                    'MySQL Error Log'       => '',                    
                    'Browser'               => '',                         
                ) 
                + $oFactory->oProp->aOptions,
                'attributes'    => array(
                    'name'  => '',
                    'rows'   => 20,
                ),        
            ),*/
            array( // Reset Submit button
                'field_id'      => 'submit_button_reset',
                'title'         => __( 'Reset Options', 'recipepress-reloaded' ),
                'type'          => 'submit',
                'label'         => __( 'Reset', 'recipepress-reloaded' ),
                'reset'         => true,
                'attributes'    => array(
                    'class' => 'button button-secondary',
                ),
                'description'   => __( 'If you press this button, a confirmation message will appear and then if you press it again, it resets all options.', 'recipepress-reloaded' ),
            )
        );      
         
    }
}