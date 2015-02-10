<?php
/*----------------------- Display Mode ----------------------------*/
/*
 * Decides wether we are in  embedded or single mode
 */
if ( !function_exists('is_recipe_embedded') ) {
	function is_recipe_embedded(){
		// Check if we are in single mode or shortcode mode (aka included recipe)
		if( get_post_type( ) != 'rpr_recipe' ){
			return true;
		} else {
			return false;
		}
	}
}

/*----------------------- Recipe Thumbnail ----------------------------*/
if ( !function_exists('get_the_recipe_thumbnail') ) {
	// thumbnail of the recipe.
	function get_the_recipe_thumbnail( $recipe_id = '') {
		$out = '';
		
		$imgclass="hidden";
		if( RPReloaded::get_option( 'recipe_display_image', '0' ) || is_recipe_embedded() ) { $imgclass=""; }
		
		$recipe_title = get_the_title( $recipe_id );
		
		// Get the image URL:
		$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $recipe_id ), 'large' );
		$thumb_url = $thumb['0'];
		
		if(!is_null($thumb_url)) {
			$full_img = wp_get_attachment_image_src( get_post_thumbnail_id( $recipe_id), 'full' );
			$full_img_url = $full_img['0'];
			
			$out .= '<div class="recipe-header-image">';
		    
		    if( RPReloaded::get_option( 'recipe_images_clickable', '0' ) == 1 ) {
		    	$out .= '<a href="' . $full_img_url . '" rel="lightbox" title="'. $recipe_title . '">';
		        $out .= '<img class="' . $imgclass . '" itemprop="image" src="' . $thumb_url . '" title="' . $recipe_title . '" />';
		        $out .= '</a>';
		    } else {
				$out .= '<img class="' . $imgclass . '" itemprop="image" src="' . $thumb_url . '" title="' . $recipe_title . '" />';
			}
			
		    $out .= '</div>';
		}
		return $out;
	}
}

if ( !function_exists('the_recipe_thumbnail') ) {
	function the_recipe_thumbnail( $recipe_id ) {
		echo get_the_recipe_thumbnail( $recipe_id );
	}
}



if ( !function_exists('get_the_recipe_print_link') ) {
	// a link to print only the recipe.
	function get_the_recipe_print_link() {
		$out = '';
		if (  RPReloaded::get_option('recipe_display_printlink', 0) == '1' ){
			$out .= '<script>';
			$out .= 'var rpr_printarea="' . RPReloaded::get_option('recipe_printlink_class', '.rpr_recipe') . '";' ;
			$out .= '</script>';
			$out .= '<span class="print-link"></span>';
		}
		return $out;
	}
}

if ( !function_exists('the_recipe_print_link') ) {
	function the_recipe_print_link() {
		echo get_the_recipe_print_link();
	}
}

if ( !function_exists('get_the_recipe_taxonomy_bar') ) {
    // template tag for taxonomy bar:
    function get_the_recipe_taxonomy_bar( $recipe_id='' ){
    	if( !$recipe_id || !is_numeric( $recipe_id ) ){
    		$recipe_id = get_post()->ID;
    	}
    	
        $recipe = get_post_custom($recipe_id);
        $out="";
        
        // Categories:
        if ( RPReloaded::get_option('recipe_tags_use_wp_categories', 1) == '1' ) { 
            if( RPReloaded::get_option('recipe_display_categories_in_recipe', 1) == '1' ) {
            	$tax = get_taxonomy( 'category' );

            	$icon='';
            	if( RPReloaded::get_option( 'recipe_icons_display', 0 ) == 1 ){ 
            		$icon = '<i class="fa fa-list-ul" title=' . $tax->labels->name . '></i> ';
        		} else {
        			$icon=$tax->labels->name . ': ';
	        	}
                $out .= sprintf(
                    '<span itemprop="recipeCategory" class="category-list">%1s%2s</span>',
                	$icon,
                    get_the_category_list(  __( '&nbsp;/&nbsp; ', 'recipepress-reloaded' ) )
                    );
            } else {
                $out .= sprintf(
                    '<span itemprop="recipeCategory" class="category-list rpr_hidden">%s</span>',
                    get_the_category_list(  __( '&nbsp;/&nbsp; ', 'recipepress-reloaded' ) )
                    );
            }
        } else {
            $terms = get_the_term_list( $recipe_id, 'rpr_category', '', ', ');
            $tax = get_taxonomy( 'rpr_category' );
            
            if(!is_wp_error($terms) && $terms != '') {
            	$icon='';
            	if( RPReloaded::get_option( 'recipe_icons_display', 0 ) == 1 ){
            		$icon = '<i class="fa fa-list-ul" title=' . $tax->labels->name . '></i> ';
        		} else {
        			$icon=$tax->labels->name . ': ';
	        	}
                $out .= sprintf(
                        '<span itemprop="recipeCategory" class="category-list">%1s%2s</span>',
                		$icon,
                         get_the_term_list( $recipe_post->ID, 'rpr_category', '', __( '&nbsp;/&nbsp; ', 'recipepress-reloaded' ), '' )
                    );
            }
        }
        
        // Course:
        $terms = get_the_term_list( $recipe_id, 'rpr_course', '', ', ');
        $tax = get_taxonomy( 'rpr_course' );
        
        if(!is_wp_error($terms) && $terms != '') {
        	$icon='';
        	if( RPReloaded::get_option( 'recipe_icons_display', 0 ) == 1 ){
        		$icon = '<i class="fa fa-cutlery" title=' . $tax->labels->name . '></i> ';
        	} else {
        		$icon=$tax->labels->name . ': ';
        	}
            $out .= sprintf(
                    '<span class="fa fa-cutlery category-list">%1s%2s</span>',
            		$icon,
                     get_the_term_list( $recipe_id, 'rpr_course', '', __( '&nbsp;/&nbsp; ', 'recipepress-reloaded' ), '' )
                    );
        }
        
        //Cuisine:
        $terms = get_the_term_list( $recipe_id, 'rpr_cuisine', '', ', ');
        $tax = get_taxonomy( 'rpr_cuisine' );
        if(!is_wp_error($terms) && $terms != '') {
        	$icon='';
        	if( RPReloaded::get_option( 'recipe_icons_display', 0 ) == 1 ){
        		$icon = '<i class="fa fa-flag" title="' . $tax->labels->name . '"></i> ';
        	} else {
        		$icon=$tax->labels->name . ': ';
        	}
            $out .=  sprintf(
                        '<span itemprop="recipeCuisine" class="category-list">%1s%2s</span>',
            			$icon,
                        get_the_term_list( $recipe_id, 'rpr_cuisine', '', __( '&nbsp;/&nbsp; ', 'recipepress-reloaded' ), '' )
                    );
        }
        
        // Custom Taxonomies:
        $done = array('rpr_category', 'rpr_tag', 'rpr_course', 'rpr_cuisine', 'rpr_ingredient');
        $taxonomies = get_option('rpr_taxonomies', array());
        foreach($taxonomies as $taxonomy => $options) {
            if( ! in_array($taxonomy, $done ) ){
                 $terms = get_the_term_list( $recipe_id, $taxonomy, '', ', ');
                 $tax = get_taxonomy( $taxonomy );
                if(!is_wp_error($terms) && $terms != '') {
                	$icon='';
                	if( RPReloaded::get_option( 'recipe_icons_display', 0 ) == 1 ){ 
                		$icon = '<i class="fa fa-list" title=' . $tax->labels->name . '></i> '; 
                	} else {
                		$icon=$tax->labels->name . ': '; 
                	}
                    $out .= sprintf(
                        '<span class="category-list">%1s%2s</span>',
                    	$icon,
                        get_the_term_list( $recipe_id, $taxonomy, '', __( '&nbsp;/&nbsp; ', 'recipepress-reloaded' ), '' )
                    );
                }
            }
         }
         
        if( $out != "" ) {
            return '<div class="entry-meta rpr_info_line" >'.$out.'</div>';
        } 
    }
 }
 
if ( !function_exists('the_recipe_taxonomy_bar') ) {
    function the_recipe_taxonomy_bar( $recipe_id ){
        echo get_the_recipe_taxonomy_bar( $recipe_id );
    }
} 

if ( !function_exists('get_the_recipe_taxonomy_term_list') ) {
	function get_the_recipe_taxonomy_term_list( $recipe_id, $taxonomy ){
		$out = '';
		
		if( isset( $taxonomy ) && $taxonomy != '' && taxonomy_exists( $taxonomy ) ){
			$terms =  get_the_terms( $recipe_id, $taxonomy);
			
			if( $terms) {
				foreach ( $terms as $term){
					$out .= '<li>';
					$out .= '<a href="' . get_term_link( $term, $taxonomy ) . '">';
					$out .= $term->name;
					$out .= '</a>';
					$out .= '</li>';
				}
			}
		}
		
		if( $out != '' ){
			return '<ul class="recipe-taxonomy entry-meta">' . $out . '</ul>';
		}
		
		return $out;
	}
}

if ( !function_exists('the_recipe_taxonomy_term_list') ) {
	function the_recipe_taxonomy_term_list( $recipe_id, $taxonomy ){
		echo get_the_recipe_taxonomy_term_list( $recipe_id, $taxonomy );
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
		
		if( isset( $recipe['rpr_recipe_servings'][0] ) &&  $recipe['rpr_recipe_servings'][0] != 0 ) {
			$out .= __( 'For: ', 'recipepress-reloaded' );
			$out .= '&nbsp;<span itemprop="recipeYield"><span class="recipe-information-servings">';
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

// ======= RECIPE NUTRITION BAR =======
if ( !function_exists('has_recipe_nutrition') ) {
	function has_recipe_nutrition( $recipe_id ){
		// Get the ID of the recipe:
			if( !isset( $recipe_id ) || !is_numeric( $recipe_id ) ){
				$recipe_id = get_post()->ID;
			}

			// Get the recipe
			$recipe = get_post_custom( $recipe_id );
			if ( RPReloaded::get_option( 'recipe_use_nutritional_info', 0 ) == 1 && ( $recipe['rpr_recipe_calorific_value'][0] + $recipe['rpr_recipe_fat'][0] +  $recipe['rpr_recipe_protein'][0] +  $recipe['rpr_recipe_carbohydrate'][0] ) >= 0 ){
				return true;
			}
		return false;		
	}
	
}

if ( !function_exists('get_the_recipe_nutrition') ) {
	function get_the_recipe_nutrition( $recipe_id ){
		$out='';

		if( RPReloaded::get_option( 'recipe_use_nutritional_info', 0 ) == 1 ) {
			// Get the ID of the recipe:
			if( !isset( $recipe_id ) || !is_numeric( $recipe_id ) ){
				$recipe_id = get_post()->ID;
			}

			// Get the recipe
			$recipe = get_post_custom( $recipe_id );

			//TODO: schema.org microformats!
			//TODO: format as list (dictionary)
			//TODO: display option for this template tag!
			
			if( isset( $recipe['rpr_recipe_nutrition_per'][0] ) ){
				$out .= '<div class="recipe-nutrition entry-meta" itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation">';
				$out .= '<span class="nutrition_per" itemprop="servingSize">';
			
			
				switch( $recipe['rpr_recipe_nutrition_per'][0] ) {
					case 'per_100g':
						$out .= __('Per 100g', 'recipepress-reloaded' );
						break;
					case 'per_portion':
						$out .= __('Per portion', 'recipepress-reloaded' );
						break;
					case 'per_recipe':
						$out .= __('Per recipe', 'recipepress-reloaded' );
						break;
					default:
						$out .= __('Per 100g', 'recipepress-reloaded' );
				}
			
			
				$out .= '</span>';
				$out .= '<dl>';
				
				if( isset( $recipe['rpr_recipe_calorific_value'][0] ) ){
					$out .= sprintf( __( '<dt>Energy:</dt><dd itemprop="calories"> %1s kcal / %2s kJ</dd>', 'recipepress-reloaded' ), $recipe['rpr_recipe_calorific_value'][0], round( 4.18*$recipe['rpr_recipe_calorific_value'][0] ) );
				}
				if( isset( $recipe['rpr_recipe_fat'][0] ) ){
					$out .= sprintf( __( '<dt>Fat:</dt><dd itemprop="fatContent">%s g</dd>', 'recipepress-reloaded' ), $recipe['rpr_recipe_fat'][0] );
				}
				if( isset( $recipe['rpr_recipe_protein'][0] ) ){
					$out .= sprintf( __( '<dt>Protein:</dt><dd itemprop="proteinContent">%s g</dd>', 'recipepress-reloaded' ), $recipe['rpr_recipe_protein'][0] );
				}
				if( isset( $recipe['rpr_recipe_carbohydrate'][0] ) ){
					$out .= sprintf( __( '<dt>Carbohydrate:</dt><dd itemprop="carbohydrateContent">%s g</dd>', 'recipepress-reloaded' ), $recipe['rpr_recipe_carbohydrate'][0] );
				}
				$out .= '</dl>';
				$out .= '</div>';
			}
		}
		return $out;
	}
}

if ( !function_exists('the_recipe_nutrition') ) {
	function the_recipe_nutrition( $recipe_id ){
		echo get_the_recipe_nutrition( $recipe_id);
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
			
				$out .= '<li itemprop="ingredients"';
				if(isset($ingredient['group'])){
					$out .= 'class="ingredient-in-group" ';
				}
				$out .= '>';
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
						$out .= '<a href="'.$custom_link.'" class="custom-ingredient-link" target="'.RPReloaded::get_option('recipe_ingredient_custom_links_target', '_blank').'">';
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
			$out.='<p class="warning">'.__('No ingredients could be found for this recipe.', 'recipepress-reloaded' ).'</p>';
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
if ( !function_exists('get_the_recipe_times') ) {
	function get_the_recipe_times( $recipe_id='' ){
		$out='';
		 
		// Get the ID of the recipe:
		if( !isset( $recipe_id ) || !is_numeric( $recipe_id ) ){
			$recipe_id = get_post()->ID;
		}
		 
		// Get the recipe
		$recipe = get_post_custom( $recipe_id );

		if( isset($recipe['rpr_recipe_prep_time'][0]) && $recipe['rpr_recipe_prep_time'][0] != '' ) {
			$out .= '<dt>';
			if( RPReloaded::get_option( 'recipe_icons_display', 0 ) == 1 ){
				$out .= '<i class="fa fa-cog recipe-times-icon" title="'.__( 'Preparation Time', 'recipepress-reloaded' ).'"></i><span class="hidden recipe-times-text">' . __( 'Preparation', 'recipepress-reloaded' ) . '</span>';
			} else {
				$out .= __( 'Preparation', 'recipepress-reloaded' );
			}
			//$out .= '<span class="recipe-times-name">' . __( 'Preparation', 'recipepress-reloaded' ) . '</span>';
			$out .= '</dt>';
			$out .= '<dd>';
			$out .= '<meta itemprop="prepTime" content="PT'.$recipe['rpr_recipe_prep_time'][0].'M">'.$recipe['rpr_recipe_prep_time'][0].' <span class="recipe-information-time-unit">'.__( 'min.', 'recipepress-reloaded' ).'</span>';
			$out .= '</dd>';
		}
		
		if( isset($recipe['rpr_recipe_cook_time'][0]) && $recipe['rpr_recipe_cook_time'][0] != '' ) {
			$out .= '<dt>';
			if( RPReloaded::get_option( 'recipe_icons_display', 0 ) == 1 ){
				$out .= '<i class="fa fa-fire recipe-times-icon" title="'.__( 'Cook Time', 'recipepress-reloaded' ).'"></i><span class="hidden recipe-times-text">' . __( 'Cooking', 'recipepress-reloaded' ) . '</span>';
			} else {
				$out .= __( 'Cooking', 'recipepress-reloaded' );
			}
			//$out .= '<span class="recipe-times-name">' . __( 'Cooking', 'recipepress-reloaded' ) . '</span>';
			$out .= '</dt>';
			$out .= '<dd>';
			$out .= '<meta itemprop="cookTime" content="PT'.$recipe['rpr_recipe_cook_time'][0].'M">'.$recipe['rpr_recipe_cook_time'][0].' <span class="recipe-information-time-unit">'.__( 'min.', 'recipepress-reloaded' ).'</span>';
			$out .= '</dd>';
		}


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
		
		if($totaltime != 0 ) {
			$out .= '<dt>';
			if( RPReloaded::get_option( 'recipe_icons_display', 0 ) == 1 ){
				$out .= '<i class="fa fa-clock-o recipe-times-icon" title="'.__( 'Total Time', 'recipepress-reloaded' ).'"></i><span class="hidden recipe-times-text">' . __( 'Ready in', 'recipepress-reloaded' ) . '</span>';
			} else {
				$out .= __( 'Ready in', 'recipepress-reloaded' );
			}
			//$out .= '<span class="recipe-times-name">' . __( 'Ready in', 'recipepress-reloaded' ) . '</span>';
			$out .= '</dt>';
			$out .= '<dd>';
			$out .= '<meta itemprop="totalTime" content="PT'.$totaltime.'">'.$totaltime.' <span class="recipe-information-time-unit">'.__( 'min.', 'recipepress-reloaded' ).'</span>';
			$out .= '</dd>';
		}
		if( $out != '') {
			return '<dl class="entry-meta rpr_times" >'.$out.'</dl>';
		}
	}
}

if ( !function_exists('the_recipe_times') ) {
	function the_recipe_times( $recipe_id ){
		echo get_the_recipe_times( $recipe_id );
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
				if( isset($instruction['image']) && $instruction['image'] != "" && RPReloaded::get_option('recipe_images_clickable', '0') == 1 ) { $instr_class = "has_thumbnail"; }
				if( isset($instruction['image']) && $instruction['image'] != "" ) { $instr_class .= " " . RPReloaded::get_option('recipe_instruction_image_position', 'rpr_instrimage_right'); }
				
				$out .= '<span class="recipe-instruction '.$instr_class.'">'.$instruction['description'].'</span>';
			
				if( isset($instruction['image']) ) {
					//if(RPReloaded::get_option('recipe_images_clickable', '0') == 1) {
					if( RPReloaded::get_option('recipe_instruction_image_position', 'rpr_instrimage_right') == 'rpr_instrimage_right' ) {
						
						$thumb = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
					} else{
						$thumb = wp_get_attachment_image_src( $instruction['image'], 'large' );
					}
					$thumb_url = $thumb['0'];
					$full_img = wp_get_attachment_image_src( $instruction['image'], 'full' );
					$full_img_url = $full_img['0'];
			
					if(RPReloaded::get_option('recipe_images_clickable', '0') == 1 && $thumb_url != "" ) {
						$out .= '<a href="' . $full_img_url . '" rel="lightbox" title="' . $instruction['description'] . '">';
						$out .= '<img class="'. RPReloaded::get_option('recipe_instruction_image_position', 'rpr_instrimage_right') .'" src="' . $thumb_url . '" width="'. $thumb[1].'" />';
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
			$out.='<p class="warning">'.__('No instructions could be found for this recipe.', 'recipepress-reloaded' ).'</p>';
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
