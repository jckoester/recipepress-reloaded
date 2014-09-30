<?php

/*
  Plugin Name: RecipePressReloaded
  Plugin URI: http://rp-reloaded.net 
  Description: A simple recipe plugin doing all you need for your food blog. Plus: there these nifty recipe previews in Google's search - automagically. Yet to come: easily create indexes of any taxonomy like ingredient, category, course, cuisine, ...
  Version: 0.7.0
  Author: Jan Köster
  Author URI: http://www.cbjck.de/author/jan
  License: GPL2

 * *************************************************************************

  Copyright (C) 2012 Jan Köster

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
define( 'RPR_VERSION', '0.7.0' );



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
		// Actions
		add_action( 'after_setup_theme', array( $this, 'rpr_admin_menu' ) );
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
		$option = vp_option( "rpr_option." . $name );
	
		return is_null($option) ? $default : $option;
	}
	
	static function get_option( $name, $default = null )
	{
		$option = vp_option( "rpr_option." . $name );
	
		return is_null($option) ? $default : $option;
	}
	
}

require_once('lib/vafpress/bootstrap.php');
$rpr = new RPReloaded();

include_once('php/template_tags.php');

/*if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/lib/ReduxFramework/ReduxCore/framework.php' ) ) {
	require_once( dirname( __FILE__ ) . '/lib/ReduxFramework/ReduxCore/framework.php' );
}
if ( !isset( $redux_demo ) && file_exists( dirname( __FILE__ ) . '/lib/ReduxFramework/sample/sample-config.php' ) ) {
	require_once( dirname( __FILE__ ) . '/views/settings.php' );
}*/
