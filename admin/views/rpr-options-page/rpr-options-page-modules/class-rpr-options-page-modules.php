<?php
/**
 * The options page functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      1.0.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The appearance tab of the options page
 *
 * Defines the options page with tabs and fields, relying on the admin page
 * framework
 *
 * @since		1.0.0
 * @package		recipepress-reloaded
 * @subpackage	recipepress-reloaded/admin
 * @author		Jan Köster <rpr@cbjck.de>
 */
class RPR_Options_Page_Modules {
       
    /**
     * The page slug to add the tab and form elements.
     */
    public $sPageSlug   = 'rpr_options';
    
    /**
     * The tab slug to add to the page.
     */
    public $sTabSlug    = 'modules';
	    
    /**
     * Sets up hooks.
     */
    public function __construct( $oFactory ) {
        /**
         * Load dependcies:
         */
	include 'class-rpr-options-page-modules-list.php';	
                              
        // Tab
        $oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => '<i class="fa fa-puzzle-piece"></i> ' . __( 'Modules', 'recipepress-reloaded' )
            )    
        );  
        
        add_action( 
            'load_' . $this->sPageSlug . '_' . $this->sTabSlug, 
            array( $this, 'replyToLoadTab' ) 
        );                
        
    }

    
    /**
     * Adds form sections.
     * 
     * Triggered when the tab is loaded.
     * @callback        action      load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        
        $_aClasses = array(
            'RPR_Options_Page_Modules_List',
        );
        foreach ( $_aClasses as $_sClassName ) {
            if ( ! class_exists( $_sClassName ) ) {
                continue;
            }
            new $_sClassName( $oFactory );
        }

    }
}