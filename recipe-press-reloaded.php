<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://tech.cbjck.de/wp-plugins/rpr/
 * @since             0.8.0
 * @package           recipepress-reloaded
 *
 * @wordpress-plugin
 * Plugin Name:       RecipePress reloaded
 * Plugin URI:        http://tech.cbjck.de/wp-plugins/rpr/
 * Description:       The swiss army knife for your food blog. A tool not only to add nicely and seo friendly formatted  recipes to your posts. But also to manage present your recipe collection.
 * Version:           0.9.2
 * Author:            Jan Köster
 * Author URI:        http://tech.cbjck.de/author/jan
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       recipepress-reloaded
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define constants
 */
const RPR_VERSION = '0.9.2';
const RPR_DBVER = '';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rpr-activator.php
 * 
 * @since 0.8.0
 * @todo move this completely to admin_init as activation_hook does not catch 
 * on multisite installations
 */
function activate_rpr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rpr-activator.php';
	RPR_Activator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rpr-deactivator.php
 * 
 * @since 0.8.0
 */
function deactivate_rpr() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rpr-deactivator.php';
	RPR_Deactivator::deactivate();
}

/**
 * The code that runs during plugin deinstallation.
 * This action is documented in includes/class-rpr-uninstaller.php
 * 
 * @since 0.8.0
 */
function uninstall_rpr() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-rpr-uninstaller.php';
    RPR_Uninstaller::uninstall();
}

/*
 * @TODO: activation hook does not work for multisite networks
 * Move the upgrade procedures to admin_init !
 */
register_activation_hook( __FILE__, 'activate_rpr' );
register_deactivation_hook( __FILE__, 'deactivate_rpr' );
register_uninstall_hook( __FILE__, 'uninstall_rpr' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rpr.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.8.0
 */
function run_rpr() {

	$plugin = new RPR();
	$plugin->run();

}

run_rpr();