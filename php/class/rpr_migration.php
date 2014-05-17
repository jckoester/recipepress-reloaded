<?php
/*
 * RPR class for migrating especially from Recipe Press and RPR < 0.5
*/

if( class_exists( 'RPReloaded' ) ) {
	
	class RPR_Migration extends RPR_Core {
	
		public function __construct( $pluginName = '', $pluginDir = '', $pluginUrl = '' ) {

			$this->pluginName = $pluginName;
			$this->pluginDir = $pluginDir;
			$this->pluginUrl = $pluginUrl;
			
			add_action('admin_init', array(&$this, 'rpr_check_migration'));
			add_action('admin_init', array(&$this, 'rpr_do_migration'));
			add_action('admin_notices', array( $this, 'rpr_admin_notice_migrate' ));
		}
		
		
		public function rpr_check_migration() {
			
			$version_updated=$this->get_version_updated();
			
			// Check if the version we've laste updated to is current:
			if ( get_option( 'rpr_version_updated' ) === get_option( 'rpr_version' ) ) {
				update_option( 'rpr_update_needed' , 0);
			} else {
				//check if old rpr or rp is installed
				if( get_option( 'rpr_version_updated' ) == "0.3.0" || get_option( 'rpr_version_updated' ) == 'RecipePress' ){
					update_option( 'rpr_update_needed' , 1);
				}
			}
			
		}
		public function rpr_admin_notice_migrate(){
			if( get_option( 'rpr_update_needed' ) == '1' ) {
				echo '<div class="updated"><p>';
				printf(__('The Recipe Press Reloaded database needs to upgraded. Make sure you have a backup before you proceed. | <a href="%1$s">Proceed</a>', $this->pluginName), '?post_type=rpr_recipe&rpr_do_migration=1');
				echo "</p></div>";
			}
		}
		
		public function rpr_do_migration() {
			$version_updated=$this->get_version_updated();
			
			if( isset($_GET['rpr_do_migration']) && $_GET['rpr_do_migration'] == '1' ){

				if( $version_updated == '0.3.0' || $version_updated == 'RecipePress' ){
					$this->migrate_recipepress_3();
				}
				
				// Set version to current version
				update_option( 'rpr_version_updated', get_option( 'rpr_version' ) );
				//var_dump(get_option('rpr_version_updated'));
				
				update_option( 'rpr_flush', '1' );
				
				wp_redirect( $_SERVER['HTTP_REFERER'] );
				exit();
			}						
		}
		
		private function get_version_updated(){
            // Check the version we've laste updated to:
			$version_updated = (get_option( 'rpr_version_updated' ));
			
			// fix if necessary:
			if( ! $version_updated ){
				// Check, if RPR < 0.5:
				if( is_array( get_option( 'rpr_options' ) ) ) {
					$version_updated = '0.3.0';
					update_option( 'rpr_version_updated', '0.3.0' );
			
					//Check if RecipePress
				} elseif( is_array( get_option( 'recipe-press-options' ) ) ) {
					$version_updated = 'RecipePress';
					update_option( 'rpr_version_updated', 'RecipePress' );
				}
			}
			return $version_updated;
		}
		//////////////////////// Migration from RecipePress and Recipe Press Reloaded < 0.5 ///////////////
		public function migrate_recipepress_3() {
			// 1.) Register old posttype and taxonomies:
			$this->setup_post_type();
			$this->setup_ingredients();
			$this->setup_oldtaxonomies();

			// 2.) get all old recipes and walk through
			$recipes = $this->get_old_recipes();
			
			foreach( $recipes as $recipe ) {
				// a) change post type
				set_post_type( $recipe->ID, 'rpr_recipe' );
				// b) move post_meta:
				$content_old=$recipe->post_content; //=> Instructions!
				$fields = $this->recipes_fields();
				
				foreach ( $fields as $field ) {
					$old = get_post_meta( $recipe->ID, $field, true );
					$new = "";
					
					// cases:
					if ( $field == 'rpr_recipe_description' ){
						$new = $recipe->post_excerpt;
					} elseif ( $field == 'rpr_recipe_featured' ) {
						$new = get_post_meta( $recipe->ID, '_recipe_featured_value', true );
					} elseif ( $field == 'rpr_recipe_rating' ) {
						$new="";
					} elseif ( $field == 'rpr_recipe_servings' ) {
						$new = get_post_meta( $recipe->ID, '_recipe_servings_value', true );
					} elseif ($field == 'rpr_recipe_servings_type') {
						$serving_size = get_post_meta( $recipe->ID, '_recipe_serving_size_value', true );
						$sizeterm = get_term_by('id', $serving_size, 'recipe-serving');
						if( is_object($sizeterm) ) {
							$new = $sizeterm->name;
						} else {
							$new="";
						}
					} elseif ( $field == 'rpr_recipe_prep_time' ) {
						$preptime = get_post_meta( $recipe->ID, '_recipe_prep_time_value', true );
						$new=$preptime;
					} elseif ( $field == 'rpr_recipe_cook_time' ) {
						$cooktime = get_post_meta( $recipe->ID, '_recipe_prep_time_value', true );
						$new = $cooktime;
					} elseif ($field == 'rpr_recipe_passive_time') {
						$readytime = get_post_meta( $recipe->ID, '_recipe_ready_time_value', true );
						$idletime=$readytime-($cooktime+$preptime);
						if($idletime!=0){
							$new = idletime;
						} else {
							$new="";
						}
					// Ingredients:
					} elseif ($field == 'rpr_recipe_ingredients') {
						$ingredients = array();
						$non_empty_ingredients = array();
						$ings = $this->get_ingredients($recipe);
				
						foreach( $ings as $ing ) {
							$ingredient = array();
					
							$ingterm = get_term_by('id', $ing['item'], 'recipe-ingredient');
							if( is_object($ingterm)){
								$ingredient['ingredient'] = $ingterm->name;
							}else{
								$ingredient['ingredient'] = "";
							}
							
							if( isset($ing['quantity'])) {
								if ($ing['quantity']==0){
									$ingredient['amount']="";
								}else{
									$ingredient['amount']=$ing['quantity'];
								}
							} else {
								$ingredient['amount']="";
							}
								
							$sizeterm = get_term_by('id', $ing['size'], 'recipe-size');
							//var_dump($sizeterm);
							
							if(is_object($sizeterm)){
								$ingredient['unit'] = $sizeterm->name;
							} else {
								$ingredient['unit'] = "";
							}
							
							if( isset($ing['notes']) ){
								$ingredient['notes']=$ing['notes'];
							}else{
								$ingredient['notes']="";
							}
					
							if( isset($ing['page-link']) ){
								$ingredient['link']=$ing['page-link'];
							}
						
							if( isset($ing['url']) ) {
								$ingredient['link']=$ing['url'];
							}
					
							if( isset($ingredient['ingredient']) ) {
								$term = term_exists($ingredient['ingredient'], 'rpr_ingredient');
								
								if ( ($term === 0 || $term === null) && isset($ingredient['ingredient']) && $ingredient['ingredient'] !="" ) {
									$term = wp_insert_term($ingredient['ingredient'], 'rpr_ingredient');
								}
						
								$term_id = intval($term['term_id']);
						
								$ingredient['ingredient_id'] = $term_id;
								$ingredients[] = $term_id;
						
								$non_empty_ingredients[] = $ingredient;
							}
						}
						wp_set_post_terms( $recipe->ID, $ingredients, 'rpr_ingredient' );
						$new = $non_empty_ingredients;
					
					// Instructions:
					} elseif ( $field == 'rpr_recipe_instructions' ) {
						$instr=explode("\n", preg_replace(array("<!--:de-->", "<!--:-->"), array("",""), $content_old));
					
						foreach( $instr as $instruction ) {
							$new[]['description']=$instruction;
						}
					
						$non_empty_instructions = array();
					
						foreach( $new as $instruction ) {
							if( ( isset($instruction['description']) && $instruction['description'] != "" )|| isset($instruction['image']) ) {
								$non_empty_instructions[] = $instruction;
							}
						}
					
						$new = $non_empty_instructions;
					// notes
					} elseif ($field == 'rpr_recipe_notes') {
						$new = get_post_meta( $recipe->ID, $field, true );
					}
					
					//echo $recipe->ID;
					// Update or delete meta data if changed
					update_post_meta( $recipe->ID, $field, $new );
				}
				//MISSING: delete old post_meta
				
				// RecipePress Taxonomies:
				$options = get_option('recipe-press-options');
				$taxonomies = $options['taxonomies'];
				
				foreach ( $taxonomies as $taxonomy ) {
					$this->recipe_migrate_term($recipe->ID, $taxonomy['slug']);
				}
				
						
			//endforeach	
			}
			//MISSING: delete old taxonomies:
			//require_once 'rpr_taxonomies.php';
			//$this->taxo = new RPR_Taxonomies($this->pluginName, $this->plugunDir, $this->pluginUrl );
			//$this->taxo->delete_taxonomy( $taxonomy['slug'] );
			
			//MISSING: migrate settings?
			delete_option('recipe-press-options');
			
			return true;
		}
				
		function get_old_recipes($orderby = 'date', $order = 'DESC', $taxonomy = '', $term = '', $limit = -1, $author = '') {
			$args = array(
					'post_type' => 'recipe',
					//'post_status' => 'publish',
					'posts_per_page' => -1,
			);
		
			$query = new WP_Query( $args );
			$recipes = array();
		
			if( $query->have_posts() ) { //recipes found
		
				while( $query->have_posts() ) {
					$query->the_post();
					global $post;
					$recipes[] = $post;
				}
			}
		
			if( $orderby == 'post_title' || $orderby == 'title' || $orderby == 'name' ) {
				usort($recipes, array($this, "compare_post_titles"));
		
				if( $order == 'DESC' ) {
					$recipes = array_reverse($recipes);
				}
			}
		
			return $recipes;
		}
		
		
		function get_ingredients($post = NULL) {
			if ( !$post ) {
				global $post;
			}
		
			$ingredients = get_post_meta($post->ID, '_recipe_ingredient_value');
		
			if ( count($ingredients) < 1 ) {
				return $this->emptyIngredients($this->rpr_options['ingredients_fields']);
			} else {
				$ings = array();
		
				$defaults = array(
						'quantity' => NULL,
						'size' => 0,
						'item' => 0,
						'notes' => NULL,
						'page-link' => NULL,
						'url' => NULL,
						'order' => 0
				);
		
		
				foreach ( $ingredients as $ingredient ) {
					$ings[$ingredient['order']] = $ingredient;
					wp_parse_args($ings[$ingredient['order']], $defaults);
				}
		
		
				ksort($ings);
				return $ings;
			}
		}
		
		function recipe_migrate_term($recipe_id, $slug)
		{
			global $wp_taxonomies;
			//echo $slug;
			$oldterms = get_the_terms( $recipe_id, $slug);
		
		
			// Find new slug:
			if( $slug == 'recipe-category' ):
				// Use WP Catories and Tags or recipe specific?
				if( $this->option('recipe_tags_use_wp_categories', 1 ) == '1' ):
					//get_option('rpr_option')['recipe_tags_use_wp_categories'] == true):
					$new_slug = 'post_category';
				else:
					$new_slug = 'rpr_category';
				endif;
			elseif( $slug == 'recipe-tags' ):
				// Use WP Catories and Tags or recipe specific?
				if( $this->option( 'recipe_tags_use_wp_tags', 1 ) == '1' ):
					//get_option('rpr_option')['recipe_tags_use_wp_tags'] == true):
					$new_slug = 'post_tag';
				else:
					$new_slug = 'rpr_tag';
				endif;
			else:
				// create custom taxonomies if necessary
				$new_slug = preg_replace( '/recipe-/', "rpr_", $slug);
				if ( !taxonomy_exists($new_slug) ):
					$taxonomy = get_taxonomy( $slug );
					$this->add_taxonomy( $taxonomy->name, $taxonomy->singular, $new_slug, $new_slug );
				endif;
			endif;
		
			if( taxonomy_exists($new_slug) == true && is_array( $oldterms ) ):
				//echo $new_slug;
				$new_terms = array();
		
				foreach ( $oldterms as $oldterm ):
					// Check if term already exists in new tax:
					$term = term_exists($oldterm->name, $new_slug);
					
					// Create term if necessary:
					if ( $term === 0 || $term === null) {
						$term = wp_insert_term($oldterm->name, $new_slug);
					}
		
					$term_id = intval($term['term_id']);
					array_push( $new_terms, $term_id );
		
				endforeach;
		
				// add terms to recipe
				wp_set_post_terms( $recipe_id, $new_terms, $new_slug, true );
				return true;
			else:
				return false;
			endif;
		}
		
		///////////////////////////////////////// OLD STUFF ///////////////////////////////////////////////
		// to separate class? rpr_migrate
		/* Register old txonomies and post types :*/
		
		function setup_post_type() {
		
			global $wp_version;
		
			$page = get_page(RPReloaded::get_option('display_page'));
			$labels = array(
					'name' => RPReloaded::get_option('plural_name'),
			);
		
			$args = array(
					'labels' => $labels,
					'public' => true,
					'publicly_queryable' => true,
					'show_ui' => true,
					'query_var' => true,
					'capability_type' => 'page',
					'hierarchical' => false,
					'menu_position' => (int) RPReloaded::get_option('menu_position'),
					'menu_icon' => RPReloaded::get_option('menu_icon'),
					'supports' => array('title', 'editor', 'author', 'excerpt',
							'page-attributes', 'custom-fields', 'thumbnail',
							'comments', 'trackbacks', 'revisions', 'post_tag',
					'category'),
					'rewrite' => true
			);
		
			register_post_type('recipe', $args);
		}
		
		function setup_ingredients() {
		
			$labels = array(
					'name' => __('Ingredients', 'recipe-press-reloaded'),
			);
			$args = array(
					'hierarchical' => false,
					'label' => __('Ingredients', 'recipe-press-reloaded'),
					'labels' => $labels,
					'public' => true,
					'show_ui' => true,
					'capabilities' => array(
							'assign_terms' => false
					),
					'rewrite' => array('slug' => 'ingredient'),
			);
			register_taxonomy('recipe-ingredient', 'recipe', $args);
			//$this->taxonomy_rewrite_rules('recipe-ingredient', array('slug' => $this->rpr_options['ingredient_slug']));
		
			return true;
		}
		
		function setup_oldtaxonomies() {
			$taxlist = array(
					/*array(
					 'slug' => 'recipe-ingredient',
							'singular' => 'Zutat',
							'plural' => 'Zutaten',
							'per-page' => 10,
							'default' => -1,
							'hierarchical' => 1,
							'active' => 1,
					),*/
					array(
							'slug' => 'recipe-size',
							'singular' => 'Größe',
							'plural' => 'Größen',
							'per-page' => 10,
							'default' => -1,
							'hierarchical' => 1,
							'active' => 1,
					),
					array(
							'slug' => 'recipe-serving',
							'singular' => 'Portion',
							'plural' => 'Portionen',
							'per-page' => 10,
							'default' => -1,
							'hierarchical' => 1,
							'active' => 1,
					),
			);
				
			// Recipe Press Options:
			$options = get_option ( 'recipe-press-options' );
			if (is_array ( $options ['taxonomies'] )) {
				$taxlist = array_merge ( $options ['taxonomies'], $taxlist );
			}
				
			// RPROptions fehlen!!! ENTSCHEIDUNG AUCH !!!
			foreach ( $taxlist as $taxonomy ) {
				
				$labels = array (
						'name' => __ ( $taxonomy ['singular'], 'recipe-press-reloaded' ) 
				);
				$args = array (
						'hierarchical' => false,
						'label' => __ ( $taxonomy ['plural'], 'recipe-press-reloaded' ),
						'labels' => $labels,
						'public' => true,
						'show_ui' => true,
						'capabilities' => array (
								'assign_terms' => false 
						),
						'rewrite' => array (
								'slug' => $taxonomy ['slug'] 
						) 
				);
				register_taxonomy ( $taxonomy ['slug'], 'recipe', $args );
			}
			//$this->taxonomy_rewrite_rules('recipe-ingredient', array('slug' => $this->rpr_options['ingredient_slug']));
		
			return true;
		}
		
	}
}
