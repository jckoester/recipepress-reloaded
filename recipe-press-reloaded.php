<?php

/*
  Plugin Name: RecipePressReloaded
  Plugin URI: http://rp-reloaded.net 
  Description: A simple recipe plugin doing all you need for your food blog. Plus: there are these nifty recipe previews in Google's search - automagically. Yet to come: easily create indexes of any taxonomy like ingredient, category, course, cuisine, ...
  Version: 0.7.6
  Author: Jan Köster
  Author URI: http://www.cbjck.de/author/jan
  License: GPL2

 * *************************************************************************

  Copyright (C) 2012-2014 Jan Köster

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see http://www.gnu.org/licenses/

 * *************************************************************************

 */
 
/*Set plugin version*/
define( 'RPR_VERSION', '0.7.6' );
define( 'RPR_TITLE', 'RecipePress reloaded' );


class RPReloaded{
	
	protected $pluginName;
	protected $pluginDir;
	protected $pluginUrl;
	
	protected $rpr_core;
	
	public function __construct()
	{
		$this->pluginName = 'recipepress-reloaded';
		$this->pluginDir = WP_PLUGIN_DIR . '/' . $this->pluginName;
		$this->pluginUrl = plugins_url() . '/' . $this->pluginName;
	
		// Version
		update_option( 'rpr_version', RPR_VERSION );
	
		// Textdomain
		load_plugin_textdomain($this->pluginName, false, basename( dirname( __FILE__ ) ) . '/language/'  );
	
		//Include core
		include_once( $this->pluginDir . '/php/class/rpr_core.php' );
		$this->rpr_core = new RPR_Core( $this->pluginName, $this->pluginDir, $this->pluginUrl );
//!!TBD>>	
		// Hooks
		register_activation_hook( __FILE__, array( $this->rpr_core, 'activate_taxonomies' ) );
		//register_activation_hook( __FILE__, array( $this, 'activation_notice' ) );
//>>TBD

//!!TBD
		// Add required plugins:
		add_action( 'tgmpa_register', array($this, 'rpr_register_required_plugins') );
		// Load Config
		
		// Actions
		//add_action( 'after_setup_theme', array( $this, 'rpr_admin_menu' ) );
		//add_action( 'after_setup_theme', array( $this, 'rpr_shortcodegenerator' ) );
		//add_action( 'init', array( $this, 'rpr_check_premium' ) );
		//add_action( 'admin_init', array( $this, 'rpr_hide_notice' ) );
		//add_action( 'wp_print_scripts', array( $this, 'rpr_styles' ), 99 ); // Not wp_print_styles because we need this to be the last outputted css
		//add_action( 'wp_footer', array( $this, 'rpr_scripts' ) );
		//add_action( 'admin_head', array( $this, 'rpr_admin_styles' ) );
		//add_action( 'admin_footer', array( $this, 'rpr_admin_scripts' ) );
		//add_action( 'admin_notices', array( $this, 'rpr_admin_notices' ) );
		//add_action( 'admin_footer-recipe_page_rpr_admin', array( $this, 'support_tab' ) );
//>>TBD	
		// Other
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( 'recipe-thumbnail', 150, 9999 );
			add_image_size( 'recipe-large', 600, 9999 );
		}


	
	}
	
	////////////////////////////// FRAMEWORK & GENERAL STUFF ///////////////////////////////////
	function init_rpr_redux() {
		require_once( WP_PLUGIN_DIR . '/redux-framework/ReduxCore/framework.php');
		require_once( dirname( __FILE__ ) . '/views/settings.php' );
	}

	function rpr_register_required_plugins()
	{
		/**
	     * Array of plugin arrays. Required keys are name and slug.
	     * If the source is NOT from the .org repo, then source is also required.
	     */
	    $plugins = array(
	 
	        // This is an example of how to include a plugin pre-packaged with a theme.
	        array(
	            'name'               => 'Redux Framework Plugin', // The plugin name.
	            'slug'               => 'redux-framework', // The plugin slug (typically the folder name).
	            'required'           => true, // If false, the plugin is only 'recommended' instead of required.
	        ),
		);
		
		$config = array(
	        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
	        'menu'         => 'tgmpa-install-plugins', // Menu slug.
	        'has_notices'  => true,                    // Show admin notices or not.
	        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
	        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
	        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
	        'message'      => '',                      // Message to output right before the plugins table.
	        'strings'      => array(
	            'page_title'                      => __( 'Install Required Plugins', 'recipe-press-reloaded' ),
	            'menu_title'                      => __( 'Install Plugins', 'recipe-press-reloaded' ),
	            'installing'                      => __( 'Installing Plugin: %s', 'recipe-press-reloaded' ), // %s = plugin name.
	            'oops'                            => __( 'Something went wrong with the plugin API.', 'recipe-press-reloaded' ),
	            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
	            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
	            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
	            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
	            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
	            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
	            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
	            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
	            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
	            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
	            'return'                          => __( 'Return to Required Plugins Installer', 'recipe-press-reloaded' ),
	            'plugin_activated'                => __( 'Plugin activated successfully.', 'recipe-press-reloaded' ),
	            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'recipe-press-reloaded' ), // %s = dashboard link.
	            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
	        )
	    );
	 
	    tgmpa( $plugins, $config );
    }
	
	/*
	 * Used in various places.
	*/
	protected function recipes_fields() {
		$return = array(
				//'recipe_title',
				'rpr_recipe_description',
				'rpr_recipe_rating',
				'rpr_recipe_featured',
				'rpr_recipe_servings',
				'rpr_recipe_servings_type',
				'rpr_recipe_prep_time',
				'rpr_recipe_cook_time',
				'rpr_recipe_passive_time',
				'rpr_recipe_ingredients',
				'rpr_recipe_instructions',
				'rpr_recipe_notes',
		);
		if( $this->get_option( 'recipe_use_nutritional_info', 0 ) == 1 ){
			array_push( $return, 'rpr_recipe_calorific_value' );
			array_push( $return, 'rpr_recipe_protein' );
			array_push( $return, 'rpr_recipe_fat' );
			array_push( $return, 'rpr_recipe_carbohydrate' );
			array_push( $return, 'rpr_recipe_nutrition_per' );
		}
		return $return;
	}
	
	/*
	 * RP Reloaded Settings page
	*/
	public function rpr_admin_menu()
	{
		require_once('php/helper/admin_menu_helper.php');
		
		require_once('views/admin.php');
	
		new VP_Option(array(
				'is_dev_mode'           => false,
				'option_key'            => 'rpr_option',
				'page_slug'             => 'rpr_admin',
				'template'              => $admin_menu,
				'menu_page'             => 'edit.php?post_type=rpr_recipe',
				'use_auto_group_naming' => true,
				'use_exim_menu'         => true,
				'minimum_role'          => 'manage_options',
				'layout'                => 'fluid',
				'page_title'            => __( 'Settings', $this->pluginName ),
				'menu_label'            => __( 'Settings', $this->pluginName ),
				'use_util_menu'			=> false,
		));
	}
	
	public function option( $name, $default = null )
	{
		global $rpr_option;
		
		if( !is_null($rpr_option) &&array_key_exists($name, $rpr_option ) ) {
			return $rpr_option[$name];	
		} else {
			return false;
		}
		/*
		$option = vp_option( "rpr_option." . $name );
	
		return is_null($option) ? $default : $option;
		 * 
		 */
	}
	
	static function get_option( $name, $default = null )
	{
		global $rpr_option;
		
		if( !is_null($rpr_option) &&array_key_exists($name, $rpr_option ) ) {
			return $rpr_option[$name];	
		} else {
			return false;
		}
		/*
		$option = vp_option( "rpr_option." . $name );
	
		return is_null($option) ? $default : $option;
		 * 
		 */
	}
	
}

//require_once('lib/vafpress/bootstrap.php');
$rpr = new RPReloaded();

include_once('php/template_tags.php');

require_once(dirname( __FILE__ ) . '/lib/tgm/class-tgm-plugin-activation.php');



//function init_rpr_redux()
//{
	if ( !class_exists( 'ReduxFramework' ) && file_exists( WP_PLUGIN_DIR . '/redux-framework/ReduxCore/framework.php' ) ) {
		//require_once( dirname( __FILE__ ) . '/lib/ReduxFramework/ReduxCore/framework.php' );
		require_once( WP_PLUGIN_DIR . '/redux-framework/ReduxCore/framework.php');
	}
	if ( !isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/views/settings.php' ) ) {
		require_once( dirname( __FILE__ ) . '/views/settings.php' );
	}
//}

//add_action('plugins_loaded', 'init_rpr_redux', 50);
