<?php

class RPR_Core extends RPReloaded {

 public function __construct( $pluginName, $pluginDir, $pluginUrl )
    {

        $this->pluginName = $pluginName;
        $this->pluginDir = $pluginDir;
        $this->pluginUrl = $pluginUrl;

        
        // Actions
//???       add_action( 'init', array( $this, 'check_theme_support' ), 20 );
        add_action( 'init', array( $this, 'recipes_init' ), 1);
//???activation!        add_action( 'init', array( $this, 'rpr_custom_taxonomies_init' ));
        
        //if migration necessary!!
        require_once 'rpr_migration.php';
        $this->mig = new RPR_Migration( $this->pluginName, $this->pluginDir, $this->pluginUrl );
        
        // setup taxonomies:
        require_once('rpr_taxonomies.php');
        $this->tax = new RPR_Taxonomies($this->pluginName, $this->pluginDir, $this->pluginUrl);  
        
        add_action( 'init', array( $this, 'ratings_init' ));
        //add image sizes
        add_filter( 'image_size_names_choose', array( $this, 'rpr_image_sizes' ) );
        add_action( 'init', array( $this, 'rpr_add_image_sizes' ));
        
        add_action( 'wp_enqueue_scripts', array( $this, 'public_plugin_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'public_plugin_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_plugin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_plugin_scripts' ) );
        add_action( 'do_meta_boxes', array( $this, 'rpr_metabox_init' ));
        add_action( 'vp_option_set_after_save', array( $this, 'set_flush_needed' ) );
        add_action( 'admin_init', array( $this, 'flush_permalinks_if_needed' ));
        add_action( 'save_post', array( $this, 'recipes_save' ), 10, 2 );
        add_action( 'pre_get_posts', array( $this, 'query_recipes' ) );

        // Filters
        add_filter( 'the_content', array( $this, 'recipes_content' ), 10 );
        add_filter( 'get_the_excerpt', array( $this, 'recipes_excerpt' ), 10 );
//        add_filter( 'the_excerpt', array( $this, 'recipes_excerpt' ), 10 );
        // Add filters for author, category tag dependent on display settings!
        if( $this->option( 'recipe_display_author_in_recipe', '1') === '1') {
            add_filter( 'get_the_author', array( $this, 'recipes_author' ), 10 );
            add_filter( 'get_the_author_link', array( $this, 'recipes_author' ), 10 );
        }
        if( $this->option( 'recipe_display_category_in_recipe', '1') === '1') {
            add_filter( 'get_the_category_list', array( $this, 'recipes_category_list' ), 10 );
        }
        if( $this->option( 'recipe_display_date_in_recipe', '1') === '1') {
            add_filter( 'get_the_date', array( $this, 'recipes_date' ), 10 );
        }
        if( $this->option( 'recipe_display_tags_in_recipe', '1') === '1') {
            add_filter( 'get_the_tag_list', array( $this, 'recipes_tag_list' ), 10 );
        }
        //make the recipe table nicer:
        add_filter('manage_rpr_recipe_posts_columns', array( $this, 'rpr_recipe_table_head') );
        add_action( 'manage_rpr_recipe_posts_custom_column', array( $this, 'rpr_recipe_table_content') , 10, 2 );
                

        // Shortcodes
        add_shortcode("rpr-recipe", array( $this, 'recipes_shortcode' ));
        add_shortcode("rpr-recipe-index", array( $this, 'recipes_index_shortcode' ));
        add_shortcode( "rpr-tax-list", array( $this, 'recipes_taxlist_shortcode' ));

        // Other
//        $this->add_link_to_ingredients();
    }
    
    /*
     * //////////////////////////////////////// General & Inits ///////////////////////////////////////
     */
    public function public_plugin_styles()
    {
        wp_register_style( 'rpr_fa', $this->pluginUrl . '/css/font-awesome.min.css');
        wp_register_style( 'rpr_pub', $this->pluginUrl . '/templates/' . $this->option( 'rpr_template', 'rpr_default') . '/public.css');
        wp_register_style( 'rpr_pub_prn', $this->pluginUrl . '/templates/' . $this->option( 'rpr_template', 'rpr_default') . '/print.css', '', RPR_VERSION, 'print');
        wp_enqueue_style( 'rpr_fa' );    	
        wp_enqueue_style( 'rpr_pub' );
        wp_enqueue_style( 'rpr_pub_prn' );
    }

    public function public_plugin_scripts( $hook )
    {
    	wp_register_script( $this->pluginName, $this->pluginUrl . '/js/rpr_public.js', array('jquery'), RPR_VERSION );
    	wp_register_script( 'jquery-print', $this->pluginUrl . '/js/rpr_print.js', array('jquery'), RPR_VERSION );
    	wp_enqueue_script( $this->pluginName );
    	wp_enqueue_script( 'jquery-print' );
    }

    public function admin_plugin_styles()
    {
    	wp_register_style( 'rpr_fa', $this->pluginUrl . '/css/font-awesome.min.css');
        wp_register_style( 'rpr_adm', $this->pluginUrl . '/css/rpr_admin.css', '', RPR_VERSION );
        wp_enqueue_style( 'rpr_fa' );
        wp_enqueue_style( 'rpr_adm' );
    }

    public function admin_plugin_scripts( $hook )
    {
        if( 'post-new.php' != $hook && 'post.php' != $hook && isset($_GET['post_type']) && 'rpr_recipe' != $_GET['post_type'] ) {
            return;
        } else {
            wp_register_script( $this->pluginName, $this->pluginUrl . '/js/rpr_admin.js', array('jquery', 'jquery-ui-sortable', 'suggest', 'wp-color-picker' ), RPR_VERSION );
            wp_enqueue_script( $this->pluginName );
        }
    }

    public function set_flush_needed()
    {
        update_option( 'wpurp_flush', '1' );
    }

    /*
     * Flush permalinks when settings were updated
    * or if option didn't exist before (first install)
    */
    public function flush_permalinks_if_needed()
    {
        if( get_option( 'wpurp_flush', '1' ) === '1' ) {
            flush_rewrite_rules();
            update_option( 'wpurp_flush', '0' );
        }
    }

    /*
     * Add image sizes for RPR
     * Sizes should be adjustable in settings!
     */
    public function rpr_add_image_sizes(){
    	// Thumb size for recipes table
    	add_image_size( 'rpr-table-thumb', 50, 50, true ); //(cropped)
    }
    public function rpr_image_sizes( $sizes ) {
    	return array_merge( $sizes, array(
    			'rpr-table-thumb' => __('RPR table thumbnail', $this->pluginName ),
    	) );
    }
    /*
     * //////////////////////////////////////////// RECIPES ///////////////////////////////////////////
    */
    
    public function recipes_init()
    {
    	$slug = $this->option('recipe_slug', 'recipe');
    
    	$name = __( 'Recipes', $this->pluginName );
    	$singular = __( 'Recipe', $this->pluginName );
    
    	$taxonomies = array( '' );
    	
    	if($this->option('recipe_tags_use_wp_categories', '1') == '1') {
    		array_push( $taxonomies, 'category' );
    	}
    	if($this->option('recipe_tags_use_wp_tags', '1') == '1') {
    		array_push( $taxonomies, 'post_tag' );
    	}
    	 
    	register_post_type( 'rpr_recipe',
	    	array(
		    	'labels' => array(
			    	'name' => $name,
			    	'singular_name' => $singular,
			    	'add_new' => __( 'Add New', $this->pluginName ),
			    	'add_new_item' => __( 'Add New', $this->pluginName ) . ' ' . $singular,
			    	'edit' => __( 'Edit', $this->pluginName ),
			    	'edit_item' => __( 'Edit', $this->pluginName ) . ' ' . $singular,
			    	'new_item' => __( 'New', $this->pluginName ) . ' ' . $singular,
			    	'view' => __( 'View', $this->pluginName ),
			    	'view_item' => __( 'View', $this->pluginName ) . ' ' . $singular,
			    	'search_items' => __( 'Search', $this->pluginName ) . ' ' . $name,
			    	'not_found' => __( 'No', $this->pluginName ) . ' ' . $name . ' ' . __( 'found.', $this->pluginName ),
			    	'not_found_in_trash' => __( 'No', $this->pluginName ) . ' ' . $name . ' ' . __( 'found in trash.', $this->pluginName ),
			    	'parent' => __( 'Parent', $this->pluginName ) . ' ' . $singular,
			    	),
		    	'public' => true,
		    	'menu_position' => 5,
		    	'supports' => array( 'title', 'thumbnail', 'comments' , 'author'),
		    	'taxonomies' => $taxonomies,
		    	'menu_icon' =>  $this->pluginUrl . '/img/icon_16.png',
		    	'has_archive' => true,
		    	'rewrite' => array(
			    	'slug' => $slug
			    	)
	    	)
		);
    }

    function query_recipes($query) {

        //if($this->option('recipe_as_posts', '1') == '1')
        //{
            // Hide recipes in admin posts overview when enabled
            //if( $this->option('show_recipes_in_posts', '1') != '1' ){
                global $pagenow;

                if( $pagenow == 'edit.php' ) {
                    return;
                }
            //}

            // Querying specific page (not set as home/posts page) or attachment
            if(!$query->is_home()) {
               if($query->get('page_id') !== 0 || $query->get('pagename') !== '' || $query->get('attachment_id') !== 0) {
                    return;
                }
            }

                // Querying a specific taxonomy
            if( is_object($query->tax_query) ){
                $tax_queries = $query->tax_query->queries;
                $recipe_taxonomies = get_object_taxonomies( 'rpr_recipe' );

                if(is_array($tax_queries)) {
                    foreach($tax_queries as $tax_query)
                    {
                        if(isset($tax_query['taxonomy']) && $tax_query['taxonomy'] !== '' && !in_array( $tax_query['taxonomy'], $recipe_taxonomies ) ) {
                            return;
                        }
                    }
                }
            }
            
            if ( $query->is_main_query() ){
                $post_type = $query->get('post_type');
                if( is_array( $post_type ) && ! array_key_exists( 'rpr_recipe', $post_type ) ){
                    $post_type[] = 'rpr_recipe';
                } else {
                    $post_type = array( 'post', 'rpr_recipe' );
                }
                $query->set( 'post_type', $post_type );
            }

            // No idea why we neeed(ed) this. However it is interfering in the search preventing other cpt to be found!
            /*$post_type = $query->get('post_type');
            var_dump($post_type);
            
            if($post_type == '' || $post_type == 'post')
            {
                $post_type = array('post','rpr_recipe');
            }
            else if( is_array($post_type) )
            {
                if(array_key_exists('post', $post_type) && !array_key_exists('rpr_recipe', $post_type)) {
                    $post_type[] = 'rpr_recipe';
                }
            }
			var_dump($post_type);*/
    //       $query->set('post_type',$post_type);

            return;
        /*}
        else
        {
            if (!in_the_loop () || !$query->is_main_query ()) {
                return;
            }

            if($this->option('recipe_tags_use_wp_categories', '1') == '1' && $this->option('recipe_tags_show_in_archives', '1') == '1')
            {
                if(is_category() || is_tag()) {
                    $post_type = $query->get('post_type');
                    if($post_type)
                        $post_type = $post_type;
                    else
                        $post_type = array('post','rpr_recipe');
                    $query->set('post_type',$post_type);
                    return;
                }
            }
        }*/

        return;
    }
    
    public function rpr_metabox_init()
    {
    	// move postimage to top:
    	remove_meta_box( 'postimagediv', 'rpr_recipe', 'side' );
    	add_meta_box('postimagediv', __('Post image', $this->pluginName), 'post_thumbnail_meta_box', 'rpr_recipe', 'side', 'high');
    	
    	//Meta box for details
    	add_meta_box(
    		'recipe_details_meta_box',
    		__('Details', $this->pluginName),
    		array(&$this, 'recipe_details_meta_box'),
    		'rpr_recipe',
    		'side',
    		'high'
    			);
    	// Metabox for nutritional information
    	if( $this->get_option( 'recipe_use_nutritional_info', 0 ) == 1 ) {
    		add_meta_box(
    			'recipe_nutrition_meta_box',
    			__('Nutritional information', $this->pluginName),
    			array(&$this, 'recipe_nutrition_meta_box'),
    			'rpr_recipe',
    			'side',
    			'high'
    			);
    	}
    	//Meta box for description
    	add_meta_box(
    		'recipe_description_meta_box',
    		__('Description', $this->pluginName),
    		 array(&$this, 'recipe_description_meta_box'),
    		 'rpr_recipe',
    		 'normal',
    		 'high'
    		 	);
    	// Meta box for ingredients
    	add_meta_box(
    		'recipe_ingredients_meta_box',
    		__( 'Ingredients', $this->pluginName ),
    		array($this, 'recipe_ingredients_meta_box'),
    		'rpr_recipe',
    		'normal',
    		'high'
    			);
    	// Meta box for instructions
    	add_meta_box(
    		'recipe_instructions_meta_box',
    		__( 'Instructions', $this->pluginName ),
    		array($this, 'recipe_instructions_meta_box'),
    		'rpr_recipe',
    		'normal',
    		'high'
    			);
    	// Meta box for notes
    	add_meta_box(
    		'recipe_notes_meta_box',
    		__('Notes', $this->pluginName),
    		array(&$this, 'recipe_notes_meta_box'),
    		'rpr_recipe',
    		'normal',
    		'high'
    		);
    	// remove meta box for ingredients
    	remove_meta_box('rpr_ingredientdiv', 'rpr_recipe', 'side');
    	
    }

    public function recipe_details_meta_box($recipe)
    {
    	/* Use nonce for verification */
    	echo wp_nonce_field( 'rpr_details_nonce', 'rpr_details_nonce_field' );
    	include($this->pluginDir . '/views/metabox_details.php');
    }
    
    public function recipe_nutrition_meta_box($recipe)
    {
    	if( $this->get_option( 'recipe_use_nutritional_info', 0 ) == 1 ) {
    		/* Use nonce for verification */
    		echo wp_nonce_field( 'rpr_nutrition_nonce', 'rpr_nutrition_nonce_field' );
    		include($this->pluginDir . '/views/metabox_nutrition.php');
    	}
    }
    
    public function recipe_description_meta_box($recipe)
    {
    	/* Use nonce for verification */
    	echo wp_nonce_field( 'rpr_description_nonce', 'rpr_description_nonce_field' );
    	include($this->pluginDir . '/views/metabox_description.php');
    }
    
    public function recipe_ingredients_meta_box($recipe)
    {
    	/* Use nonce for verification */
    	echo wp_nonce_field( 'rpr_ingredients_nonce', 'rpr_ingredients_nonce_field' );
    	include($this->pluginDir . '/views/metabox_ingredients.php');
    }
    
    public function recipe_instructions_meta_box($recipe)
    {
    	/* Use nonce for verification */
    	echo wp_nonce_field( 'rpr_instructions_nonce', 'rpr_instructions_nonce_field' );
    	include($this->pluginDir . '/views/metabox_instructions.php');
    }
    
    public function recipe_notes_meta_box($recipe)
    {
    	/* Use nonce for verification */
    	echo wp_nonce_field( 'rpr_notes_nonce', 'rpr_notes_nonce_field' );
    	include($this->pluginDir . '/views/metabox_notes.php');
    }
    
    public function recipes_save( $recipe_id, $recipe )
    {
    	$data=$_POST;
    	//if(!isset($data)||$data==""){$data=$_POST;}
    	if( $recipe->post_type == 'rpr_recipe' )
    	{
    		$errors = false;
    		// verify if this is an auto save routine.
    		// If it is our form has not been submitted, so we dont want to do anything
    		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    			$errors = "There was an error doing autosave";
    		
    		//Verify the nonces for the metaboxes
    		if ( isset( $data['rpr_description_nonce_field'] ) &&  !wp_verify_nonce($data['rpr_description_nonce_field'],'rpr_description_nonce') ){
    			$errors = "There was an error saving the recipe. Description nonce not verified";
    			//return;
    		}
    		if ( isset( $data['rpr_ingredients_nonce_field'] ) &&  !wp_verify_nonce($data['rpr_ingredients_nonce_field'],'rpr_ingredients_nonce') ){
    			$errors = "There was an error saving the recipe. Ingredients nonce not verified";
    			//return;
    		}
    		if ( isset( $data['rpr_instructions_nonce_field'] ) &&  !wp_verify_nonce($data['rpr_instructions_nonce_field'],'rpr_instructions_nonce') ){
    			$errors = "There was an error saving the recipe. Instructions nonce not verified";
    			//return;
    		}
    		if ( isset( $data['rpr_notes_nonce_field'] ) &&  !wp_verify_nonce($data['rpr_notes_nonce_field'],'rpr_notes_nonce') ){
    			$errors = "There was an error saving the recipe. Notes nonce not verified";
    			//return;
    		}
    		if ( isset($data['rpr_details_nonce_field']) && !wp_verify_nonce($data['rpr_details_nonce_field'],'rpr_details_nonce') ){
    			$errors = "There was an error saving the recipe. Details nonce not verified";
    			//return;
    		}
    		if ( isset($data['rpr_nutrition_nonce_field']) && !wp_verify_nonce($data['rpr_nutrition_nonce_field'],'rpr_nutrition_nonce') ){
    			$errors = "There was an error saving the recipe. Nutrition nonce not verified";
    			//return;
    		}
    		
    		// Check permissions
    		if ( !current_user_can( 'edit_post', $recipe_id ) ){
    			$errors = "There was an error saving the recipe. No sufficient rights.";
    		//return;
    		}
    		
    		//If we have an error update the error_option and return
    		if( $errors ) {
    			//update_option('rpr_admin_errors', $errors);
    			return $recipe_id;
    		}
    
    		$fields = $this->recipes_fields();
 //   		var_dump($fields); 
 //   		var_dump($_POST['rpr_recipe_ingredients']); die;
//var_dump($_POST);
    		foreach ( $fields as $field )
    		{
    			if(isset($data[$field])){
    				$old = get_post_meta( $recipe_id, $field, true );
					$new = $data[$field];
	   
	    			// Field specific adjustments
	    			if ($field == 'rpr_recipe_ingredients')
	    			{
	    				$ingredients = array();
	    				$non_empty_ingredients = array();
	    
	    				foreach($new as $ingredient) {
	    					if($ingredient['ingredient'] != '')
	    					{
	    						$term = term_exists($ingredient['ingredient'], 'rpr_ingredient');
	    
	    						if ( $term === 0 || $term === null) {
	    							$term = wp_insert_term($ingredient['ingredient'], 'rpr_ingredient');
	    						}
//var_dump($term);	    						
	    						$term_id = intval($term['term_id']);
	    
	    						$ingredient['ingredient_id'] = $term_id;
	    						$ingredients[] = $term_id;
	    
	    						$non_empty_ingredients[] = $ingredient;
	    					}
	    				}
	    
	    				wp_set_post_terms( $recipe_id, $ingredients, 'rpr_ingredient' );
	    				$new = $non_empty_ingredients;
	    			}
	    			elseif ($field == 'rpr_recipe_instructions')
	    			{
	    				$non_empty_instructions = array();
	
	    				foreach($new as $instruction) {
	    					if($instruction['description'] != '' || $instruction['image'] != '')
	    					{
	    						$non_empty_instructions[] = $instruction;
	    					}
	    				}
	    
	    				$new = $non_empty_instructions;
	    			}
	    			//echo '<div style="color:red">';var_dump($new);echo'</div>';
	    			// Update or delete meta data if changed
	    			if (isset($new) && $new != $old)
	    			{
	    				update_post_meta( $recipe_id, $field, $new );
	    			}
	    			elseif ($new == '' && $old)
	    			{
	    				delete_post_meta( $recipe_id, $field, $old );
	    			}
	    		}
    		}
    	}
    }
    
    /* 
     * Make the recipe table nicer by adding extra metadata
     */
    function rpr_recipe_table_head( $defaults ) {
    	$defaults = array(
    			'cb' => '<input type="checkbox" />',
    			'thumbnail' => __('Image', $this->pluginName ),
    			'title' => __('Recipe Title', $this->pluginName ),
    			'author' =>	__('Author', $this->pluginName ),
    			'categories' => __( 'Category', $this->pluginName ),
    			'tags' => __( 'Tags', $this->pluginName ),
    			'comments' => __( 'Comments', $this->pluginName ),
    			'date' => __('Date', $this->pluginName )
    	);
    	//$defaults['thumbnail']=__('Image', $this->pluginName );
    	/*foreach ( $this->options['taxonomies'] as $tax => $settings ) {
    		$settings = $this->taxDefaults($settings);
    		if ( $settings['active'] and taxonomy_exists($tax) ) {
    			$columns[$tax] = $settings['plural'];
    		}
    	}
    	
    	$columns['ingredients'] = __('Ingredients', 'recipe-press');
    	
    	if ( $this->options['use-featured'] ) {
    		$columns['featured'] = __('Featured', 'recipe-press');
    	}
    	
    	$columns ['author'] = __('Author', 'recipe-press');
    	
    	if ( $this->options['use-comments'] ) {
    		$columns['comments'] = '<img src="' . get_option('siteurl') . '/wp-admin/images/comment-grey-bubble.png" alt="Comments">';
    	}
    	
    	$columns['date'] = __('Date', 'recipe-press');
    	 */
    	//$defaults['thumbnail']  = 'Thumbnail';
    	return $defaults;
    }
    
    function rpr_recipe_table_content( $column_name, $post_id ) {
    	if ($column_name == 'thumbnail') {
    		if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) {
            	the_post_thumbnail('rpr-table-thumb');
            }
    	}
    	
    
    }
    
    /*TODO: make this nice and working!
     /*
     * Returns array of all recipes
     */
    protected function get_recipes( $orderby = 'date', $order = 'DESC', $taxonomy = '', $term = '', $limit = -1, $author = '' ) {
        $args = array(
            'post_type' => 'rpr_recipe',
            'post_status' => 'publish',
            'orderby' => $orderby,
            'order' => $order,
            'posts_per_page' => $limit,
        );

        if( is_null($limit) || $limit == -1 ) {
            $args['nopaging'] = true;
        }
        
        if( $taxonomy && !$term ) {
            $args['tax_query'] = array(
                'taxonomy' => $taxonomy,
            );
        }
        
        if( $taxonomy && $term ) {
            if( $taxonomy == 'category' ) {
                $args['category_name'] = $term;
            } else if ( $taxonomy == 'post_tag' ) {
                $args['tag'] = $term;
            } else {
                $args[$taxonomy] = $term;
            }
        }

        if( !is_null($author) && $author != '' ) {
            $args['author'] = $author;
        }
        
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
    
    /*
     * TODO - This is probably not that performant but does the job for now
     */
    protected function compare_post_titles($a, $b)
    {
        return strcmp(ucfirst($a->post_title), ucfirst($b->post_title));
    }
    
    public function recipes_content( $content )
    {
    	if (!in_the_loop () || !is_main_query ()) {
    		return $content;
    	}
    
    	if ( get_post_type() == 'rpr_recipe' ) {
    		remove_filter('the_content', array( $this, 'recipes_content' ), 10);
    
    		$recipe_post = get_post();
    		$recipe = get_post_custom($recipe_post->ID);
    		
    		$totaltime = 0;
    		// Calculate total time:
    		if(isset ($recipe['rpr_recipe_prep_time'][0]) && $recipe['rpr_recipe_prep_time'][0] != 0){
    			$totaltime += $recipe['rpr_recipe_prep_time'][0];
    		}
    		if( isset( $recipe['rpr_recipe_cook_time'][0] ) && $recipe['rpr_recipe_cook_time'][0] != 0 ){
    			$totaltime += $recipe['rpr_recipe_cook_time'][0];
    		}
    		if( isset( $recipe['rpr_recipe_passive_time'][0] ) && $recipe['rpr_recipe_passive_time'][0] != 0){
    			$totaltime += $recipe['rpr_recipe_passive_time'][0];
    		}
    		
    		if( $totaltime != 0){
                $recipe['rpr_recipe_total_time']=array($totaltime);
            }
    
    		if (is_single() || $this->option('recipe_archive_display', 'excerpt') == 'full')
    		{
    			$taxonomies = $this->get_custom_taxonomies();
    			unset($taxonomies['ingredient']);
    
    			ob_start();
    			
    			include($this->pluginDir . '/templates/'.$this->option( 'rpr_template', 'rpr_default' ).'/recipe.php');
    
    			$recipe_box = ob_get_contents();
    			ob_end_clean();

    			//if(strpos($content, '[rpr_recipe]') !== false) {
    			//	$content = str_replace('[rpr_recipe]', $recipe_box, $content);
    			//} else { // Add recipe to end of post
    				$content = $recipe_box;
    			//}
    		}
    		else
    		{
    			$content = $this->get_recipes_excerpt();
    		}
    
    		add_filter('the_content', array( $this, 'recipes_content' ), 10);
    	}
    
    	return $content;
    }
    
    public function recipes_excerpt( $content ) {
        if (!in_the_loop () || !is_main_query ()) {
            return $content;
        }

        if ( get_post_type() == 'rpr_recipe' ) {
            remove_filter('get_the_excerpt', array( $this, 'recipes_excerpt' ), 10);
            $recipe_post = get_post();
            //$content = $recipe_post->post_excerpt
            $recipe = get_post_custom($recipe_post->ID);

            if( isset($recipe['rpr_recipe_description'][0]) ){
                $content = $recipe['rpr_recipe_description'][0];
            }

          add_filter('get_the_excerpt', array( $this, 'recipes_excerpt' ), 10);
          $content = get_the_recipe_taxonomy_bar().wpautop($content).get_the_recipe_times();              
        }
        return $content;
    }
     
    public function get_recipes_excerpt() {
/*        if (!in_the_loop () || !is_main_query ()) {
            return $content;
        }

        if ( get_post_type() == 'rpr_recipe' ) {
            remove_filter('get_the_excerpt', array( $this, 'recipes_excerpt' ), 10);
*/            $recipe_post = get_post();
            //$content = $recipe_post->post_excerpt
            $recipe = get_post_custom($recipe_post->ID);

            if( isset($recipe['rpr_recipe_description'][0]) ){
                $content = $recipe['rpr_recipe_description'][0];
            }

  //          add_filter('get_the_excerpt', array( $this, 'recipes_excerpt' ), 10);
  //      }
        
    //    return $content;
    return get_the_recipe_taxonomy_bar().wpautop($content).get_the_recipe_times();    
    }

    // These filters will only be regoistered, if the apropriate settings are made
    public function recipes_author() {
        return false;
    }

    public function recipes_category_list() {
        return false;
    }

    public function recipes_tag_list() {
        return false;
    }

    public function recipes_date() {
        return false;
    }
    
    public function recipes_shortcode($options) {
        $options = shortcode_atts(array(
            'id' => 'n/a'
        ), $options);

        $recipe_post = null;
        if ($options['id'] != 'n/a') {

            if( $options['id'] == 'random' ) {

                $posts = get_posts(array(
                    'post_type' => 'rpr_recipe',
                    'nopaging' => true
                ));

                $recipe_post = $posts[array_rand($posts)];

            } else {
                $recipe_post = get_post(intval($options['id']));
            }
        }
        if(!is_null($recipe_post) && $recipe_post->post_type == 'rpr_recipe')
        {
            $recipe = get_post_custom($recipe_post->ID);

            $taxonomies = $this->get_custom_taxonomies();

            ob_start();
            
            include($this->pluginDir . '/templates/'.$this->option( 'rpr_template', 'rpr_default' ).'/recipe.php');
            
            $output = ob_get_contents();
            ob_end_clean();
        }
        else
        {
            $output = '';
        }

        return do_shortcode($output);
    }

    public function recipes_index_shortcode($options) {
        $options = shortcode_atts(array(
            'headers' => 'false'
        ), $options);

        $posts = $this->get_recipes( 'post_title', 'ASC' );

        $out = '<div class="rpr-index-container">';
        if($posts) {

            $letters = array();

            foreach($posts as $post)
            {
                $title = ucfirst($post->post_title);//$this->get_recipe_title( $post );

                if($title != '')
                {
                    //if ($options['headers'] != 'false'){
                        $first_letter = substr($title,0,1);

                        if(!in_array($first_letter, $letters))
                        {
                            $letters[] = $first_letter;
                            $out .= '<h2><a name="'.$first_letter.'"></a>';
                            $out .= $first_letter;
                            $out .= '</h2>';
                        }
                    //}

                    $out .= '<a href="'.get_permalink($post->ID).'">';
                    $out .= $title;
                    $out .= '</a><br/>';
                }
            }
        }
        else
        {
            $out .= __( "You have to create a recipe first, check the 'Recipes' menu on the left.", $this->pluginName );
        }
        $out .= '</div>';
        
        return $this->letter_navigation( $letters ).$out.$this->letter_navigation( $letters );
    }
    
    public function recipes_taxlist_shortcode($options) {
        $options = shortcode_atts(array(
            'headers' => 'false',
            'tax' => 'n/a',
        ), $options);

        $terms = get_terms( $options['tax'], array('orderby'=> 'name', 'order' => 'ASC' ) );

        $out = '<div class="rpr-index-container">';
        if($terms) {

            $letters = array();

            foreach($terms as $term) {

                $title = ucfirst($term->name);//$this->get_recipe_title( $post );

                if($title != '')
                {
                    //if ($options['headers'] != 'false'){
                        $first_letter = substr($title,0,1);

                        if(!in_array($first_letter, $letters))
                        {
                            $letters[] = $first_letter;
                            $out .= '<h2><a name="'.$first_letter.'"></a>';
                            $out .= $first_letter;
                            $out .= '</h2>';
                        }
                    //}

                    //$out .= '<a href="'.get_permalink($term->term_id).'">';
                    $out .= '<a href="'.get_term_link( $term ).'">';
                    $out .= $title;
                    $out .= '</a><br/>';
                }
            }
        }
        else
        {
            $out .= __( "You have to create a recipe first, check the 'Recipes' menu on the left.", $this->pluginName );
        }
        $out .= '</div>';
        
        return $this->letter_navigation( $letters ).$out.$this->letter_navigation( $letters );
    }

    function letter_navigation($letters){
        $out = '<ul class="rpr_letter_nav">';
        foreach( $letters as $l ) {
            $out .= '<li><a href="#'.$l.'">'.$l.'</a></li>';
        }
        $out .= '</ul>';
        return $out;
    }
	/*
 	* ///////////////////////////////////// TAXONOMIES ///////////////////////////////////////////
 	*/

    public function activate_taxonomies()
    {
    	$this->recipes_init();
    	$this->rpr_custom_taxonomies_init();
    
    	update_option( 'rpr_flush', '1' );
    }
    // => Move to seperate class? (rpr_taxonomies...)   
    
    function get_custom_taxonomies()
    {
    	return get_option('rpr_taxonomies', array());
    }
    
    
    /* in install routine einbauen?*/
    function rpr_custom_taxonomies_init()
    {
    	$taxonomies = $this->get_custom_taxonomies();

    	if(count($taxonomies) == 0)
    	{
    
    		$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_ingredient', __( 'Ingredients', $this->pluginName ), __( 'Ingredient', $this->pluginName ));
    		$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_course', __( 'Courses', $this->pluginName ), __( 'Course', $this->pluginName ));
    		$taxonomies = $this->add_taxonomy_to_array($taxonomies, 'rpr_cuisine', __( 'Cuisines', $this->pluginName ), __( 'Cuisine', $this->pluginName ));
    
    		update_option('rpr_taxonomies', $taxonomies);
    		update_option( 'rpr_flush', '1' );
    
    		wp_insert_term( __( 'Breakfast', $this->pluginName ), 'course' );
    		wp_insert_term( __( 'Appetizer', $this->pluginName ), 'course' );
    		wp_insert_term( __( 'Soup', $this->pluginName ), 'course' );
    		wp_insert_term( __( 'Main Course', $this->pluginName ), 'course' );
    		wp_insert_term( __( 'Side Dish', $this->pluginName ), 'course' );
    		wp_insert_term( __( 'Salad', $this->pluginName ), 'course' );
    		wp_insert_term( __( 'Dessert', $this->pluginName ), 'course' );
    		wp_insert_term( __( 'Snack', $this->pluginName ), 'course' );
    		wp_insert_term( __( 'Drinks', $this->pluginName ), 'course' );
    
    		wp_insert_term( __( 'French', $this->pluginName ), 'cuisine' );
    		wp_insert_term( __( 'Italian', $this->pluginName ), 'cuisine' );
    		wp_insert_term( __( 'Mediterranean', $this->pluginName ), 'cuisine' );
    		wp_insert_term( __( 'Indian', $this->pluginName ), 'cuisine' );
    		wp_insert_term( __( 'Chinese', $this->pluginName ), 'cuisine' );
    		wp_insert_term( __( 'Japanese', $this->pluginName ), 'cuisine' );
    		wp_insert_term( __( 'American', $this->pluginName ), 'cuisine' );
    		wp_insert_term( __( 'Mexican', $this->pluginName ), 'cuisine' );
    	}
    }
    
    private function add_taxonomy_to_array($arr, $tag, $name, $singular)
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
    			'hierarchical' => true,
    			'rewrite' => array(
    					'slug' => $singular_lower
    			)
    	);
    
    	return $arr;
    }
    
    /*
     * ////////////////////////////////////////// RATINGS //////////////////////////////////////////////
     */
    
    public function ratings_init()
    {
    	register_taxonomy(
    	'rpr_rating',
    	'rpr_recipe',
    	array(
    	'labels' => array(
    	'name'                       => __( 'Ratings', $this->pluginName ),
    	'singular_name'              => __( 'Rating', $this->pluginName ),
    	'search_items'               => __( 'Search Ratings', $this->pluginName ),
    	'popular_items'              => __( 'Popular Ratings', $this->pluginName ),
    	'all_items'                  => __( 'All Ratings', $this->pluginName ),
    	'edit_item'                  => __( 'Edit Rating', $this->pluginName ),
    	'update_item'                => __( 'Update Rating', $this->pluginName ),
    	'add_new_item'               => __( 'Add New Rating', $this->pluginName ),
    	'new_item_name'              => __( 'New Rating Name', $this->pluginName ),
    	'separate_items_with_commas' => __( 'Separate ratings with commas', $this->pluginName ),
    	'add_or_remove_items'        => __( 'Add or remove ratings', $this->pluginName ),
    	'choose_from_most_used'      => __( 'Choose from the most used ratings', $this->pluginName ),
    	'not_found'                  => __( 'No ratings found.', $this->pluginName ),
    	'menu_name'                  => __( 'Ratings', $this->pluginName )
    	),
    	'show_ui' => false,
    	'show_tagcloud' => false,
    	'hierarchical' => false
    	)
    	);
    }
    


      
}
