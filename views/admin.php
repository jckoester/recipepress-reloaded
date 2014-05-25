<?php

$templates = rpr_admin_template_list();
/*
if ($handle = opendir($this->pluginDir.'/themes')) {
	while (false !== ($file = readdir($handle))) {
		if( $file !='.' && $file !='..') {
			//echo "$file\n";
		}
	}
}

$themes_array=array();
*/
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
// =============== DISPLAY & TEMPLATES =============
    	array(
    			'title' => __('Display', $this->pluginName),
    			'name' => 'display',
    			'icon' => 'font-awesome:fa-file-photo-o',
    			'controls' => array(
    					array(
							'type' => 'notebox',
    						'name' => 'recipe_todo_item',
    						'label' => 'TODO:',
    						'description' => __('Very very beta!', $this->pluginName) ,
    						'status' => 'warning',
    						),
    					array(
    						'type' => 'section',
    						'title' => __('Template', $this->pluginName),
    						'name' => 'section_template',
    						'fields' => array(
    							array(
    								'type' => 'radioimage',
    								'name' => 'rpr_template',
    								'label' => __( 'Choose a template', $this->pluginName ),
    								'description' => sprintf (__( 'Templates define how your recipes will look like. Choose one of the installed templates or <a href="%s">create one yourself</a>.', $this->pluginName ) , 'http://rp-reloaded.net/templates/create' ),
    								'item_max_height' => '300',
    								'item_max_width' => '300',
    								'items' => $templates,
    								),
    						),
    					),
    			),
    	),
 
//=============== TEST=======================
/*    		array(
    				'title' => __('Image', 'vp_textdomain'),
    				'name' => 'submenu_2',
    				'icon' => 'font-awesome:fa-picture-o',
    				'controls' => array(
    						array(
    								'type' => 'section',
    								'title' => __('Check Images', 'vp_textdomain'),
    								'fields' => array(
    										array(
    												'type' => 'textbox',
    												'name' => 'tb_3',
    												'label' => __('Numeric', 'vp_textdomain'),
    												'description' => __('Only numbers allowed here.', 'vp_textdomain'),
    												'default' => '123',
    												'validation' => 'numeric'
    								),
    										array(
    												'type' => 'slider',
    												'name' => 'sl_1',
    												'label' => __('Decimal Slider', 'vp_textdomain'),
    												'description' => __('This slider has minimum value of -10, maximum value of 17.5, sliding step of 0.1 and default value 15.9, everything can be customized.', 'vp_textdomain'),
    												'min' => '0',
    												'max' => '800',
    												'step' => '1',
    												'default' => '300',
    										),
    								),
    						),
    						array(
    								'type' => 'section',
    								'title' => __('Check Images', 'vp_textdomain'),
    								'fields' => array(
    										array(
    												'type' => 'checkimage',
    												'name' => 'ci_1',
    												'label' => __('Various Sized Images', 'vp_textdomain'),
    												'description' => __('CheckImage with unspecified item max height and item max width', 'vp_textdomain'),
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/100x100',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/120x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x120',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/50x50',
    														),
    												),
    										),
    										array(
    												'type' => 'checkimage',
    												'name' => 'ci_2',
    												'label' => __('Specified Images Maximum Height', 'vp_textdomain'),
    												'description' => __('CheckImage with specified item max height', 'vp_textdomain'),
    												'item_max_height' => '70',
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/100x100',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/120x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x120',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/50x50',
    														),
    												),
    												'default' => array(
    														'value_1',
    														'value_2',
    												),
    										),
    										array(
    												'type' => 'checkimage',
    												'name' => 'ci_3',
    												'label' => __('Specified Images Maximum Width', 'vp_textdomain'),
    												'description' => __('CheckImage with specified item max width', 'vp_textdomain'),
    												'item_max_width' => '50',
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/100x100',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/120x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x120',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/50x50',
    														),
    												),
    												'default' => array(
    														'value_3',
    														'value_4',
    												),
    										),
    										array(
    												'type' => 'checkimage',
    												'name' => 'ci_4',
    												'label' => __('Specified Images Maximum Width and Height', 'vp_textdomain'),
    												'description' => __('CheckImage with specified item max width and item max height', 'vp_textdomain'),
    												'item_max_height' => '70',
    												'item_max_width' => '70',
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/100x100',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/120x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x120',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/50x50',
    														),
    												),
    												'default' => array(
    														'value_1',
    														'value_4',
    												),
    										),
    										array(
    												'type' => 'checkimage',
    												'name' => 'ci_5',
    												'label' => __('Validation Rules Applied', 'vp_textdomain'),
    												'description' => __('Minimum selected of 2 items and Maximum selected of 3 items.', 'vp_textdomain'),
    												'item_max_height' => '70',
    												'item_max_width' => '70',
    												'validation' => 'required|minselected[2]|maxselected[3]',
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x80',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x80',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x80',
    														),
    												),
    												'default' => array(
    														'value_1',
    												),
    										),
    								),
    						),
    						array(
    								'type' => 'section',
    								'title' => __('Radio Images', 'vp_textdomain'),
    								'fields' => array(
    										array(
    												'type' => 'radioimage',
    												'name' => 'ri_1',
    												'label' => __('Various Sized Images', 'vp_textdomain'),
    												'description' => __('RadioImage with unspecified item max height and item max width', 'vp_textdomain'),
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/100x100',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/120x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x120',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/50x50',
    														),
    												),
    										),
    										array(
    												'type' => 'radioimage',
    												'name' => 'ri_2',
    												'label' => __('Specified Images Maximum Height', 'vp_textdomain'),
    												'description' => __('RadioImage with specified item max height', 'vp_textdomain'),
    												'item_max_height' => '70',
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/100x100',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/120x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x120',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/50x50',
    														),
    												),
    												'default' => array(
    														'value_1',
    												),
    										),
    										array(
    												'type' => 'radioimage',
    												'name' => 'ri_3',
    												'label' => __('Specified Images Maximum Width', 'vp_textdomain'),
    												'description' => __('RadioImage with specified item max width', 'vp_textdomain'),
    												'item_max_width' => '50',
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/100x100',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/120x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x120',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/50x50',
    														),
    												),
    												'default' => array(
    														'value_3',
    												),
    										),
    										array(
    												'type' => 'radioimage',
    												'name' => 'ri_4',
    												'label' => __('Specified Images Maximum Width and Height', 'vp_textdomain'),
    												'description' => __('RadioImage with specified item max width and item max height', 'vp_textdomain'),
    												'item_max_height' => '70',
    												'item_max_width' => '70',
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/100x100',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/120x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x120',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/50x50',
    														),
    												),
    												'default' => array(
    														'value_4',
    												),
    										),
    										array(
    												'type' => 'radioimage',
    												'name' => 'ri_5',
    												'label' => __('Validation Rules Applied', 'vp_textdomain'),
    												'description' => __('Required to Choose.', 'vp_textdomain'),
    												'item_max_height' => '70',
    												'item_max_width' => '70',
    												'validation' => 'required',
    												'items' => array(
    														array(
    																'value' => 'value_1',
    																'label' => __('Label 1', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x80',
    														),
    														array(
    																'value' => 'value_2',
    																'label' => __('Label 2', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x80',
    														),
    														array(
    																'value' => 'value_3',
    																'label' => __('Label 3', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x80',
    														),
    														array(
    																'value' => 'value_4',
    																'label' => __('Label 4', 'vp_textdomain'),
    																'img' => 'http://placehold.it/80x80',
    														),
    												),
    										),
    								),
    						),
    				),
    		),
    	
    	  		*/
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
            			'title' => __('Nutritional Information', $this->pluginName),
            			'name' => 'section_nutrition',
            			'fields' => array(
            					array(
            							'type' => 'notebox',
            							'name' => 'recipe_todo_item',
            							'label' => 'TODO:',
            							'description' => __('This needs to be moved somewhere else.', $this->pluginName) ,
            							'status' => 'warning',
            					),
            					array(
            							'type' => 'toggle',
            							'name' => 'recipe_use_nutritional_info',
            							'label' => __('Use nutritional information', $this->pluginName),
            							'description' => __( 'Add nutritional information to your recipes.', $this->pluginName ),
            							'default' => '0',
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
                    	/*	array(
                    				'type' => 'notebox',
                    				'name' => 'recipe_todo_item',
                    				'label' => 'TODO:',
                    				'description' => __('This link is wrong. Fix before release.', $this->pluginName) ,
                    				'status' => 'warning',
                    		),*/
                        array(
                            'type' => 'notebox',
                            'name' => 'recipe_slug_notebox',
                            'label' => __('404 error/page not found?', $this->pluginName),
                            'description' => __('Try', $this->pluginName) . ' <a href="http://rp-reloaded.net/documentation/404-error-page-found/" target="_blank">'.__('flushing your permalinks', $this->pluginName).'</a>.',
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
