<?php
if ( !function_exists('get_the_recipe_category_bar') ) {
    // template tag for category list:
    function get_the_recipe_category_bar( $recipe_post ){
    	if( !$recipe_post || $recipe_post == '' ){
    		$recipe_post = get_post();
    	}
    	
        $recipe = get_post_custom($recipe_post->ID);
        $out="";
        
        // Categories:
        if ( RPReloaded::get_option('recipe_tags_use_wp_categories', 1) == '1' ) { 
            if( RPReloaded::get_option('recipe_display_categories_in_recipe', 1) == '1' ) {
                $out .= sprintf(
                    '<span itemprop="recipeCategory" class="fa fa-list-ul category-list">%s</span>',
                    get_the_category_list(  __( '&nbsp;/&nbsp; ', 'recipe-press-reloaded' ) )
                    );
            } else {
                $out .= sprintf(
                    '<span itemprop="recipeCategory" class="category-list rpr_hidden">%s</span>',
                    get_the_category_list(  __( '&nbsp;/&nbsp; ', 'recipe-press-reloaded' ) )
                    );
            }
        } else {
            $terms = get_the_term_list( $recipe_post->ID, 'rpr_category', '', ', ');
            if(!is_wp_error($terms) && $terms != '') {
                $out .= sprintf(
                        '<span itemprop="recipeCategory" class="fa fa-list-ul category-list">%s</span>',
                         get_the_term_list( $recipe_post->ID, 'rpr_category', '', __( '&nbsp;/&nbsp; ', 'recipe-press-reloaded' ), '' )
                    );
            }
        }
        
        // Course:
        $terms = get_the_term_list( $recipe_post->ID, 'rpr_course', '', ', ');
        if(!is_wp_error($terms) && $terms != '') {
            $out .= sprintf(
                    '<span class="fa fa-cutlery category-list">%s</span>',
                     get_the_term_list( $recipe_post->ID, 'rpr_course', '', __( '&nbsp;/&nbsp; ', 'recipe-press-reloaded' ), '' )
                    );
        }
        
        //Cuisine:
        $terms = get_the_term_list( $recipe_post->ID, 'rpr_cuisine', '', ', ');
        if(!is_wp_error($terms) && $terms != '') {
            $out .=  sprintf(
                        '<span itemprop="recipeCuisine" class="fa fa-flag category-list">%s</span>',
                        get_the_term_list( $recipe_post->ID, 'rpr_cuisine', '', __( '&nbsp;/&nbsp; ', 'recipe-press-reloaded' ), '' )
                    );
        }
        
        // Custom Taxonomies:
        $done = array('rpr_category', 'rpr_tag', 'rpr_course', 'rpr_cuisine', 'rpr_ingredient');
        $taxonomies = get_option('rpr_taxonomies', array());
        foreach($taxonomies as $taxonomy => $options) {
            if( ! in_array($taxonomy, $done ) ){
                 $terms = get_the_term_list( $recipe_post->ID, $taxonomy, '', ', ');
                if(!is_wp_error($terms) && $terms != '') {
                    $out .= sprintf(
                        '<span class="fa fa-list-alt category-list">%s</span>',
                        get_the_term_list( $recipe_post->ID, $taxonomy, '', __( '&nbsp;/&nbsp; ', 'recipe-press-reloaded' ), '' )
                    );
                }
            }
         }
         
        if( $out != "" ) {
            return '<div class="entry-meta rpr_info_line" >'.$out.'</div>';
        } 
    }
 }
 
if ( !function_exists('the_recipe_category_bar') ) {
    function the_recipe_category_bar( $recipe_post ){
        echo get_the_recipe_category_bar( $recipe_post );
    }
} 



// ======= RECIPE SERVINGS BAR =======
if ( !function_exists('get_the_recipe_servings_bar') ) {
	function get_the_recipe_servings_bar( $recipe_id ){
		$out='';
		
		// Get the ID of the recipe:
		if( !isset( $recipe_id ) || !is_numeric( $recipe_id ) ){
			$recipe_id = get_post()->ID;
		}
		
		// Get the recipe
		$recipe = get_post_custom( $recipe_id );
		
		if($recipe['rpr_recipe_servings'][0] != '') {
			$out .= __( 'For: ', 'recipe-press-reloaded' );
			$out .= '<span itemprop="recipeYield"><span class="recipe-information-servings">';
			$out .= $recipe['rpr_recipe_servings'][0];
			$out .= '</span> <span class="recipe-information-servings-type">';
			$out .= $recipe['rpr_recipe_servings_type'][0];
			$out .= '</span></span>';
		}
		
		return $out;
	}
}

if ( !function_exists('the_recipe_servings_bar') ) {
	function the_recipe_servings_bar( $recipe_id ){
		echo get_the_recipe_servings_bar( $recipe_id);
	}
}

// ======= INGREDIENT LIST =========
if ( ! function_exists('get_the_recipe_ingredient_list') ) {
	function get_the_recipe_ingredient_list( $args ){
		$out='';
		
		// Get the ID of the recipe:
		if ( !isset($args['ID']) || ! is_numeric( $args['ID'] ) ){
			$args['ID'] = get_post()->ID;
		}
		// Get the recipe
		$recipe = get_post_custom( $args['ID'] );
				
		// Get the ingredients:
		$ingredients = unserialize($recipe['rpr_recipe_ingredients'][0]);
		if(!empty($ingredients))
		{
			$out .= '<ul class="recipe-ingredients">';
			
			$previous_group = '';
			foreach($ingredients as $ingredient) {
			
				if( isset( $ingredient['ingredient_id'] ) ) {
					$term = get_term($ingredient['ingredient_id'], 'ingredient');
					if ( $term !== null && !is_wp_error( $term ) ) {
						$ingredient['ingredient'] = $term->name;
					}
				}
			
				if(isset($ingredient['group']) && $ingredient['group'] != $previous_group) {
					$out .= '<li class="group">' . $ingredient['group'] . '</li>';
					$previous_group = $ingredient['group'];
				}
			
				$out .= '<li itemprop="ingredients">';
				$out .= '<span class="recipe-ingredient-quantity-unit"><span class="recipe-ingredient-quantity" data-original="'.$ingredient['amount'].'">'.$ingredient['amount'].'</span> <span class="recipe-ingredient-unit">'.$ingredient['unit'].'</span></span>';
			
			
				$taxonomy = get_term_by('name', $ingredient['ingredient'], 'rpr_ingredient');
			
				$out .= ' <span class="recipe-ingredient-name">';
			
				$ingredient_links = RPReloaded::get_option('recipe_ingredient_links', 'archive_custom');
			
				$closing_tag = '';
				if (!empty($taxonomy) && $ingredient_links != 'disabled') {
			
					if( isset($ingredient['link']) && ( $ingredient_links == 'archive_custom' || $ingredient_links == 'custom' ) ) {
						$custom_link = $ingredient['link'];//WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy->slug, 'link' );
					} else {
						$custom_link = false;
					}
			
					if($custom_link !== false && $custom_link !== '' && $custom_link !== NULL ) {
						$out .= '<a href="'.$custom_link.'" class="custom-ingredient-link" target="'.$this->option('recipe_ingredient_custom_links_target', '_blank').'">';
						$closing_tag = '</a>';
					} elseif($ingredient_links != 'custom') {
						$out .= '<a href="'.get_term_link($taxonomy->slug, 'rpr_ingredient').'">';
						$closing_tag = '</a>';
					}
				}
			
				$out .= $ingredient['ingredient'];
				$out .= $closing_tag;
				$out .= '</span>';
			
				if($ingredient['notes'] != '') {
					$out .= ' ';
					$out .= '<span class="recipe-ingredient-notes">'.$ingredient['notes'].'</span>';
				}
			
				$out .= '</li>';
			}
			$out.='</ul>';
			
		} else {
			$out.='<p class="warning">'.__('No ingredients could be found for this recipe.', $this->pluginName).'</p>';
		}
		// Return output
		return $out;
	}
}

if ( ! function_exists('the_recipe_ingredient_list') ) {
	function the_recipe_ingredient_list( $args ){
		echo get_the_recipe_ingredient_list( $args );
	}	
}

// ================= RECIPE TIME BAR ==============
if ( !function_exists('get_the_recipe_time_bar') ) {
	function get_the_recipe_time_bar( $recipe_id ){
		$out='';
		 
		// Get the ID of the recipe:
		if( !isset( $recipe_id ) || !is_numeric( $recipe_id ) ){
			$recipe_id = get_post()->ID;
		}
		 
		// Get the recipe
		$recipe = get_post_custom( $recipe_id );

		if( isset($recipe['rpr_recipe_prep_time'][0]) ) {
			$out .= '<span class="fa fa-cog recipe-times" title="'. __( 'Preparation Time', 'recipe-press-reloaded' ).'">';
			$out .= '<meta itemprop="prepTime" content="PT'.$recipe['rpr_recipe_prep_time'][0].'M">'.$recipe['rpr_recipe_prep_time'][0].'<span class="recipe-information-time-unit">'.__( 'min.', 'recipe-press-reloaded' ).'</span></span>';
		}
		if( isset($recipe['rpr_recipe_cook_time'][0]) ) {
			$out .= '<span class="fa fa-fire recipe-times" title="'.__( 'Cook Time', 'recipe-press-reloaded' ).'">';
			$out .= '<meta itemprop="cookTime" content="PT'.$recipe['rpr_recipe_cook_time'][0].'M">'.$recipe['rpr_recipe_cook_time'][0].'<span class="recipe-information-time-unit">'.__( 'min.', 'recipe-press-reloaded' ).'</span></span>';
		}


		$total_time = $recipe['rpr_recipe_prep_time'][0]+$recipe['rpr_recipe_cook_time'][0]+$recipe['rpr_recipe_passive_time'][0];
		if($total_time != '') {
			$out .= '<span class="fa fa-clock-o recipe-times" title="'.__( 'Total Time', 'recipe-press-reloaded' ).'">';
			$out .= '<meta itemprop="totalTime" content="PT'.$total_time.'">'.$total_time.'<span class="recipe-information-time-unit">'.__( 'min.', 'recipe-press-reloaded' ).'</span></span>';
		}
		if( $out != '') {
			return '<div class="entry-meta rpr_time_line" >'.$out.'</div>';
		}
	}
}

if ( !function_exists('the_recipe_time_bar') ) {
	function the_recipe_time_bar( $recipe_id ){
		echo get_the_recipe_time_bar( $recipe_id );
	}
}

// =============== RECIPE INSTRUCTIONS LIST ===============
if ( ! function_exists('get_the_recipe_instruction_list') ) {
	function get_the_recipe_instruction_list( $args ){
		$out='';
		
		// Get the ID of the recipe:
		if ( !isset($args['ID']) || ! is_numeric( $args['ID'] ) ){
			$args['ID'] = get_post()->ID;
		}
		// Get the recipe
		$recipe = get_post_custom( $args['ID'] );
		
		// Get the instructions:
		$instructions = unserialize($recipe['rpr_recipe_instructions'][0]);
		if(!empty($instructions)){
			$out .= '<ol class="recipe-instructions">';
			
			$previous_group = '';
			foreach($instructions as $instruction) {
				if(isset($instruction['group']) && $instruction['group'] != $previous_group) {
					$out .= '</ol>';
					$out .= '<div class="instruction-group">' . $instruction['group'] . '</div>';
					$out .= '<ol class="recipe-instructions">';
					$previous_group = $instruction['group'];
				}
			
				$out .= '<li itemprop="recipeInstructions">';
				$instr_class="";
				if( isset($instruction['image']) && $this->option('recipe_images_clickable', '0') == 1 ) { $instr_class = "has_thumbnail"; }
				$out .= '<span class="recipe-instruction '.$instr_class.'">'.$instruction['description'].'</span>';
			
				if( isset($instruction['image']) ) {
					if($this->option('recipe_images_clickable', '0') == 1) {
						$thumb = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
						 
					} else{
						$thumb = wp_get_attachment_image_src( $instruction['image'], 'large' );
					}
			
					$thumb_url = $thumb['0'];
					$full_img = wp_get_attachment_image_src( $instruction['image'], 'full' );
					$full_img_url = $full_img['0'];
			
					if($this->option('recipe_images_clickable', '0') == 1 && $thumb_url != "" ) {
						$out .= '<a href="' . $full_img_url . '" rel="lightbox" title="' . $instruction['description'] . '">';
						$out .= '<img src="' . $thumb_url . '" />';
						$out .= '</a>';
					} else {
						if( $thumb_url != "" ){
							$out .= '<img src="' . $thumb_url . '" />';
						}
					}
				}
			
				$out .= '</li>';
			}
			$out .= '</ol>';
		} else {
			$out.='<p class="warning">'.__('No instructions could be found for this recipe.', $this->pluginName).'</p>';
		}
		
		return $out;
	}
}

if ( ! function_exists('the_recipe_instruction_list') ) {
	function the_recipe_instruction_list( $args ){
		echo get_the_recipe_instruction_list( $args );
	}
}

// ================ RECIPE NOTES ===================
if ( ! function_exists('get_the_recipe_notes') ) {
	function get_the_recipe_notes( $args ){
		$out='';

		// Get the ID of the recipe:
		if ( !isset($args['ID']) || ! is_numeric( $args['ID'] ) ){
			$args['ID'] = get_post()->ID;
		}
		// Get the recipe
		$recipe = get_post_custom( $args['ID'] );
		
		$out='<div class="recipe-notes">';
        $out .= wpautop( $recipe['rpr_recipe_notes'][0] );
    	$out .='</div>';
    	
    	return $out;
	}
}

if ( ! function_exists('the_recipe_notes') ) {
	function the_recipe_notes( $args ){
		echo get_the_recipe_notes( $args );
	}
}

// ================ RECIPE FOOTER ======================

if ( !function_exists('get_the_recipe_footer') ) {
    function get_the_recipe_footer(){
        $out="";
    
        if( RPReloaded::get_option('recipe_tags_use_wp_tags', '1') != '1' ) { 
            $terms = get_the_term_list( $recipe_post->ID, 'rpr_tag', '', ', ');
            if(!is_wp_error($terms) && $terms != '') { 
                $out.='<span class="fa fa-tags recipe-tags">';
                $out.= get_the_term_list( $recipe_post->ID, 'rpr_tag', '', ', ', '');
                $out.="</span>";
            }
        } elseif( RPReloaded::get_option('recipe_display_tags_in_recipe', '1') == '1' ) {
            $out.='<span class="fa fa-tags recipe-tags">';
            $out.=get_the_tag_list('', ', ', '');
            $out.="</span>"; 
        }
        if( RPReloaded::get_option('recipe_author_display_in_recipe', '1') == '1' ) {
            $out.=sprintf( 
                '<span class="byline fa fa-user entry-author author vcard"><a class="url fn n" href="%1$s" rel="author">%2$s</a></span>',
                esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
        		get_the_author()
	            );
        }
        if( RPReloaded::get_option('recipe_time_display_in_recipe', '1') == '1' ) {
            $out.=sprintf( 
                '<span class="fa fa-calendar entry-date published"><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s">%3$s</time></a></span>',
	    	        esc_url( get_permalink() ),
            		esc_attr( get_the_date( 'c' ) ),
	    	        esc_html( get_the_date() )
	    	    );
        }
    
        if( $out != "" ){
            return '<div class="entry-meta recipe_footer">'.$out.'</div>';
        }
    }
}
    
if ( !function_exists('the_recipe_footer') ) {
    function the_recipe_footer(){
        echo get_the_recipe_footer();
    }
}