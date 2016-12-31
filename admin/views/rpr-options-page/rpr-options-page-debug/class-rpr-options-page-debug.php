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
 * The debug tab of the options page
 *
 * Defines the options page with tabs and fields, relying on the admin page
 * framework
 *
 * @since		0.8.0
 * @package		recipepress-reloaded
 * @subpackage	recipepress-reloaded/admin
 * @author		Jan Köster <rpr@cbjck.de>
 */
class RPR_Options_Page_Debug {
    
    private $oFactory;
    private $sPageSlug   = 'rpr_options';
    private $sTabSlug    = 'debug';
	
    
    /**
     * Sets up hooks.
     */
    public function __construct( $oFactory ) {
		
		$this->oFactory = $oFactory;
		/**
		 * Load dependcies:
		 */
		require_once 'class-rpr-options-page-debug-saved.php';
		
                              
        // Tab
        $oFactory->addInPageTabs(    
            $this->sPageSlug, // target page slug
            array(
                'tab_slug'  => $this->sTabSlug,
                'title'     => '<i class="fa fa-bug"></i> ' . __( 'Debug', 'recipepress-reloaded' ),    
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
            'RPR_Options_Page_Debug_Saved',
        );
        foreach ( $_aClasses as $_sClassName ) {
            if ( ! class_exists( $_sClassName ) ) {
                continue;
            }
            new $_sClassName( $oFactory );
        }

		add_action( 'do_' . $this->sPageSlug . '_' . $this->sTabSlug, array( $this, 'replyToDoTab' ) );

    }
	

    public function replyToDoTab() {
   
        ?>
        <h3><?php echo '<i class="fa fa-save"></i> ' . __( 'Saved Data', 'recipepress-reloaded' ); ?></h3>
        <p>
        <?php
            echo __( "These are all the options you've set. </br>Please provide these information in a bug report when asked to do so. They might help do hunt down nasty bugs.", 'recipepress-reloaded' ); // ' syntax fixer
        ?>
        </p>
        <?php
            echo $this->oFactory->oDebug->getArray( $this->oFactory->oProp->aOptions ); 
        
    }
    
}