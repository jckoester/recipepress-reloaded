<?php

/**
 * The admin-specific migration functionality of the plugin.
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 */

/**
 * The admin-specific migration functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
class RPR_Admin_Migration {

	/**
	 * The version of this plugin.
	 *
	 * @since   0.8.0
	 * @access  private
	 * @var     string  $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The database version of the plugin files. Used to compare with the
	 * version number saved in the database to decide if a database update is 
	 * necessary
	 * 
	 * @since   0.8.0
	 * @access  private
	 * @var     int   $dbversion  The database version of the plugin file.
	 */
	private $dbversion;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.8.0
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $version, $dbversion ) {

		$this->version	 = $version;
		$this->dbversion = $dbversion;
	}

	/**
	 * Fix the database version information
	 * 
	 * @since 0.9.0
	 */
	public function fix_dbversion() {
		/*
		 *  check for very old versions:
		 */
		if ( ! get_option( 'rpr_version_updated' ) ) {
			// Check, if RPR < 0.5:
			if ( is_array( get_option( 'rpr_options' ) ) ) {
				update_option( 'rpr_version_updated', '0.3.0' );
				//Check if RecipePress
			} elseif ( is_array( get_option( 'recipe-press-options' ) ) ) {
				update_option( 'rpr_version_updated', 'RecipePress' );
			} else {
				// RPR hasn't been installed previously.
				return;
			}
		}
		
		/*
		 * Check if site was affected by the 0.8.x multisite update bug
		 */
		if ( get_option( 'rpr_version_updated' ) && get_option( 'rpr_dbversion' ) ) {
			// If db version 5 is set but rpr_option still is there, migration did not trigger on previous update 
			if( version_compare( get_option( 'rpr_dbversion' ), '5', '=' ) && get_option( 'rpr_option' ) ) {
				// Reset the dbversion to 4 (as it hasn't been updated to 5)
				update_option( 'rpr_dbversion', '4' );
				// Remove old version string 'rpr_version_updated'
				delete_option( 'rpr_version_updated' );
			}
		} else {

			/*
			 * Check for older versions
			 */
			if ( get_option( 'rpr_version_updated' ) ) {
				// Fix the dbversion option for old versions of recipepress reloaded
				if ( version_compare( get_option( 'rpr_version_updated' ), '0.8.0', '<' ) ) {
					update_option( 'rpr_dbversion', '4' );
				}
				if ( version_compare( get_option( 'rpr_version_updated' ), '0.7.12', '<' ) ) {
					update_option( 'rpr_dbversion', '3' );
				}
				if ( version_compare( get_option( 'rpr_version_updated' ), '0.7.9', '<' ) ) {
					update_option( 'rpr_dbversion', '2' );
				}
				if ( version_compare( get_option( 'rpr_version_updated' ), '0.7.7', '<' ) ) {
					update_option( 'rpr_dbversion', '1' );
				}
				if ( get_option( 'rpr_version_updated' ) == "0.3.0" || get_option( 'rpr_version_updated' ) == 'RecipePress' ) {
					update_option( 'rpr_dbversion', '0' );
				}
			}
		}
			// Remove old version string 'rpr_version_updated'
			delete_option( 'rpr_version_updated' );
	}
		

	/**
	 * Check if the database needs an update by comparing the dbversion of 
	 * plugin an database
	 * 
	 * @since 0.8.0
	 */
	public function check_migration() {
		// Update version information in database if necessary
		if ( version_compare( get_option( 'rpr_version' ), $this->version, '<' ) ) {
			update_option( 'rpr_version', $this->version );
		}

		if ( get_option( 'rpr_dbversion' ) && get_option( 'rpr_dbversion' ) < $this->dbversion ) {
			/**
			 * Set Option to enable the update procedures
			 */
			update_option( 'rpr_update_needed', 1 );
		}
	}

	/**
	 * Create a database update notice
	 * We don't want to do a database change without the user knowing about it
	 * and having the chance to do a backup before.
	 * @since 0.8.0
	 */
	public function notice_migration() {
		if ( get_option( 'rpr_update_needed' ) == '1' ) {
			echo '<div class="updated"><p>';
			if ( get_option( 'rpr_dbversion' ) < 4 ) {
				printf( __( '%1s You are upgrading from an old version of RecipePress reloaded. A migration procedure is provided but it is not guaranteed it will work. Please make sure you have a backup you can roll back to.<br/> In case of problems file a bug report at the <a href="%2s" target="_blank">support forum</a> or file an issue at <a href="%3s" target="_blank">github</a>.<br/>', 'recipepress-reloaded' ), '<i class="fa fa-exclamation-triangle fa-3x" style="float:left; margin-right:6px; color:red;"></i>', 'https://wordpress.org/support/plugin/recipepress-reloaded', 'https://github.com/dasmaeh/recipepress-reloaded/issues' );
			}
			printf( __( 'The Recipe Press Reloaded database needs to upgraded. Make sure you have a backup before you proceed. | <a href="%1$s">Proceed</a>', 'recipepress-reloaded' ), '?post_type=rpr_recipe&rpr_do_migration=1' );
			echo "</p></div>";
		}
	}

	/**
	 * Do the actual migration dependent on database version
	 * @since 0.8.0
	 */
	public function rpr_do_migration() {
		// Just to be sure, run the fix_dbversion()
		$this->fix_dbversion();
		$dbver = get_option( 'rpr_dbversion' );

		// Check if the user has actively allowed the update:
		if ( isset( $_GET['rpr_do_migration'] ) && $_GET['rpr_do_migration'] == '1' ) {
			// Do all the update tasks dependent on the version of the database
			if ( get_option( 'rpr_dbversion' ) <= '0' ) {
				// Migrate from the old RecipePress plugin
				$this->rpr_update_from_rp();
			}
			if ( get_option( 'rpr_dbversion' ) <= '1' ) {
				// Migrate from db version 1
				$this->rpr_update_from_1();
			}
			if ( get_option( 'rpr_dbversion' ) <= '3' ) {
				// Migrate from db version 3
				$this->rpr_update_from_3();
			}
			if ( get_option( 'rpr_dbversion' ) <= '4' ) {
				// Migrate from db version 4
				$this->rpr_update_from_4();
			}
			// Flush permalinks
			flush_rewrite_rules();

			// Mark update as done
			update_option( 'rpr_update_needed', 0 );
		}
	}

	/**
	 * @todo TEST with data from 0.7.11 or older
	 */
	/**
	 * ***** ALL THE STUFF TO MIGRATE FROM RECIPE PRESS RELOADED = 0.7.12 *******
	 * ****************************** AKA DBVER 4 *******************************
	 */

	/**
	 * Do the migration from db version 4
	 * @since 0.8.0
	 */
	private function rpr_update_from_4() {


		$new_options = get_option( 'rpr_options' );
		$old_options = get_option( 'rpr_option' );

		// create a backup of the old options:
		update_option( 'rpr_options_backup', $old_options );

		// Move options to new scheme:
		// General options
		if ( $old_options['recipe_slug'] ) {
			$new_options['general']['slug'] = sanitize_title( $old_options['recipe_slug'] );
		}
		if ( $old_options['use_taxcloud_widget'] ) {
			$new_options['general']['use_taxcloud_widget'] = sanitize_key( $old_options['use_taxcloud_widget'] );
		}
		if ( $old_options['use_taxlist_widget'] ) {
			$new_options['general']['use_taxlist_widget'] = sanitize_title( $old_options['use_taxlist_widget'] );
		}
		$new_options['general']['homepage_display']	 = $old_options['recipe_homepage_display'];
		$new_options['general']['archive_display']	 = $old_options['recipe_archive_display'];

		// Include taxonomies in the new options array
		$old_tax = get_option( 'rpr_taxonomies' );

		// Ingredient options:
		if ( $old_tax['rpr_ingredient']['rewrite']['slug'] ) {
			$new_options['tax_builtin']['ingredients']['slug'] = $old_tax['rpr_ingredient']['rewrite']['slug'];
		}
		if ( $old_tax['rpr_ingredient']['labels']['name'] ) {
			$new_options['tax_builtin']['ingredients']['plural'] = $old_tax['rpr_ingredient']['labels']['name'];
		}
		if ( $old_tax['rpr_ingredient']['labels']['singular_name'] ) {
			$new_options['tax_builtin']['ingredients']['singular'] = $old_tax['rpr_ingredient']['labels']['singular_name'];
		}

		// All other taxonomies:
		unset( $old_tax['rpr_ingredient'] );

		if( is_array( $old_tax ) ) {
			foreach ( $old_tax as $key => $value ) {
				$taxarray = array(
					'tab_title'		 => $value['labels']['singular_name'],
					'singular'		 => $value['labels']['singular_name'],
					'plural'		 => $value['labels']['name'],
					'hierarchical'	 => $value['hierarchical'],
					'filter'		 => '0',
					'table'			 => '0'
				);
				if ( $value['rewrite']['slug'] ) {
					$taxarray['slug'] = $value['rewrite']['slug'];
				} else {
					$taxarray['slug'] = $key;
				}
				$new_options['tax_custom'][] = $taxarray;
			}
		}
		
		// Get a list of all units used for ingredients
		$units	 = array();
		global $wpdb;
		$rows	 = $wpdb->get_results( "SELECT meta_value FROM wp_postmeta WHERE meta_key='rpr_recipe_ingredients'" );

		foreach ( $rows as $row ) {
			$data = unserialize( $row->meta_value );
			foreach ( $data as $line ) {
				array_push( $units, sanitize_text_field( $line['unit'] ) );
			}
		}
		$units = array_values( array_unique( $units ) );
		sort( $units, SORT_NATURAL | SORT_FLAG_CASE );

		$new_options['units']['ingredient_units'] = $units;

		/**
		 *  Work down the list of excluded ingredients,
		 * exclusion from listings is now a term_meta
		 */
		if ( is_array( $old_options['ingredients_exclude_list'] ) ) {
			foreach ( $old_options['ingredients_exclude_list'] as $id ) {
				update_term_meta( $id, 'use_in_list', '0' );
			}
		}
		/**
		 * Migrate ingredient link target
		 */
		if ( $old_options['recipe_ingredient_links'] ) {
			switch ( $old_options['recipe_ingredient_links'] ) {
				case 'disabled':
					$new_options['tax_builtin']['ingredients']['link_target']	 = 0;
					break;
				case 'archive':
					$new_options['tax_builtin']['ingredients']['link_target']	 = 1;
					break;
				case 'archive_custom':
					$new_options['tax_builtin']['ingredients']['link_target']	 = 2;
					break;
				case 'custom':
					$new_options['tax_builtin']['ingredients']['link_target']	 = 3;
					break;
				default:
					$new_options['tax_builtin']['ingredients']['link_target']	 = 3;
			}
		}

		/**
		 * Migrate ingredient note separator
		 */
		if ( $old_options['ingredient_comment_sep'] ) {
			switch ( $old_options['ingredient_comment_sep'] ) {
				case 'none':
					$new_options['tax_builtin']['ingredients']['comment_sep']	 = 0;
					break;
				case 'brackets':
					$new_options['tax_builtin']['ingredients']['comment_sep']	 = 1;
					break;
				case 'comma':
					$new_options['tax_builtin']['ingredients']['comment_sep']	 = 2;
					break;
				default:
					$new_options['tax_builtin']['ingredients']['comment_sep']	 = 0;
			}
		}

		/**
		 * Create a list of units from serving sized
		 */
		// Get a list of all units used for ingredients
		$units	 = array();
		global $wpdb;
		$rows	 = $wpdb->get_results( "SELECT meta_value FROM wp_postmeta WHERE meta_key='rpr_recipe_servings_type'" );

		foreach ( $rows as $row ) {
			array_push( $units, sanitize_text_field( $row->meta_value ) );
		}
		$units = array_values( array_unique( $units ) );
		sort( $units, SORT_NATURAL | SORT_FLAG_CASE );

		$new_options['units']['serving_units'] = $units;

		/**
		 * Migrate the layout options
		 */
		$new_options['layout_general']['layout']					 = $old_options['rpr_template'];
		// Icon usage is now an option of the layout itself
		$new_options['layout']['rpr_default']['icons_display']		 = $old_options['recipe_icons_display'];
		$new_options['layout']['rpr2column']['icons_display']		 = $old_options['recipe_icons_display'];
		// Printlink is now an option of the layout itself
		$new_options['layout']['rpr_default']['printlink_display']	 = $old_options['recipe_display_printlink'];
		$new_options['layout']['rpr_default']['printlink_class']	 = $old_options['recipe_printlink_class'];
		$new_options['layout']['rpr2column']['printlink_display']	 = $old_options['recipe_display_printlink'];
		$new_options['layout']['rpr2column']['printlink_class']		 = $old_options['recipe_printlink_class'];
		// Image settings remain global for the moment. Might move to the layouts as well:
		$new_options['layout_general']['images_link']				 = $old_options['recipes_images_clickable'];
		$new_options['layout_general']['images_instruction']		 = $old_options['recipe_instruction_image'];
		if ( $old_options['recipe_instruction_image_position'] === 'rpr_instrimage_below' ) {
			$new_options['layout_general']['images_instr_pos'] = 'below';
		} else {
			$new_options['layout_general']['images_instr_pos'] = 'right';
		}

		/**
		 * Migrate advanced theming options
		 */
		$new_options['advanced']['display_image']		 = $old_options['recipe_display_image'];
		$new_options['advanced']['display_author']		 = $old_options['recipe_author_display_in_recipe'];
		$new_options['advanced']['display_time']		 = $old_options['recipe_time_display_in_recipe'];
		$new_options['advanced']['display_categories']	 = $old_options['recipe_display_categories_in_recipe'];
		$new_options['advanced']['display_tags']		 = $old_options['recipe_display_tags_in_recipe'];

		// Save the new options
		update_option( 'rpr_options', $new_options );
		update_option( 'rpr_dbversion', 5 );

		// Remove old options
		delete_option( 'rpr_option' );
		delete_option( 'rpr_option_transients' );
		delete_option( 'rpr_taxonomies' );
		delete_option( 'rpr_flush' );
	}

	/**
	 * @todo TEST with data from 0.7.11 or older
	 */
	/**
	 * ***** ALL THE STUFF TO MIGRATE FROM RECIPE PRESS RELOADED < 0.7.12 *******
	 * ****************************** AKA DBVER 3 *******************************
	 */

	/**
	 * Do the migration from db version 3
	 * @since 0.8.0
	 */
	private function rpr_update_from_3() {
		$array = get_option( 'rpr_taxonomies', array() );
		if ( $array['rpr_category']['rewrite']['slug'] == 'rpr category' ) {
			$array['rpr_category']['rewrite']['slug'] = 'rpr-category';
		}
		if ( $array['rpr_tag']['rewrite']['slug'] == 'rpr tag' ) {
			$array['rpr_tag']['rewrite']['slug'] = 'rpr-tag';
		}
		update_option( 'rpr_taxonomies', $array );
	}

	/**
	 * @todo TEST with data from 0.5 or 0.6
	 */
	/**
	 * ******* ALL THE STUFF TO MIGRATE FROM RECIPE PRESS RELOADED < 0.7 ********
	 * ****************************** AKA DBVER 1 *******************************
	 */

	/**
	 * Do the migration from db version 1
	 * @since 0.8.0
	 */
	private function rpr_update_from_1() {
		$rpr_option = get_option( 'rpr_option' );

		$array				 = $rpr_option['taxonomies'];
		$array['category']	 = $rpr_option['recipe_tags_use_wp_categories'];
		$array['post_tag']	 = $rpr_option['recipe_tags_use_wp_tags'];

		$taxonomies	 = get_option( 'rpr_taxonomies' );
		$taxonomies	 = $this->add_taxonomy_to_array( $taxonomies, 'rpr_season', __( 'Seasons', 'recipepress-reloaded' ), __( 'Season', 'recipepress-reloaded' ) );
		$taxonomies	 = $this->add_taxonomy_to_array( $taxonomies, 'rpr_difficulty', __( 'Difficulties', 'recipepress-reloaded' ), __( 'Difficulty', 'recipepress-reloaded' ) );
		update_option( 'rpr_taxonomies', $taxonomies );
		update_option( 'rpr_dbversion', '2' );
	}

	private function add_taxonomy_to_array( $arr, $tag, $name, $singular, $hierarchical = false ) {
		$name_lower		 = strtolower( $name );
		$singular_lower	 = strtolower( $singular );

		$arr[$tag] = array(
			'labels'		 => array(
				'name'						 => $name,
				'singular_name'				 => $singular,
				'search_items'				 => __( 'Search', 'recipepress-reloaded' ) . ' ' . $name,
				'popular_items'				 => __( 'Popular', 'recipepress-reloaded' ) . ' ' . $name,
				'all_items'					 => __( 'All', 'recipepress-reloaded' ) . ' ' . $name,
				'edit_item'					 => __( 'Edit', 'recipepress-reloaded' ) . ' ' . $singular,
				'update_item'				 => __( 'Update', 'recipepress-reloaded' ) . ' ' . $singular,
				'add_new_item'				 => __( 'Add New', 'recipepress-reloaded' ) . ' ' . $singular,
				'new_item_name'				 => __( 'New', 'recipepress-reloaded' ) . ' ' . $singular . ' ' . __( 'Name', 'recipepress-reloaded' ),
				'separate_items_with_commas' => __( 'Separate', 'recipepress-reloaded' ) . ' ' . $name_lower . ' ' . __( 'with commas', 'recipepress-reloaded' ),
				'add_or_remove_items'		 => __( 'Add or remove', 'recipepress-reloaded' ) . ' ' . $name_lower,
				'choose_from_most_used'		 => __( 'Choose from the most used', 'recipepress-reloaded' ) . ' ' . $name_lower,
				'not_found'					 => __( 'No', 'recipepress-reloaded' ) . ' ' . $name_lower . ' ' . __( 'found.', 'recipepress-reloaded' ),
				'menu_name'					 => $name
			),
			'show_ui'		 => true,
			'show_tagcloud'	 => true,
			'hierarchical'	 => $hierarchical,
			'rewrite'		 => array(
				'slug' => preg_replace( '/_/', '-', $singular_lower )
			)
		);

		return $arr;
	}

	/**
	 * @todo TEST with data from RP or RPR 0.3
	 */
	/**
	 * ******* ALL THE STUFF TO MIGRATE FROM RECIPE PRESS AND RPR < 0.5 *********
	 * *************************** AKA DBVER -1 and 1 ***************************
	 */

	/**
	 * Do the migration from the old RecipePress plugin
	 * @since 0.8.0
	 */
	private function rpr_update_from_rp() {
		// 1.) Register old posttype and taxonomies:
		$this->rp_setup_post_type();
		$this->rp_setup_ingredients();
		$this->rp_setup_taxonomies();
		// 2.) get all old recipes
		$recipes = $this->rp_get_old_recipes();
		// Walk through all the recipes and move them to the new format:
		foreach ( $recipes as $recipe ) {
			// a) change post type
			set_post_type( $recipe->ID, 'rpr_recipe' );
			// b) move post_meta:
			$content_old = $recipe->post_content; //=> Instructions!
			$fields		 = $this->rp_get_fields();

			foreach ( $fields as $field ) {
				$old = get_post_meta( $recipe->ID, $field, true );
				$new = "";

				// cases:
				if ( $field == 'rpr_recipe_description' ) {
					$new = $recipe->post_excerpt;
				} elseif ( $field == 'rpr_recipe_featured' ) {
					$new = get_post_meta( $recipe->ID, '_recipe_featured_value', true );
				} elseif ( $field == 'rpr_recipe_rating' ) {
					$new = "";
				} elseif ( $field == 'rpr_recipe_servings' ) {
					$new = get_post_meta( $recipe->ID, '_recipe_servings_value', true );
				} elseif ( $field == 'rpr_recipe_servings_type' ) {
					$serving_size	 = get_post_meta( $recipe->ID, '_recipe_serving_size_value', true );
					$sizeterm		 = get_term_by( 'id', $serving_size, 'recipe-serving' );
					if ( is_object( $sizeterm ) ) {
						$new = $sizeterm->name;
					} else {
						$new = "";
					}
				} elseif ( $field == 'rpr_recipe_prep_time' ) {
					$preptime	 = get_post_meta( $recipe->ID, '_recipe_prep_time_value', true );
					$new		 = $preptime;
				} elseif ( $field == 'rpr_recipe_cook_time' ) {
					$cooktime	 = get_post_meta( $recipe->ID, '_recipe_prep_time_value', true );
					$new		 = $cooktime;
				} elseif ( $field == 'rpr_recipe_passive_time' ) {
					$readytime	 = get_post_meta( $recipe->ID, '_recipe_ready_time_value', true );
					$idletime	 = $readytime - ($cooktime + $preptime);
					if ( $idletime != 0 ) {
						$new = $idletime;
					} else {
						$new = "";
					}
					// Ingredients:
				} elseif ( $field == 'rpr_recipe_ingredients' ) {
					$ingredients			 = array();
					$non_empty_ingredients	 = array();
					$ings					 = $this->rp_get_ingredients( $recipe );

					foreach ( $ings as $ing ) {
						$ingredient = array();

						$ingterm = get_term_by( 'id', $ing['item'], 'recipe-ingredient' );

						if ( is_object( $ingterm ) ) {
							$ingredient['ingredient'] = $ingterm->name;
						} else {
							$ingredient['ingredient'] = "";
						}

						if ( isset( $ing['quantity'] ) ) {
							if ( $ing['quantity'] == 0 ) {
								$ingredient['amount'] = "";
							} else {
								$ingredient['amount'] = $ing['quantity'];
							}
						} else {
							$ingredient['amount'] = "";
						}

						$sizeterm = get_term_by( 'id', $ing['size'], 'recipe-size' );

						if ( is_object( $sizeterm ) ) {
							$ingredient['unit'] = $sizeterm->name;
						} else {
							$ingredient['unit'] = "";
						}

						if ( isset( $ing['notes'] ) ) {
							$ingredient['notes'] = $ing['notes'];
						} else {
							$ingredient['notes'] = "";
						}

						if ( isset( $ing['page-link'] ) ) {
							$ingredient['link'] = $ing['page-link'];
						}

						if ( isset( $ing['url'] ) ) {
							$ingredient['link'] = $ing['url'];
						}

						if ( isset( $ingredient['ingredient'] ) ) {
							$term = term_exists( $ingredient['ingredient'], 'rpr_ingredient' );

							if ( ($term === 0 || $term === null) && isset( $ingredient['ingredient'] ) && $ingredient['ingredient'] != "" ) {
								$term = wp_insert_term( $ingredient['ingredient'], 'rpr_ingredient' );
							}

							$term_id = intval( $term['term_id'] );

							$ingredient['ingredient_id'] = $term_id;
							$ingredients[]				 = $term_id;

							$non_empty_ingredients[] = $ingredient;
						}
					}
					wp_set_post_terms( $recipe->ID, $ingredients, 'rpr_ingredient' );
					$new = $non_empty_ingredients;

					// Instructions:
				} elseif ( $field == 'rpr_recipe_instructions' ) {
					$instr = explode( "\n", preg_replace( array( "<!--:de-->", "<!--:-->" ), array( "", "" ), $content_old ) );

					foreach ( $instr as $instruction ) {
						$new[]['description'] = $instruction;
					}

					$non_empty_instructions = array();

					foreach ( $new as $instruction ) {
						if ( ( isset( $instruction['description'] ) && $instruction['description'] != "" ) || isset( $instruction['image'] ) ) {
							$non_empty_instructions[] = $instruction;
						}
					}

					$new = $non_empty_instructions;
					// notes
				} elseif ( $field == 'rpr_recipe_notes' ) {
					$new = get_post_meta( $recipe->ID, $field, true );
				}

				echo $recipe->ID;
				// Update or delete meta data if changed
				update_post_meta( $recipe->ID, $field, $new );
			}
			//MISSING: delete old post_meta
			// RecipePress Taxonomies:
			$options	 = get_option( 'recipe-press-options' );
			$taxonomies	 = $options['taxonomies'];
			if ( $taxonomies ) {
				foreach ( $taxonomies as $taxonomy ) {
					$this->rp_migrate_term( $recipe->ID, $taxonomy['slug'] );
				}
			}
		}
		//MISSING: delete old taxonomies:
		//require_once 'rpr_taxonomies.php';
		//$this->taxo = new RPR_Taxonomies($this->pluginName, $this->plugunDir, $this->pluginUrl );
		//$this->taxo->delete_taxonomy( $taxonomy['slug'] );
		//MISSING: migrate settings?
		delete_option( 'recipe-press-options' );
		// Write new dbver
		update_option( 'rpr_dbversion', '1' );
		//var_dump( get_option( 'rpr_dbversion' ) ); die;
		return true;
	}

	private function rp_setup_post_type() {
		global $wp_version;

		//$page = get_page( RPReloaded::get_option('display_page') );
		$labels = array(
			'name' => __( 'recipes', 'recipepress-reloaded' )
		);

		$args = array(
			'labels'			 => $labels,
			'public'			 => true,
			'publicly_queryable' => true,
			'show_ui'			 => true,
			'query_var'			 => true,
			'capability_type'	 => 'page',
			'hierarchical'		 => false,
			//'menu_position' => (int) RPReloaded::get_option('menu_position'),
			//'menu_icon' => RPReloaded::get_option('menu_icon'),
			'supports'			 => array( 'title', 'editor', 'author', 'excerpt', 'page-attributes', 'custom-fields', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'post_tag', 'category' ),
			'rewrite'			 => true
		);

		register_post_type( 'recipe', $args );
	}

	private function rp_setup_ingredients() {
		$labels	 = array(
			'name' => __( 'Ingredients', 'recipepress-reloaded' ),
		);
		$args	 = array(
			'hierarchical'	 => false,
			'label'			 => __( 'Ingredients', 'recipepress-reloaded' ),
			'labels'		 => $labels,
			'public'		 => true,
			'show_ui'		 => true,
			'capabilities'	 => array(
				'assign_terms' => false
			),
			'rewrite'		 => array( 'slug' => 'ingredient' ),
		);
		register_taxonomy( 'recipe-ingredient', 'recipe', $args );

		return true;
	}

	private function rp_setup_taxonomies() {
		$taxlist = array(
			array(
				'slug'			 => 'recipe-size',
				'singular'		 => __( 'size', 'recipepress-reloaded' ),
				'plural'		 => __( 'sizes', 'recipepress-reloaded' ),
				'per-page'		 => 10,
				'default'		 => -1,
				'hierarchical'	 => 1,
				'active'		 => 1,
			),
			array(
				'slug'			 => 'recipe-serving',
				'singular'		 => __( 'size', 'recipepress-reloaded' ),
				'plural'		 => __( 'sizes', 'recipepress-reloaded' ),
				'per-page'		 => 10,
				'default'		 => -1,
				'hierarchical'	 => 1,
				'active'		 => 1,
			),
		);

		// Recipe Press Options:
		$options = get_option( 'recipe-press-options' );
		if ( is_array( $options ['taxonomies'] ) ) {
			$taxlist = array_merge( $options ['taxonomies'], $taxlist );
		}

		foreach ( $taxlist as $taxonomy ) {
			$labels	 = array(
				'name' => __( $taxonomy ['singular'], 'recipepress-reloaded' )
			);
			$args	 = array(
				'hierarchical'	 => false,
				'label'			 => __( $taxonomy ['plural'], 'recipepress-reloaded' ),
				'labels'		 => $labels,
				'public'		 => true,
				'show_ui'		 => true,
				'capabilities'	 => array(
					'assign_terms' => false
				),
				'rewrite'		 => array(
					'slug' => $taxonomy ['slug']
				)
			);
			register_taxonomy( $taxonomy ['slug'], 'recipe', $args );
		}

		return true;
	}

	private function rp_get_old_recipes( $orderby = 'date', $order = 'DESC', $taxonomy = '', $term = '', $limit = -1, $author = '' ) {
		$args = array(
			'post_type'		 => 'recipe',
			'posts_per_page' => -1,
		);

		$query	 = new WP_Query( $args );
		$recipes = array();

		if ( $query->have_posts() ) { //recipes found
			while ( $query->have_posts() ) {
				$query->the_post();
				global $post;
				$recipes[] = $post;
			}
		}

		if ( $orderby == 'post_title' || $orderby == 'title' || $orderby == 'name' ) {
			usort( $recipes, array( $this, "compare_post_titles" ) );

			if ( $order == 'DESC' ) {
				$recipes = array_reverse( $recipes );
			}
		}

		return $recipes;
	}

	private function rp_get_fields() {
		$return	 = array(
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
		$options = get_option( 'rpr_option' );

		//if( $options['recipe_use_nutritional_info'] == 1 ){
		array_push( $return, 'rpr_recipe_calorific_value' );
		array_push( $return, 'rpr_recipe_protein' );
		array_push( $return, 'rpr_recipe_fat' );
		array_push( $return, 'rpr_recipe_carbohydrate' );
		array_push( $return, 'rpr_recipe_nutrition_per' );
		//}
		return $return;
	}

	private function rp_get_ingredients( $post = NULL ) {
		if ( ! $post ) {
			global $post;
		}

		$ingredients = get_post_meta( $post->ID, '_recipe_ingredient_value' );

		if ( count( $ingredients ) < 1 ) {
			return $this->rp_empty_ingredients();
		} else {
			$ings		 = array();
			$defaults	 = array(
				'quantity'	 => NULL,
				'size'		 => 0,
				'item'		 => 0,
				'notes'		 => NULL,
				'page-link'	 => NULL,
				'url'		 => NULL,
				'order'		 => 0
			);

			foreach ( $ingredients as $ingredient ) {
				$ings[$ingredient['order']] = $ingredient;
				wp_parse_args( $ings[$ingredient['order']], $defaults );
			}

			ksort( $ings );
			return $ings;
		}
	}

	private function rp_empty_ingredients( $count = 5 ) {
		$ingredients = array();
		for ( $ctr = 0; $ctr < $count; ++ $ctr ) {
			$ingredients[$ctr]['size']	 = 'none';
			$ingredients[$ctr]['item']	 = 0;
		}
		return $ingredients;
	}

	private function rp_migrate_term( $recipe_id, $slug ) {
		global $wp_taxonomies;
		//echo $slug;
		$oldterms = get_the_terms( $recipe_id, $slug );

		$options = get_option( 'rpr_option' );
		// Find new slug:
		if ( $slug == 'recipe-category' ):
			// Use WP Catories and Tags or recipe specific?
			if ( $options['recipe_tags_use_wp_categories'] == '1' ):
				//get_option('rpr_option')['recipe_tags_use_wp_categories'] == true):
				$new_slug = 'post_category';
			else:
				$new_slug = 'rpr_category';
			endif;
		elseif ( $slug == 'recipe-tags' ):
			// Use WP Catories and Tags or recipe specific?
			if ( $options['recipe_tags_use_wp_tags'] == '1' ):
				//get_option('rpr_option')['recipe_tags_use_wp_tags'] == true):
				$new_slug = 'post_tag';
			else:
				$new_slug = 'rpr_tag';
			endif;
		else:
			// create custom taxonomies if necessary
			$new_slug = preg_replace( '/recipe-/', "rpr_", $slug );
			if ( ! taxonomy_exists( $new_slug ) ):
				$taxonomy = get_taxonomy( $slug );
				$this->rp_add_taxonomy( $taxonomy->name, $taxonomy->singular, $new_slug, $new_slug );
			endif;
		endif;

		if ( taxonomy_exists( $new_slug ) == true && is_array( $oldterms ) ):
			//echo $new_slug;
			$new_terms = array();

			foreach ( $oldterms as $oldterm ):
				// Check if term already exists in new tax:
				$term = term_exists( $oldterm->name, $new_slug );

				// Create term if necessary:
				if ( $term === 0 || $term === null ) {
					$term = wp_insert_term( $oldterm->name, $new_slug );
				}

				$term_id = intval( $term['term_id'] );
				array_push( $new_terms, $term_id );

			endforeach;

			// add terms to recipe
			wp_set_post_terms( $recipe_id, $new_terms, $new_slug, true );
			return true;
		else:
			return false;
		endif;
	}

	private function rp_add_taxonomy( $name, $singular, $slug, $hierarchical, $edit_tag_name ) {
		$editing = false;

		if ( strlen( $edit_tag_name ) > 0 ) {
			$editing = true;
		}

		if ( ! $editing && taxonomy_exists( strtolower( $singular ) ) ) {
			die( 'This taxonomy already exists.' );
		}

		if ( strlen( $name ) > 1 && strlen( $singular ) > 1 ) {
			$taxonomies = get_option( 'rpr_taxonomies', array() );

			$name_lower		 = strtolower( $name );
			$singular_lower	 = strtolower( $singular );

			$tag_name = $singular_lower;

			if ( $editing ) {
				$tag_name = $edit_tag_name;
			}

			$taxonomies[$tag_name] = array(
				'labels'		 => array(
					'name'						 => $name,
					'singular_name'				 => $singular,
					'search_items'				 => sprintf( __( 'Search %s', 'recipepress-reloaded' ), $name ),
					'popular_items'				 => sprintf( __( 'Popular %s', 'recipepress-reloaded' ), $name ),
					'all_items'					 => sprintf( __( 'All %s', 'recipepress-reloaded' ), $name ),
					'edit_item'					 => sprintf( __( 'Edit %s', 'recipepress-reloaded' ), $singular ),
					'update_item'				 => sprintf( __( 'Update %s', 'recipepress-reloaded' ), $singular ),
					'add_new_item'				 => sprintf( __( 'Add New %s', 'recipepress-reloaded' ), $singular ),
					'new_item_name'				 => sprintf( __( 'New %s Name', 'recipepress-reloaded' ), $singular ),
					'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'recipepress-reloaded' ), $name_lower ),
					'add_or_remove_items'		 => sprintf( __( 'Add or remove %s', 'recipepress-reloaded' ), $name_lower ),
					'choose_from_most_used'		 => sprintf( __( 'Choose from the most used %s', 'recipepress-reloaded' ), $name_lower ),
					'not_found'					 => sprintf( __( 'No %s found.', 'recipepress-reloaded' ), $name_lower ),
					'menu_name'					 => $name
				),
				'show_ui'		 => true,
				'show_tagcloud'	 => true,
				'query_var'		 => true,
				'hierarchical'	 => $hierarchical,
				'rewrite'		 => array(
					'slug'			 => $slug,
					'hierarchical'	 => $hierarchical
				)
			);
			update_option( 'rpr_taxonomies', $taxonomies );
			//$this->taxonomies=$taxonomies;
			return true;
		}
	}

}
