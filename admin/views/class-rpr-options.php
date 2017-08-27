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
 * The options page functionality of the plugin.
 *
 * Defines the options page with tabs and fields, relying on the admin page
 * framework
 *
 * @since		0.8.0
 * @package		recipepress-reloaded
 * @subpackage	recipepress-reloaded/admin
 * @author		Jan Köster <rpr@cbjck.de>
 */
class RPR_Options extends AdminPageFramework {

	/**
	 * The version of this plugin.
	 *
	 * @since    0.8.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
        
        /**
         *
         * @since 1.0.0
         * @var type array  $modules    A list of all active modules
         */
        private $modules;

        /**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.8.0
	 * @param    string     $version    The version of this plugin.
         * @param    array      $modules    List of active modules.
	 */
	public function __construct( $version="", $modules=array() ) {

		$this->version		 = $version;
                $this->modules = $modules;
		
		/**
		 * Load dependcies:
		 */
		require_once 'rpr-options-page/class-rpr-options-page.php';
		
		/**
		 * Call constructor of the parent class
		 */
		parent::__construct();
                //var_dump('loaded');
	}

	/**
	 * Setup function to set the basic values and create the page
	 *
	 * @since 0.8.0
	 */
	public function setUp() {

            // Create the root menu - specifies to which parent menu to add.
            $this->setRootMenuPageBySlug( 'edit.php?post_type=rpr_recipe' );

            // Add the pages -  below classes do not extend the framework factory class but uses the framework hooks to add pages.
            new RPR_Options_Page( $this->version, $this->modules );
	}
}
