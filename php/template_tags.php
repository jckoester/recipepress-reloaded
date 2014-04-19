<?php
if ( !function_exists('get_the_recipe_category_bar') ) {
    // template tag for category list:
    function get_the_recipe_category_bar(){
        $recipe_post = get_post();
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
    function the_recipe_category_bar(){
        echo get_the_recipe_category_bar();
    }
} 

if ( !function_exists('get_the_recipe_time_bar') ) {
    function get_the_recipe_time_bar(){
        $recipe_post = get_post();
        $recipe = get_post_custom($recipe_post->ID);
     //   var_dump($recipe);
        $out = "";
        
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
    function the_recipe_time_bar(){
        echo get_the_recipe_time_bar();
    }
}

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
