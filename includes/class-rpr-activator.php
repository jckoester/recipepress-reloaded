<?php

/**
 * Fired during plugin activation
 *
 * @link       http://tech.cbjck.de/wp-plugins/rpr/
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/includes
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Activator {

	/**
	 * Fixing version information and triggering update in case i is necesary
         * 
	 * This function should be used later to create things on activation. 
         * For example it could create a sample recipe.
	 *
	 * @since    0.8.0
	 */
	public static function activate() {
           
           /**
		    * @todo: Move this to admin_init hook as activation_hook does not
		    * catch on multisite installations
		    */ 
            if( ! get_option( 'rpr_dbversion' ) ){
                // Install sample content
                self::install_sample_content();
            }
            /**
             * NOTE: Migration is done in admin/class-rpr-admin-migration.php
             */
	}
	


        /**
         * Install sample recipes and taxonomies to provide an easy start
         * @since 0.8.0
         */
        private static function install_sample_content() {
            /**
             *  We just set an option here and let class-rpr-migration.php have 
             * a notice appear. So the user can decide to use sample data or not.
             */
            update_option( 'rpr_install_sample_data', 1 );
            update_option( 'rpr_first_install', 1 );

	}
}	
