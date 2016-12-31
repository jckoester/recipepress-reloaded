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
 * The actual options page functionality of the plugin.
 *
 * Defines the options page with tabs and fields, relying on the admin page
 * framework
 *
 * @since		0.8.0
 * @package		recipepress-reloaded
 * @subpackage          recipepress-reloaded/admin
 * @author		Jan Köster <rpr@cbjck.de>
 */
class RPR_Options_Page {
    
    private $_sClassName = 'RPR_Options';
    
    private $_sPageSlug  = 'rpr_options';
		
    private $version;


	/**
     * Adds a page item and sets up hooks.
     */
    public function __construct( $version, $sClassName='' ) {
        /**
         * Load dependcies:
         */
        require_once 'rpr-options-page-advanced/class-rpr-options-page-advanced.php';
        require_once 'rpr-options-page-appearance/class-rpr-options-page-appearance.php';
        require_once 'rpr-options-page-debug/class-rpr-options-page-debug.php';
        require_once 'rpr-options-page-general/class-rpr-options-page-general.php';
        // require_once 'rpr-options-page-i18n/class-rpr-options-page-i18n.php';
        require_once 'rpr-options-page-metadata/class-rpr-options-page-metadata.php';
        require_once 'rpr-options-page-taxonomies/class-rpr-options-page-taxonomies.php';
        require_once 'rpr-options-page-units/class-rpr-options-page-units.php';

		
        $this->version = $version;
        
        $this->_sClassName = $sClassName ? $sClassName : $this->_sClassName;
        
        add_action(
            'set_up_' . $this->_sClassName,
            array( $this, 'replyToSetUp' )
        );
        
    }
    
    /**
     * @callback        action      set_up_{instantiated class name}
     */
    public function replyToSetUp( $oFactory ) {
        
        $oFactory->addSubMenuItems( 
            array(
                'title'         => __( 'Options', 'recipepress-reloaded' ),
                'page_slug'     => $this->_sPageSlug,    // page slug
            )
        );        
              
        add_action( 'load_' . $this->_sPageSlug, array( $this, 'replyToLoadPage' ) );
        add_action( 'do_' . $this->_sPageSlug, array( $this, 'replyToDoPage' ) );
        
    }

    /**
     * Called when the page starts loading.
     * 
     * @callback        action      load_{page slug}
     * */
    public function replyToLoadPage( $oFactory ) { 

        // Define in-page tabs - here tabs are defined in the below classes.
        $_aTabClasses = array(
			'RPR_Options_Page_General',
			'RPR_Options_Page_Taxonomies',
			'RPR_Options_Page_Metadata',
                        'RPR_Options_Page_Units',
			'RPR_Options_Page_Appearance',
			// 'RPR_Options_Page_I18n',
			'RPR_Options_Page_Advanced',
			'RPR_Options_Page_Debug',
        );
        foreach ( $_aTabClasses as $_sTabClassName ) {
            if ( ! class_exists( $_sTabClassName ) ) {
                continue;                
            }        
            new $_sTabClassName( $oFactory );
        }
    
    }     
    
    /*
     * Handles the page output.
     * 
     * @callback        action      do_{page slug}
     * */
    public function replyToDoPage() { 
        submit_button();
    }     
     
}