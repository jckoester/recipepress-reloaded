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
			global $rpr_option;
			global $rpr_reduxConfig;
			
			$this->taxonomies = get_option('rpr_taxonomies', array());
			$tax_opts = $rpr_option['taxonomies'];
			// Check if there are any taxonomies to restore:
			
			if( count($rpr_option['restore_taxonomies']) > 0){
				// merge existing taxonomies with taxonomies to restore
				$options = $rpr_option['restore_taxonomies'];
				//var_dump($options);
				foreach( $options as $tax=>$value){
					//var_dump($tax);
					if( $rpr_option['restore_taxonomies'][$tax]==1){
						$this->taxonomies = $this->add_taxonomy_to_array($this->taxonomies, $tax, $tax, $tax, false);
						$options[$tax] = 0;	
					}
					
				}
			  	update_option('rpr_taxonomies', $this->taxonomies);
			 	// reset the restore list
			  	$rpr_reduxConfig->ReduxFramework->set('restore_taxonomies', $options );
				$rpr_reduxConfig->ReduxFramework->set('restore_taxonomies_switch', 0 );
			}
			
			$this->taxonomies = get_option('rpr_taxonomies', array());
			
			// Restore taxonomies if active but deleted:
			if( !isset($this->taxonomies['rpr_ingredient']) ){
				$this->taxonomies = $this->add_taxonomy_to_array($this->taxonomies, 'rpr_ingredient', __( 'Ingredients', $this->pluginName ), __( 'Ingredient', $this->pluginName ), true);
			}
			if( $rpr_option['taxonomies']['rpr_category'] == '1' && !isset($this->taxonomies['rpr_category']) ){
				$this->taxonomies = $this->add_taxonomy_to_array($this->taxonomies, 'rpr_category', __( 'RPR Categories', $this->pluginName ), __( 'RPR Category', $this->pluginName ), true);
			}
			if( $rpr_option['taxonomies']['rpr_tag'] == '1' && !isset($this->taxonomies['rpr_tag']) ){
				$this->taxonomies = $this->add_taxonomy_to_array($this->taxonomies, 'rpr_tag', __( 'RPR Tags', $this->pluginName ), __( 'RPR Tag', $this->pluginName ), false);
			}
			if( $rpr_option['taxonomies']['rpr_course'] == '1' && !isset($this->taxonomies['rpr_course']) ){
				$this->taxonomies = $this->add_taxonomy_to_array($this->taxonomies, 'rpr_course', __( 'Courses', $this->pluginName ), __( 'Course', $this->pluginName ), true);
			}
			if( $rpr_option['taxonomies']['rpr_cuisine'] == '1' && !isset($this->taxonomies['rpr_cuisine']) ){
				$this->taxonomies = $this->add_taxonomy_to_array($this->taxonomies, 'rpr_cuisine', __( 'Cuisines', $this->pluginName ), __( 'Cuisine', $this->pluginName ), true);
			}
			if( $rpr_option['taxonomies']['rpr_season'] == '1' && !isset($this->taxonomies['rpr_season']) ){
				$this->taxonomies = $this->add_taxonomy_to_array($this->taxonomies, 'rpr_season', __( 'Seasons', $this->pluginName ), __( 'Season', $this->pluginName ));
			}
			if( $rpr_option['taxonomies']['rpr_difficulty'] == '1' && !isset($this->taxonomies['rpr_difficulty']) ){
				$this->taxonomies = $this->add_taxonomy_to_array($this->taxonomies, 'rpr_difficulty', __( 'Difficulties', $this->pluginName ), __( 'Difficulty', $this->pluginName ));
				}
			
            // register taxonomies:
			foreach($this->taxonomies as $name => $options) {
				if( $name != 'rpr_ingredient' ){
					// Check if taxonomy is enabled:
					if( (isset($rpr_option['taxonomies'][$name]) && $rpr_option['taxonomies'][$name] == 1) || !isset($rpr_option['taxonomies'][$name]) ){
						register_taxonomy(
							$name,
							'rpr_recipe',
							$options
						);
				
						register_taxonomy_for_object_type( $name, 'rpr_recipe' );
					}
				} else {
					register_taxonomy(
							$name,
							'rpr_recipe',
							$options
					);
				
					register_taxonomy_for_object_type( $name, 'rpr_recipe' );
				}
			}

			// Register WP Categories if necessary:
			if( $rpr_option['taxonomies']['category'] == 1 ){
				register_taxonomy_for_object_type( 'category', 'rpr_recipe' );
			}
			// Register WP Tags if necessary:
			if( $rpr_option['taxonomies']['post_tag'] == 1 ){
				register_taxonomy_for_object_type( 'post_tag', 'rpr_recipe' );
			}
			/*// Register RPR Tags if necessary:
			if( $rpr_option['taxonomies']['rpr_tag'] == 1 ){
	    		//$this->taxonomies = $this->add_taxonomy_to_array($this->taxonomies, 'rpr_tag', __( 'RPR Tags', $this->pluginName ), __( 'RPR Tag', $this->pluginName ), false);
	    		
	    		//update_option('rpr_taxonomies', $this->taxonomies);
	    		//update_option( 'rpr_flush', '1' );
				
				register_taxonomy_for_object_type( 'rpr_tag', 'rpr_recipe' );
			}*/
			update_option('rpr_taxonomies', $this->taxonomies);
	    	//update_option( 'rpr_flush', '1' );
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
		                        <th scope="col" id="hierarchical" class="manage-column">
		                        	'.__( 'Hierarchical', $this->pluginName ).'
		                        </th>
		                        <th scope="col" id="hierarchical-value" class="manage-column hidden">
		                        	'.__( 'True/False value, usually hidden', $this->pluginName) .'
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
						$hierarchy_string ='<i class="fa fa-times"/>';
						$hierarchy_value = false;
						if($taxonomy->hierarchical){
							$hierarchy_string =  '<i class="fa fa-check"/>'; 
							$hierarchy_value = true;
						}
						$out .= '<tr>
		                            <td><strong>' . $taxonomy->name . '</strong></td>
		                            <td class="singular-name">' . $taxonomy->labels->singular_name . '</td>
		                            <td class="name">' . $taxonomy->labels->name . '</td>
		                            <td class="slug">' . $taxonomy->rewrite['slug'] . '</td>
		                            <td class="hierarchical">' . $hierarchy_string . '</td>
		                            <td class="hierarchical-value hidden">' . $hierarchy_value . '</td>
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
			// Hierarchy, only for fully editable taxonomies:
			$out .= '<tr valign="top" id="hierarchy_row">
		            	<th scope="row">'.__( 'Hierarchical', $this->pluginName ).'</th>
		                <td>
		                	<input type="checkbox" value="true" id="rpr_custom_taxonomy_hierarchical" name="rpr_custom_taxonomy_hierarchical" />
		                    <label for="rpr_custom_taxonomy_hierarchical"> '  . __('Set to yes for a category style taxonomy and to no for a tag like taxonomy.', $this->pluginName ) . '</label>
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
			$hierarchical = $_POST['rpr_custom_taxonomy_hierarchical'];
			
			var_dump($hierarchical);
			$edit_tag_name = $_POST['rpr_edit'];
			
			$this->add_taxonomy($name, $singular, $slug, $hierarchical, $edit_tag_name);
			
			$this->rpr_taxonomies_init();
			update_option( 'rpr_flush', '1' );
			
			wp_redirect( $_SERVER['HTTP_REFERER'] ); 
			die;
			exit();
			
		}
		
		public function add_taxonomy($name, $singular, $slug, $hierarchical, $edit_tag_name) {
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
						'hierarchical' => $hierarchical,
						'rewrite' => array(
								'slug' => $slug,
								'hierarchical' => $hierarchical
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
		
		/*
	 	* ACTIVATE TAXONOMIES
	 	* called in the register_activation_hook in recipe-press-reloaded.php
	 	*/
	 
		public function activate_taxonomies()
    	{
    		$this->recipes_init();
    		$this->rpr_custom_taxonomies_init();
    
   		 	update_option( 'rpr_flush', '1' );
    	}

		function rpr_custom_taxonomies_init()
	    {
	    	$taxonomies = get_option('rpr_taxonomies', array() );
	
	    	if(count($taxonomies) == 0)
	    	{
	    		$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_category', __( 'RPR Categories', $this->pluginName ), __( 'RPR Category', $this->pluginName ), true);
	    		$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_tag', __( 'RPR Tags', $this->pluginName ), __( 'RPR Tag', $this->pluginName ), false);
	    		$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_ingredient', __( 'Ingredients', $this->pluginName ), __( 'Ingredient', $this->pluginName ), true);
	    		$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_course', __( 'Courses', $this->pluginName ), __( 'Course', $this->pluginName ), true);
	    		$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_cuisine', __( 'Cuisines', $this->pluginName ), __( 'Cuisine', $this->pluginName ), true);
				$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_season', __( 'Seasons', $this->pluginName ), __( 'Season', $this->pluginName ));
				$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_difficulty', __( 'Difficulties', $this->pluginName ), __( 'Difficulty', $this->pluginName ));
				
	    		update_option('rpr_taxonomies', $taxonomies);
	    		update_option( 'rpr_flush', '1' );
	    	}
	    }
    
	    public function add_taxonomy_to_array($arr, $tag, $name, $singular, $hierarchical=false)
	    {
	    	$name_lower = strtolower($name);
	    	$singular_lower = strtolower($singular);
	    
	    	$arr[$tag] =
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
	    			'hierarchical' => $hierarchical,
	    			'rewrite' => array(
	    					'slug' => $singular_lower
	    			)
	    	);
	    
	    	return $arr;
	    }
		
	}
}
