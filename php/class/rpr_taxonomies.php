<?php
/*
 * RPR class for managing taxonomies
*/

if( class_exists( 'RPReloaded' ) ) {
	
	class RPR_Taxonomies extends RPR_Core {
	
		public function __construct( $pluginName = '', $pluginDir = '', $pluginUrl = '' ) {

			$this->pluginName = $pluginName;
			$this->pluginDir = $pluginDir;
			$this->pluginUrl = $pluginUrl;
	
			// Recipe taxonomies that users should not be able to edit:
			$this->ignoreTaxonomies = array('rpr_rating');
			// Recipe taxonomies that users should not be able to delete:
			$this->nodeleteTaxonomies = array('rpr_ingredient', 'post_tag', 'category');
			
			//Actions
			add_action( 'init', array( $this, 'rpr_taxonomies_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'rpr_taxonomies_enqueue' ) ); //TODO: Only on custom taxonomies page
			add_action( 'admin_init', array( $this, 'rpr_taxonomies_settings' ) );
			add_action( 'admin_menu', array( $this, 'rpr_taxonomies_menu' ) );
			add_action( 'admin_action_delete_taxonomy', array( $this, 'delete_taxonomy_form' ) );
			add_action( 'admin_action_add_taxonomy', array( $this, 'save_taxonomy_form' ) );
		}
		
		public function rpr_taxonomies_enqueue()
		{
			wp_register_style( 'rpr-custom-taxonomies', $this->pluginUrl . '/css/rpr_taxonomies.css' );
			wp_enqueue_style( 'rpr-custom-taxonomies' );
			wp_register_script( 'rpr-custom-taxonomies', $this->pluginUrl . '/js/rpr_taxonomies.js', array( 'jquery' ) );
			wp_enqueue_script( 'rpr-custom-taxonomies' );
		}
		


		public function rpr_taxonomies_settings()
		{
			add_submenu_page( null, __( 'Custom Taxonomies', $this->pluginName ), __( 'Manage Tags', $this->pluginName ), 'manage_options', 'rpr_taxonomies', array( $this, 'rpr_taxonomies_page' ) );
			add_settings_section( 'rpr_taxonomies_list_section', __('Current Recipe Taxonomies', $this->pluginName ), array( $this, 'admin_menu_list_taxonomies' ), 'rpr_taxonomies_settings' );
			add_settings_section( 'rpr_taxonomies_settings_section', __('Add new / edit Recipe Taxonomy', $this->pluginName ), array( $this, 'admin_menu_settings_taxonomies' ), 'rpr_taxonomies_settings' );
		
		}
		
	
		public function rpr_taxonomies_menu()
		{
			// Custom taxonomies menu:
			add_submenu_page( null, __( 'Custom Taxonomies', $this->pluginName ), __( 'Manage Tags', $this->pluginName ), 'manage_options', 'rpr_taxonomies', array( $this, 'rpr_taxonomies_page' ) );
		}
		
		public function rpr_taxonomies_page() {
			if (!current_user_can('manage_options')) {
				wp_die('You do not have sufficient permissions to access this page.');
			}
		
			include($this->pluginDir . '/php/helper/rpr_taxonomies_builder.php');
		}
		
		public function rpr_taxonomies_init() {
		
			$this->taxonomies = get_option('rpr_taxonomies', array());
			

			if( $this->option('recipe_tags_use_wp_categories', 1) == '1'):
				/*if( taxonomy_exists( 'rpr_category' ) ) {
					$this->delete_taxonomy('rpr_category');
				}*/
				unset($this->taxonomies['rpr_category']);
				update_option('rpr_taxonomies', $this->taxonomies);
			else:
				// Not using WP categories
				if( ! in_array( 'rpr_category', $this->taxonomies )):
                    $this->add_taxonomy(__('Recipe Category', $this->pluginName), __('Recipe Category', $this->pluginName), 'rpr_category', 'rpr_category');
                    update_option('rpr_taxonomies', $this->taxonomies);
				endif;
			endif;
	
			if( $this->option( 'recipe_tags_use_wp_tags', 1) == '1'):
				/*if( taxonomy_exists( 'rpr_tag' ) ) {
					$this->delete_taxonomy('rpr_tag');
				}*/
				unset($this->taxonomies['rpr_tag']);
				update_option('rpr_taxonomies', $this->taxonomies);
			else:
				// Not using WP tags
				if( ! in_array( 'rpr_tag', $this->taxonomies )):
    				$this->add_taxonomy(__('Recipe Tag', $this->pluginName), __('Recipe Tag', $this->pluginName), 'rpr_tag', 'rpr_tag');
    				update_option('rpr_taxonomies', $this->taxonomies);
				endif;
			endif;
			
            // register taxonomies:
			foreach($this->taxonomies as $name => $options) {
				register_taxonomy(
					$name,
					'rpr_recipe',
					$options
				);
		
				register_taxonomy_for_object_type( $name, 'rpr_recipe' );
			}
		}
		
		// DAS MUSS SAUBERER GEHEN!!!
		public function admin_menu_list_taxonomies() {
		
			echo  '<form id="rpr_delete_taxonomy" method="POST" action="' . admin_url( 'admin.php' ) . '" onsubmit="return confirm(\'Do you really want to delete this taxonomy?\');">
               		<input type="hidden" name="action" value="delete_taxonomy">';
			wp_nonce_field( 'delete_taxonomy', 'delete_taxonomy_nonce', false );
			echo 	'<input type="hidden" id="rpr_delete_taxonomy_name" name="rpr_delete_taxonomy_name" value="">';
		
			echo   '<table id="rpr-tags-table" class="wp-list-table widefat" cellspacing="0">
                        <thead>
                        <tr>
                            <th scope="col" id="tag" class="manage-column">
                                '.__( 'Tag', $this->pluginName ).'
                            </th>
                            <th scope="col" id="singular-name" class="manage-column">
                                '.__( 'Singular Name', $this->pluginName ).'
                            </th>
                            <th scope="col" id="name" class="manage-column">
                                '.__( 'Name', $this->pluginName ).'
                            </th>
                            <th scope="col" id="slug" class="manage-column">
                                '.__( 'Slug', $this->pluginName ).'
                            </th>
                            <th scope="col" id="action" class="manage-column">
                                '.__( 'Actions', $this->pluginName ).'
                            </th>
                        </tr>
                        </thead>
		
                        <tbody id="the-list">';
		
			$taxonomies = get_object_taxonomies( 'rpr_recipe', 'objects' );
			
			if ( is_array($taxonomies) ) {
				foreach ( $taxonomies as $taxonomy ) {
		
					if( !in_array( $taxonomy->name, $this->ignoreTaxonomies ) ) {
						echo
						'<tr>
                                <td><strong>' . $taxonomy->name . '</strong></td>
                                <td class="singular-name">' . $taxonomy->labels->singular_name . '</td>
                                <td class="name">' . $taxonomy->labels->name . '</td>
                                <td class="slug">' . $taxonomy->rewrite['slug'] . '</td>
                                <td>
                                    <span class="rpr_adding">
                                        <button type="button" class="button rpr-edit-tag" data-tag="' . $taxonomy->name . '">Edit</button>';
						if( ! in_array( $taxonomy->name, $this->nodeleteTaxonomies ) ) {
							echo 		'<button type="button" class="button rpr-delete-tag" data-tag="' . $taxonomy->name . '">Delete</button> ';
						}
						echo    '    </span>
                                </td>
                            </tr>';
					}
		
				}
			}
		
			echo        '</tbody>
                    </table>
                    </form>';
		}
		public function admin_menu_settings_taxonomies() {
			_e( 'Create custom tags for your recipes.', $this->pluginName );
		
			echo  '<form method="POST" action="' . admin_url( 'admin.php' ) . '">
                        <input type="hidden" name="action" value="add_taxonomy">
                        <input type="hidden" id="rpr_edit_tag_name" name="rpr_edit" value="">';
			wp_nonce_field( 'add_taxonomy', 'add_taxonomy_nonce', false );
		
			echo '<div id="rpr_editing" class="rpr_editing">'.__( 'Currently editing tag: ', $this->pluginName ).'<span id="rpr_editing_tag"></span></div>';
			echo '<table class="form-table"><tbody>';
		
			// Name
			echo     '<tr valign="top">
                        <th scope="row">'.__( 'Name', $this->pluginName ).'</th>
                        <td>
                            <input type="text" id="rpr_custom_taxonomy_name" name="rpr_custom_taxonomy_name" />
                            <label for="rpr_custom_taxonomy_name"> '  . __('(e.g. Courses)', $this->pluginName ) . '</label>
                        </td>
                      </tr>';
		
			// Singular name
			echo     '<tr valign="top">
                        <th scope="row">'.__( 'Singular Name', $this->pluginName ).'</th>
                        <td>
                            <input type="text" id="rpr_custom_taxonomy_singular_name" name="rpr_custom_taxonomy_singular_name" />
                            <label for="rpr_custom_taxonomy_singular_name"> '  . __('(e.g. Course)', $this->pluginName ) . '</label>
                        </td>
                      </tr>';
		
			// Slug
			echo     '<tr valign="top">
                        <th scope="row">'.__( 'Slug', $this->pluginName ).'</th>
                        <td>
                            <input type="text" id="rpr_custom_taxonomy_slug" name="rpr_custom_taxonomy_slug" />
                            <label for="rpr_custom_taxonomy_slug"> '  . __('(e.g. http://www.yourwebsite.com/course/)', $this->pluginName ) . '</label>
                        </td>
                      </tr>';
		
		
			echo '</tbody></table><br/>';
			echo '<span class="rpr_adding">';
			//echo '<button type="button" class="button button-primary">'.__( 'Add new tag', $this->pluginName ).'</button>';
			//echo '<strong> ' . __( 'Adding new tags is only possible in', $this->pluginName ) . ' <a href="http://www.wpultimaterecipeplugin.com/premium/" target="_blank">WP Ultimate Recipe Premium</a></strong>';
			echo '</span>';
			echo '<span>';
			submit_button( __( 'Save', $this->pluginName ), 'primary', 'submit', false );
			echo ' <button type="button" id="rpr_cancel_editing" class="button">'.__( 'Cancel', $this->pluginName ).'</button>';
			echo '</span></form>';
		}
		
		public function save_taxonomy_form() {
			if ( !wp_verify_nonce( $_POST['add_taxonomy_nonce'], 'add_taxonomy' ) ) {
				die( 'Invalid nonce.' . var_export( $_POST, true ) );
			}
			
			$name = $_POST['rpr_custom_taxonomy_name'];
			$singular = $_POST['rpr_custom_taxonomy_singular_name'];
			$slug = strtolower($_POST['rpr_custom_taxonomy_slug']);
			
			$edit_tag_name = $_POST['rpr_edit'];
			
			$this->add_taxonomy($name, $singular, $slug, $edit_tag_name);
			
			$this->rpr_taxonomies_init();
			update_option( 'rpr_flush', '1' );
			
			wp_redirect( $_SERVER['HTTP_REFERER'] );
			exit();
			
		}
		
		public function add_taxonomy($name, $singular, $slug, $edit_tag_name) {
			$editing = false;
		
			if( strlen($edit_tag_name) > 0 ) {
				$editing = true;
			}
			/*
			if( !$editing ) {
				die( 'There was an unexpected error. Please try again.' );
			}
		*/
			if( !$editing && taxonomy_exists( strtolower($singular) ) ) {
				die( 'This taxonomy already exists.' );
			}
		
			if( strlen($name) > 1 && strlen($singular) > 1 ) {
		
				$taxonomies = get_option('rpr_taxonomies', array());
		
		
				$name_lower = strtolower($name);
				$singular_lower = strtolower($singular);
		
				$tag_name = $singular_lower;
		
				if( $editing ) {
					$tag_name = $edit_tag_name;
				}
		
				$taxonomies[$tag_name] =
				array(
						'labels' => array(
								'name'                       => $name,
								'singular_name'              => $singular,
								'search_items'               => __( 'Search', $this->pluginName ) . ' ' . $name,
								'popular_items'              => __( 'Popular', $this->pluginName ) . ' ' . $name,
								'all_items'                  => __( 'All', $this->pluginName ) . ' ' . $name,
								'edit_item'                  => __( 'Edit', $this->pluginName ) . ' ' . $singular,
								'update_item'                => __( 'Update', $this->pluginName ) . ' ' . $singular,
								'add_new_item'               => __( 'Add New', $this->pluginName ) . ' ' . $singular,
								'new_item_name'              => __( 'New', $this->pluginName ) . ' ' . $singular . ' ' . __( 'Name', $this->pluginName ),
								'separate_items_with_commas' => __( 'Separate', $this->pluginName ) . ' ' . $name_lower . ' ' . __( 'with commas', $this->pluginName ),
								'add_or_remove_items'        => __( 'Add or remove', $this->pluginName ) . ' ' . $name_lower,
								'choose_from_most_used'      => __( 'Choose from the most used', $this->pluginName ) . ' ' . $name_lower,
								'not_found'                  => __( 'No', $this->pluginName ) . ' ' . $name_lower . ' ' . __( 'found.', $this->pluginName ),
								'menu_name'                  => $name
						),
						'show_ui' => true,
						'show_tagcloud' => true,
						'query_var' => true,
						'hierarchical' => true,
						'rewrite' => array(
								'slug' => $slug,
								'hierarchical' => true
						)
				);
				update_option('rpr_taxonomies', $taxonomies);
                $this->taxonomies=$taxonomies;
				return true;
			}
		}
		
		public function delete_taxonomy_form() {
			if ( !wp_verify_nonce( $_POST['delete_taxonomy_nonce'], 'delete_taxonomy' ) ) {
				die( 'Invalid nonce.' . var_export( $_POST, true ) );
			}
				
			$tag_name = $_POST['rpr_delete_taxonomy_name'];
				
			$this->delete_taxonomy( $tag_name );
				
			$this->rpr_taxonomies_init();
			update_option( 'rpr_flush', '1' );
				
			wp_redirect( $_SERVER['HTTP_REFERER'] );
			exit();
		}
		
		public function delete_taxonomy($tag_name) {
			global $wp_taxonomies;
			if( ! taxonomy_exists( $tag_name ) ) {
				die( 'This taxonomy does not exist.' );
			}
		
			// delete existing terms?
			$terms = get_terms($tag_name);
			
			foreach($terms as $term ):
				wp_delete_tag( $term->term_id, $tag_name);
			endforeach;
			
			$taxonomies = get_option('rpr_taxonomies', array());
			
			unset( $taxonomies[$tag_name] );
			update_option('rpr_taxonomies', $taxonomies);

			
			return true;
		}
		
	}
}
