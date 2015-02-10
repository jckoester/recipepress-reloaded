<?php

/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */
require_once( WP_PLUGIN_DIR . '/recipepress-reloaded/php/helper/admin_menu_helper.php');


if (!class_exists('RPR_Settings')) {

    class RPR_Settings {

    	//pluginName for i8n, should be set by parameter...
    	private $pluginName = 'recipepress-reloaded';
    	private $pluginTitle = RPR_TITLE;
    	private $pluginVersion = RPR_VERSION;
    	
        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs.  ;)
            if (  true == Redux_Helpers::isTheme(__FILE__) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }

        }

        public function initSettings() {

            // Just for demo purposes. Not needed per say.
//            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            // If Redux is running as a plugin, this will remove the demo notice and links
            //add_action( 'redux/loaded', array( $this, 'remove_demo' ) );
            
            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            //add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 3);
            
            // Change the arguments after they've been declared, but before the panel is created
            //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
            
            // Change the default value of a field after it's been set, but before it's been useds
            //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
            
            // Dynamically add a section. Can be also used to modify sections/fields
            //add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        /**

          This is a test function that will let you see when the compiler hook occurs.
          It only runs if a field	set with compiler=>true is changed.

         * */
        function compiler_action($options, $css, $changed_values) {
            echo '<h1>The compiler hook has run!</h1>';
            echo "<pre>";
            print_r($changed_values); // Values that have changed since the last save
            echo "</pre>";
            //print_r($options); //Option values
            //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

            /*
              // Demo of how to use the dynamic CSS and write your own static CSS file
              $filename = dirname(__FILE__) . '/style' . '.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
              WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
             */
        }

        /**

          Custom function for filtering the sections array. Good for child themes to override or add to the sections.
          Simply include this function in the child themes functions.php file.

          NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
          so you must use get_template_directory_uri() if you want to use any of the built in icons

         * */
        function dynamic_section($sections) {
            //$sections = array();
            $sections[] = array(
                'title' => __('Section via hook', 'redux-framework-demo'),
                'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'redux-framework-demo'),
                'icon' => 'el-icon-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }

        /**

          Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

         * */
        function change_arguments($args) {
            //$args['dev_mode'] = true;

            return $args;
        }

        /**

          Filter hook for filtering the default value of any given field. Very useful in development mode.

         * */
        function change_defaults($defaults) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            global $rpr_option;
            /**
              Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
             * */
            // Background Patterns Reader
/*            $sample_patterns_path   = ReduxFramework::$_dir . '../sample/patterns/';
            $sample_patterns_url    = ReduxFramework::$_url . '../sample/patterns/';
            $sample_patterns        = array();

            if (is_dir($sample_patterns_path)) :

                if ($sample_patterns_dir = opendir($sample_patterns_path)) :
                    $sample_patterns = array();

                    while (( $sample_patterns_file = readdir($sample_patterns_dir) ) !== false) {

                        if (stristr($sample_patterns_file, '.png') !== false || stristr($sample_patterns_file, '.jpg') !== false) {
                            $name = explode('.', $sample_patterns_file);
                            $name = str_replace('.' . end($name), '', $sample_patterns_file);
                            $sample_patterns[]  = array('alt' => $name, 'img' => $sample_patterns_url . $sample_patterns_file);
                        }
                    }
                endif;
            endif;

            ob_start();

            $ct             = wp_get_theme();
            $this->theme    = $ct;
            $item_name      = $this->theme->get('Name');
            $tags           = $this->theme->Tags;
            $screenshot     = $this->theme->get_screenshot();
            $class          = $screenshot ? 'has-screenshot' : '';

            $customize_title = sprintf(__('Customize &#8220;%s&#8221;', 'redux-framework-demo'), $this->theme->display('Name'));
            
            ?>
            <div id="current-theme" class="<?php echo esc_attr($class); ?>">
            <?php if ($screenshot) : ?>
                <?php if (current_user_can('edit_theme_options')) : ?>
                        <a href="<?php echo wp_customize_url(); ?>" class="load-customize hide-if-no-customize" title="<?php echo esc_attr($customize_title); ?>">
                            <img src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                        </a>
                <?php endif; ?>
                    <img class="hide-if-customize" src="<?php echo esc_url($screenshot); ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
                <?php endif; ?>

                <h4><?php echo $this->theme->display('Name'); ?></h4>

                <div>
                    <ul class="theme-info">
                        <li><?php printf(__('By %s', 'redux-framework-demo'), $this->theme->display('Author')); ?></li>
                        <li><?php printf(__('Version %s', 'redux-framework-demo'), $this->theme->display('Version')); ?></li>
                        <li><?php echo '<strong>' . __('Tags', 'redux-framework-demo') . ':</strong> '; ?><?php printf($this->theme->display('Tags')); ?></li>
                    </ul>
                    <p class="theme-description"><?php echo $this->theme->display('Description'); ?></p>
            <?php
            if ($this->theme->parent()) {
                printf(' <p class="howto">' . __('This <a href="%1$s">child theme</a> requires its parent theme, %2$s.') . '</p>', __('http://codex.wordpress.org/Child_Themes', 'redux-framework-demo'), $this->theme->parent()->display('Name'));
            }
            ?>

                </div>
            </div>

            <?php
            $item_info = ob_get_contents();

            ob_end_clean();

            $sampleHTML = '';
            if (file_exists(dirname(__FILE__) . '/info-html.html')) {
                Redux_Functions::initWpFilesystem();
                
                global $wp_filesystem;

                $sampleHTML = $wp_filesystem->get_contents(dirname(__FILE__) . '/info-html.html');
            }
*/
            // ACTUAL DECLARATION OF SECTIONS
            
                        
           /* $this->sections[] = array(
                'title'     => __('Import / Export', 'redux-framework-demo'),
                'desc'      => __('Import and Export your Redux Framework settings from file, text or URL.', 'redux-framework-demo'),
                'icon'      => 'el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your Redux options',
                        'full_width'    => false,
                    ),
                ),
            );  */

            $this->sections[] = array(
            	'icon' 		=> 'fa-cogs',
            	'title'		=> __( 'General Settings', $this->pluginName ),
            	'heading'	=> __( 'Recipe Archive Pages', $this->pluginName ),
            	'fields'	=> array(
            		array(
            			'id'	=> 'recipe_homepage_display',
            			'type'	=> 'switch',
            			'title' => __('Home Page', $this->pluginName),
            			'subtitle' => __( 'Defines if recipes should be displayed on the homepage like \'normal\' posts.', 'recipepress-reloaded' ) ,
            			'default' => true,
            			'on' => __( 'Yes', $this->pluginName ),
            			'off' => __( 'No', $this->pluginName ),
            		),
            		array(
            			'id'	=> 'recipe_archive_display',
            			'type'	=> 'select',
            			'title' => __('Archive Page', $this->pluginName),
            			'subtitle' => __( 'Defines what to show of your recipes on the archive page', $this->pluginName) ,
            			'options'	=> array(
            				'excerpt' => __( 'Only the excerpt', $this->pluginName ),
            				'full' => __('The entire recipe', $this->pluginName),
            			    ),
            			 'default' => 'excerpt',
            		),
                    array(
                        'id' => 'recipe_slug',
                        'type' => 'text',
                        'title' => __('Slug', $this->pluginName),
                        'default' => 'recipe',
                        'validate' => 'not_empty',
                    ),
                    array(
                        'id'        => 'rpr_slug_preview',
                        'type'      => 'callback',
                        'callback'  => 'rpr_admin_slug_preview'
                    ),
                    array(
                        'id'        => 'recipe_slug_notebox',
                        'type'      => 'info',
                        'notice'    => true,
                        'style'     => 'info',
                        'icon'      => 'fa-info',
                        'title'     => __('404 error/page not found?', $this->pluginName),
                        'desc'      => __('Try', $this->pluginName) . ' <a href="http://rp-reloaded.net/documentation/404-error-page-found/" target="_blank">'.__('flushing your permalinks', $this->pluginName).'</a>.',
                    ),
                )
            );
			
			$this->sections[] = array(
            	'icon' 		=> 'fa-newspaper-o',
            	'title'		=> __( 'Appearance', $this->pluginName ),
            	'fields'	=> rpr_admin_layout_settings(),
            );
			
			$this->sections[] = array(
            	'icon' 		=> 'fa-image',
            	'title'		=> __( 'Images', $this->pluginName ),
            	'subsection' => true,
            	'fields'	=> array(
								array(
									'id' => 'recipes_images_clickable',
   									'type' => 'switch',
   									'title' => __('Clickable Images', $this->pluginName),
   									'subtitle' => __( 'Best used in combination with a lightbox plugin.', $this->pluginName ),
   									'default' => false,
   								),
   								array(
   									'id' => 'recipe_instruction_image',
   									'type' => 'switch',
   									'title' => __('Instruction Images', $this->pluginName),
   									'subtitle' => __( 'Allow to attach images to instruction steps.', $this->pluginName ),
   									'default' => true,
								),
								array(
									'id' => 'recipe_instruction_image_position',
   									'type' => 'select',
   									'title' => __( 'Position of instruction images', $this->pluginName ),
   									'subtitle' => __( 'Decide wether your instruction images should be display next to the instructions or below.', $this->pluginName ),
   									'options' => array(
   													'rpr_instrimage_right' => __('Right of instruction', $this->pluginName ),
   													'rpr_instrimage_below' =>  __('Below the instruction', $this->pluginName ),
   												),
   									'default' => 'rpr_instrimage_right',
   									'required' => array('recipe_instruction_image','!=','0')
   								),
							)
            	);
				
			$this->sections[] = array(
            	'icon' 		=> 'fa-cog',
            	'title'		=> __( 'Widgets', $this->pluginName ),
            	'subsection' => true,
            	'fields'	=> array(
            					array(
									'id' => 'widget_info',
									'type' => 'info',
									'icon' => 'fa-lightbulb-o',
									'class' => 'info',
									'title' => __('Widgets', $this->pluginName), 
									'subtitle' => __('Widgets can be really useful to display information in sidebars or other places.<br/> However, if you have a lot of widgets its hard to keep the overview. Here you can switch of widgets shipped with RecipePress reloaded if you don\'t need them.', $this->pluginName )
								),
								array(
									'id' => 'use_taxcloud_widget',
   									'type' => 'switch',
   									'title' => __('Taxonomy Cloud', $this->pluginName),
   									'subtitle' => __( 'Allows you to create tag clouds not only from tags but from every taxonomy.<br/><b>Good to know:</b> You can use this widget for any type of taxonomy, not only recipe related. <br/>If used the standard tag cloud will be hidden.', $this->pluginName ),
   									'default' => true,
   								),
   							/*	array(
									'id' => 'use_taxlist_widget',
   									'type' => 'switch',
   									'title' => __('Taxonomy List', $this->pluginName),
   									'subtitle' => __( 'Allows you to create tag lists not only from tags but from every taxonomy.<br/><b>Good to know:</b> You can use this widget for any type of taxonomy, not only recipe related.', $this->pluginName ),
   									'default' => true,
   								),*/
							)
            	);
				
			$this->sections[] = array(
				'icon'		=> 'fa-sliders',
				'title' 	=> __('Advanced theming options', $this->pluginName),
				'subsection' => true,
    			'fields' 	=> array(
    							array(
    						 		'type' => 'info',
    								'id' => 'recipe_note_item',
    								'icon' => 'fa-lightbulb-o',
    								'class' => 'warning',
    								'title' => __( 'Please note:', $this->pluginName ) ,
    								'subtitle' => __( 'In this section you can make some adjustments on the look and feel of your recipes. Normally this should not be necessary and your theme should take care of all this. Some themes however might have limited capabilities and you might want RecipePress reloaded to display some information in it\'s part of the theme. Then however you might also consider to adjust your theme, i.e. by creating a child theme.' , $this->pluginName ) ,
    								),
    							array(
									'type' => 'switch',
									'id' => 'rpr_use_advanced_theming',
									'title' => __( 'Use advanced theming settings', $this->pluginName ),
									'subtitle' => __( 'Activate additional settings', $this->pluginName ),
									'default' => false
									),
    							array(
    								'type' => 'switch',
    								'id' => 'recipe_display_image',
    								'title' => __('Display recipe image', $this->pluginName),
    								'subtitle' => __('Usually this is the job of your theme. Only use, if your theme does not support post images', $this->pluginName),
    								'default' => false,
    								'required' => array('rpr_use_advanced_theming', 'equals', true)
    								),
    							array(
    								'type' => 'switch',
    								'id' => 'recipe_author_display_in_recipe',
    								'title' => __('Display the author in recipe', $this->pluginName),
    								'subtitle ' => __('Display the author in the recipe part of the theme. Ususally your theme will display the author.', $this->pluginName ),
    								'default' => false,
    								'required' => array('rpr_use_advanced_theming', 'equals', true)
    								),
    							array(
    								'type' => 'switch',
    								'id' => 'recipe_time_display_in_recipe',
    								'title' => __('Display date in recipe', $this->pluginName),
    								'subtitle' => __('Display date in the recipe part of the theme. Ususally your theme will display the date.', $this->pluginName ),
    								'default' => false,
    								'required' => array('rpr_use_advanced_theming', 'equals', true)
    								),
    							array(
    								'type' => 'switch',
    								'id' => 'recipe_display_categories_in_recipe',
    								'title' => __('Display Categories in recipe', $this->pluginName ),
    								'subtitle' => __('Display WP Categories in the recipe part of the theme instead the default one.', $this->pluginName),
    								'default' => false,
    								'required' => array('rpr_use_advanced_theming', 'equals', true)
    								),
    							array(
    								'type' => 'switch',
    								'id' => 'recipe_display_tags_in_recipe',
    								'title' => __('Display tags in recipe', $this->pluginName ),
    								'subtitle' => __('Display WP Tags in the recipe part of the theme instead the default one.', $this->pluginName),
    								'default' => false,
    								'required' => array('rpr_use_advanced_theming', 'equals', true)
    								),
    					),
    		);
    						
    		$this->sections[] = array(
				'icon'		=> 'fa-tags',
				'title' 	=> __( 'Recipe Metadata' , $this->pluginName ),
				'fields' 	=> array(
            					array(
            						'type' => 'select',
            						'id' => 'recipe_ingredient_links',
            						'title' => __('Ingredient Links', $this->pluginName),
            						'subtitle' => __( 'Links to be used in the ingredient list.', $this->pluginName ),
            						'options' => array(
            										'disabled'	=> __('No ingredient links', $this->pluginName),
            										'archive' 	=> __('Only link to ingredient archive page', $this->pluginName),
            										'archive_custom' => __('Custom link if provided, otherwise archive page', $this->pluginName),
            										'custom' => __('Custom links if provided, otherwise no link', $this->pluginName),
            										),
            						'default' => 'archive_custom',
            					),
            					array(
									'type' => 'select',
									'id' => 'ingredients_exclude_list',
									'title' => __( 'Exclude ingredients from listings', $this->pluginName ),
									'subtitle' => __( 'Choose ingredients you don\'t want to appear on ingredeint listings like the ingredient index or the ingredient cloud', $this->pluginName ),
									'description' => __( 'Select multiple ingredients like "salt" or "pepper" you don\'t want to see on the listings', $this->pluginName ),
									'data' => 'terms',
									'args' => array( 'taxonomies' => 'rpr_ingredient', 'args' => array() ),
									'multi' => true,
								),
								array(
                        			'id'    => 'ingredients-divide',
                        			'type'  => 'divide',
                    			),
								array(
            						'type' => 'switch',
            						'id' => 'recipe_use_nutritional_info',
            						'title' => __('Use nutritional information', $this->pluginName),
            						'subtitle' => __( 'Add nutritional information to your recipes.', $this->pluginName ),
            						'default' => '0',
            					),
            					array(
                        			'id'    => 'taxonomies-divide',
                        			'type'  => 'divide',
                    			),
                    			array(
									'id' => 'manage_custom_taxonomies_box',
									'type' => 'raw',
									'title' => __( 'Manage custom taxonomies', $this->pluginName ),
									'content' => rpr_admin_manage_tags(),
								),
            					array(
                            		'type' => 'switch',
                            		'id' => 'recipe_tags_use_wp_categories',
                            		'title' => __('Use Categories', $this->pluginName),
                            		'subtitle' => __( 'Use the default WP Categories to organize your recipes. If set to "Off", RPR will create own categories.', $this->pluginName ),
                            		'default' => true,
                        		),
                    			array(
                    				'type' => 'switch',
                    				'id' => 'recipe_tags_use_wp_tags',
                    				'title' => __('Use Tags', $this->pluginName),
                    				'subtitle' => __( 'Use the default WP Tags to organize your recipes. If set to "Off", RPR will create own tags.', $this->pluginName ),
                    				'default' => true,
                    			),
            			)
			);		
				
			//var_dump($rpr_option['recipe_instruction_image']);
                    
            $this->sections[] = array(
                'type' => 'divide',
            );

            $this->sections[] = array(
                'icon'      => 'fa-exchange',
                'title'     => __('Changelog', $this->pluginName ),
                'fields'    => array(
                    array(
                        'id'        => 'changelog',
                        'type'      => 'raw',
                    	'content'   => rpr_admin_latest_news_changelog(),
                    	'content'   => replace_readme_parser_tag(trailingslashit(dirname(__FILE__)) . '../readme.txt'),
                    )
                ),
            );
			$this->sections[] = array(
				'icon' => 'fa-question',
				'title' => __( 'Documentation', $this->pluginName),
				'heading' => __( 'Documentation & Support', $this->pluginName),
				'fields'	=> array(
					array(
						'id' => 'documentation',
						'type' => 'info',
						'style' => 'info',
						'title' => __( 'Need more help?', $this->pluginName ),
						'subtitle' => __('Have a look at the <a href="http://rp-reloaded.net/documentation" target="_blank">Documentation</a> (currently being built up) or ask your questions at the <a href="http://wordpress.org/support/plugin/recipepress-reloaded" target="_blank">support forum</a>.', $this->pluginName),
					)
				)
			);
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => __('Theme Information 1', 'redux-framework-demo'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-2',
                'title'     => __('Theme Information 2', 'redux-framework-demo'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'redux-framework-demo')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'redux-framework-demo');
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            $theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'rpr_option',            // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => $this->pluginTitle,     // Name that appears at the top of your panel
                'display_version'   => $this->pluginVersion,  // Version that appears at the top of your panel
                'menu_type'         => 'submenu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => true,                    // Show the sections below the admin menu item or not
                'menu_title'        => __('Settings', $this->pluginName ),
                'page_title'        => __('Settings', $this->pluginName ),
                
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                //'google_api_key' => '', // Must be defined to add google fonts to the typography module
                
                'async_typography'  => true,                    // Use a asynchronous font on the front end or font string
                'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar'         => true,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => false,                    // Show the time the page took to load, etc
                'update_notice'     => true,                    // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                'customizer'        => false,                    // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => 'edit.php?post_type=rpr_recipe',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => '',                      // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
                'page_slug'         => '_options',              // Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => false,                   // Shows the Import/Export panel when not used as a field.
                
                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
                
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'https://wordpress.org/plugins/recipepress-reloaded/',
                'title' => 'Wordpress repository page',
                'icon'  => 'fa-wordpress'
                //'img'   => '', // You can use icon OR img. IMG needs to be a full URL.
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://www.rp-reloaded.net',
                'title' => 'Visit the plugins home page for documentation and demo',
                'icon'  => 'fa-home'
            );
            

            // Panel Intro text -> before the form
            //$this->args['intro_text'] = '';

            // Add content after the form.
            //$this->args['footer_text'] = __('<p>This settings panel was created using <a href="http://reduxframework.com/">ReduxFramework</a>.</p>', $this->pluginName );
        }

    }
    
    global $reduxConfig;
    $reduxConfig = new RPR_Settings();
}

/**
  Custom function for the callback referenced above
 */
if (!function_exists('redux_my_custom_field')):
    function redux_my_custom_field($field, $value) {
        print_r($field);
        echo '<br/>';
        print_r($value);
    }
endif;

/**
  Custom function for the callback validation referenced above
 * */
if (!function_exists('redux_validate_callback_function')):
    function redux_validate_callback_function($field, $value, $existing_value) {
        $error = false;
        $value = 'just testing';

        /*
          do your validation

          if(something) {
            $value = $value;
          } elseif(something else) {
            $error = true;
            $value = $existing_value;
            $field['msg'] = 'your custom error message';
          }
         */

        $return['value'] = $value;
        if ($error == true) {
            $return['error'] = $field;
        }
        return $return;
    }
endif;

if( ! function_exists( 'replace_readme_parser_tag' ) ){
// Idea Taken from here: http://www.tomsdimension.de/wp-plugins/readme-parser, an abandoned wordpress plugin
 // and here:  http://www.webmaster-source.com/2009/07/08/parse-a-wordpress-plugins-readme-txt-with-regular-expressions/
/**
 * creates readme code
 *
 * @param string $url URL of textfile readme.txt
 * @return string readme code
 */
	function replace_readme_parser_tag( $url )
	{
		// no/wrong url
		if ( empty($url) or basename($url) != 'readme.txt')
			return false;
	
		// read file
		$file = @file_get_contents( $url );
		if (empty($file))
			return __( '<b>Readme Parser: readme.txt ot found!</b>' , 'recipepress-reloaded' );
	
		// Find the changelog part:
		$start = strpos ( $file, '== Changelog ==' );
		$end = strpos ( $file, '== Frequently Asked Questions ==' );
		$file = substr( $file, $start, ( $end-$start ) );
		
		// line end to \n
		$file = preg_replace("/(\n\r|\r\n|\r|\n)/", "\n", $file);
	
		// place version
		$file = readme_parser_get_version( $file );
	
		// set screenshot links
		//$file = readme_parser_get_screenshots( $url, $file );
	
		// urls
		$file = str_replace('http://www.', 'www.', $file);
		$file = str_replace('www.', 'http://www.', $file);
		$file = preg_replace("/\[(.*?)\]\s?\((.*?)\)/i", '<a href="$2">$1</a>', $file);
		$file = preg_replace('#(^|[^\"=]{1})(http://|ftp://|mailto:|https://)([^\s<>]+)([\s\n<>]|$)#', '$1<a href="$2$3">$3</a>$4', $file);
		
		
		// headlines
		$s = array('===','==','=' );
		$r = array('h2' ,'h3','h4');
		for ( $x = 0; $x < sizeof($s); $x++ )
			$file = preg_replace('/(.*?)'.$s[$x].'(?!\")(.*?)'.$s[$x].'(.*?)/', '$1<'.$r[$x].'>$2</'.$r[$x].'>$3', $file);
	
		// inline
		$s = array('\*\*','\''  );
		$r = array('b'   ,'code');
		for ( $x = 0; $x < sizeof($s); $x++ )
			$file = preg_replace('/(.*?)'.$s[$x].'(?!\s)(.*?)(?!\s)'.$s[$x].'(.*?)/', '$1<'.$r[$x].'>$2</'.$r[$x].'>$3', $file);
	
		// ' _italic_ '
		$file = preg_replace('/(\s)_(\S.*?\S)_(\s|$)/', ' <em>$2</em> ', $file);
	
		// ul lists
		$s = array('\*','\+','\-');
		for ( $x = 0; $x < sizeof($s); $x++ )
		$file = preg_replace('/^['.$s[$x].'](\s)(.*?)(\n|$)/m', '<li>$2</li>', $file);
		$file = preg_replace('/\n<li>(.*?)/', '<ul><li>$1', $file);
		$file = preg_replace('/(<\/li>)(?!<li>)/', '$1</ul>', $file);
	
		// ol lists
		$file = preg_replace('/(\d{1,2}\.)\s(.*?)(\n|$)/', '<li>$2</li>', $file);
		$file = preg_replace('/\n<li>(.*?)/', '<ol><li>$1', $file);
		$file = preg_replace('/(<\/li>)(?!(\<li\>|\<\/ul\>))/', '$1</ol>', $file);
	
		// ol screenshots style
		$file = preg_replace('/(?=Screenshots)(.*?)<ol>/', '$1<ol class="readme-parser-screenshots">', $file);
	
		// line breaks
		$file = preg_replace('/(.*?)(\n)/', "$1<br/>\n", $file);
		$file = preg_replace('/(1|2|3|4)(><br\/>)/', '$1>', $file);
		$file = str_replace('</ul><br/>', '</ul>', $file);
		$file = str_replace('<br/><br/>', '<br/>', $file);
	
	
		// divs
		//$file = preg_replace('/(<h3> Description <\/h3>)/', "$1\n<div id=\"readme-description\" class=\"readme-div\">\n", $file);
		//$file = preg_replace('/(<h3> Installation <\/h3>)/', "</div>\n$1\n<div id=\"readme-installation\" class=\"readme-div\">\n", $file);
		//$file = preg_replace('/(<h3> Frequently Asked Questions <\/h3>)/', "</div>\n$1\n<div id=\"readme-faq\" class=\"readme-div\">\n", $file);
		//$file = preg_replace('/(<h3> Screenshots <\/h3>)/', "</div>\n$1\n<div id=\"readme-screenshots\" class=\"readme-div\">\n", $file);
		//$file = preg_replace('/(<h3> Arbitrary section <\/h3>)/', "</div>\n$1\n<div id=\"readme-arbitrary\" class=\"readme-div\">\n", $file);
		$file = preg_replace('/(<h3> Changelog <\/h3>)/', "", $file);
		$file = $file.'</div>';
	
		// promotion ;)
		//$promo = '<div style="text-align:right;"><small>created by <a href="http://www.tomsdimension.de/wp-plugins/readme-parser">Readme Parser</a></small></div>';
	
		return  '<div class="readme-parser">'.$file.'</div>';
	}
}

if( !function_exists('readme_parser_get_version')){
	/**
	 * inserts version in after plugin name
	 *
	 * @param string $file file
	 * @return string file
	 */
	function readme_parser_get_version( $file )
	{
		$start = strpos( $file, 'Stable tag:' ) + 12;
		$end = strpos( $file, "\n", $start );
		$version = substr( $file, $start, $end - $start );
		$file = str_replace( ' ===', ' '.$version.' ===', $file );
		return $file;
	}
}