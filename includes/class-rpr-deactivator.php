<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://tech.cbjck.de/wp-plugins/rpr/
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/includes
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Deactivator {

	/**
	 * Actually doing nothing yet.
	 *
	 * This function could be used later to revert changes made on activation.
         * Tidying up shoul be done on uninstall.
	 *
	 * @since    0.8.0
	 */
	public static function deactivate() {
            // Flush cache:
            wp_cache_flush();
            // Flush permalinks:
            flush_rewrite_rules();
	}

}
