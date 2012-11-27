<?php

if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * rpr_administration.php - RecipePress Administration Class
 *
 * @package RecipePress
 * @subpackage classes
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 2.0.4
 */
class RPR_Admin extends RPR_Core {

     static $instance;

     /**
      * Initialize the class.
      */
     function RPR_Admin() {
          parent::RPR_Core();

          /* Administration Actions */
          /*Add a menu to our admin-area:*/
          add_action('admin_menu', array(&$this, 'rpr_admin_menu'));
          /*Add styles and scripts to the admin-area*/
          add_action('admin_print_styles', array(&$this, 'admin_print_styles'));
          add_action('admin_print_scripts', array(&$this, 'admin_print_scripts'));
          /*Initalize the settings*/
          add_action('admin_init', array(&$this, 'admin_init'));
          
          /*Save a recipe*/
          add_action('save_post', array(&$this, 'save_recipe'));
          
          /* Notices for the admin, ie error on save */
          add_action( 'admin_notices', array( &$this, 'rpr_admin_notice_handler' ) );


          add_action('right_now_content_table_end', array(&$this, 'right_now_content_table_end'));
          add_action('manage_posts_custom_column', array(&$this, 'manage_posts_custom_column'));
          add_filter('manage_edit-recipe_columns', array(&$this, 'manage_recipe_edit_columns'));
          add_action('wp_ajax_ingredient_lookup', array(&$this, 'ingredient_lookup'));
//          add_action('wp_ajax_nopriv_ingredient_lookup', array(&$this, 'ingredient_lookup'));
          add_action('wp_ajax_recipe_press_view_all_tax', array(&$this, 'view_all_taxonomy'));
//          add_action('wp_ajax_nopriv_recipe_press_view_all_tax', array(&$this, 'view_all_taxonomy'));

          /* Administration Filters */
          add_filter('plugin_action_links', array(&$this, 'plugin_action_links'), 10, 2);
          
          if ( function_exists('register_uninstall_hook') ) register_uninstall_hook(__FILE__, 'example_deinstall');
     }

     /**
      * Initialize the administration area.
      */
     static function initialize() {
          $instance = self::get_instance();
     }

     /**
      * Returns singleton instance of object
      *
      * @return instance
      */
     static function get_instance() {
          if ( is_null(self::$instance) ) {
               self::$instance = new RPR_Admin;
          }
          return self::$instance;
     }

     /**
      * Add the number of recipes to the Right Now on the Dasboard.
      */
     function right_now_content_table_end() {
          if ( !post_type_exists('recipe') ) {
               return false;
          }

          $num_posts = wp_count_posts('recipe');
          $num = number_format_i18n($num_posts->publish);
          $text = _n('Recipe', 'Recipes', intval($num_posts->publish), 'recipe-press-reloaded');
          if ( current_user_can('edit_posts') ) {
               $num = "<a href='edit.php?post_type=recipe'>$num</a>";
               $text = "<a href='edit.php?post_type=recipe'>$text</a>";
          }
          echo '<td class="first b b-recipes">' . $num . '</td>';
          echo '<td class="t recipes">' . $text . '</td>';

          echo '</tr>';

          if ( $num_posts->pending > 0 ) {
               $num = number_format_i18n($num_posts->pending);
               $text = _n('Recipe Pending', 'Recipes Pending', intval($num_posts->pending), 'recipe-press-reloaded');
               if ( current_user_can('edit_posts') ) {
                    $num = "<a href='edit.php?post_status=pending&post_type=recipe'>$num</a>";
                    $text = "<a href='edit.php?post_status=pending&post_type=recipe'>$text</a>";
               }
               echo '<td class="first b b-recipes">' . $num . '</td>';
               echo '<td class="t recipes">' . $text . '</td>';

               echo '</tr>';
          }
     }

     /**
      * Add extra columns to the edit recipes page.
      *
      * @param array $columns  Current columns.
      * @return array
      */
     function manage_recipe_edit_columns($columns) {
          $columns = array(
               'cb' => '<input type="checkbox" />',
               'thumbnail' => __('Image', 'recipe-press-reloaded'),
               'title' => __('Recipe Title', 'recipe-press-reloaded'),
               'intro' => __('Introduction', 'recipe-press-reloaded')
          );

          foreach ( $this->rpr_options['taxonomies'] as $tax => $settings ) {
               //$settings = $this->taxDefaults($settings);
               if ( $settings['active'] and taxonomy_exists($tax) ) {
                    $columns[$tax] = $settings['plural_name'];
               }
          }

//          $columns['ingredients'] = __('Ingredients', 'recipe-press-reloaded');

          if ( $this->rpr_options['use_featured'] ) {
               $columns['featured'] = __('Featured', 'recipe-press-reloaded');
          }

          $columns ['author'] = __('Author', 'recipe-press-reloaded');

          if ( $this->rpr_options['use_comments'] ) {
               $columns['comments'] = '<img src="' . get_option('siteurl') . '/wp-admin/images/comment-grey-bubble.png" alt="Comments">';
          }

          $columns['date'] = __('Date', 'recipe-press-reloaded');

          return $columns;
     }

     /**
      * Display the content of the custom columns.
      *
      * @global object $post
      * @param string $column      Name of the column
      * @return string
      */
     function manage_posts_custom_column($column) {
          global $post;

          if ( $post->post_type != 'recipe' ) {
               return;
          }

          switch ($column) {
               case 'thumbnail':
                    if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) {
                         the_post_thumbnail('recipe-press-thumb');
                    }
                    break;
               case 'intro':
                    echo rpr_inflector::trim_excerpt($post->post_excerpt, 25);
                    break;
               case 'featured':
                    if ( get_post_meta($post->ID, '_recipe_featured_value', true) ) {
                         _e('Yes', 'recipe-press-reloaded');
                    } else {
                         _e('No', 'recipe-press-reloaded');
                    }
                    break;
               case 'ingredients':
                    echo get_the_term_list($post->ID, 'recipe-ingredient', '', ', ', '');
                    break;
          }

          /* Display taxonomies if taxonomy is active and activate to display on posts list*/
          if ( isset($this->rpr_options['taxonomies'][$column]) and $this->rpr_options['taxonomies'][$column] and taxonomy_exists($column) ) {
               echo get_the_term_list($post->ID, $column, '', ', ', '');
          }
     }


	
     /**
      * Admin Init Action
      */
     function admin_init() {
          register_setting( 'rpr_options', 'rpr_options', array(&$this, 'rpr_options_validate' ));
          //Settings Section general
		  add_settings_section('rpr_general', __("General Settings", "recipe-press-reloaded"), array(&$this, 'rpr_section_general_callback_function'), 'general');
		  //Add a settings section for each taxonomy
		  $this->rpr_sections_taxonomies();
          //Settings section display
          add_settings_section('rpr_display', __("Display Settings", "recipe-press-reloaded"), array(&$this, 'rpr_section_display_callback_function'), 'display');
          //Settings section admin display
          add_settings_section('rpr_admin_post_list', __("Admin Post List Settings", "recipe-press-reloaded"), array(&$this, 'rpr_section_admin_post_list_callback_function'), 'admin_post_list');
          
          //Register Style and Scripts for the backend view
          wp_register_style('rpr_admin_CSS', RPR_URL . 'css/rpr-admin.css');
          //wp_register_script('recipePressAdminJS', RPR_URL . 'js/recipe-press-admin.js');
          //wp_register_script('recipePressOverlibJS', RPR_URL . 'js/overlib/overlib.js');
          wp_register_script('rpr_admin_JS', RPR_URL . 'js/rpr-admin.js');
     }
     
     /**
      * Add the admin page for the settings panel.
      *
      * @global string $wp_version
      */
     function rpr_admin_menu() {
     	  //add_options_page('RecipePress reloaded', 'RecipePress reloaded', 'manage_options', 'reicpe-press-reloaded', array(&$this, 'rpr_options_page'));
     	
          global $wp_version, $wpdb;

          $pages = array();

          // Set up the settings page 
          $pages[] = add_submenu_page('edit.php?post_type=recipe', __('RecipePress reloaded Settings', 'recipe-press-reloaded'), __('Settings', 'recipe-press-reloaded'), 'edit_posts', 'recipe-press-reloaded_settings', array(&$this, 'settings'));
		  $pages[] = add_submenu_page('edit.php?post_type=recipe', __('RecipePress reloaded Acknowledgements', 'recipe-press-reloaded'), __('Acknowledgements', 'recipe-press-reloaded'), 'edit_posts', 'recipe-press-reloaded_acknowledgements', array(&$this, 'acknowledgements'));

          $tableName = $wpdb->prefix . 'rp_recipes';
          if ( $wpdb->get_var("SHOW TABLES LIKE '{$tableName}'") == $tableName ) {
               $pages[] = add_submenu_page('edit.php?post_type=recipe', 'Convert', 'Convert Recipes', 'edit_posts', 'recipe-press-convert', array(&$this, 'convert'));
          }

          foreach ( $pages as $page ) {
               add_action('admin_print_styles-' . $page, array(&$this, 'admin_styles'));
               add_action('admin_print_scripts-' . $page, array(&$this, 'admin_scripts'));
          }
     }

     /**
      * Settings management panel.
      */
     function settings() {
          include(RPR_PATH . 'php/inc/settings.php');
     }
     
      /*
       * Settings management panel.
      */
     function acknowledgements() {
          include(RPR_PATH . 'php/inc/acknowledgements.php');
     }

     function admin_print_styles() {
          global $post;

          if ( is_object($post) and $post->post_type == 'recipe' ) {
               $this->admin_styles();
          }
     }

     function admin_print_scripts() {
          global $post;

          if ( is_object($post) and $post->post_type == 'recipe' ) {
          	
               $this->admin_scripts();
          }
     }

     function admin_styles() {
          wp_enqueue_style('rpr_admin_CSS');
     }

     function admin_scripts() {
     	  wp_localize_script('rpr_admin_JS', 'RPAJAX', array(
               'ajaxurl' => admin_url('admin-ajax.php'),
          ));
          wp_enqueue_script('jquery.autocomplete');
          wp_enqueue_script('rpr_admin_JS');
     }
     
      /**
      * Add a configuration link to the plugins list.
      *
      * @staticvar <object> $this_plugin
      * @param <array> $links
      * @param <array> $file
      * @return <array>
      */
     function plugin_action_links($links, $file) {
          static $this_plugin;

          if ( !$this_plugin ) {
               $this_plugin = plugin_basename(dirname(dirname(__FILE__))) . '/recipe-press-reloaded.php';
          }

          if ( $file == $this_plugin ) {
               $settings_link = '<a href="' . get_admin_url() . 'edit.php?post_type=recipe&page=' . $this->menuName . '">' . __('Settings', 'recip-press') . '</a>';
               array_unshift($links, $settings_link);
          }

          return $links;
     }
     
    /*Section general*/
     function rpr_section_general_callback_function() {
     	add_settings_field('rpr_index_slug', __("Index slug", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'general', 'rpr_general', array('id' => 'index_slug', 'name' => '[index_slug]', 'value' => $this->rpr_options['index_slug'], 'desc' => __( 'This will be used as the slug (URL) for the recipe index pages.', 'recipe-press-reloaded' ), 'link' => "<a href=\"".get_option('home')."/".$this->rpr_options['index_slug']."\">".__('View on Site', 'recipe-press-reloaded')."</a>" ) );
     	add_settings_field('rpr_use_plugin_permalinks', __("Use plugin permalinks?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_plugin_permalink', 'checked' => $this->rpr_options['use_plugin_permalinks'], 'desc' => __("Check this if you want to use your own permalink structure defined below. If not checked the default wordpress permalink structure will be used", "recipe-press-reloaded") ));
     	add_settings_field('rpr_singular_name', __("Singular name", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'general', 'rpr_general', array('id' => 'singular_name', 'name' => '[singular_name]', 'value' => $this->rpr_options['singular_name'], 'desc' => __("The name for a single recipe-post", "recipe-press" ) )  );
     	add_settings_field('rpr_plural_name', __("Plural name", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'general', 'rpr_general', array('id' => 'plural_name', 'name' => '[plural_name]', 'value' => $this->rpr_options['plural_name'], 'desc' => __("The name for multiple recipe-posts", "recipe-press" ) )  );
     	add_settings_field('rpr_identifier', __("Identifier", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'general', 'rpr_general', array('id' => 'identifier', 'name' => '[identifier]', 'value' => $this->rpr_options['identifier'], 'desc' => __("The <strong>%identifier%</strong> used in the permalink structure below", "recipe-press-reloaded") )  );
     	add_settings_field('rpr_permalink_structure', __("Permalink structure", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'general', 'rpr_general', array('id' => 'permalink_structure', 'name' => '[permalink_structure]', 'value' => $this->rpr_options['permalink_structure'], 'desc' => __(" This permalink structure will be used to create the custom URL structure for your individual recipes. These follow WP's normal <a href=\"http://codex.wordpress.org/Using_Permalinks\" title=\"Wordpress Documentation on permalinks\">permalink tags</a>, but must also include the content type <strong>%identifier%</strong> and at least one of these unique tags: <strong>%postname%</strong> or <strong>%post_id%</strong>.<br/>Allowed tags: %year%, %monthnum%, %day%, %hour%, %minute%, %second%, %postname%, %post_id% ", "recipe-press-reloaded") )  );
     	add_settings_field('rpr_use_categories', __("Use categories?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_categories', 'checked' => $this->rpr_options['use_categories'], 'desc' => __("Check this if you want to use the recipe categories taxonomy", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_cuisines', __("Use cuisines?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_cuisines', 'checked' => $this->rpr_options['use_cuisines'], 'desc' => __("Check this if you want to use the recipe cuisines taxonomy", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_servings', __("Use servings?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_servings', 'checked' => $this->rpr_options['use_servings'], 'desc' => __("Check this if you want to use the servings taxonomy", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_times', __("Use times?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_times', 'checked' => $this->rpr_options['use_times'], 'desc' => __("Check this if you want to use the times taxonomy", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_courses', __("Use courses?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_courses', 'checked' => $this->rpr_options['use_courses'], 'desc' => __("Check this if you want to use the courses taxonomy", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_seasons', __("Use seasons?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_seasons', 'checked' => $this->rpr_options['use_seasons'], 'desc' => __("Check this if you want to use the seasons taxonomy", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_thumbnails', __("Use thumbnails?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_thumbnails', 'checked' => $this->rpr_options['use_thumbnails'], 'desc' => __("Check this if you want to use thumbnails", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_featured', __("Use featured?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_featured', 'checked' => $this->rpr_options['use_featured'], 'desc' => __("Check this if you want to use featured recipes", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_comments', __("Use comments?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_comments', 'checked' => $this->rpr_options['use_comments'], 'desc' => __("Check this if you want to use comments", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_trackbacks', __("Use trackbacks?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_trackbacks', 'checked' => $this->rpr_options['use_trackbacks'], 'desc' => __("Check this if you want to use trackbacks", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_custom_fields', __("Use custom fields?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_custom_fields', 'checked' => $this->rpr_options['use_custom_fields'], 'desc' => __("Check this if you want to use custom fields", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_revisions', __("Use revisions?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_revisions', 'checked' => $this->rpr_options['use_revisions'], 'desc' => __("Check this if you want to use revisions", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_post_categories', __("Use post categories?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_post_categories', 'checked' => $this->rpr_options['use_post_categories'], 'desc' => __("Check this if you want to use the post categories instead of the recipe categories", "recipe-press-reloaded") ));
     	add_settings_field('rpr_use_post_tags', __("Use post tags?", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'general', 'rpr_general', array( 'id' => 'use_post_tags', 'checked' => $this->rpr_options['use_post_tags'], 'desc' => __("Check this if you want to use the post tags", "recipe-press-reloaded") ));
     }
      
    
     /*Section taxonomies*/
     function rpr_sections_taxonomies() {
     	foreach($this->rpr_options['taxonomies'] as $key=>$tax):
          	add_settings_section('rpr_taxonomies_'.$key, sprintf(__("Taxonomy %s", "recipe-press-reloaded"), $key), array(&$this, 'rpr_section_taxonomies_tax_callback_function'), 'taxonomies_'.$key, array('key'=>$key));
          	
          	add_settings_field('rpr_taxonomies_'.$key.'_slug', __("Taxonomy slug", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'id'=>'rpr_options_taxonomies_'.$key.'_slug', 'name' => '[taxonomies][' . $key . '][slug]', 'value' => $this->rpr_options['taxonomies'][$key]['slug'],  'desc'=>__('The URL slug for listing all terms of this taxonomy.', 'recipe-press-reloaded'), 'link'=>"&nbsp;<a href=\"".get_option('home')."/".$this->rpr_options['taxonomies'][$key]['slug']."\">".__('View on Site', 'recipe-press-reloaded')."</a>"));
          	add_settings_field('rpr_taxonomies_'.$key.'_singular_name', __("Singular name", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'id'=>'taxonomies_'.$key.'_singular_name', 'name' => '[taxonomies][' . $key . '][singular_name]', 'value' => $this->rpr_options['taxonomies'][$key]['singular_name'],  'desc'=>__('The name for a single term.', 'recipe-press-reloaded')));
          	add_settings_field('rpr_taxonomies_'.$key.'_plural_name', __("Plural name", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'id'=>'taxonomies_'.$key.'_plural_name', 'name' => '[taxonomies][' . $key . '][plural_name]', 'value' => $this->rpr_options['taxonomies'][$key]['plural_name'],  'desc'=>__('The name for multiple term.', 'recipe-press-reloaded')));
          	add_settings_field('rpr_taxonomies_'.$key.'_page', __("Display page", "recipe-press-reloaded"), array(&$this, 'rpr_options_dropdown_pages'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'key'=>$key, 'selected' => $this->rpr_options['taxonomies'][$key]['page'],  'desc'=>sprintf(__('The page where this taxonomy will be listed. You must place the short code <strong>[%1$s]</strong> on this page to display the recipes. This will be the page that users will be directed to if the template file "%2$s" does not exist in your theme.', 'recipe-press-reloaded'), 'recipe-tax tax=' . $key, 'taxonomy-recipe.php')));
          	add_settings_field('rpr_taxonomies_'.$key.'_per_page', __("Display how many per page", "recipe-press-reloaded"), array(&$this, 'rpr_options_dropdown'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'name' => 'rpr_options[taxonomies]['.$key.'][per_page]', 'id' => 'rpr_taxonomies_'.$key.'_per_page', 'selected' => $this->rpr_options['taxonomies'][$key]['per_page'], 'options' => range(1,25), 'desc'=>__('How many items shall be shown on one page?', 'recipe-press-reloaded')));
          	
          	if($key != "recipe-ingredient"):
          		add_settings_field('rpr_taxonomies_'.$key.'_default', __("Default value", "recipe-press-reloaded"), array(&$this, 'rpr_options_dropdown_categories'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'key'=>$key, 'id' => 'default',  'selected' => $this->rpr_options['taxonomies'][$key]['default'], 'desc'=>__('Default value for this taxononomy.', 'recipe-press-reloaded'))); 
          		add_settings_field('rpr_taxonomies_'.$key.'_hierarchical', __("Hierarchical", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'id'=>'taxonomies_'.$key.'_hierarchical',  'name' => '[taxonomies][' . $key . '][hierarchical]', 'checked' => $this->rpr_options['taxonomies'][$key]['hierarchical'],  'desc'=>__('Check this if you want to enable nested terms for this the taxonomy', 'recipe-press-reloaded')));
          		add_settings_field('rpr_taxonomies_'.$key.'_allow_multiple', __("Allow multiple", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'id'=>'taxonomies_'.$key.'_allow_multiple',  'name' => '[taxonomies][' . $key . '][allow_multiple]', 'checked' => $this->rpr_options['taxonomies'][$key]['allow_multiple'],  'desc'=>__('Check this if you want to allow more than one term assigned to recipe', 'recipe-press-reloaded')));
          		add_settings_field('rpr_taxonomies_'.$key.'_active', __("Active", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'id'=>'taxonomies_'.$key.'_active', 'name' => '[taxonomies][' . $key . '][active]', 'checked' => $this->rpr_options['taxonomies'][$key]['active'],  'desc'=>__('Check this if you want this taxonomy to be active', 'recipe-press-reloaded')));
          	endif;
          	add_settings_field('rpr_taxonomies_'.$key.'_show_on_posts_list', __("Show on posts list", "recipe-press-reloaded"), array(&$this, 'rpr_options_checkbox'), 'taxonomies_'.$key, 'rpr_taxonomies_'.$key, array( 'name' => 'rpr_options[taxonomies]['.$key.'][show_on_posts_list]', 'id' => 'rpr_taxonomies_'.$key.'_show_on_posts_list', 'checked' => $this->rpr_options['taxonomies'][$key]['show_on_posts_list'],  'desc'=>__('Check this if you want to have the taxonomy items displayed on the recipes posts list in the admin area.', 'recipe-press-reloaded')));
		endforeach;
     }
     
     function rpr_section_taxonomies_tax_callback_function() {
     	
     }
     
     /*Section display*/
     function rpr_section_display_callback_function() {
     	add_settings_field('rpr_default_excerpt_length', __("Default excerpt length", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'display', 'rpr_display', array('id' => 'default_excerpt_length', 'name' => '[default_excerpt_length]', 'value' => $this->rpr_options['default_excerpt_length'], 'desc' => __( 'Default length of introduction excerpt when displaying in lists.', 'recipe-press' ) ) );
    	add_settings_field('rpr_add_to_author_list', __('Add to author list', 'recipe-press-reloaded'), array(&$this, 'rpr_options_checkbox'), 'display', 'rpr_display', array( 'id' => 'add_to_author_list', 'checked' => $this->rpr_options['add_to_author_list'], 'desc' => __('Check this to include the recipes by each author in their respective post list.', 'recipe-press' ) ) );
     	add_settings_field('rpr_recipe_count', __("Number of recipes to display per page", "recipe-press-reloaded"), array(&$this, 'rpr_options_dropdown'), 'display', 'rpr_display', array( 'name' => 'rpr_options[recipe_count]', 'id' => 'rpr_recipe_count', 'selected' => $this->rpr_options['recipe_count'], 'options' => range(1,25), 'desc'=>__('How many recipes to display per page on the listing pages.', 'recipe-press-reloaded')));
     	add_settings_field('rpr_recipe_orderby', __("Order by", "recipe-press-reloaded"), array(&$this, 'rpr_options_dropdown'), 'display', 'rpr_display', array( 'name' => 'rpr_options[recipe_orderby]', 'id' => 'rpr_recipe_orderby', 'selected' => $this->rpr_options['recipe_orderby'], 'options' => array( __('Date', 'recipe-press-reloaded') => 'date', __('Title', 'recipe-press-reloaded') => 'title', __('Random', 'recipe-press-reloaded') => 'random', __('Comment count', 'recipe-press-reloaded') => 'comment_count', __('Menu order', 'recipe-press-reloaded') => 'menu_order' ) ) );
     	add_settings_field('rpr_recipe_order', "", array(&$this, 'rpr_options_dropdown'), 'display', 'rpr_display', array( 'name' => 'rpr_options[recipe_order]', 'id' => 'rpr_recipe_order', 'selected' => $this->rpr_options['recipe_order'], 'options' => array( __('Ascending', 'recipe-press-reloaded') => 'asc', __('Descending', 'recipe-press-reloaded') => 'desc'), 'desc'=>__('The listing order of recipes on the index page.', 'recipe-press-reloaded')));
     	
     	add_settings_field('rpr_custom_css', __('Use plugin CSS', 'recipe-press-reloaded'), array(&$this, 'rpr_options_checkbox'), 'display', 'rpr_display', array( 'id' => 'custom_css', 'checked' => $this->rpr_options['custom_css'], 'desc' => __('Check this to use the builtin css from the plugin.', 'recipe-press' ) ) );
     	add_settings_field('rpr_disable_content_filter', __('Disable content filter', 'recipe-press-reloaded'), array(&$this, 'rpr_options_checkbox'), 'display', 'rpr_display', array( 'id' => 'disable_content_filter', 'checked' => $this->rpr_options['disable_content_filter'], 'desc' => __('Check this this option to completely disable any content filtering. <strong>Warning!</strong> Only do this if you have created template files and are having an issue with template display.', 'recipe-press' ) ) );
     	
     	add_settings_field('rpr_link_ingredients', __('Link ingredients', 'recipe-press-reloaded'), array(&$this, 'rpr_options_checkbox'), 'display', 'rpr_display', array( 'id' => 'link_ingredients', 'checked' => $this->rpr_options['link_ingredients'], 'desc' => __('Check this to link ingredients to the taxonomy listing or the page set in the taxonomies tab.', 'recipe-press' ) ) );
     	
     	add_settings_field('rpr_time_display_type', __("Time display type", "recipe-press-reloaded"), array(&$this, 'rpr_options_dropdown'), 'display', 'rpr_display', array( 'name' => 'rpr_options[time_display_type]', 'id' => 'time_display_type', 'selected' => $this->rpr_options['time_display_type'], 'options' => array(__("Two lines", "recipe-press-reloaded")=>'double', __('One line', 'recipe-press-reloaded') => 'single' ), 'desc' => 'Mode to display the time field. Double means time and unit in seperate lines' ) );
     	add_settings_field('rpr_default_hour_text', __("Hours text", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'display', 'rpr_display', array('id' => 'hour_text', 'name' => '[hour_text]', 'value' => $this->rpr_options['hour_text'], 'desc' => __( 'Text that will be displayed in front of times greater than 60 min. Use singular only.', 'recipe-press' ) ) );
     	add_settings_field('rpr_default_minute_text', __("Minutes text", "recipe-press-reloaded"), array(&$this, 'rpr_options_input'), 'display', 'rpr_display', array('id' => 'minute_text', 'name' => '[minute_text]', 'value' => $this->rpr_options['minute_text'], 'desc' => __( 'Text that will be displayed in front of times. Use singular only.', 'recipe-press' ) ) );
     	
     	add_settings_field('rpr_image_size_image', __("Image size", "recipe-press-reloaded"), array(&$this, 'rpr_options_image_size'), 'display', 'rpr_display', array('id' => 'image_sizes_image', 'name' => '[image_sizes][image]', 'width' => $this->rpr_options['image_sizes']['image']['width'], 'height' => $this->rpr_options['image_sizes']['image']['height'], 'crop' => $this->rpr_options['image_sizes']['image']['crop'], 'desc' => __( 'Image size that will be used to display images in the single view. Might be overriden by your theme.', 'recipe-press' ) ) );
     	add_settings_field('rpr_image_size_thumb', __("Thumbnail size", "recipe-press-reloaded"), array(&$this, 'rpr_options_image_size'), 'display', 'rpr_display', array('id' => 'image_sizes_thumb', 'name' => '[image_sizes][thumb]', 'width' => $this->rpr_options['image_sizes']['thumb']['width'], 'height' => $this->rpr_options['image_sizes']['thumb']['height'], 'crop' => $this->rpr_options['image_sizes']['thumb']['crop'], 'desc' => __( 'Image size that will be used to display images in the list view. Might be overriden by your theme.', 'recipe-press' ) ) );
     }
     
     /*Section admin post_list*/
     function rpr_section_admin_post_list_callback_function() {
     	foreach($this->rpr_options['taxonomies'] as $key=>$tax):
     	//	add_settings_section('rpr_taxonomies_'.$key, sprintf(__("Taxonomy %s", "recipe-press-reloaded"), $key), array(&$this, 'rpr_section_taxonomies_tax_callback_function'), 'taxonomies_'.$key, array('key'=>$key));
     		add_settings_field('rpr_taxonomies_'.$key.'_show_on_posts_list', sprintf(__("Show ''%s' on posts list", "recipe-press-reloaded"), $key), array(&$this, 'rpr_options_checkbox'), 'admin_post_list', 'rpr_admin_post_list', array( 'name' => 'rpr_options[taxonomies]['.$key.'][show_on_posts_list]', 'id' => 'rpr_taxonomies_'.$key.'_show_on_posts_list', 'checked' => $this->rpr_options['taxonomies'][$key]['show_on_posts_list']));//,  'desc'=>__('Check this if you want to have the taxonomy items displayed on the recipes posts list in the admin area.', 'recipe-press-reloaded')));
     	endforeach;
     }
     
     /*Creates a checkbox field
      * args: 
      * - id (id for the field)
      * - name (optional, name for the field)
      * - checked (boolean value at which the field should appear checked)
      * - desc (optional, descriptive text)
      */
     function rpr_options_checkbox($args){
     	if(isset($args['id']) && $args['id']!="" && isset($args['checked']) ):
     		$outp="<input id=\"rpr_options_" . $args['id'] . "\" name=\"";
     		if( isset( $args['name'] ) && $args['name'] != "" ) :
     			$outp.="rpr_options".$args['name'];
     		else:
     			$outp.=	"rpr_options[".$args['id']."]";
			endif;
			$outp.="\" type=\"checkbox\" value=\"1\" ";
     		$outp.= checked( '1', $args['checked'], false );
     		$outp.="/>";
     		if ( isset($args['desc']) && $args['desc'] != ""):
     			$outp.="<p>" . $args['desc'] . "</p>";
     		endif;
     	else:
     		$outp="<p class=\"error\">".sprintf( __('There was an error in %1$s in function %2$s. Please file a bug!', "recipe-press-reloaded"), "rpr_administration.php", "rpr_options_checkbox()")."</p>";
     	endif;
     	echo $outp;
     }
     
     
    /* Creates an input field
    * args:
    * - id (id for the field)
    * - name (name for the field)
    * - value (current value of the option)
    * - size (optional)
    * - desc (optional, descriptive text)
    * - link (optional, view on site link)
    */
	function rpr_options_input($args) {
		if( isset( $args['id'] ) && $args['id'] != "" && isset( $args['name'] ) && $args['name'] != "" && isset( $args['value'] ) && ($args['value'] != "" )):
			$outp="<input id=\"" . $args['id'] ."\" name=\"rpr_options" . $args['name'] . "\" type=\"text\" size=\"";
			if ( isset( $args['size'] ) && is_int( $args['size'] )):
				$outp.=$args['size'];
			else:
				$outp.="40";
			endif;
			$outp.="\" value=\"" . $args['value'] . " \" />";
			if ( isset($args['link']) && $args['link'] != ""):
				$outp.="&nbsp;".$args['link'];
			endif;
			if ( isset($args['desc']) && $args['desc'] != ""):
     			$outp.="<p>" . $args['desc'] . "</p>";
     		endif;
		else:
			$outp="<p class=\"error\">".sprintf( __('There was an error in %1$s in function %2$s. Please file a bug!', "recipe-press-reloaded"), "rpr_administration.php", "rpr_options_input()")."</p>";
		endif;
		echo $outp;
	}
	
	
	/*Creates a dropdown field
	 * args:
	 * - name (of the field)
	 * - id (name for the field)
	 * - selected (current value)
	 * - options (array aof options)
	 * - desc (optional, descriptive text)
	 */
	 function rpr_options_dropdown($args) {
	 	if( isset( $args['name'] ) && $args['name'] != "" && isset( $args['id'] ) && $args['id'] != "" && isset( $args['selected'] )  && isset($args['options']) && is_array($args['options'] ) ) :
	 	 	$outp = "<select name=\"" . $args['name'] . "\" id=\"". $args['id'] ." \">\n";
	 	 	if ( array_values($args['options']) === $args['options'] ):
				foreach($args['options'] as $opt ) :
					$outp .= "<option value=\"$opt\" " .selected($args['selected'], $opt, false) . ">$opt</option>\n";
				endforeach;
			else:
				foreach($args['options'] as $key => $value ) :
					$outp .= "<option value=\"$value\" " .selected($args['selected'], $value, false) . ">$key</option>\n";
				endforeach;
			endif;
			$outp .= "</select>";
	 	 	if ( isset($args['desc']) && $args['desc'] != ""):
     			$outp .= "<p>" . $args['desc'] . "</p>";
     		endif;
		else:
			$outp="<p class=\"error\">".sprintf( __('There was an error in %1$s in function %2$s. Please file a bug!', "recipe-press-reloaded"), "rpr_administration.php", "rpr_options_dropdown_pages()")."</p>";
		endif;
		echo $outp;
	 }
	 
	/*Creates a wp_page_dropdown field
	 * args:
	 * - key (of the taxonomy)
	 * - selected (current value)
	 * - desc (optional, descriptive text)
	 */
	 function rpr_options_dropdown_pages($args) {
	 	if( isset( $args['key'] ) && $args['key'] != "" && isset( $args['selected'] ) ):
	 	 	$outp = wp_dropdown_pages(array(
											'name' =>  'rpr_options[taxonomies][' . $args['key'] . '][page]', 
											'show_option_none' => __('None', 'recipe-press-reloaded'), 
											'selected' => $args['selected'], 
											'echo' => false 
											));
	 	 	if ( isset($args['desc']) && $args['desc'] != ""):
     			$outp.="<p>" . $args['desc'] . "</p>";
     		endif;
		else:
			$outp="<p class=\"error\">".sprintf( __('There was an error in %1$s in function %2$s. Please file a bug!', "recipe-press-reloaded"), "rpr_administration.php", "rpr_options_dropdown_pages()")."</p>";
		endif;
		echo $outp;
	 }
	 
	 /*Creates a wp_category_dropdown field
	  * - key (of the taxonomy)
	  * - selected (current value)
	  * - desc (optional, descriptive text)
	  */
	 function rpr_options_dropdown_categories($args){
	 	if( isset( $args['key'] ) && $args['key'] != "" && isset($args['selected']) ):
	 	 	$outp =  wp_dropdown_categories(array(
	 	 										'name' => 'rpr_options[taxonomies][' . $args['key'] . '][default]', 
												'id' => $args['key'], 
	 	 										'hierarchical' => $this->rpr_options['taxonomies'][$args['key']]['hierarchical'], 
												'taxonomy' => $args['key'], 
												'show_option_none' => __('No Default', 'recipe-press-reloaded'), 
												'hide_empty' => false, 
												'orderby' => 'name', 
												'selected' => $args['selected'],
												'echo' => false,
												));
	 	 	if ( isset($args['desc']) && $args['desc'] != ""):
     			$outp.="<p>" . $args['desc'] . "</p>";
     		endif;
		else:
			$outp="<p class=\"error\">".sprintf( __('There was an error in %1$s in function %2$s. Please file a bug!', "recipe-press-reloaded"), "rpr_administration.php", "rpr_options_dropdown_pages()")."</p>";
		endif;
		echo $outp;	
	 }
	 
	 /*Creates a settings set for an image size
	  * - id (id for the field)
	  * - name (name for the field)
	  * - crop
	  * - width
	  * - height
	  */
	 function rpr_options_image_size($args){
	 	if( isset( $args['id'] ) && $args['id'] != "" && isset($args['crop'])):
	 		$outp="<input id=\"" . $args['id'] ."_width\" name=\"rpr_options" . $args['name'] . "[width]\" type=\"text\" size=\"10\" value=\"" . $args['width'] . " \" />";
			$outp.="<input id=\"" . $args['id'] ."_height\" name=\"rpr_options" . $args['name'] . "[height]\" type=\"text\" size=\"10\" value=\"" . $args['height'] . " \" />";
			
			$outp.= "<select name=\"rpr_options" . $args['name'] . "[crop]\" id=\"". $args['id'] ."_crop \">\n";
	 	 		$outp .= "<option value=\"1\" " .selected($args['crop'], '1', false) . ">" . __("crop", "recipe-press-reloaded") . "</option>\n";
	 	 		$outp .= "<option value=\"0\" " .selected($args['crop'], '0', false) . ">" . __("proportional", "recipe-press-reloaded") . "</option>\n";
	 	 	$outp.= "</select>\n";
			
	 	 	if ( isset($args['desc']) && $args['desc'] != ""):
     			$outp.="<p>" . $args['desc'] . "</p>";
     		endif;
		else:
			$outp="<p class=\"error\">".sprintf( __('There was an error in %1$s in function %2$s. Please file a bug!', "recipe-press-reloaded"), "rpr_administration.php", "rpr_options_dropdown_pages()")."</p>";
		endif;
		echo $outp;	
	 }

     /* validate our options*/
	function rpr_options_validate($input) {
		//Using a very simple valifdator which just strips any HTML:
		// Create our array for storing the validated options  
    	
    	$output = array();  
 
    	foreach( $input as $key => $value ):  
        	// Check to see if the current option has a value and ist not an array  
        	if( $key == 'taxonomies'):
        		$output[$key] = $this->rpr_options_taxonomies_validate($input[$key]);
        	elseif($key == 'image_sizes'):
        		$output[$key] = $this->rpr_options_image_sizes_validate($input[$key]);
        	else:
        		$output[$key] = $this->rpr_validate_value($input[$key], $key);
            endif; 
    	endforeach;
    	
	    // Return the array processing any additional functions filtered by this action  
    	return apply_filters( 'rpr_options_validate', $output, $input );
	}  
	
	private function rpr_options_taxonomies_validate($input) {
		foreach($this->rpr_options['taxonomies'] as $taxkey=>$value):
			foreach($this->rpr_options['taxonomies'][$taxkey] as $key=>$value):
				if(isset($input[$taxkey][$key])):
					$out[$taxkey][$key] = $this->rpr_validate_value($input[$taxkey][$key], $key);
				else:
					$out[$taxkey][$key] = $value;
				endif;
			endforeach;
		endforeach;
		
		return $out;
	}
	
	private function rpr_options_image_sizes_validate($input) {
		foreach($this->rpr_options['image_sizes'] as $imkey=>$image_size):
			foreach($this->rpr_options['image_sizes'][$imkey] as $key => $value):
				if(isset($input[$imkey][$key])):
					$out[$imkey][$key] = $this->rpr_validate_value($input[$imkey][$key], $key);
				else:
					$out[$imkey][$key] = $value;
				endif;
			endforeach;
		endforeach;
		
		return $out;
	}
		/*
		$options = $this->rpr_options;
		$defaults=$this->rpr_options_defaults;
		
		$options['index_slug'] = strtolower(trim($input['index_slug']));
		if(!valid_slug($options['index_slug'])):
			add_settings_error("index_slug", "index_slug", "This is not a valid slug!");
			$options['index_slug'] = $defaults['index_slug'];
		endif;
		
		$options['use_plugin_permalinks'] = trim($input['use_plugin_permalinks']);
		if(!valid_checkbox($options['use_plugin_permalinks'])):
			$options['use_plugin_permalinks']=$defaults['use_plugin_permalinks'];
		endif;
		
		$options['singular_name'] = trim($input['singular_name']);
		if(!preg_match('/^[a-z0-9]+$/i', $options['singular_name'])):
			$options['singular_name'] = $defaults['singular_name'];
		endif;$options['use_trackbacks'] = trim($input['use_trackbacks']);
		if(!valid_checkbox($options['use_trackbacks'])):
			$options['use_trackbacks']=$defaults['use_trackbacks'];
		endif;
		
		$options['plural_name'] = trim($input['plural_name']);
		if(!preg_match('/^[a-z0-9]+$/i', $options['plural_name'])):
			$options['plural_name'] = $defaults['plural_name'];
		endif;
		
		$options['identifier'] = strtolower(trim($input['identifier']));
		if(!valid_slug($options['identifier'])):
			$options['identifier'] = $defaults['identifier'];
		endif;
		
		$options['permalink_structure'] = strtolower(trim($input['permalink_structure']));
		if(!preg_match('/^[a-z0-9\-\+\/\%\_]+$/i', $options['plural_name'])):
			$options['permalink_structure'] = $defaults['permalink_structure'];
		endif;
		
		$options['use_servings'] = trim($input['use_servings']);
		if(!valid_checkbox($options['use_servings'])):
			$options['use_servings']=$defaults['use_servings'];
		endif;
		
		$options['use_times'] = trim($input['use_times']);
		if(!valid_checkbox($options['use_times'])):
			$options['use_times']=$defaults['use_times'];
		endif;
		
		$options['use_courses'] = trim($input['use_courses']);
		if(!valid_checkbox($options['use_courses'])):
			$options['use_courses']=$defaults['use_courses'];
		endif;
		
		$options['use_seasons'] = trim($input['use_seasons']);
		if(!valid_checkbox($options['use_seasons'])):
			$options['use_seasons']=$defaults['use_seasons'];
		endif;
		
		$options['use_thumbnails'] = trim($input['use_thumbnails']);
		if(!valid_checkbox($options['use_thumbnails'])):
			$options['use_thumbnails']=$defaults['use_thumbnails'];
		endif;
		
		$options['use_featured'] = trim($input['use_featured']);
		if(!valid_checkbox($options['use_featured'])):
			$options['use_featured']=$defaults['use_featured'];
		endif;
		
		$options['use_comments'] = trim($input['use_comments']);
		if(!valid_checkbox($options['use_comments'])):
			$options['use_comments']=$defaults['use_comments'];
		endif;
		
		$options['use_trackbacks'] = trim($input['use_trackbacks']);
		if(!valid_checkbox($options['use_trackbacks'])):
			$options['use_trackbacks']=$defaults['use_trackbacks'];
		endif;
		
		$options['use_custom_fields'] = trim($input['use_custom_fields']);
		if(!valid_checkbox($options['use_custom_fields'])):
			$options['use_custom_fields']=$defaults['use_custom_fields'];
		endif;
		
		$options['use_revisions'] = trim($input['use_revisions']);
		if(!valid_checkbox($options['use_revisions'])):
			$options['use_revisions']=$defaults['use_revisions'];
		endif;
		
		$options['use_post_categories'] = trim($input['use_post_categories']);
		if(!valid_checkbox($options['use_post_categories'])):
			$options['use_post_categories']=$defaults['use_post_categories'];
		endif;
		
		$options['use_post_tags'] = trim($input['use_post_tags']);
		if(!valid_checkbox($options['use_post_tags'])):
			$options['use_post_tags']=$defaults['use_post_tags'];
		endif;
		
		return $options;
	}*/
	
	//Might be of use when writing a more sophisticated validation function, currently unused:
	/*Validators*/

	
	private function rpr_validate_value($in, $key='') {
		$checkboxes = array(
    		'use_categories',
			'use_cuisines',
			'use_servings',
			'use_times',
			'use_courses',
			'use_seasons',
			'use_thumbnails',
			'use_featured',
			'use_comments',
			'use_trackbacks',
			'use_custom_fields',
			'use_revisions',
			'use_post_categories',
			'use_post_tags',
			'hierarchical',
			'active',
			'allow_multiple',
			'crop',
			'link_ingredients',
    	);
		// Strip all HTML and PHP tags and properly handle quoted strings  
        $out = strip_tags( stripslashes( $in ) );
        //if is checkbox:
        if(in_array($key, $checkboxes)):
			if ( $in == 1 or $in == '1' ) {
				$out = true;
			} else {
				$out = false;
			}
		endif;
		return $out;
	}

     
     /**
      * Method to handle special features on the settings pages.
      * 
      * @param array $old
      * @param array $new 
      */
    /* function update_option($old, $new) {
          remove_action('update_option_' . $this->optionsName, array(&$this, 'update_option'));

          if ( isset($_REQUEST['confirm-reset-options']) ) {
               delete_option($this->optionsName);
               update_option($this->optionsName, array('version' => $this->version));

               wp_redirect(admin_url('admin.php?page=recipe-press&tab=' . $_POST['active_tab'] . '&tax=' . $_POST['active_tax'] . '&reset=true'));
               exit();
          }

          // Delete Recipes if checked 
          if ( isset($_POST['remove-pending-recipes']) or isset($_POST['remove-all-recipes']) ) {
               $args = array(
                    'post_type' => 'recipe',
                    'post_status' => 'pending',
                    'numberposts' => -1
               );

               if ( isset($_POST['remove-all-recipes']) ) {
                    $args['post_status'] = 'all';
               }

               $posts = get_posts($args);
               foreach ( $posts as $post ) {
                    wp_trash_post($post->ID);
               }
          }

          // Remove taxonomy data if checked 

          $builtins = array('recipe-size', 'recipe-serving', 'recipe-ingredient');
          $taxonomies = array_keys($this->options['taxonomies']);

          foreach ( array_merge($taxonomies, $builtins) as $taxonomy ) {
               if ( isset($_POST['remove-empty-' . $taxonomy]) or isset($_POST['remove-all-' . $taxonomy]) ) {
                    $args = array(
                         'hide_empty' => false,
                         'pad_counts' => true
                    );

                    $terms = get_terms($taxonomy, $args);

                    foreach ( $terms as $term ) {
                         if ( isset($_POST['remove-all-' . $taxonomy]) or $term->count == 0 ) {
                              wp_delete_term($term->term_id, $taxonomy);
                         }
                    }
               }
          }
          unset($taxonomies);

          // Remove taxonomies marked as delete 
          foreach ( $new['taxonomies'] as $tax => $settings ) {
               if ( !isset($settings['delete']) ) {
                    $taxonomies[$tax] = $settings;
               }
          }

          // Create new taxonomy if entered 
          if ( isset($_POST['new_taxonomy']) and $_POST['new_taxonomy'] != '' ) {
               $name = rpr_inflector::humanize($_POST['new_taxonomy']);
               $tax_args = array(
                    'slug' => $_POST['new-taxonomy'],
                    'singular' => ucwords($name),
                    'plural' => ucwords(rpr_inflector::plural($name, 2)),
                    'active' => true
               );
               $taxonomies[$_POST['new_taxonomy']] = $this->taxDefaults($tax_args);
               $_POST['active_tax'] = $_POST['new_taxonomy'];
          }

          $new['taxonomies'] = $taxonomies;

          update_option($this->optionsName, $new);

          add_action('update_option_' . $this->optionsName, array(&$this, 'update_option'), 10);

          wp_redirect(admin_url('admin.php?page=recipe-press&tab=' . $_POST['active_tab'] . '&tax=' . $_POST['active_tax'] . '&updated=true'));
          exit();
     }
*/

     function checked($data, $value) {
          if (
                  (is_array($data) and in_array($value, $data) )
                  or $data == $value
          ) {
               echo 'checked="checked"';
          }
     }
     
     
	/**
	 * Delete options in database
 	*/
	function example_deinstall() {
		delete_option('rpr_options');
	}
	
	 /**
      * AJAX Handler for the ingredient lookup form.
      */
     function ingredient_lookup() {

          $args = array(
               'name__like' => $_REQUEST['q'],
               'number' => 20,
               'ordeby' => 'name',
               'order' => 'asc'
          );

          $terms = get_terms('recipe-ingredient', $args);

          foreach ( $terms as $term ) {
               echo $term->name . '<span class="ingredient-id"> : ' . $term->term_id . "</span>\n";
          }

          die();
     }
     
     /**
      * AJAX handler for view all taxonomies
      */
      //OUTDATED!!!!
     function view_all_taxonomy() {
          global $this_instance;
          $instance = get_option('widget_recipe_press_taxonomy_widget');

          $defaults = array(
               'orderby' => $this->rpr_options['widget_orderby'],
               'order' => $this->rpr_options['widget_order'],
               'style' => $this->rpr_options['widge_style'],
               'thumbnail_size' => 'recipe-press-thumb', //There no thumbnail-sizes in RPR!
               'hide-empty' => $this->rpr_options['widget_hide_empty'],
               'exclude' => NULL,
               'include' => NULL,
               'taxonomy' => 'recipe-category',
               'title' => '',
               'items' => $this->rpr_options['widget_items'],
               'show-count' => false,
               'before-count' => ' ( ',
               'after-count' => ' ) ',
               'show-view-all' => false,
               'view-all-text' => '&darr;' . __('View All', 'recipe-press-reloaded'),
               'submit_link' => false,
               'list-class' => 'recipe-press-taxonomy-widget',
               'item-class' => 'recipe-press-taxonomy-item',
               'child-class' => 'recipe-press-child-item',
               'target' => 'none',
          );

          $this_instance = $instance = wp_parse_args($instance['5'], $defaults);

          $taxArgs = array(
               'orderby' => $instance['orderby'],
               'order' => $instance['order'],
               'style' => $instance['style'],
               'show_count' => $instance['show-count'],
               'hide_empty' => $instance['hide-empty'],
               'use_desc_for_title' => 1,
               'child_of' => 0,
               'exclude' => $instance['exclude'],
               'include' => get_published_categories($_REQUEST['tax']),
               'hierarchical' => ($instance['taxonomy'] == 'recipe-ingredient') ? false : $this->rpr_options['taxonomies'][$instance['taxonomy']]['hierarchical'],
               'title_li' => '',
               'show_option_none' => __('No categories'),
               'number' => NULL,
               'echo' => 1,
               'depth' => 0,
               'current_category' => 0,
               'pad_counts' => false,
               'taxonomy' => $_REQUEST['tax'],
               'walker' => new Walker_RPR_Taxonomy
          );

          wp_list_categories($taxArgs);
          echo '<div class="cleared" style="clear:both"></div>';

          die();
     }
     
     /**
      * Checks if the Recipe Form was submitted and creates the recipe.
      */
	/*function catch_recipe_form() {
          // Check if form is submitted 
          if ( isset($_POST['recipe-form-nonce']) and wp_verify_nonce($_POST['recipe-form-nonce'], 'recipe-form-submit') ) {
               $errors = $this->create_recipe();

               if ( count($errors) == 0 ) {
                    //$page = get_page($this->rpr_options['form-redirect']);

                    if ( $page->ID == $post->ID ) {
                         $url = get_option('home');
                    } else {
                         $url = get_post_permalink($page->ID, true);
                    }

                    wp_redirect($url);
                    exit();
               }
          } elseif ( isset($_POST['recipe-form-nonce']) ) {
               wp_die(__('This form was submitted without a proper <emph>nonce</emph> - a security means. Please contact the site administrator.', 'recipe-press-reloaded'));
          }
     }*/
     
      /**
      * Save the recipe.
      */
	function save_recipe( $post_id ) {
		$errors = false;
     	// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			 $errors = "There was an error doing autosave";
			//return;

      	//Verify the nonces for the metaboxes
		if ( isset( $_POST['rpr_ingredients_nonce_field'] ) &&  !wp_verify_nonce($_POST['rpr_ingredients_nonce_field'],'rpr_ingredients_nonce') ){
			$errors = "There was an error saving the recipe. Ingredients nonce not verified";
			//return;
		}	
		if ( $_POST['rpr_details_nonce_field'] && !wp_verify_nonce($_POST['rpr_details_nonce_field'],'rpr_details_nonce') ){
			$errors = "There was an error saving the recipe. Details nonce not verified";
			//return;
		}
		
		// Check permissions
  		if ( !current_user_can( 'edit_post', $post_id ) )
        	$errors = "There was an error saving the recipe. No sufficient rights.";
        	//return;
		
		//If we have an error update the error_option and return
		if( $errors ) {
			update_option('rpr_admin_errors', $errors);
			return;
		}
		
  		
  		// OK, we're authenticated: we need to find and save the data
  		// Save the details
  		$details = $_POST['recipe_details'];
        $details['recipe_ready_time'] = $this->readyTime();
        $details['recipe_ready_time_raw'] = $this->readyTime(NULL, NULL, false);
  		foreach ( $details as $key => $value ) :
			$key = '_' . $key . '_value';
			if ( get_post_meta($post_id, $key) == "" ) {
				add_post_meta($post_id, $key, $value, true);
			} elseif ( $value != get_post_meta($post_id, $key . '_value', true) ) {
				update_post_meta($post_id, $key, $value);
			} elseif ( $value == "" ) {
				delete_post_meta($post_id, $key, get_post_meta($post_id, $key, true));
			}
        endforeach;
		
		/* Turn off featured if not checked */
		if ( !isset($_POST['recipe_details']['recipe_featured']) ) {
			update_post_meta($post_id, '_recipe_featured_value', 0);
		}

		// Save the ingredients
		$detailkey = '_recipe_ingredient_value';
        $postIngredients = array();
        delete_post_meta($post_id, $detailkey);
        $ing_count = 0;
        
        foreach ( $_POST['ingredients'] as $id => $ingredient ) :
        	$ingredient['order'] = $ing_count;
        	
        	if ( (isset($ingredient['item']) and $ingredient['item'] != -1 and $ingredient['item'] != '' and $ingredient['item'] != '0')
                       or (isset($ingredient['new-ingredient']) and $ingredient['new-ingredient'] != '') ) :
                       
            		if ( isset($ingredient['size']) and $ingredient['size'] == 'divider' ) :
                         $ingredient['item'] = $ingredient['new-ingredient'];
                	else :
                         /* Save ingredient taxonomy information */
                         if ( isset($ingredient['item']) ) {
                              $term = get_term_by('id', $ingredient['item'], 'recipe-ingredient');
                         } else {
                              $term = array();
                         }

                         if ( is_object($term) and !isset($term->errors) ) {
                              array_push($postIngredients, (int) $term->term_id);
                         } elseif ( isset($ingredient['new-ingredient']) and $ingredient['new-ingredient'] != '' ) {
                              $term = wp_insert_term($ingredient['new-ingredient'], 'recipe-ingredient');
                              if ( isset($term->errors) ) {
                                   $ingredient['item'] = $term->error_data['term_exists'];
                              } else {
                                   $ingredient['item'] = $term['term_id'];
                              }

                              $term = get_term_by('id', $ingredient['item'], 'recipe-ingredient');
                              array_push($postIngredients, $term->slug);
                         }
                    endif;
                    
                    unset($ingredient['new-ingredient']);

                    add_post_meta($post_id, $detailkey, $ingredient, false);
                       	
            endif;
        	
        	++$ing_count;
        endforeach;
        
        wp_set_object_terms($post_id, $postIngredients, 'recipe-ingredient', false);
		
		//WAS IST DAS HIER???
  		/*
          global $post;

          if ( is_object($post) and $post->post_type == 'revision' ) {
               return;
          }

          //do_action('rpr_before_save');
		*/
          
		//do_action('rpr_after_save');

        //return $post_id;
	}
     

     
     // Display any errors
	function rpr_admin_notice_handler() {

    	$errors = get_option('rpr_admin_errors');

    	if($errors) {
			echo '<div class="error"><p>' . $errors . '</p></div>';
    	}
    	/* Reset the option */
		update_option('rpr_admin_errors', false);
	}

}