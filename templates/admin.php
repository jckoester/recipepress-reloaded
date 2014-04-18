<?php

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
            		/*array(
            				'type' => 'notebox',
            				'name' => 'recipe_todo_item',
            				'label' => 'TODO:',
            				'description' => __('Release notes should be available here', $this->pluginName) ,
            				'status' => 'warning',
            		),*/
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
                    'title' => __('Recipe', $this->pluginName),
                    'name' => 'section_recipe',
                    'fields' => array(
			array(
				'type' => 'toggle',
				'name' => 'recipe_display_image',
				'label' => __('Display recipe image', $this->pluginName),
				'description' => __('Usually this is the job of your theme. Only use, if your theme does not support post images', $this->pluginName),
				'default' => 0,
			),
				
                    	/*array(
                    			'type' => 'notebox',
                    			'name' => 'recipe_todo_item',
                    			'label' => 'TODO:',
                    			'description' => __('These features are not tested. Remove them or make thew work before release.', $this->pluginName) ,
                    			'status' => 'warning',
                    		),*/
                        /*
                        array(
                            'type' => 'toggle',
                            'name' => 'recipe_adjustable_servings',
                            'label' => __('Adjustable Servings', $this->pluginName),
                            'description' => __( 'Allow users to dynamically adjust the servings of recipes.', $this->pluginName ),
                            'default' => '1',
                        ),*/
                        array(
                            'type' => 'toggle',
                            'name' => 'recipe_images_clickable',
                            'label' => __('Clickable Images', $this->pluginName),
                            'description' => __( 'Best used in combination with a lightbox plugin.', $this->pluginName ),
                            'default' => '',
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
                        /*array(
                            'type' => 'toggle',
                            'name' => 'recipe_linkback',
                            'label' => __('Link to plugin', $this->pluginName),
                            'description' => __( 'Show a link to the plugin website as a little thank you.', $this->pluginName ),
                            'default' => '1',
                        ),*/
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
                        /*array(
                            'type' => 'select',
                            'name' => 'recipe_ingredient_custom_links_target',
                            'label' => __('Custom Links', $this->pluginName),
                            'description' => __( 'Custom links can be added on the ', $this->pluginName ) . '<a href="'.admin_url('edit-tags.php?taxonomy=ingredient&post_type=recipe').'" target="_blank">' . __( 'ingredients page', $this->pluginName ) . '</a>.',
                            'items' => array(
                                array(
                                    'value' => '_self',
                                    'label' => __('Open in the current tab/window', $this->pluginName),
                                ),
                                array(
                                    'value' => '_blank',
                                    'label' => __('Open in a new tab/window', $this->pluginName),
                                ),
                            ),
                            'default' => array(
                                '_blank',
                            ),
                            'dependency' => array(
                                'field' => '',
                                'function' => 'rpr_admin_premium_installed',
                            ),
                            'validation' => 'required',
                        ),*/
                    ),
                ),
            	array(
            			'type' => 'section',
            			'title' => __('Instructions', $this->pluginName),
            			'name' => 'section_instructions',
            			'fields' => array(
            					array(
            							'type' => 'toggle',
            							'name' => 'recipe_instruction_image',
            							'label' => __('Instruction Images', $this->pluginName),
            							'description' => __( 'Allow to attach images to instruction steps.', $this->pluginName ),
            							'default' => '1',
            					),
                            )
            	),
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
                    				'name' => 'recipe_todo_item',
                    				'label' => 'TODO:',
                    				'description' => __('This link is wrong. Fix before release.', $this->pluginName) ,
                    				'status' => 'warning',
                    		),
                        array(
                            'type' => 'notebox',
                            'name' => 'recipe_slug_notebox',
                            'label' => __('404 error/page not found?', $this->pluginName),
                            'description' => __('Try', $this->pluginName) . ' <a href="http://rp-reloaded.net/en/solving-errors/error-404-page-not-found" target="_blank">'.__('flushing your permalinks', $this->pluginName).'</a>.',
                            'status' => 'info',
                        ),
                    ),
                ),
            ),
        ),


//=-=-=-=-=-=-= RECIPE TAGS =-=-=-=-=-=-=
        array(
            'title' => __('Recipe Tags', $this->pluginName),
            'name' => 'recipe_tags',
            'icon' => 'font-awesome:fa-tags',
            'controls' => array(
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
//=-=-=-=-=-=-= FAQ & SUPPORT =-=-=-=-=-=-=
        array(
            'title' => __('FAQ & Support', $this->pluginName),
            'name' => 'faq_support',
            'icon' => 'font-awesome:fa-book',
            'controls' => array(
            		array(
            				'type' => 'notebox',
            				'name' => 'recipe_todo_item',
            				'label' => 'TODO:',
            				'description' => __('Fix this link.', $this->pluginName) ,
            				'status' => 'warning',
            		),
                array(
                    'type' => 'notebox',
                    'name' => 'faq_support_notebox',
                    'label' => __('Need more help?', $this->pluginName),
                    'description' => '<a href="http://rp-reloaded.net/support" target="_blank">Recipe Press Reloaded ' .__('FAQ & Support', $this->pluginName) . '</a>',
                    'status' => 'info',
                ),
            ),
        ),
    ),
);
