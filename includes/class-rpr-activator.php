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
            // Fix version string for older versions of RPR
            self::fix_dbver();
            
            if( ! get_option( 'rpr_dbversion' ) ){
                // Install sample content
                self::install_sample_content();
            }
            /**
             * NOTE: Migration is done in admin/class-rpr-admin-migration.php
             */
	}
	
        /**
         * Move old version scheme to new dbversion scheme to make updates
         * easier.
         * @since 0.8.0
         */
        private static function fix_dbver() {
            // check for very old versions:
            if( ! get_option( 'rpr_version_updated' ) ){
                // Check, if RPR < 0.5:
		if( is_array( get_option( 'rpr_options' ) ) ) {
                    update_option( 'rpr_version_updated', '0.3.0' );
                //Check if RecipePress
		} elseif( is_array( get_option( 'recipe-press-options' ) ) ) {
                    update_option( 'rpr_version_updated', 'RecipePress' );
		} else {
                    // RPR hasn't been installed previously.
                    return;
                }
            }
            
            if( get_option( 'rpr_version_updated') ) {
                // Fix the dbversion option for old versions of recipepress reloaded
                if( version_compare(get_option( 'rpr_version_updated' ), '0.8.0', '<' ) ) {
                    update_option( 'rpr_dbversion', '4' );
                }
                if( version_compare(get_option( 'rpr_version_updated' ), '0.7.12', '<' ) ) {
                    update_option( 'rpr_dbversion', '3' );
                }
                if( version_compare(get_option( 'rpr_version_updated' ), '0.7.9', '<' ) ) {
                    update_option( 'rpr_dbversion', '2' );
                }
                if( version_compare(get_option( 'rpr_version_updated' ), '0.7.7', '<' ) ) {
                    update_option( 'rpr_dbversion', '1' );
                }
                if( get_option( 'rpr_version_updated' ) == "0.3.0" || get_option( 'rpr_version_updated' ) == 'RecipePress' ){
                    update_option( 'rpr_dbversion', '0' );
                }
            
                // Remove old version string 'rpr_version_updated'
                delete_option( 'rpr_version_updated' );
            }
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
