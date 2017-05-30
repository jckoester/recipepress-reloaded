<?php

/**
 * The template tags for recipepress-reloaded
 * 
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      0.8.0
 *
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/public
 *
 * Template tags are building blogs for templates and layouts. 
 * Small snippets that layout a specific piece of information like date, author, 
 * categories, ingredient list and so on.
 * These template tags can be used to make layouting easier. However it's always
 * possible to do the rendering of informatopn from scratch in any layout file.
 */

/**
 * Save the recipe id to a global variable so the template tags can access it
 * This is necessary to get the correct id especially for embedded recipes
 */
if( $recipe_post && $recipe_post->ID ){
	$GLOBALS['recipe_id'] = $recipe_post->ID;
}

/** ****************************************************************************
 * TAXONOMY RELATED TEMPLATE TAGS
 */

if( !function_exists( 'get_the_rpr_taxonomy_headline' ) ) {
	/**
	 * Render the headline for a given taxonomy
	 * 
	 * @since 0.8.0
	 * @param string $taxonomy
	 * @param string $icons
	 * @return string
	 */
	function get_the_rpr_taxonomy_headline($taxonomy, $icons=false ){
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
        
		/**
		 * Get the taxonomy
		 */
		$tax = get_taxonomy( $taxonomy );
				
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Add a third level heading for embedded recipes or a second level 
		 * heading for a standalone recipe
		 */
		if( recipe_is_embedded() ){
			$out .= '<h3>';
		}else{
			$out .= '<h2>';
		}
		
		/**
		 * Add icon if desired
		 */
		if( $icons ){
			/*
			 *  Get the icon class:
			 */
			if( $taxonomy === 'category' || $taxonomy === 'post_tag' ){
				$icon_class = esc_html( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', $taxonomy, 'icon_class' ), 'fa-list-ul' ) );
			} else{
				$icon_class = esc_html( AdminPageFramework::getOption( 'rpr_options', array( 'tax_custom', $optkey, 'icon_class' ), 'fa-list-ul' ) );
			}
            $prefix = '<i class="fa ' . $icon_class . '" title=' . esc_html( $tax->labels->name ) . '></i> ';
        } else {
        	$prefix = $tax->labels->name . ': ';
		}
		
		$out .= $tax->labels->name;
		
		if( recipe_is_embedded() ){
			$out .= '</h3>';
		}else{
			$out .= '</h2>';
		}
		/**
		 * Return the rendered headline
		 */
		return $out;
	}
}
if( !function_exists( 'the_rpr_taxonomy_headline' ) ) {
	/**
	 * Outputs the headline rendered above
	 * 
	 * @since 0.8.0
	 * @param type $taxonomy
	 * @param type $icons
	 */
	function the_rpr_taxonomy_headline( $taxonomy, $icons = false ){
		echo get_the_rpr_taxonomy_headline( $taxonomy, $icons );
	}
}


if( !function_exists( 'get_the_rpr_taxonomy_terms' ) ) {
	/**
	 * Does the actual rendering of the taxonomy bar
	 * 
	 * @since 0.8.0
	 * 
	 * @param string $taxonomy
	 * @param boolean $icons
	 * @param string $sep
	 * @return string $out rendered ouptut
	 */
	function get_the_rpr_taxonomy_terms( $taxonomy, $icons=false, $label=false, $sep='&nbsp;/&nsbp;' ) {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
    	
		/*
		 *  Create an empty output string
		 */
		$out = '';
		
		$terms = get_the_term_list( $recipe_id, $taxonomy, '', __( $sep, 'recipepress-reloaded' ), '' );
		$tax = get_taxonomy( $taxonomy );
		
		/*
		 *  Get the index of the tax_custom array:
		 */
		$optkey = get_opt_tax_custom_id( $taxonomy );
		
		/*
		 *  Add icons if set so:
		 */
		if( $icons ){
			/*
			 *  Get the icon class:
			 */
			if( $taxonomy === 'category' || $taxonomy === 'post_tag' ){
				$icon_class = esc_html( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', $taxonomy, 'icon_class' ), 'fa-list-ul' ) );
			} else{
				$icon_class = esc_html( AdminPageFramework::getOption( 'rpr_options', array( 'tax_custom', $optkey, 'icon_class' ), 'fa-list-ul' ) );
			}
            $prefix = '<i class="fa ' . $icon_class . '" title=' . esc_html( $tax->labels->name ) . '></i> ';
        } elseif( $label && $tax ) {
        	$prefix = $tax->labels->name . ': ';
		} else {
			$prefix = ""; 
		}
		
		/*
		 *  Get the structured data property:
		 */
		if( $taxonomy === 'category'){
			$property_id = 'recipeCategory';
		} elseif( $taxonomy === 'post_tag' ){
			$property_id = 'keywords';
		} else{
			$property_id = esc_html( AdminPageFramework::getOption( 'rpr_options', array( 'tax_custom', $optkey, 'property_id' ), '' ) );
		}
		
		/*
		 *  Render the structured data property string
		 */
		$struct = '';
		if( $property_id ){
			if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ){
				$struct = 'itemprop="' . esc_html( $property_id ) . '"';
			} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ) {
				$struct = 'property="' . esc_html( $property_id ) . '"';
			}
		}
		
		/*
		 *  Only display the term list if we have terms assigned to the recipe:
		 */
		if( $terms && ! is_wp_error( $terms) ) {
				$out .= sprintf(
                    '<span %1s class="term-list">%2s%3s</span>',
					$struct,
                	$prefix,
                    $terms
                    );
				$out .= '&nbsp;';
		}
		
		/*
		 *  return the rendered output
		 */
		return $out;
	}
}

if ( !function_exists( 'the_rpr_taxonomy_terms' ) ) {
    /**
     * Outputs the rendered taxonomy bar
     * 
     * @since 0.8.0
     * 
     * @param string $taxonomy
     * @param boolean $icons
     * @param string $sep
     */
    function the_rpr_taxonomy_terms( $taxonomy, $icons=false, $label=false, $sep='&nbsp;/&nbsp;' ){
        echo get_the_rpr_taxonomy_terms( $taxonomy, $icons, $label, $sep );
    }
}


if( !function_exists( 'get_the_rpr_taxonomy_list' ) ) {
	/**
	 * Does the actual rendering of the taxonomy bar
	 * 
	 * @since 0.8.0
	 * 
	 * @param boolean $icons
	 * @param string $sep
	 * @param boolean $term set to true to enforce inclusion of terms (if active) 
	 * @return string $out rendered ouptut
	 */
	function get_the_rpr_taxonomy_list( $icons=false, $label=false, $sep='&nbsp;/&nsbp;', $tags = false ) {
		/*
		 *  Define the output string 
		 */
        $out = "";
		
		/*
		 *  include categories to the taxonomy bar, if used:
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'categories', 'use' ), false ) ){
			/*
			 *  Only include if advanced setting enforces this
			 */
			if( AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'display_categories' ), false ) ){
				$out .= get_the_rpr_taxonomy_terms('category', $icons, $label, $sep);
				$out .= '&nbsp;';
			} else{
				$out .= '<div class="rpr-hidden">';
				$out .= get_the_rpr_taxonomy_terms('category', $icons, $label, $sep);
				$out .= '</div>';
			}
		}
		/*
		 * Include tags to the taxonomy bar if enforced and used.
		 * I believe tags belong to the recipe footer, not to the taxonomy bar
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'post_tag', 'use' ), false ) && $tags ){
			/*
			 *  Only include if advanced setting enforces this
			 */
			if( AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'display_tags' ), false ) ){
				$out .= get_the_rpr_taxonomy_terms('post_tag', $icons, $label, $sep);
				$out .= '&nbsp;';
			} else{
				$out .= '<div class="rpr-hidden">';
				$out .= get_the_rpr_taxonomy_terms('post_tag', $icons, $label, $sep);
				$out .= '</div>';
			}
		}
		
		/*
		 *  Add the custom taxonomies
		 */
		foreach( AdminPageFramework::getOption( 'rpr_options', array( 'tax_custom' ), array() ) as $taxonomy ){
			$out .= get_the_rpr_taxonomy_terms($taxonomy['slug'], $icons, $label, $sep);
		}
		
		/*
		 *  return the rendered output
		 */
		return $out;
	}
}
if ( !function_exists( 'the_rpr_taxonomy_list' ) ) {
	/**
	 * Outputs the rendered taxonomy bar
	 * 
	 * @since 0.8.0
	 * 
	 * @param boolean $icons
	 * @param string $sep
	 * @param boolean $term set to true to enforce inclusion of terms (if active) 
	 */
    function the_rpr_taxonomy_list( $icons=false, $label=false, $sep='&nbsp;/&nbsp;', $tags=false ){
        echo get_the_rpr_taxonomy_list( $icons, $label, $sep, $tags );
    }
}

/** ****************************************************************************
 * STRUCTURED DATA RELATED TEMPLATE TAGS
 */
if( !function_exists( 'get_the_rpr_structured_data_header' ) ){
	/**
	 * Structured data help search engines to recognize the type and content
	 * of posts. This tag inserts the appropriate header depending which of the 
	 * three strcutured data types by http://schema.org is set in the options
	 * 
	 * @since 0.8.0
	 * @return string
	 */
	function get_the_rpr_structured_data_header() {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
		$recipe = get_post_custom( $recipe_id );
    	
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Start the recipe output according to the structured data model
		 * Some data as Title, image, ... are rendered by the theme but need
		 * to be included here as well for SEO. Here they are hidden!
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ) {
			$out .= '<div itemscope itemtype="http://schema.org/Recipe">';
			$out .= '<span class="rpr_title rpr-hidden" itemprop="name">' . get_the_title( $recipe_id ) . '</span>';
			$out .= '<span class="rpr_author rpr-hidden" itemprop="author">' . get_the_author() . '</span>';
			$out .= '<span class="rpr_date rpr-hidden" itemprop="datePublished" content="' . get_the_time( 'Y-m-d' ) . '">' . 
				get_the_time( get_option('date_format') ) . '</span>';
			// Number of comments
			if( get_comments_number() > 0 ){
				$out .= '<div itemprop="interactionStatistic" itemscope itemtype="http://schema.org/InteractionCounter">';
				$out .= '<meta itemprop="interactionType" content="http://schema.org/CommentAction" />';
				$out .= '<meta itemprop="userInteractionCount" content="' . get_comments_number() . '" />';
				$out .= '</div>';
			}
			// Recipe image
			if( has_post_thumbnail() ) {
				$out .= '<img src="' . get_the_post_thumbnail_url( $recipe_id, 'thumbnail') .'" itemprop="image" class="rpr-hidden" />';
				$out .= '<link itemprop="thumbnailUrl" href="' . get_the_post_thumbnail_url( $recipe_id, 'thumbnail' ) . '" />';
			}
			
		} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ) {
			$out .= '<div vocab="http://schema.org/" typeof="Recipe">';
			$out .= '<span class="rpr_title rpr-hidden" property="name">' . get_the_title( $recipe_id ) . '</span>';
			$out .= '<span class="rpr_author rpr-hidden" property="author">' . get_the_author() . '</span>';
			$out .= '<meta class="rpr_date rpr-hidden" property="datePublished" content="' . get_the_time( 'Y-m-d' ) . '">' . 
				get_the_time( get_option('date_format') ) . '</meta>';
			// Number of comments
			if( get_comments_number() > 0 ){
				$out .= '<div property="interactionStatistic" typeof="InteractionCounter">';
				$out .= '<meta property="interactionType" content="http://schema.org/CommentAction" />';
				$out .= '<meta property="userInteractionCount" content="' . get_comments_number() . '" />';
				$out .= '</div>';
			}
			// Recipe image
			if( has_post_thumbnail() ) {
				$out .= '<img src="' . get_the_post_thumbnail_url( $recipe_id, 'thumbnail') .'" property="image" class="rpr-hidden" />';
				$out .= '<link property="thumbnailUrl" href="' . get_the_post_thumbnail_url( $recipe_id, 'thumbnail' ) . '" />';
			}
		} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'json-ld' ) {
			$out .= '<script type="application/ld+json">';
			$out .= '{';
			$out .= '"@context": "http://schema.org",';
			$out .= '"@type": "Recipe",';
			$out .= '"name": "' . get_the_title( $recipe_id ) . '",';
			$out .= '"author": "' . get_the_author() . '",';
			$out .= '"datePublished": "' . get_the_time( 'Y-m-d' ) . '",';
			// Number of comments
			if( get_comments_number() > 0 ){
				$out .= '"interactionStatistic": {';
				$out .= '"@type": "InteractionCounter",';
				$out .= '"interactionType": "http://schema.org/Comment",';
				$out .= '"userInteractionCount": "' . get_comments_number() . '"';
				$out .= '},';
			}
			// Recipe Image
			if( has_post_thumbnail() ) {
				$thumb_id = get_post_thumbnail_id();
				$img_url = wp_get_attachment_image_src( $thumb_id, 'thumbnail', true );
				$thumb_url = wp_get_attachment_image_src( $thumb_id, 'full', true );
				$out .= '"image": "' . $img_url[0] . '",'; //get_post_thumbnail( $recipe_id, 'medium' ) . '",';
				$out .= '"thumbnailUrl": "' . $thumb_url[0] . '",'; //get_post_thumbnail_url( $recipe_id, 'thumbnail' ) . '",';
			}
			// Description
			if( isset( $recipe['rpr_recipe_description'][0] ) ){
				$description = strip_tags($recipe['rpr_recipe_description'][0]);
				$description = preg_replace("/\s+/", " ", $description);
				$out .= '"description": "' . esc_html( $description ) . '",';
			}
			// Ingredients
			if( isset( $recipe['rpr_recipe_ingredients'][0] ) && count( $recipe['rpr_recipe_ingredients'][0] ) > 0 ) {
				$out .= '"recipeIngredient": [';
				$ingredients = unserialize( $recipe['rpr_recipe_ingredients'][0] );
				
				foreach( $ingredients as $ingredient ){
					if( !isset( $ingredient['grouptitle'] ) ){
						if( isset( $ingredient['ingredient_id'] ) ){
							$term = get_term_by( 'id', $ingredient['ingredient_id'], 'rpr_ingredient' );
						} else {
							$term = get_term_by( 'name', $ingredient['ingredient'], 'rpr_ingredient' );
						}

						$out .= '"' . esc_html( $ingredient['amount'] ) . ' ' . esc_html( $ingredient['unit'] ) . ' ' . $term->name;
						if( isset( $ingredient['notes'] ) && $ingredient['notes'] != '' ){
							$out .= ', ' . esc_html( $ingredient['notes'] );
						}
						$out .= '",';
					}
				}
				$out = rtrim($out, ",");
				$out .='], ';
			}
			// Instructions
			if( isset( $recipe['rpr_recipe_instructions'][0] ) && count( $recipe['rpr_recipe_instructions'][0] ) > 0 ) {
				$instructions = unserialize( $recipe['rpr_recipe_instructions'][0] );
				
				$out .= '"recipeInstructions": "';
				foreach( $instructions as $instruction ){
					if( !isset( $instruction['grouptitle'] ) ){
						$out .= $instruction['description'];
					}
				}
				$out .= '",';
			}
			// Metadata like servings, nutrtion, ...
			if( isset( $recipe['rpr_recipe_servings'][0] ) ){
				$out .= '"recipeYield": "' . esc_html( $recipe['rpr_recipe_servings'][0] ) . ' ' . esc_html( $recipe['rpr_recipe_servings_type'][0] ) . '",';
			}
			if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'use_nutritional_data' ), false ) == true &&
			( $recipe['rpr_recipe_calorific_value'][0] + $recipe['rpr_recipe_fat'][0] +  $recipe['rpr_recipe_protein'][0] +  $recipe['rpr_recipe_carbohydrate'][0] ) >= 0 ) {
				$out .= '"nutrition": {';
				$out .= '"@type": "NutritionInformation",';
				
				if( isset( $recipe['rpr_recipe_calorific_value'][0] ) ){
					$out .= '"calories": "' . esc_html( $recipe['rpr_recipe_calorific_value'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_carbohydrate'][0] ) ){
					$out .= '"carbohydrateContent": "' . esc_html( $recipe['rpr_recipe_carbohydrate'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_cholesterol'][0] ) ){
					$out .= '"cholesterolContent": "' . esc_html( $recipe['rpr_recipe_cholesterol'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_fat'][0] ) ){
					$out .= '"fatContent": "' . esc_html( $recipe['rpr_recipe_fat'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_fibre'][0] ) ){
					$out .= '"fibreContent": "' . esc_html( $recipe['rpr_recipe_fibre'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_protein'][0] ) ){
					$out .= '"proteinContent": "' . esc_html( $recipe['rpr_recipe_protein'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_saturatedFat'][0] ) ){
					$out .= '"saturatedFatContent": "' . esc_html( $recipe['rpr_recipe_saturatedFat'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_sodium'][0] ) ){
					$out .= '"sodiumContent": "' . esc_html( $recipe['rpr_recipe_sodium'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_sugar'][0] ) ){
					$out .= '"sugarContent": "' . esc_html( $recipe['rpr_recipe_sugar'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_transFat'][0] ) ){
					$out .= '"transFatContent": "' . esc_html( $recipe['rpr_recipe_transFat'][0] ) . '",';
				}
				if( isset( $recipe['rpr_recipe_unsaturatedFat'][0] ) ){
					$out .= '"unsaturatedFatContent": "' . esc_html( $recipe['rpr_recipe_unsaturatedFat'][0] ) . '",';
				}
				$out = rtrim($out, ",");
				$out .= '},';
			}
			// Source
			if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'use_source') , false ) ) {
				if( isset( $recipe['rpr_recipe_source'] ) ) {
					$out .= '"citation": "' . esc_html( $recipe['rpr_recipe_source'][0] ) . '",';
				}
			}
			// Times
			// fix missing times:
			if( !isset( $recipe['rpr_recipe_prep_time'][0] ) ){
				$recipe['rpr_recipe_prep_time'][0] = 0;
			}
			if( !isset( $recipe['rpr_recipe_cook_time'][0] ) ){
				$recipe['rpr_recipe_cook_time'][0] = 0;
			}
			if( !isset( $recipe['rpr_recipe_passive_time'][0] ) ){
				$recipe['rpr_recipe_passive_time'][0] = 0;
			}
			if( $recipe['rpr_recipe_prep_time'][0] != 0 ) {
				$out .= '"prepTime": "' . rpr_format_time_xml( $recipe['rpr_recipe_prep_time'][0] ) . '",';
			}
			if( $recipe['rpr_recipe_cook_time'][0] != 0 ) {
				$out .= '"cookTime": "' . rpr_format_time_xml( $recipe['rpr_recipe_cook_time'][0] ) . '",';
			}
			if(  $recipe['rpr_recipe_prep_time'][0] + $recipe['rpr_recipe_cook_time'][0] +  $recipe['rpr_recipe_passive_time'][0]  > 0 ) {
				$out .= '"totalTime": "' . rpr_format_time_xml( $recipe['rpr_recipe_prep_time'][0] + $recipe['rpr_recipe_cook_time'][0] + $recipe['rpr_recipe_passive_time'][0] ) . '",';
			}
			$out = rtrim($out, ",");
			$out .= '}';
			$out .= '</script>';
		}
		/**
		 * return the renderd output
		 */
		return $out;
	}
}

if( !function_exists( 'the_rpr_structured_data_header') ) {
	/**
	 * Outputs the structured data header from above
	 * 
	 * @since 0.8.0
	 */
	function the_rpr_structured_data_header() {
		echo get_the_rpr_structured_data_header();
	}
}

if( !function_exists( 'get_the_rpr_structured_data_footer' ) ){
	/**
	 * Defines a proper closing for the structured data header defined above
	 * telling search engines the end of the recipe.
	 * 
	 * @since 0.8.0
	 * @return string
	 */
	function get_the_rpr_structured_data_footer() {
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		
		/**
		 * Close the recipe structure properly according to the structured 
		 * data format
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ){
			$out .= '</div>';
		} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ){
			$out .= '</div>';
		}
		/**
		 * return the renderd output
		 */
		return $out;
	}
}

if( !function_exists( 'the_rpr_structured_data_footer') ) {
	/**
	 * Outputs the structured data footer from above
	 * 
	 * @sonce 0.8.0
	 */
	function the_rpr_structured_data_footer() {
		echo get_the_rpr_structured_data_footer();
	}
}

/** ***************************************************************************
 * RECIPE MAIN DATA TEMPLATE TAGS
 */
if( !  function_exists( 'get_the_rpr_recipe_description' ) ){
	/**
	 * Renders the description. No output if description is empty.
	 * 
	 * @since 0.8.0
	 * @return string
	 */
	function get_the_rpr_recipe_description() {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
        $recipe = get_post_custom( $recipe_id );
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Render the description only if it is not empty
		 */
		if( strlen( $recipe['rpr_recipe_description'][0] ) > 0 ) {
			$out .= '<span class="rpr_description" ';
			if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ){
				$out .= ' itemprop="description" >';
			} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ){
				$out .= ' property="description" >';
			} else {
				$out .= '>';
			}
                        $out .= sanitize_post_field( 'rpr_recipe_description', $recipe['rpr_recipe_description'][0], $recipe_id);
	//		$out .= apply_filters('the_content', $recipe['rpr_recipe_description'][0] );
			$out .= '</span>';
		}
		
		/**
		 * Return the rendered description
		 */
		return $out;
	}
}

if( !  function_exists( 'the_rpr_recipe_description' ) ){
	/**
	 * Outputs the rendered description
	 * 
	 * @since 0.8.0
	 */
	function the_rpr_recipe_description() {
		echo get_the_rpr_recipe_description();
	}
}

if( !  function_exists( 'get_the_rpr_recipe_ingredients_headline' ) ){
	/**
	 * Renders the headline for ingredient list.
	 * Icons are optional, headline level depends on embedded or standalone 
	 * recipe
	 * 
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_ingredients_headline( $icons ) {
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Add a third level heading for embedded recipes or a second level 
		 * heading for a standalone recipe
		 */
		if( recipe_is_embedded() ){
			$out .= '<h3>';
		}else{
			$out .= '<h2>';
		}
		
		/**
		 * Add icon if desired
		 */
		if( $icons ){
			$out .= '<i class="' . esc_attr( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'icon_class' ), 'fa fa-shopping-cart' ) ) . '"></i>&nbsp;';
		}
		
		$out .= __( 'Ingredients', 'recipepress-reloaded' );
		
		if( recipe_is_embedded() ){
			$out .= '</h3>';
		}else{
			$out .= '</h2>';
		}
		/**
		 * Return the rendered headline
		 */
		return $out;
	}
}

if( !  function_exists( 'the_rpr_recipe_ingredients_headline' ) ){
	/**
	 * Outputs the rendered headline
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_ingredients_headline( $icons=false ) {
		echo get_the_rpr_recipe_ingredients_headline( $icons );
	}
}

if( !  function_exists( 'get_the_rpr_recipe_ingredients' ) ){
	/**
	 * Renders the ingredient list
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_ingredients( $icons=false ){
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
        $recipe = get_post_custom( $recipe_id );

		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 *  Get the ingredients:
		 */
		$ingredients = unserialize( $recipe['rpr_recipe_ingredients'][0] );
		
		if( count( $ingredients ) > 0 ) {
			/**
			* Loop over all the ingredients
			*/
			$i =0;
			if( is_array( $ingredients ) ){
				foreach ( $ingredients as $ingredient ){
					/**
					 * Check if the ingredient is a grouptitle
					 */
					if( isset( $ingredient['grouptitle'] ) ){

						/**
						 * Render the grouptitle
						 */
						$out .= rpr_render_ingredient_grouptitle( $ingredient );
					} else {
						/**
						 * Start the list on the first item
						 */
						 if( $i == 0 ) {
						//if( isset( $ingredient['sort'] ) && $ingredient['sort'] == 1 ){
							$out .= '<ul class="rpr-ingredient-list" >';
						}
						/**
						 * Render the ingredient line
						 */
						$out .= rpr_render_ingredient_line( $ingredient );
						/**
						 * Close the list on the last item
						 */
						if( isset( $ingredient['sort'] ) && $ingredient['sort'] == count( $ingredients ) ){
							$out .= '</ul>';
						}
					}
					$i++;
				}
			}
		   /**
             * Close the list on the last item
             */	
            $out .= '</ul>';
		} else {
			/**
			 * Issue a warning, if there are no ingredients for the recipe
			 */
			$out .= '<p class="warning">' . __( 'No ingredients could be found for this recipe.', 'recipepress-reloaded' ) . '</p>';
		}
		
		
		/**
		 * Return the rendered ingredient list
		 */
		return $out;
	}
	
	/**
	 * Renders the ingredient group headline
	 * 
	 * @since 0.8.0
	 * @param array $ingredient
	 * @return string
	 */
	function rpr_render_ingredient_grouptitle( $ingredient ){
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		if( $ingredient['sort'] === 0 ){
			/**
			 * Do not close the ingredient list of the previous group if this is 
			 * the first group
			*/
		} else {
			/**
			 * Close close the ingredient list of the previous group
			 */
			$out .= '</ul>';
		}
		
		/**
		 * Create the headline for the ingredient group
		 */
		if( recipe_is_embedded() ){
			/**
			 * Fourth level headline for embedded recipe
			 */
			$out .= '<h4 class="rpr-ingredient-group-title">' . esc_html( $ingredient['grouptitle'] ) . '</h4>';
		} else {
			/**
			 * Third level headline for standalone recipes
			 */
			$out .= '<h3 class="rpr-ingredient-group-title">' . esc_html( $ingredient['grouptitle'] ) . '</h3>';
		}
		
		/** 
		 * Start the list for this ingredient group
		 */
		$out .= '<ul class="rpr-ingredient-list">';
		
		/**
		 * Return the rendered output
		 */
		return $out;
	}
	
	/**
	 * Render the actual ingredient line
	 * 
	 * @since 0.8.0
	 * @param array $ingredient
	 * @return string
	 */
	function rpr_render_ingredient_line( $ingredient ){
		/**
		 * Get the term object for the ingredient
		 */
		if( isset( $ingredient['ingredient_id'] ) && get_term_by( 'id', $ingredient['ingredient_id'], 'rpr_ingredient' ) ){
			$term = get_term_by( 'id', $ingredient['ingredient_id'], 'rpr_ingredient' );
		} else {
			$term = get_term_by( 'name', $ingredient['ingredient'], 'rpr_ingredient' );
		}
                		
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Start the line
		 */
		$out .= '<li class="rpr-ingredient">';
		
		/**
		 * Add the structured data properties
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ){
			$out .= '<span itemprop="recipeIngredient" >';
		} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ){
			$out .= '<span property="recipeIngredient" >';
		}
			
		/**
		 * Render amount
		 */
		$out .= '<span class="recipe-ingredient-quantity">' . esc_html( $ingredient['amount'] ) . '</span>&nbsp;';
		
		/**
		 * Render the unit
		 */
		$out .= '<span class="recipe-ingredient-unit">' . esc_html( $ingredient['unit'] ) . '</span>&nbsp;';
	
		/**
		 * Render the ingredient link according to the settings
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'link_target' ), 2 ) == 0 ){
			/**
			 * Set no link
			 */
			$closing_tag = '&nbsp;';
		} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'link_target' ), 2 ) == 1 ){
			/**
			 * Set link to archive
			 */
			$out .= '<a href="' . get_term_link( $term->slug, 'rpr_ingredient' ) . '">';
			$closing_tag = '</a>&nbsp;';
		} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'link_target' ), 2 ) == 2 ){
			/**
			 * Set custom link if available, link to archive if not
			 */
			if( isset( $ingredient['link'] ) && $ingredient['link'] != '' ){
				$out .= '<a href="' . esc_url( $ingredient['link'] ) . '" target="_blank" >';
				$closing_tag = '</a>';
			} else {
				$out .= '<a href="' . get_term_link( $term->slug, 'rpr_ingredient' ) . '">';
			}
			
			$closing_tag ='</a>&nbsp;';
		} else{ 
			/**
			 * Set custom link if available, no link if not
			 */
			if( isset( $ingredient['link'] ) && $ingredient['link'] != '' ){
				$out .= '<a href="' . esc_url( $ingredient['link'] ) . '" target="_blank" >';
				$closing_tag = '</a>';
			} else {
				$closing_tag = '&nbsp;';
			}
		}
		
		/**
		 * Render the ingredient name
		 */
		if( isset( $ingredient['amount'] ) && $ingredient['amount'] > 1  && $ingredient['unit'] === '' && AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'auto_plural' ), 0 )){
			/**
			 * Use plural if amount > 1
			 */
			if( get_term_meta( $term->term_id, 'plural', true ) != '' ){
				$out .= '<span name="rpr-ingredient-name" >' . esc_html( get_term_meta( $term->term_id, 'plural', true ) ) . '</span>';
			} else {
				$out .= '<span name="rpr-ingredient-name" >' . $term->name . __( 's', 'recipepress-reloaded' ) . '</span>';
			}
		} else {
			/**
			 * Use singular
			 */
			$out .= '<span name="rpr-ingredient-name" >' . $term->name . '</span>';
		}
		
		$out .= $closing_tag;
	
		/**
		 * Render the ingredient note
		 */
		if ( isset( $ingredient['notes'] ) && $ingredient['notes'] != '' ){
			$out .= '<span class="rpr-ingredient-note">';
			/**
			 * Add the correct separator as set in the options
			 */
			if( AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'comment_sep' ), 0 ) == 0 ) {
				/**
				 * No separator
				 */
				$closing_tag = '';
			} elseif (AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'comment_sep' ), 0 ) == 1 ) {
				/**
				 * Brackets
				 */
				$out .= __( '(', 'reciperess-reloaded' );
				$closing_tag = __( ')', 'recipepress-reloaded' );
			} else {
				/**
				 * comma
				 */
				$out .= __( ',', 'recipepress-reloaded' );
				$closing_tag = '';
			}
			$out .= '&nbsp;' .  esc_html( $ingredient['notes'] ) . $closing_tag . '</span>';
			
		}
		
		/**
		 * End the structured data span
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) != 'json-ld' ){
			$out .= '</span>';
		}
		
		/**
		 * End the line
		 */
		$out .= '</li>';
		
		/**
		 * Return the rendered output
		 */
		return $out;
	}
}
if( !  function_exists( 'the_rpr_recipe_ingredients' ) ){
	/**
	 * Outputs the ingredient list rendered above
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_ingredients( $icons=false ){
		echo get_the_rpr_recipe_ingredients( $icons );
	}
}


if( !function_exists( 'get_the_rpr_recipe_instructions_headline' ) ){
	/**
	 * Renders the headline for instruction list.
	 * Icons are optional, headline level depends on embedded or standalone 
	 * recipe
	 * 
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_instructions_headline( $icons ) {
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Add a third level heading for embedded recipes or a second level 
		 * heading for a standalone recipe
		 */
		if( recipe_is_embedded() ){
			$out .= '<h3>';
		}else{
			$out .= '<h2>';
		}
		
		/**
		 * Add icon if desired
		 */
		if( $icons ){
			/**
			 * @todo: Create an option for instructions icon
			 */
			$out .= '<i class="fa fa-cogs"></i>&nbsp;';
			//$out .= '<i class="' . AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'icon_class' ), 'fa fa-shoppingcart' ) . '"></i>&nbsp;';
		}
		
		$out .= __( 'Instructions', 'recipepress-reloaded' );
		
		if( recipe_is_embedded() ){
			$out .= '</h3>';
		}else{
			$out .= '</h2>';
		}
		/**
		 * Return the rendered headline
		 */
		return $out;
	}
}

if( !  function_exists( 'the_rpr_recipe_instructions_headline' ) ){
	/**
	 * Outputs the rendered headline
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_instructions_headline( $icons=false ) {
		echo get_the_rpr_recipe_instructions_headline( $icons );
	}
}

if( !  function_exists( 'get_the_rpr_recipe_instructions' ) ){
    /**
     * Render the instructions list
     * 
     * @since 0.8.0
     * @return string
     */
    function get_the_rpr_recipe_instructions(){
        /**
         *  Get the recipe id
         */
        if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
            $recipe_id = $GLOBALS['recipe_id'];
        } else {
            $recipe_id = get_post()->ID;
        }
        $recipe = get_post_custom( $recipe_id );

        /**
         *  Create an empty output string
         */
        $out = '<div class="rpr_instruction">';

        /**
         *  Get the instructions:
         */
        $instructions = unserialize( $recipe['rpr_recipe_instructions'][0] );
		
        if( count( $instructions ) > 0 ) {
            /**
             * Add the structured data tag
             */
            if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ){
                $out .= '<span itemprop="recipeInstructions" >';
            } elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ){
                $out .= '<span property="recipeInstructions" >';
            }
            
            /**
             * Loop over all the ingredients
            */
			if( is_array( $instructions ) ){
				$i =0;
				foreach ( $instructions as $instruction ){
					/**
					 * Check if the ingredient is a grouptitle
					 */
					if( isset( $instruction['grouptitle'] ) ){		   
						/**
						 * Render the grouptitle
						 */
						$out .= rpr_render_instruction_grouptitle( $instruction );
					} else {

						if( $i == 0 ) {
								//isset( $instruction['sort'] ) && $instruction['sort'] == 0 ){
							/**
							 * Start the list on the first item
							 */
							$out .= '<ol class="rpr-instruction-list" >';
						}
						/**
						 * Render the instrcution block
						 */
						$out .= rpr_render_instruction_block( $instruction );
					}
					$i++;
				}
			}
            /**
             * Close the list on the last item
             */	
            $out .= '</ol>';
		   
            /**
             * Close the structured data tag
             */
            if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) != 'json-ld' ){
                $out .= '</span>';
            }	   
        } else {
            /**
             * Issue a warning, if there are no instructions for the recipe
             */
            $out .= '<p class="warning">' . __( 'No instructions could be found for this recipe.', 'recipepress-reloaded' ) . '</p>';
        }
        
        $out .= '</div>';
		
        /**
        * Return the rendered instructions list
        */
        return $out;
    }
	
    /**
     * Render the grouptitle for a instruction group
     * 
     * @since 0.8.0
     * @param array $instruction
     * @return string
     */
    function rpr_render_instruction_grouptitle( $instruction ){
        /**
         *  Create an empty output string
         */
        $out = '';
		
        if( $instruction['sort'] == 0 ){
            /**
            * Do not close the instruction list of the previous group if this is 
            * the first group
            */
        } else {
            /**
             * Close the instruction list of the previous group
             */
            $out .= '</ol>';
        }
	
        /**
         * Create the headline for the instruction group
         */
        if( recipe_is_embedded() ){
            /**
             * Fourth level headline for embedded recipe
             */
            $out .= '<h4 class="rpr-instruction-group-title">' . esc_html( $instruction['grouptitle'] ) . '</h4>';
        } else {
            /**
             * Third level headline for standalone recipes
             */
            $out .= '<h3 class="rpr-instruction-group-title">' . esc_html( $instruction['grouptitle'] ) . '</h3>';
        }
		
        /** 
         * Start the list for this ingredient group
         */
        $out .= '<ol class="rpr-instruction-list">';
		
        /**
         * Return the rendered output
         */
        return $out;
    }
	
    /**
     * Render an instruction block
     * 
     * @since 0.8.0
     * @param type $instruction
     * @return string
     */
    function rpr_render_instruction_block( $instruction ){
        /**
         *  Create an empty output string
         */
        $out = '';
		
        /**
         * Start the line
         */
        $out .= '<li class="rpr-instruction">';
		
        /** 
         * Determine the class for the instruction text depending on image options
         */
        if( isset( $instruction['image'] ) && $instruction['image'] != '' ){
            $instr_class = " has_thumbnail";
            $instr_class .= ' ' . esc_attr( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'images_instr_pos' ), 'right' ) ); 
        } else {
            $instr_class = "";
        }
		
        /**
         * Render the instruction text
         */
        $out .= '<span class="rpr-recipe-instruction-text' . $instr_class . '">' . esc_html( $instruction['description'] ) . '</span>' ;
		
        /**
         * Render the instruction step image
         */
        if( isset( $instruction['image'] ) && $instruction['image'] != '' ){
            /**
             * Get the image data
             */
            if( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'images_instr_pos' ), 'right' ) === 'right' ){
                $img = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
            } else {
                $img = wp_get_attachment_image_src( $instruction['image'], 'large' );
            }

            /**
             * Get link target for clickable images:
             */
            if( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'images_link' ), true ) && is_array( $img ) && $img[0] != '' ){
                $img_full = wp_get_attachment_image_src( $instruction['image'], 'full' );
                $out .= '<a class="rpr_img_link" href="' . esc_url( $img_full[0] ) . '" rel="lightbox" title="' . esc_html( substr( $instruction['description'], 150) ) . '">';
            }

            /**
             * Render the image
             */
            $out .= '<img class="';
            $out .= esc_attr( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'images_instr_pos' ), 'right' ) );
            $out .= '" src="' . esc_url( $img[0] ) . '" width="'. esc_attr( $img[1] ) .'" height="'. esc_attr( $img[2] ) .'" />';
			
            /**
             * Close the link for clickable images
             */
            if( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'images_link' ), true ) && is_array( $img ) && $img[0] != '' ){
                $out .= '</a>';
            }
        }
		
        /**
         * End the line
         */
        $out .= '</li>';
		
        /**
         * Return the rendered output
         */
        return $out;
    }
}
if( ! function_exists( 'the_rpr_recipe_instructions' ) ){
	function the_rpr_recipe_instructions(){
		echo get_the_rpr_recipe_instructions();
	}
}
if( !  function_exists( 'get_the_rpr_recipe_notes_headline' ) ){
	/**
	 * Renders the headline for notes.
	 * Icons are optional, headline level depends on embedded or standalone 
	 * recipe
	 * 
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_notes_headline( $icons ) {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}

		$recipe = get_post_custom( $recipe_id );
		
		/**
		 * Exit if recipe has no notes:
		 * isset returns true with empty strings, also check if notes is empty
		 */
		if( isset( $recipe['rpr_recipe_notes'][0] ) && empty( $recipe['rpr_recipe_notes'][0] ) ) {
			return;
		}
		
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Add a third level heading for embedded recipes or a second level 
		 * heading for a standalone recipe
		 */
		if( recipe_is_embedded() ){
			$out .= '<h3>';
		}else{
			$out .= '<h2>';
		}
		
		/**
		 * Add icon if desired
		 */
		if( $icons ){
			/**
			 * @todo: Create an option for notes icon
			 */
			$out .= '<i class="fa fa-paperclip"></i>&nbsp;';
			//$out .= '<i class="' . AdminPageFramework::getOption( 'rpr_options', array( 'tax_builtin', 'ingredients', 'icon_class' ), 'fa fa-shoppingcart' ) . '"></i>&nbsp;';
		}
		
		$out .= __( 'Notes', 'recipepress-reloaded' );
		
		if( recipe_is_embedded() ){
			$out .= '</h3>';
		}else{
			$out .= '</h2>';
		}
		/**
		 * Return the rendered headline
		 */
		return $out;
	}
}

if( !  function_exists( 'the_rpr_recipe_notes_headline' ) ){
	/**
	 * Outputs the rendered headline
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_notes_headline( $icons=false ) {
		echo get_the_rpr_recipe_notes_headline( $icons );
	}
}

if( !  function_exists( 'get_the_rpr_recipe_notes' ) ){
	/**
	 * Renders the notes. No output if no notes saved.
	 * 
	 * @since 0.8.0
	 * @return string
	 */
	function get_the_rpr_recipe_notes() {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}

		$recipe = get_post_custom( $recipe_id );

		/**
		 * Exit if recipe has no notes:
		 * isset returns true with empty strings, also check if notes is empty
		 */
		if( isset( $recipe['rpr_recipe_notes'][0] ) && empty( $recipe['rpr_recipe_notes'][0] ) ) {
			return;
		}

		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Render the notes only if it is not empty
		 */
		if( isset( $recipe['rpr_recipe_notes'][0] ) &&  strlen( $recipe['rpr_recipe_notes'][0] ) > 0 ) {
			$out .= '<span class="rpr_notes" >';
			$out .= apply_filters('the_content', $recipe['rpr_recipe_notes'][0] );
			$out .= '</span>';
		}
		
		/**
		 * Return the rendered description
		 */
		return $out;
	}
}

if( !  function_exists( 'the_rpr_recipe_notes' ) ){
	/**
	 * Outputs the rendered notes
	 * 
	 * @since 0.8.0
	 */
	function the_rpr_recipe_notes() {
		echo get_the_rpr_recipe_notes();
	}
}

/** ****************************************************************************
 * META DATA TEMPLATE TAGS
 */
if( !function_exists( 'get_the_rpr_recipe_servings' ) ){
	/**
	 * Renders the serving size information
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_servings( $icons ) {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
        $recipe = get_post_custom( $recipe_id );

		/**
		 * Return if no servings are saved
		 */
		if( !isset( $recipe['rpr_recipe_servings'][0] ) ){
			return;
		}

		/**
		 *  Create an empty output string
		 */
		$out = '';
		
				
		/**
		 * Add servings in the correct structured data format
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ){
			$out .= '<div itemprop="recipeYield" class="rpr_servings" >';
		} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ){
			$out .= '<div property="recipeYield" class="rpr_servings" >';
		} else {
			$out .= '<div class="rpr_servings">';
		}
		
		/** 
		 * Add icon if set to do so:
		 */
		if( $icons ) {
			/**
			 * @todo: add option for icon class
			 */
			$out .= '<i class="fa fa-pie-chart"></i>&nbsp;';
		} else {
			$out .= __( 'For:' , 'recipepress-reloaded' );
			$out .= '&nbsp;';
		}
		
		$out .= '<span class="rpr_servings" >' . esc_html( $recipe['rpr_recipe_servings'][0] ) . '</span>&nbsp;';
		$out .= '<span class="rpr_servings_type" >' . esc_html( $recipe['rpr_recipe_servings_type'][0] ) . '</span>';
		
		$out .= '</div>';
		/**
		 * Return the rendered servings data
		 */
		return $out;
	}
}
if( !function_exists( 'the_rpr_recipe_servings' ) ){
	/**
	 * Outputs the servings rendered above
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_servings( $icons=false ) {
		echo get_the_rpr_recipe_servings( $icons );
	}
}


if( !function_exists( 'get_the_rpr_recipe_nutrition_headline' ) ) {
	/**
	 * Render the headline for the recipe times
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_nutrition_headline( $icons=false ){
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Add a third level heading for embedded recipes or a second level 
		 * heading for a standalone recipe
		 */
		if( recipe_is_embedded() ){
			$out .= '<h3>';
		}else{
			$out .= '<h2>';
		}
		
		/**
		 * Add icon if desired
		 */
		if( $icons ){
            $out .= '<i class="fa fa-fire" title=' . __( 'Nutritional data', 'recipepress-reloaded' ) . '></i> ';
		}
		
		$out .= __( 'Nutritional data', 'recipepress-reloaded' );
		
		if( recipe_is_embedded() ){
			$out .= '</h3>';
		}else{
			$out .= '</h2>';
		}
		/**
		 * Return the rendered headline
		 */
		return $out;
	}
}
if( !function_exists( 'the_rpr_recipe_nutrition_headline' ) ) {
	/**
	 * Outputs the headline rendered above
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_nutrition_headline( $icons = false ){
		echo get_the_rpr_recipe_nutrition_headline( $icons );
	}
}

if( !function_exists( 'get_the_rpr_recipe_nutrition' ) ){
	/**
	 * Renders the nutritional information
	 * 
	 * @since 0.8.0
	 * @todo: Add icons if desired by option
	 * 
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_nutrition( $icons=false ) {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
        $recipe = get_post_custom( $recipe_id );

		/**
		 * Return if no nutritional data are saved or nutritional data is not 
		 * enabled
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'use_nutritional_data' ), false ) === false ||
			( $recipe['rpr_recipe_calorific_value'][0] + $recipe['rpr_recipe_fat'][0] +  $recipe['rpr_recipe_protein'][0] +  $recipe['rpr_recipe_carbohydrate'][0] ) <= 0 ) {
			return;
		}
		
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
				
		/**
		 * Add nutritional data in the correct structured data format
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ){
			$out .= '<div itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation" class="rpr_nutritional_data">';
			$out .= '<span class="nutrition_per" itemprop="servingSize">';
			$struct_calo = 'itemprop="calories" ';
			$struct_carb = 'itemprop="carbohydrateContent"';
			$struct_chol = 'itemprop="cholesterolContent"';
			$struct_fat  = 'itemprop="fatContent"';
			$struct_fibe = 'itemprop="fibreContent"';
			$struct_prot = 'itemprop="proteinContent"';
			$struct_satu = 'itemprop="saturatedFatContent"';
			$struct_sodi = 'itemprop="sodiumContent"';
			$struct_suga = 'itemprop="sugarContent"';
			$struct_tran = 'itemprop="transFatContent"';
			$struct_unsa = 'itemprop="unsaturatedFatContent"';
		} elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ){
			$out .= '<div property="nutrition" typeof="NutritionInformation" class="rpr_nutritional_data">';
			$out .= '<span class="nutrition_per" property="servingSize">';
			$struct_calo = 'property="calories" ';
			$struct_carb = 'property="carbohydrateContent"';
			$struct_chol = 'property="cholesterolContent"';
			$struct_fat  = 'property="fatContent"';
			$struct_fibe = 'property="fibreContent"';
			$struct_prot = 'property="proteinContent"';
			$struct_satu = 'property="saturatedFatContent"';
			$struct_sodi = 'property="sodiumContent"';
			$struct_suga = 'property="sugarContent"';
			$struct_tran = 'property="transFatContent"';
			$struct_unsa = 'property="unsaturatedFatContent"';
		} else {
			$out .= '<div class="rpr_nutritional_data" >';
			$out .= '<span class="nutrition_per">';
			$struct_calo = '';
			$struct_carb = '';
			$struct_chol = '';
			$struct_fat  = '';
			$struct_fibe = '';
			$struct_prot = '';
			$struct_satu = '';
			$struct_sodi = '';
			$struct_suga = '';
			$struct_tran = '';
			$struct_unsa = '';
		}
		
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
		
		/**
		 * These are only the basic nutritional data as of rpr v0.8
		 * to be extended in the future to acomplete set of nutritional data
		 * as described here: http://1.schemaorgae.appspot.com/NutritionInformation
		 */
		if( isset( $recipe['rpr_recipe_calorific_value'][0] ) ){
			$out .= sprintf(  '<dt>' . __( 'Energy:', 'recipepress-reloaded') . '</dt><dd ' . $struct_calo . '> %1s kcal / %2s kJ</dd>', esc_html( $recipe['rpr_recipe_calorific_value'][0] ), esc_html( round( 4.18*$recipe['rpr_recipe_calorific_value'][0] ) ) );
		}
		if( isset( $recipe['rpr_recipe_fat'][0] ) ){
			$out .= sprintf( '<dt>' . __(  'Fat:', 'recipress-reloaded' ) . '</dt><dd ' . $struct_fat . '>%s g</dd>', esc_html( $recipe['rpr_recipe_fat'][0] ) );
		}
		if( isset( $recipe['rpr_recipe_protein'][0] ) ){
			$out .= sprintf( '<dt>' . __( 'Protein:' , 'recipepress-reloaded' ) . '</dt><dd ' . $struct_prot . '>%s g</dd>', esc_html( $recipe['rpr_recipe_protein'][0] ) );
		}
		if( isset( $recipe['rpr_recipe_carbohydrate'][0] ) ){
			$out .= sprintf( '<dt>' . __( 'Carbohydrate:', 'recipepress-reloaded' ) . '</dt><dd ' . $struct_carb . '>%s g</dd>', esc_html( $recipe['rpr_recipe_carbohydrate'][0] ) );
		}
		
		$out .= '</dl>';
		$out .= '</div>';
		
		/**
		 * Return the rendered servings data
		 */
		return $out;
	}
}
if( !function_exists( 'the_rpr_recipe_nutrition' ) ){
	/**
	 * Outputs the nutritional data rendered above
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_nutrition( $icons=false ) {
		echo get_the_rpr_recipe_nutrition( $icons );
	}
}

if( !function_exists( 'get_the_rpr_recipe_times_headline' ) ) {
	/**
	 * Render the headline for the recipe times
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_times_headline( $icons=false ){
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Add a third level heading for embedded recipes or a second level 
		 * heading for a standalone recipe
		 */
		if( recipe_is_embedded() ){
			$out .= '<h3>';
		}else{
			$out .= '<h2>';
		}
		
		/**
		 * Add icon if desired
		 */
		if( $icons ){
            $out .= '<i class="fa fa-clock-o" title=' . __( 'Time', 'recipepress-reloaded' ) . '></i> ';
		}
		
		$out .= __( 'Time', 'recipepress-reloaded' );
		
		if( recipe_is_embedded() ){
			$out .= '</h3>';
		}else{
			$out .= '</h2>';
		}
		/**
		 * Return the rendered headline
		 */
		return $out;
	}
}
if( !function_exists( 'the_rpr_recipe_times_headline' ) ) {
	/**
	 * Outputs the headline rendered above
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_times_headline( $icons = false ){
		echo get_the_rpr_recipe_times_headline( $icons );
	}
}

if( !function_exists( 'get_the_rpr_recipe_times' ) ){
	/**
	 * Renders the cook, prep and total time
	 * 
	 * @since 0.8.0
	 * 
	 * @param boolean $icons
	 * @return string
	 */
	function get_the_rpr_recipe_times( $icons=false ) {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
        $recipe = get_post_custom( $recipe_id );

		/**
		 * Fix empty times 
		 */
		if( !isset( $recipe['rpr_recipe_prep_time'][0] ) ){
			$recipe['rpr_recipe_prep_time'][0] = 0;
		}
		if( !isset( $recipe['rpr_recipe_cook_time'][0] ) ){
			$recipe['rpr_recipe_cook_time'][0] = 0;
		}
		if( !isset( $recipe['rpr_recipe_passive_time'][0] ) ){
			$recipe['rpr_recipe_passive_time'][0] = 0;
		}
		/**
		 * Return if no times are saved
		 */
		if( $recipe['rpr_recipe_prep_time'][0] + $recipe['rpr_recipe_cook_time'][0] +  $recipe['rpr_recipe_passive_time'][0]  <= 0 ) {
			return;
		}
		
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Add the times in correct structured data format
		 */
		$out .= '<div class="rpr_times">';
		$out .= '<dl>';
		if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ){
			$struct_prep = '<meta itemprop="prepTime" content="' . rpr_format_time_xml( esc_attr( $recipe['rpr_recipe_prep_time'][0] ) ) . '" />';
			$struct_cook = '<meta itemprop="cookTime" content="' . rpr_format_time_xml( esc_attr( $recipe['rpr_recipe_cook_time'][0] ) ) . '" />';
			$struct_total = '<meta itemprop="totalTime" content="' . rpr_format_time_xml( esc_attr( $recipe['rpr_recipe_prep_time'][0] ) + esc_attr( $recipe['rpr_recipe_cook_time'][0] ) + esc_attr( $recipe['rpr_recipe_passive_time'][0] ) ) . '" />';
		} elseif ( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ) {
			$struct_prep = '<meta property="prepTime" content="' . rpr_format_time_xml( esc_attr( $recipe['rpr_recipe_prep_time'][0] ) ) . '" />';
			$struct_cook = '<meta property="cookTime" content="' . rpr_format_time_xml( esc_attr( $recipe['rpr_recipe_cook_time'][0] ) ) . '" />';
			$struct_total = '<meta property="totalTime" content="' . rpr_format_time_xml( esc_attr( $recipe['rpr_recipe_prep_time'][0] ) + esc_attr( $recipe['rpr_recipe_cook_time'][0] ) + esc_attr( $recipe['rpr_recipe_passive_time'][0] ) ) . '" />';
		} else {
			$struct_prep = '';
			$struct_cook = '';
			$struct_total = '';
		}
		if( $recipe['rpr_recipe_prep_time'][0] > 0 ) {
			$out .= '<dt>';
			if( $icons ){
				$out .= '<i class="fa fa-cog" title="' . __( 'Preparation: ', 'recipepress-reloaded' ) .'"></i>&nbsp;';
			} else {
				$out .= __( 'Preparation: ', 'recipepress-reloaded' );
			}
			$out .= '</dt>';
			$out .= '<dd>' . $struct_prep . rpr_format_time_hum( esc_attr( $recipe['rpr_recipe_prep_time'][0] ) ) . '</dd>';
		}
		if( $recipe['rpr_recipe_cook_time'][0] > 0 ) {
			$out .= '<dt>';
			if( $icons ){
				$out .= '<i class="fa fa-fire" title="' . __( 'Cooking: ', 'recipepress-reloaded' ) .'"></i>&nbsp;';
			} else {
				$out .= __( 'Cooking: ', 'recipepress-reloaded' );
			}
			$out .= '</dt>';
			$out .= '<dd>' . $struct_cook . rpr_format_time_hum( esc_attr( $recipe['rpr_recipe_cook_time'][0] ) ) . '</dd>';
		}
		$out .= '<dt>';
		if( $icons ){
			$out .= '<i class="fa fa-clock-o" title="' . __( 'Ready in: ', 'recipepress-reloaded' ) . '"></i>&nbsp;';
		} else {
			$out .= __( 'Ready in: ', 'recipepress-reloaded' );
		}
		$out .= '</dt>';
		$out .= '<dd>' . $struct_total . rpr_format_time_hum( esc_attr( $recipe['rpr_recipe_prep_time'][0] ) + esc_attr( $recipe['rpr_recipe_cook_time'][0] ) + esc_attr( $recipe['rpr_recipe_passive_time'][0] ) ) . '</dd>';
		
		$out .= '</dl>';
		$out .= '</div>';
		/**
		 * Return the rendered times data
		 */
		return $out;
	}
}
if( !function_exists( 'the_rpr_recipe_times' ) ){
	/**
	 * Outputs the rendered times from above
	 * 
	 * @since 0.8.0
	 * @param boolean $icons
	 */
	function the_rpr_recipe_times( $icons=false ) {
		echo get_the_rpr_recipe_times( $icons );
	}
}

/**
 * Formats a number of minutes to a machine readable xml time string
 * 
 * @param int $min
 * @return string
 */
function rpr_format_time_xml( $min ){
	$hours = floor( $min / 60 );
	$minutes = $min % 60;
	if( $hours > 0 && $minutes > 0 ){
		return sprintf( 'PT%1$dH%2$dM', $hours, $minutes );
	} elseif( $hours > 0 && $minutes === 0 ){
		return sprintf( 'PT%dH', $hours );
	}
	else {
		return sprintf( 'PT%dM', $minutes );
	}
}

/**
 * Formats a number of minutes to a human readable time string
 * 
 * @param int $min
 * @return string
 */
function rpr_format_time_hum( $min ){
	$hours = floor( $min / 60 );
	$minutes = $min % 60;
	if( $hours > 0 && $minutes > 0 ){
		return sprintf( '%1$d h %2$d min', $hours, $minutes );
	} elseif( $hours > 0 && $minutes === 0 ){
		return sprintf( '%d h', $hours );
	}
	else {
		return sprintf( '%d min', $minutes );
	}
}
/** ****************************************************************************
 * OTHER TEMPLATE TAGS
 */

if( !function_exists( 'get_the_rpr_recipe_image' ) ) {
	/**
	 * Includes the recipe post image in embedded recipes and if is set in 
	 * advanced options to fix the shortcomings of some recipes
	 * 
	 * @since 0.8.0
	 * @return string
	 */
	function get_the_rpr_recipe_image() {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
		
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Recipe image only needs to be included if settings tell so
		 * or recipe is embedded into another post
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'display_image' ), false ) || recipe_is_embedded() ){
			if( has_post_thumbnail( $recipe_id ) ) {
				/**
				 * @todo: make the rel="lightbox" optional ???
				 */
				if( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'images_link' ), false ) ) {
					$out .= '<a href="' . get_the_post_thumbnail_url( $recipe_id, 'full' ) . '" rel="lightbox" title="' . get_the_title( $recipe_id ) .'">';
				}
				$out .= get_the_post_thumbnail( $recipe_id, 'large' );
				if( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'images_link' ), false ) ) {
					$out .= '</a>';
				}
			}
		}
		/**
		 * return the renderd output
		 */
		return $out;
	}
}

if( !function_exists( 'the_rpr_recipe_image' ) ) {
	/**
	 * Outputs the post image rendered above
	 * 
	 * @since 0.8.0
	 */
	function the_rpr_recipe_image() {
		echo get_the_rpr_recipe_image();
	}
}

if( !function_exists( 'get_the_rpr_recipe_author' ) ) {
	/**
	 * Renders a link to the author. Only displays if set in the advanced 
	 * options to fix the shortcomings of some themes.
	 * 
	 * @since 0.8.0
	 * @todo: prefix, icons
	 * @return string
	 */
	function get_the_rpr_recipe_author() {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
		
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Recipe author only needs to be included if settings tell so
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'display_author' ), false ) ){
			$out .= '<span class="rpr_author">' . get_the_author_link() . '</span>&nbsp;';
		}
		/**
		 * return the renderd output
		 */
		return $out;
	}
}

if( !function_exists( 'the_rpr_recipe_author' ) ) {
	/**
	 * Outputs the rendered author link
	 * 
	 * @since 0.8.0
	 */
	function the_rpr_recipe_author() {
		echo get_the_rpr_recipe_author();
	}
}

if( !function_exists( 'get_the_rpr_recipe_date' ) ) {
	/**
	 * Renders the published date. Only displays if set in the advanced 
	 * options to fix the shortcomings of some themes.
	 * 
	 * @since 0.8.0
	 * @todo: prefix, icons
	 * @return string
	 */
	function get_the_rpr_recipe_date() {
		/**
		 *  Get the recipe id
		 */
		if( isset( $GLOBALS['recipe_id'] ) && $GLOBALS['recipe_id'] != '' ){
			$recipe_id = $GLOBALS['recipe_id'];
		} else {
			$recipe_id = get_post()->ID;
		}
		
		/**
		 *  Create an empty output string
		 */
		$out = '';
		
		/**
		 * Recipe date only needs to be included if settings tell so
		 */
		if( AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'display_date' ), false ) ){
			$out .= '<span class="rpr_date">' . get_the_date( get_option( 'date_format') ) . '</span>';
		}
		/**
		 * return the rendered output
		 */
		return $out;
	}
}

if( !function_exists( 'the_rpr_recipe_date' ) ) {
	/**
	 * Outputs the rendered date
	 * 
	 * @since 0.8.0
	 */
	function the_rpr_recipe_date() {
		echo get_the_rpr_recipe_date();
	}
}

/* *****************************************************************************
 * Recipe source
 */
if ( !function_exists( 'get_the_rpr_recipe_source' ) ) {

    /**
     * Renders the source of a recipe if meta data is saved
     * @since 0.9.0
     */
    function get_the_rpr_recipe_source() {
        /**
         *  Get the recipe id
         */
        if ( isset( $GLOBALS[ 'recipe_id' ] ) && $GLOBALS[ 'recipe_id' ] != '' ) {
            $recipe_id = $GLOBALS[ 'recipe_id' ];
        } else {
            $recipe_id = get_post()->ID;
        }

				if ( get_post_meta( $recipe_id, "rpr_recipe_source", true ) == '' ) {
					return; // Return early if no recipe source data is stored
				}
        
        $out = '';
        
        /**
         * Only render the source if option is set so
         */
        if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'use_source') , false ) ) {

						$out .= '<cite class="rpr_source">';
            $out .= '<label for="rpr_source">' . __( 'Source', 'recipepress-reloaded' ) . ': </label>';

            /**
             * Get the data
             */
            $source = get_post_meta( $recipe_id, "rpr_recipe_source", true );
            $source_link = get_post_meta( $recipe_id, "rpr_recipe_source_link", true );
            
            /**
             * Render the structured data
						 */
            $out .= '<span id="rpr_source" class="rpr_source" ';
            if( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'microdata' ){
                $out .= ' itemprop="citation" >';
            } elseif( AdminPageFramework::getOption( 'rpr_options', array( 'metadata', 'structured_data_format' ), 'microdata' ) === 'rdfa' ){
                $out .= ' property="citation" >';
            } else {
                $out .= '>';
            }
            
            if( $source_link !== '' ) {
                $out .= '<a href="' . esc_url( $source_link ) . '" target="_blank" >';
            }
            $out .= sanitize_text_field( $source );
            if( $source_link != '' ) {
                $out.='</a>';
            }
            $out .= '</span>';
            $out .= '</cite>';
        }
        
        return $out;
    }

}

if( !function_exists( 'the_rpr_recipe_source') ){
    /**
     * Outputs the rendered data
     * @since 0.9.0
     */
    function the_rpr_recipe_source() {
        echo get_the_rpr_recipe_source();
    }
}


/** ****************************************************************************
 * Alphabet navigation bar for listings
 */
if( ! function_exists( 'get_the_alphabet_nav_bar' ) ){
	function get_the_alphabet_nav_bar( $letters=false ){
		// An array with the (complete) alphabet
		$alphabet = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' );
		// Create an aempty output string
		$out = '';
		
		// Start the list:
		$out .= '<ul class="rpr_alphabet_navigation">';
		
		foreach( $alphabet as $a ){
			// loop through the alphabet
			if( $letters ) {
				if( in_array( $a, $letters ) ){
					// active letter, so we should set a link in the nav menu
					$out .= '<li class="active"><a href="#' . $a . '">' . $a .'</a></li>';
				} else {
					// inactive letter, no link
					$out .= '<li class="inactive">' . $a . '</li>';
				}
			} else {
				// each letter active
				$out .= '<li class="active"><a href="#' . $a . '">' . $a .'</a></li>';
			}
		}
		
		// End the list:
		$out .= '</ul>';
		
		// return the renderd nav bar
		return $out;
	}
}

if( !function_exists( 'the_alphabet_nav_bar' ) ){
	function the_alphabet_nav_bar( $letters=false ){
		echo get_the_alphabet_nav_bar( $letters );
	}
}

/** ****************************************************************************
 * GENERAL HELPER FUNCTIONS
 */

/**
 * Get the index number for a given taxonomy in the tax_custom options array
 * 
 * @since 0.8.0
 * 
 * @param string $tax
 * @return int $key index number
 */
function get_opt_tax_custom_id( $tax ){
	foreach( AdminPageFramework::getOption( 'rpr_options', array( 'tax_custom' ) ) as $key => $taxonomy ){
		if( $taxonomy['slug'] === $tax ){
			return $key;
		}
	}
}

/**
 * Check if the recipe is embedded into a post, page or another custom post type
 * @return boolean
 */
function recipe_is_embedded() {
	if( get_post_type( ) != 'rpr_recipe' ){
		return true;
	} else {
		return false;
	}
}

/**
 * Replaces special charactters and Umlaute with their basic letter
 * 
 * @todo: Maybe better replace Umlaute like Ö => Oe instead of O
 * @since 0.8.0
 * @param string $text
 * @return string
 */
function normalize_special_chars( $text ){
	// Replacement matrix for special characters:
	$trans = array(
			'á' => 'a',
			'à' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'Á' => 'A',
			'À' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'ä' => 'a',
			'Ä' => 'A',
			'é' => 'e',
			'è' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'É' => 'E',
			'È' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'í' => 'i',
			'ì' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'Í' => 'I',
			'Ì' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'ö' => 'o',
			'Ö' => 'O',
			'ø' => 'o',
			'Ø' => 'O',
			'ü' => 'u',
			'Ü' => 'U'
		);

	// Replace special chars
	$text = str_replace( array_keys( $trans ), array_values( $trans ), $text );
	
	// return the sanitized text
	return $text;
}