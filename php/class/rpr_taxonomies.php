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
			add_action( 'admin_init', array( $this, 'rpr_taxonomies_settings' ) );
			add_action( 'admin_menu', array( $this, 'rpr_taxonomies_menu' ) );
			add_action( 'admin_action_delete_taxonomy', array( $this, 'delete_taxonomy_form' ) );
			add_action( 'admin_action_add_taxonomy', array( $this, 'save_taxonomy_form' ) );
		}
		


		public function rpr_taxonomies_settings()
		{
			add_submenu_page( null, __( 'Custom Taxonomies', $this->pluginName ), __( 'Manage Tags', $this->pluginName ), 'manage_options', 'rpr_taxonomies', array( $this, 'rpr_taxonomies_page' ) );
			add_settings_section( 'rpr_taxonomies_list_section', __('Manage Recipe Taxonomies', $this->pluginName ), array( $this, 'admin_menu_manage_taxonomies' ), 'rpr_taxonomies_settings' );
			//add_settings_section( 'rpr_taxonomies_settings_section', __('Add new / edit Recipe Taxonomy', $this->pluginName ), array( $this, 'admin_menu_settings_taxonomies' ), 'rpr_taxonomies_settings' );
		
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
				if( isset( $this->taxonomies['rpr_category'] ) ){
    				unset($this->taxonomies['rpr_category']);
    			}
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
			
			// add optional taxonomies:
/*
			if( $this->option( 'recipe_use_difficulty', 1) == '1')
			{
				$this->add_taxonomy(__('Difficulty', $this->pluginName), __('Difficulty', $this->pluginName), 'rpr_difficulty', 'rpr_difficulty');
    			update_option('rpr_taxonomies', $this->taxonomies);
			}
*/
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
		
		public function admin_menu_manage_taxonomies(){
			$out = '';
	
			// Form for deleting taxonomies
			$out .= '<form id="rpr_delete_taxonomy" method="POST" action="' . admin_url( 'admin.php' ) . '" onsubmit="return confirm(\''. __('Do you really want to delete this taxonomy?', $this->pluginName). '\');">';
			$out .= '<input type="hidden" name="action" value="delete_taxonomy">';
			$out .= wp_nonce_field( 'delete_taxonomy', 'delete_taxonomy_nonce', false );
			$out .= '<input type="hidden" id="rpr_delete_taxonomy_name" name="rpr_delete_taxonomy_name" value="">';
			$out .= '</form>';
			
			// Table with all existing recipe taxonomies
			$out .=  '<table id="rpr-tags-table" class="wp-list-table widefat" cellspacing="0">
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
						$out .= '<tr>
		                            <td><strong>' . $taxonomy->name . '</strong></td>
		                            <td class="singular-name">' . $taxonomy->labels->singular_name . '</td>
		                            <td class="name">' . $taxonomy->labels->name . '</td>
		                            <td class="slug">' . $taxonomy->rewrite['slug'] . '</td>
		                            <td>
		                                <span class="rpr_adding">
		                                <button type="button" id="rpr-edit-taxonomy" class="button rpr-edit-tag" data-tag="' . $taxonomy->name . '">'. __('Edit', $this->pluginName) .'</button>';
						if( ! in_array( $taxonomy->name, $this->nodeleteTaxonomies ) ) {
							$out .= '<button type="button" class="button rpr-delete-tag" data-tag="' . $taxonomy->name . '">'. __('Delete', $this->pluginName ) .'</button> ';
						}
						$out .= '		</span>
		                	     	</td>
		                    	 </tr>';
					}
				
				}
			}
				
			$out .= '</tbody>
		             </table>';
		    $out .= '<button type="button" id="rpr-add-taxonomy" class="button rpr-edit-tag button-primary" >'. __('Add Taxonomy', $this->pluginName) .'</button>';
			
			// Edit taxonomy dialog:
			$out .= '<div class="wp-dialog" title="'. __('Edit Taxonomy', $this->pluginName ) .'" id="rpr_manage_taxonomies_dialog">';
				
			$out .= '<form method="POST" action="' . admin_url( 'admin.php' ) . '" id="rpr_manage_taxonomies_dialog_form">
		            	<input type="hidden" name="action" value="add_taxonomy">
		                <input type="hidden" id="rpr_edit_tag_name" name="rpr_edit" value="">';
			$out .= wp_nonce_field( 'add_taxonomy', 'add_taxonomy_nonce', false );
				
			$out .= '<div id="rpr_editing" class="rpr_editing">'.__( 'Currently editing tag: ', $this->pluginName ).'<span id="rpr_editing_tag"></span></div>';
			$out .= '<table class="form-table"><tbody>';
				
			// Name
			$out .=	'<tr valign="top">
		            	<th scope="row">'.__( 'Name', $this->pluginName ).'</th>
		                <td>
		                	<input type="text" id="rpr_custom_taxonomy_name" name="rpr_custom_taxonomy_name" />
		                    <label for="rpr_custom_taxonomy_name"> '  . __('(e.g. Courses)', $this->pluginName ) . '</label>
		                </td>
		             </tr>';
				
			// Singular name
			$out .= '<tr valign="top">
		            	<th scope="row">'.__( 'Singular Name', $this->pluginName ).'</th>
		                <td>
		                	<input type="text" id="rpr_custom_taxonomy_singular_name" name="rpr_custom_taxonomy_singular_name" />
		                    <label for="rpr_custom_taxonomy_singular_name"> '  . __('(e.g. Course)', $this->pluginName ) . '</label>
		                </td>
		            </tr>';
				
			// Slug
			$out .= '<tr valign="top">
		            	<th scope="row">'.__( 'Slug', $this->pluginName ).'</th>
		                <td>
		                	<input type="text" id="rpr_custom_taxonomy_slug" name="rpr_custom_taxonomy_slug" />
		                    <label for="rpr_custom_taxonomy_slug"> '  . __('(e.g. http://www.yourwebsite.com/course/)', $this->pluginName ) . '</label>
		                </td>
		            </tr>';
				
			$out .= '</tbody></table><br/>';
			$out .= '</form>';
			$out .= '</div>';
			
			echo $out;
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
								'search_items'               => sprintf( __( 'Search %s', $this->pluginName ), $name ),
								'popular_items'              => sprintf( __( 'Popular %s', $this->pluginName ), $name ),
								'all_items'                  => sprintf( __( 'All %s', $this->pluginName ), $name ),
								'edit_item'                  => sprintf( __( 'Edit %s', $this->pluginName ), $singular ),
								'update_item'                => sprintf( __( 'Update %s', $this->pluginName ), $singular ),
								'add_new_item'               => sprintf( __( 'Add New %s', $this->pluginName ), $singular ),
								'new_item_name'              => sprintf( __( 'New %s Name', $this->pluginName ), $singular ),
								'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', $this->pluginName ), $name_lower ),
								'add_or_remove_items'        => sprintf( __( 'Add or remove %s', $this->pluginName ), $name_lower ),
								'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', $this->pluginName ), $name_lower ),
								'not_found'                  => sprintf( __( 'No %s found.', $this->pluginName ), $name_lower ),
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
