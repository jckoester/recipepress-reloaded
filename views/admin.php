<?php

$templates = rpr_admin_template_list();


$admin_menu = array(
    'title' => 'Recipe Press Reloaded ' . __('Settings', $this->pluginName),
    'logo'  => $this->pluginUrl . '/img/logo.png',
    'menus' => array(
//=-=-=-=-=-=-= LATEST NEWS =-=-=-=-=-=-=
        array(
            'title' => __('Latest News', $this->pluginName),
            'name' => 'latest_news',
            'icon' => 'font-awesome:fa-comments-o',
            'controls' => array(
                array(
                    'type' => 'section',
                    'title' => __('Changelog', $this->pluginName),
                    'name' => 'section_changelog',
                    'fields' => array(
                        array(
                            'type' => 'html',
                            'name' => 'latest_news_changelog_' . get_option($this->pluginName . '_version'),
                            'binding' => array(
                                'field'    => '',
                                'function' => 'rpr_admin_latest_news_changelog',
                            ),
                        ),
                    ),
                ),
            ),
        ),

 
//=-=-=-=-=-=-= GENERAL SETTINGS =-=-=-=-=-=-=
        array(
            'title' => __('General Settings', $this->pluginName),
            'name' => 'general_settings',
            'icon' => 'font-awesome:fa-wrench',
            'controls' => array(
                array(
                    'type' => 'section',
                    'title' => __('Recipe Archive Pages', $this->pluginName),
                    'name' => 'section_recipe_archive_pages',
                    'fields' => array(
                        array(
                            'type' => 'select',
                            'name' => 'recipe_archive_display',
                            'label' => __('Display', $this->pluginName),
                            'items' => array(
                                array(
                                    'value' => 'excerpt',
                                    'label' => __('Only the excerpt', $this->pluginName),
                                ),
                                array(
                                    'value' => 'full',
                                    'label' => __('The entire recipe', $this->pluginName),
                                ),
                            ),
                            'default' => array(
                                'excerpt',
                            ),
                            'validation' => 'required',
                        ),
                        /*array(
                            'type' => 'select',
                            'name' => 'recipe_theme_thumbnail',
                            'label' => __('Display Thumbnail', $this->pluginName),
                            'description' => __( 'Thumbnail position depends on the theme you use', $this->pluginName ) . '.',
                            'items' => array(
                                array(
                                    'value' => 'never',
                                    'label' => __('Never', $this->pluginName),
                                ),
                                array(
                                    'value' => 'archive',
                                    'label' => __('Only on archive pages', $this->pluginName),
                                ),
                                array(
                                    'value' => 'recipe',
                                    'label' => __('Only on recipe pages', $this->pluginName),
                                ),
                                array(
                                    'value' => 'always',
                                    'label' => __('Always', $this->pluginName),
                                ),
                            ),
                            'default' => array(
                                'always',
                            ),
                            'validation' => 'required',
                        ),*/
                        array(
                            'type' => 'textbox',
                            'name' => 'recipe_slug',
                            'label' => __('Slug', $this->pluginName),
                            'default' => 'recipe',
                            'validation' => 'required',
                        ),
                        array(
                            'type' => 'html',
                            'name' => 'recipe_slug_preview',
                            'binding' => array(
                                'field'    => 'recipe_slug',
                                'function' => 'rpr_admin_recipe_slug_preview',
                            ),
                        ),
                        array(
                            'type' => 'notebox',
                            'name' => 'recipe_slug_notebox',
                            'label' => __('404 error/page not found?', $this->pluginName),
                            'description' => __('Try', $this->pluginName) . ' <a href="http://rp-reloaded.net/documentation/404-error-page-found/" target="_blank">'.__('flushing your permalinks', $this->pluginName).'</a>.',
                            'status' => 'info',
                        ),
                    	
                    ),
                	/*array(
                		 'type' => 'section',
                				'title' => __('Recipe', $this->pluginName),
                				'name' => 'section_recipe',
                				'fields' => array(
                							
                						*/
                		/*array(
                		 'type' => 'toggle',
                				'name' => 'recipe_linkback',
                				'label' => __('Link to plugin', $this->pluginName),
                				'description' => __( 'Show a link to the plugin website as a little thank you.', $this->pluginName ),
                				'default' => '1',
                		),*/
                		/*     ),
                		 ),*/
                ),
            ),
        ),

// =============== DISPLAY & TEMPLATES =============
   		array(
   				'title' => __('Appearance', $this->pluginName),
   				'name' => 'display',
   				'icon' => 'font-awesome:fa-file-photo-o',
   				'controls' => array(
   						array(
   						 'type' => 'section',
   								'title' => __('Template', $this->pluginName),
   								'name' => 'section_template',
   								'fields' => array(
   										array(
   												'type' => 'radioimage',
   												'name' => 'rpr_template',
   												'label' => __( 'Choose a template', $this->pluginName ),
   												'description' => sprintf (__( 'Templates define how your recipes will look like. Choose one of the installed templates.', $this->pluginName), ''),// or <a href="%s">create one yourself</a>.', $this->pluginName ) , 'http://rp-reloaded.net/templates/create' ),
   												'item_max_height' => '300',
   												'item_max_width' => '300',
									    		'items' => $templates,
   												),
   								),
   							),
   						array(
   							'type' => 'section',
   							'title' => __( 'Images', $this->pluginName ),
   							'name' => 'section_images',
   							'fields' => array(
   									array(
   											'type' => 'toggle',
   											'name' => 'recipe_images_clickable',
   											'label' => __('Clickable Images', $this->pluginName),
   											'description' => __( 'Best used in combination with a lightbox plugin.', $this->pluginName ),
   											'default' => '',
   									),
   									array(
   											'type' => 'toggle',
   											'name' => 'recipe_instruction_image',
   											'label' => __('Instruction Images', $this->pluginName),
   											'description' => __( 'Allow to attach images to instruction steps.', $this->pluginName ),
   											'default' => '1',
   									),
   									array(
   											'type' => 'select',
   											'name' => 'recipe_instruction_image_position',
   											'label' => __( 'Position of instruction images', $this->pluginName ),
   											'description' => __( 'Decide wether your instruction images should be display next to the instructions or below.', $this->pluginName ),
   											'items' => array(
   												array(
   													'value' => 'rpr_instrimage_right',
   													'label' => __('Right of instruction', $this->pluginName ),
   												),
   												array(
   													'value' => 'rpr_instrimage_below',
   													'label' => __('Below the instruction', $this->pluginName ),
   												),
   											),
   											'default' => array(
   												'rpr_instrimage_right',
   											),
   									),
   									

   								),		
   						),
   						array(
   							'type' => 'section',
				    		'title' => __('Print Link', $this->pluginName),
   							'name' => 'section_printlink',
   							'fields' => array(
   							array(
   								'type' => 'toggle',
   								'name' => 'recipe_display_printlink',
   								'label' => __('Display print link', $this->pluginName),
   								'description' => __( 'Adds a print link to your recipes. It\'s recommended to use one of the numerous print plugins for wordpress to include a print link to ALL of your posts.', $this->pluginName ),
   								'default' => '',
   							),
   						array(
   							'type' => 'textbox',
   							'name' => 'recipe_printlink_class',
   							'label' => __('Class of the print area', $this->pluginName),
   							'description' => __( 'Print links should only print an area of the page, usually a post. This is higly depending on wordpress theme you are using. Add here the class (prefixed by \'.\') or the id (prefixed by \'#\') of the printable area.', $this->pluginName ),
   							'default' => '.rpr_recipe',
   						),
		    		),
    			),
    		),
   		),
    		
// ============= Advanced Theming Settings =========================== //
    		array(
    				'title' => __('Advanced theming options', $this->pluginName),
    				'name' => 'display_advanced',
    				'icon' => 'font-awesome:fa-file-photo-o',
    				'controls' => array(
    						array(
    						 'type' => 'notebox',
    								'name' => 'recipe_note_item',
    								'label' => __( 'Please note:', $this->pluginName ) ,
    								'description' => __( 'In this section you can make some adjustments on the look and feel of your recipes. Normally this should not be necessary and your theme should take care of all this. Some themes however might have limited capabilities and you might want RecipePress reloaded to display some information in it\'s part of the theme. Then however you might also consider to adjust your theme, i.e. by creating a child theme.' , $this->pluginName ) ,
    								'status' => 'note',
    						),
    						array(
    								'type' => 'section',
    								'title' => __('Display options', $this->pluginName),
    								'name' => 'section_printlink',
    								'fields' => array(
    										array(
    												'type' => 'toggle',
    												'name' => 'recipe_display_image',
    												'label' => __('Display recipe image', $this->pluginName),
    												'description' => __('Usually this is the job of your theme. Only use, if your theme does not support post images', $this->pluginName),
    												'default' => 0,
    										),
    										array(
    												'type' => 'toggle',
    												'name' => 'recipe_author_display_in_recipe',
    												'label' => __('Display the author in recipe', $this->pluginName),
    												'description' => __('Display the author in the recipe part of the theme. Ususally your theme will display the author.', $this->pluginName ),
    												'default' => '',
    										),
    										array(
    												'type' => 'toggle',
    												'name' => 'recipe_time_display_in_recipe',
    												'label' => __('Display date in recipe', $this->pluginName),
    												'description' => __('Display date in the recipe part of the theme. Ususally your theme will display the date.', $this->pluginName ),
    												'default' => '',
    										),
    										array(
    												'type' => 'toggle',
    												'name' => 'recipe_display_categories_in_recipe',
    												'label' => __('Display Categories in recipe', $this->pluginName ),
    												'description' => __('Display WP Categories in the recipe part of the theme instead the default one.', $this->pluginName),
    												'default' => '0'
    										),
    										array(
    												'type' => 'toggle',
    												'name' => 'recipe_display_tags_in_recipe',
    												'label' => __('Display tags in recipe', $this->pluginName ),
    												'description' => __('Display WP Tags in the recipe part of the theme instead the default one.', $this->pluginName),
    												'default' => '0'
    										),
    								),
    						),
    						array(
    								'type' => 'section',
    								'title' => __('Icons' , $this->pluginName ),
    								'name' => 'section_icons',
    								'fields' => array(
    										array(
    												'type' => 'toggle',
    												'name' => 'recipe_icons_display',
    												'label' => __( 'Use icons' , $this->pluginName ),
    												'description' => __( 'Display icons in front of headlines.' , $this->pluginName ),
    												'default' => '0',
    										),
    						),

    				),
    		),
    		),
//=============== Recipe Meta =============== //
        array(
            'title' => __('Recipe Metadata', $this->pluginName),
            'name' => 'recipe_meta',
            'icon' => 'font-awesome:fa-tags',
            'controls' => array(
            	array(
            		'type' => 'section',
            		'title' => __('Nutritional Information', $this->pluginName),
            		'name' => 'section_nutrition',
            		'fields' => array(
            			array(
            				'type' => 'toggle',
            				'name' => 'recipe_use_nutritional_info',
            				'label' => __('Use nutritional information', $this->pluginName),
            				'description' => __( 'Add nutritional information to your recipes.', $this->pluginName ),
            				'default' => '0',
            				),
            			),
            		),
            		array(
            				'type' => 'section',
            				'title' => __('Ingredients', $this->pluginName),
            				'name' => 'section_ingredients',
            				'fields' => array(
            						array(
            								'type' => 'select',
            								'name' => 'recipe_ingredient_links',
            								'label' => __('Ingredient Links', $this->pluginName),
            								'description' => __( 'Links to be used in the ingredient list.', $this->pluginName ),
            								'items' => array(
            										array(
            												'value' => 'disabled',
            												'label' => __('No ingredient links', $this->pluginName),
            										),
            										array(
            												'value' => 'archive',
            												'label' => __('Only link to ingredient archive page', $this->pluginName),
            										),
            										array(
            												'value' => 'archive_custom',
            												'label' => __('Custom link if provided, otherwise archive page', $this->pluginName),
            										),
            										array(
            												'value' => 'custom',
            												'label' => __('Custom links if provided, otherwise no link', $this->pluginName),
            										),
            								),
            								'default' => array(
            										'archive_custom',
            								),
            								'validation' => 'required',
            						),
            						/*
            						 array(
            						 		'type' => 'toggle',
            						 		'name' => 'recipe_adjustable_servings',
            						 		'label' => __('Adjustable Servings', $this->pluginName),
            						 		'description' => __( 'Allow users to dynamically adjust the servings of recipes.', $this->pluginName ),
            						 		'default' => '1',
            						 ),*/
            						
            		
            				),
            		),
                array(
                    'type' => 'section',
                    'title' => __('Custom Recipe Tags', $this->pluginName),
                    'name' => 'section_recipe_tags_custom',
                    'fields' => array(
                        array(
                            'type' => 'html',
                            'name' => 'recipe_tags_manage_custom',
                            'binding' => array(
                                'field'    => '',
                                'function' => 'rpr_admin_manage_tags',
                            ),
                        ),
                    ),
                ),
                array(
                    'type' => 'section',
                    'title' => __('WordPress Categories & Tags', $this->pluginName),
                    'name' => 'section_recipe_tags_wordpress',
                    'fields' => array(
                        array(
                            'type' => 'toggle',
                            'name' => 'recipe_tags_use_wp_categories',
                            'label' => __('Use Categories', $this->pluginName),
                            'description' => __( 'Use the default WP Categories to organize your recipes. If set to "Off", RPR will create own categories.', $this->pluginName ),
                            'default' => '1',
                        ),
                    	array(
                    		'type' => 'toggle',
                    		'name' => 'recipe_tags_use_wp_tags',
                    		'label' => __('Use Tags', $this->pluginName),
                    		'description' => __( 'Use the default WP Tags to organize your recipes. If set to "Off", RPR will create own tags.', $this->pluginName ),
                    		'default' => '1',
                    	),
                    	
                    ),
                ),
            ),
        ),
//=-=-=-=-=-=-= Documentation & SUPPORT =-=-=-=-=-=-=
        array(
            'title' => __('Documentation & Support', $this->pluginName),
            'name' => 'doc_support',
            'icon' => 'font-awesome:fa-book',
            'controls' => array(
            	array(
            		'type' => 'notebox',
            		'name' => 'doc_support_notebox_sup',
            		'label' => __('Need more help?', $this->pluginName),
            		'description' => __('Have a look at the <a href="http://rp-reloaded.net/documentation" target="_blank">Documentation</a> (currently being built up) or ask your questions at the <a href="http://wordpress.org/support/plugin/recipepress-reloaded" target="_blank">support forum</a>.', $this->pluginName),
            		'status' => 'info',
            	),
            ),
        ),
    ),
);
