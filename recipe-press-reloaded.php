<?php

/*
  Plugin Name: RecipePressReloaded
  Plugin URI: 
  Description: A Fork of the famous RecipePress Plugin by GrandSlambert. Turn your Wordpress site into a full fledged recipe sharing system. Allow users to submit recipes, organize recipes in hierarchal categories, make comments, and embed recipes in posts and pages.
  Version: 0.2
  Author: dasmaeh
  Author URI: 
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
 
/*Set plugin location constants:*/
if ( !defined('RPR_URL') )
	define( 'RPR_URL', plugin_dir_url( __FILE__ ) );
if ( !defined('RPR_PATH') )
	define( 'RPR_PATH', plugin_dir_path( __FILE__ ) );
if ( !defined('RPR_TEMPLATES_PATH') )
	define( 'RPR_TEMPLATES_PATH', RPR_PATH."/templates");
	if ( !defined('RPR_TEMPLATES_URL') )
	define( 'RPR_TEMPLATES_URL', RPR_URL."/templates");
if ( !defined('RPR_BASENAME') )
	define( 'RPR_BASENAME', plugin_basename( __FILE__ ) );

/*Load textdomain*/
//load_plugin_textdomain( 'recipe-press-reloaded', false, '/recipe-press-reloaded/language' );
//var_dump(get_locale());
/*Set plugin version*/
define( 'RPR_VERSION', '0.2' );

require_once('php/class/rpr_core.php');
require_once('php/class/rpr_administration.php');

require_once('php/class/rpr_shortcodes.php');
require_once('php/class/rpr_initialize.php');
include_once('php/inc/form_tags.php');
include_once('php/inc/template_tags.php');
include_once('php/inc/taxonomy_tags.php');
require_once('php/inc/inflector.php');

rpr_inflector::init();


class RecipePressReloaded extends RPR_Core {

     /**
      * Initialize the plugin.
      */
     function RecipePressReloaded() {
          parent::RPR_Core();

          /* Add Options Pages and Links */
          add_action('wp_loaded', array(&$this, 'wp_loaded'));
          add_action('wp_print_styles', array(&$this, 'wp_print_styles'));
          add_action('wp_print_scripts', array(&$this, 'wp_print_scripts'));
          add_filter('query_vars', array(&$this, 'query_vars'));
          add_action('template_redirect', array(&$this, 'template_redirect'));
          
          add_action('pre_get_posts', array(&$this, 'pre_get_posts'));
          

          /* Optional filters */
          if ( $this->rpr_options['add_to_author_list'] ) {
               add_filter('request', array(&$this, 'add_recipe_to_author_list'));
          }

          /* Content filtering */
          if ( !$this->rpr_options['disable_content_filter'] ) {
               add_filter('the_content', array(&$this, 'the_content_filter'));
               add_filter('the_excerpt', array(&$this, 'the_content_filter'));
          }

          /* Add Custom Theme Directory */
          if ( function_exists('register_theme_directory') ) {
               register_theme_directory(RPR_PATH . 'themes');
          }

          if ( is_admin ( ) ) {
               RPR_Admin::initialize();
          } else {
               RPR_ShortCodes::initialize();
          }
          RPR_Init::initialize();
     }

     /**
      * Add additional query vars for special features.
      *
      * @param string $qvars
      * @return string
      */
     function query_vars($qvars) {
          $qvars[] = 'print';
          //$qvars[] = 'recipe-form';
          $qvars[] = 'recipe-taxonomy';
          //$qvars[] = 'recipe-box';
          //$qvars[] = 'box-page';
          return $qvars;
     }

     

     /**
      * Catch the submit form and other special requests.
      *
      * @global  $wp_query
      * @global <type> $post
      * @return <type>
      */
     function template_redirect() {
          global $wp_query, $post;


          if ( !is_object($post) ) {
               return;
          }

          if ( $post->post_type == 'recipe' and get_query_var('print') ) {
               remove_filter('the_content', array(&$this, 'the_content_filter'));
               remove_filter('the_excerpt', array(&$this, 'the_content_filter'));
               include ($this->get_template('recipe-print'));
               exit;
          }
     }

     /**
      * Overrides the post count for recipes.
      *
      * @global <object> $wp_query
      * @return <boolean>
      */
     function pre_get_posts() {
          global $wp_query;

          if ( !isset($wp_query->query_vars['post_type']) or (isset($wp_query->query_vars['post_type']) and $wp_query->query_vars['post_type'] != 'recipe' or is_admin()) ) {
               return;
          }

          $wp_query->set('orderby', $this->rpr_options['recipe_orderby']);
          $wp_query->set('order', $this->rpr_options['recipe_order']);

          if ( $this->rpr_options['recipe_count'] != 'default' ) {
               $wp_query->set('posts_per_page', $this->rpr_options['recipe_count']);
          }
     }

     /**
      * Set up the styles and scripts for the plugin.
      */
     function wp_loaded() {
          wp_register_style('rpr_included_CSS', RPR_URL . 'css/rpr-styles.css');
          wp_register_style('rpr_CSS', $this->get_template('recipe-press-reloaded', '.css', 'url'));
     }

     /**
      * Print the stylesheets for the plugin.
      */
     function wp_print_styles() {

          if ( $this->rpr_options['custom_css'] ) {
               wp_enqueue_style('rpr_included_CSS');
               wp_enqueue_style('rpr_CSS');
          }
     }

     /**
      * Print the javascript needed for the form.
      */
     function wp_print_scripts() {
     	 
          wp_enqueue_script('jquery');
          wp_enqueue_script('jquery-ui-sortable');
          wp_enqueue_script('suggest');
     }

     /**
      * Add recipes to author list
      */
     function add_recipe_to_author_list($query) {
          if ( isset($query['author_name']) ) {
               if ( isset($query['post_type']) && is_array($query['post_type']) ) {
                    array_push($query['post_type'], 'recipe');
               } else {
                    $query['post_type'] = array('post', 'recipe');
               }
          }
          return $query;
     }

     

    

}


/* Instantiate the Plugin */
if(class_exists("RecipePressReloaded")):
	$RECIPEPRESSOBJ = new RecipePressReloaded;
	
	/* Add Widgets */
	include_once(RPR_PATH . 'widgets/list-widget.php');
	include_once(RPR_PATH . 'widgets/taxonomy-cloud-widget.php');
	include_once(RPR_PATH . 'widgets/same-taxonomy-widget.php');
	include_once(RPR_PATH . 'widgets/taxonomy-list-widget.php');
	
	/* Activation Hook */
	register_activation_hook(__FILE__, 'recipe_press_activation');
	
	function recipe_press_activation() {
	     global $wpdb;
	     if ( !post_type_exists('recipes') ) {
	          $wpdb->update($wpdb->prefix . 'posts', array('post_type' => 'recipe'), array('post_type' => 'recipes'));
	     }
	
	     /* Rename the built in taxonomies to be singular names */
	     $wpdb->update($wpdb->prefix . 'term_taxonomy', array('taxonomy' => 'recipe-category'), array('taxonomy' => 'recipe-categories'));
	     $wpdb->update($wpdb->prefix . 'term_taxonomy', array('taxonomy' => 'recipe-cuisine'), array('taxonomy' => 'recipe-cuisines'));
	}
endif;
