<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/includes
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.8.0
	 * @access   protected
	 * @var      RPR_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.8.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.8.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current database version of the plugin.
	 *
	 * @since    0.8.0
	 * @access   protected
	 * @var      string    $version    The current version of the database of the plugin.
	 */
	protected $dbversion;

	/**
	 * A list of all activated modules
	 *
	 * @since: 1.0.0
	 */
	protected $modules = array();

	/**
	 * Instance of the admin class
	 *
	 * @var RPR_Admin
	 * @since 1.0.0
	 */
	protected $admin;

	/**
	 * Instance of the public class
	 *
	 * @var RPR_Public
	 * @since 1.0.0
	 */
	protected $public;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the modules, load the dependencies, define the locale
	 * and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.8.0
	 */
	public function __construct() {
		$this->plugin_name = 'recipepress-reloaded';
		$this->version     = RPR_VERSION;
		$this->dbversion   = RPR_DBVER;

		$this->register_posttype();
		$this->load_modules();
		$this->load_dependencies();

		$this->admin  = new RPR_Admin( $this->version, $this->dbversion, $this->modules );
		$this->public = new RPR_Public( $this->version, $this->modules );

		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Register the post type for all the recipes
	 *
	 * @since 1.0.0
	 */
	private function register_posttype() {
		/**
		 * The class defining the custom post type
		 * It needs to be instantiated here, as AFP is using its own loader and hooks
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rpr-recipeposttype.php';
		new RPR_RecipePostType( $this->plugin_name, $this->version );
	}
	/**
	 * Load all enabled modules and instantiate objects
	 *
	 * @since 1.0.0
	 * @todo Generate the list of modules from options
	 */
	protected function load_modules() {
		// Load the module helper-update
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/helper-module-list.php';
		/**
		 * Load the abstract class for RPR_Modules
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/abstract-class-rpr-module.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/abstract-class-rpr-module-metabox.php';

		// $active_modules = $this->get_active_modules();
		$active_modules = rpr_get_active_modules();
		// Load the active modules:
				 $this->modules = rpr_load_modules();

		return $this->modules;

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - RPR_Loader. Orchestrates the hooks of the plugin.
	 * - RPR_i18n. Defines internationalization functionality.
	 * - RPR_Admin. Defines all hooks for the admin area.
	 * - RPR_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.8.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rpr-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-rpr-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-rpr-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-rpr-public.php';

		/**
		 * The admin page framework to create options pages and metaboxes
		 *
		 * @link http://www.admin-page-framework.michaeluno.jp/
		 * @since 0.8.0
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'libraries/apf/admin-page-framework.php';

		/**
		 * Load dependencies for all modules:
		 */
		foreach ( $this->modules as $module ) {
			if ( is_a( $module, 'RPR_Module' ) ) {
				$module->load_module_dependencies( $this->modules );
			}
		}

		$this->loader = new RPR_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the RPR_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.8.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new RPR_i18n( $this->plugin_name, $this->version );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.8.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new RPR_Admin( $this->version, $this->dbversion, $this->modules );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Install demo data / sample recipes
		$this->loader->add_action( 'admin_init', $plugin_admin->demo, 'do_install_base_options' );
		$this->loader->add_action( 'admin_init', $plugin_admin->demo, 'rpr_do_install_samples' );
		$this->loader->add_action( 'admin_notices', $plugin_admin->demo, 'notice_demo' );

		// Options page
		$this->loader->add_action( 'init', $plugin_admin, 'create_options' );

		// Save recipe
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_recipe', 10, 2 );

		// Display error messages
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notice_handler' );

		// Add recipes to Recent Activity widget
		$this->loader->add_filter( 'dashboard_recent_posts_query_args', $plugin_admin, 'add_to_dashboard_recent_posts_widget' );

		// Add recipes to 'At a Glance' widget
		$this->loader->add_filter( 'dashboard_glance_items', $plugin_admin, 'add_recipes_glance_items' );

		// Add messages on the recipe editor screen
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'updated_rpr_messages' );

		/**
		 * Define the admin hooks for all modules
		 */
		foreach ( $this->modules as $module ) {
			if ( is_a( $module, 'RPR_Module' ) ) {
				$module->define_module_admin_hooks( $this->loader );
			}
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.8.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new RPR_Public( $this->version, $this->modules );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Manipulate the query to include recipes to home page (if set)
		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'query_recipes' );
		// Add recipes to main rss
		$this->loader->add_filter( 'request', $plugin_public, 'add_recipes_to_feed' );

		// Do the recipe specific content and layout operations
		$this->loader->add_filter( 'the_excerpt', $plugin_public, 'get_recipe_excerpt' );
		$this->loader->add_filter( 'the_content', $plugin_public, 'get_recipe_content' );

		// register the widgets
		$this->loader->add_action( 'widgets_init', $plugin_public, 'register_widgets' );

		/**
		 * Define the admin hooks for all modules
		 */
		foreach ( $this->modules as $module ) {
			if ( is_a( $module, 'RPR_Module' ) ) {
				$module->define_module_public_hooks( $this->loader );
			}
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.8.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.8.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.8.0
	 * @return    RPR_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	public function get_modules() {
		return $this->modules;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.8.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the version number of the database of the plugin.
	 *
	 * @since     0.8.0
	 * @return    string    The version number of the database of the plugin.
	 */
	public function get_dbversion() {
		return $this->dbversion;
	}
}
