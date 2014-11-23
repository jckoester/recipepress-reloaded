<?php
//=-=-=-=-=-=-= ADMIN =-=-=-=-=-=-=
global $rpr_option;

function rpr_admin_latest_news_changelog()
{
	// Idea found at http://www.webmaster-source.com/2009/07/08/parse-a-wordpress-plugins-readme-txt-with-regular-expressions/
	$readme = file_get_contents( WP_PLUGIN_DIR . '/recipepress-reloaded/readme.txt');
	// Find the changelog part:
	$start = strpos ( $readme, '== Changelog ==' );
	$end = strpos ( $readme, '== Frequently Asked Questions ==' );
	$readme = substr( $readme, $start, ( $end-$start ) );
	
	// Make links clickable
	$readme = make_clickable(nl2br(esc_html($readme)));
	
	// Backticks to <code>-Tags:
	$readme = preg_replace('/`(.*?)`/', '<code>\\1</code>', $readme);
	
	// * to italic and ** to bold
	$readme = preg_replace('/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme);
	$readme = preg_replace('/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme);
	
	// headlines
	$readme = preg_replace('/== (.*?) ==/', '', $readme);
	$readme = preg_replace('/= (.*?) =/', '<h4>\\1</h4>', $readme);
	rpr_admin_template_list();
	// creating lists:
	$readme = preg_replace('/\*(.*?)\n/', ' <li>\\1</li>', $readme);
	$readme = preg_replace('/<\/h4>/', '</h4><ul>', $readme);
	$readme = preg_replace('/<h4>/', '</ul><h4>', $readme);
	
	
	
	// Remove <br>
	$readme = preg_replace('/(<br\W*?\/>)/', '', $readme);
	
    return $readme;
}


function rpr_admin_recipe_slug_preview( $slug )
{
    return  __( 'The recipe archive can be found at', 'recipepress-reloaded' ) . ' <a href="'.site_url('/'.$slug.'/').'" target="_blank">'.site_url('/'.$slug.'/').'</a>';
}

function rpr_admin_slug_preview( $slug )
{
    global $rpr_option;
    print '<div style="margin:10px 0">'.__( 'The recipe archive can be found at', 'recipepress-reloaded' ) . ' <a href="'.site_url('/'.$rpr_option['recipe_slug'] .'/').'" target="_blank">'.site_url('/'.$rpr_option['recipe_slug'] . '/').'</a></div>';
}

function rpr_admin_manage_tags()
{
    return '<a href="'.admin_url('edit.php?post_type=rpr_recipe&page=rpr_taxonomies').'" class="button button-primary" target="_blank">'.__('Manage custom recipe tags', 'recipe-press-reloaded').'</a>';
}

function f_comment( $entry ){
	return $entry[0] == T_COMMENT;
}

function rpr_admin_template_list()
{
	$dirname = WP_PLUGIN_DIR . '/recipepress-reloaded/templates/';
	$templates = array();
	
	if ($handle = opendir( $dirname )) {
		//$i=0;
		while (false !== ($file = readdir($handle))) {
			if( $file !='.' && $file !='..' && $file != '.svn' ) {
				// Param parsing inspired by http://stackoverflow.com/questions/11504541/get-comments-in-a-php-file
				// put in an extra function?
				$params=array();
				$filename = $dirname . $file . '/recipe.php';
				
				$docComments = array_filter(
						token_get_all( file_get_contents( $filename ) ), 
						/*function($entry) {
							return $entry[0] == T_COMMENT;
						}*/
						"f_comment"
				);
				
				$fileDocComment = array_shift( $docComments );
				
				$regexp = "/.*\:.*\n/";
				preg_match_all($regexp, $fileDocComment[1], $matches);
				
				foreach( $matches[0] as $match ){
					$param = explode(": ", $match);
					$params[ trim( $param[0] ) ] = trim( $param[1] );
				}

				$templates[$file] = array(
						'value' => $file,
						'title' => $params['Template Name'],
						'img' => WP_PLUGIN_URL . '/recipepress-reloaded/templates/' . $file . '/screenshot.png',
					);
				//$i++;
			}
		}
	}
	return $templates;
}

function rpr_admin_template_settings()
{
	$dirname = WP_PLUGIN_DIR . '/recipepress-reloaded/templates/';
	$templates = array();
	
	$template_settings=array(
						array(
    						'type' => 'switch',
    						'id' => 'recipe_icons_display',
    						'title' => __( 'Use icons' , 'recipepress-reloaded' ),
    						'subtitle' => __( 'Display icons in front of headlines.' , 'recipepress-reloaded' ),
    						'description' => __( 'Icons not only look nice. They also can save you space. With this setting activated most layouts <span class="admin_demo""><i class="fa fa-clock-o" title="ready in" ></i> 35min</span> will display instead of <span class="admin_demo"><span style="text-transform:uppercase; font-weight:bold;">Ready in: </span>35min</span>.', 'recipepress-reloaded'),
    						'default' => false,
    						),
    					array(
   							'type' => 'switch',
   							'id' => 'recipe_display_printlink',
   							'title' => __('Display print link', 'recipepress-reloaded'),
   							'subtitle' => __( 'Adds a print link to your recipes. It\'s recommended to use one of the numerous print plugins for wordpress to include a print link to ALL of your posts.', 'recipepress-reloaded' ),
   							'default' => false,
   							),
   						array(
   							'type' => 'text',
   							'id' => 'recipe_printlink_class',
   							'title' => __('Class of the print area', 'recipepress-reloaded'),
   							'subtitle' => __( 'Print links should only print an area of the page, usually a post. This is higly depending on wordpress theme you are using. Add here the class (prefixed by \'.\') or the id (prefixed by \'#\') of the printable area.', 'recipepress-reloaded'),
   							'default' => '.rpr_recipe',
   							'required' => array('recipe_display_printlink','equals',true),
   						),
    						
						array(
                        	'id'        => 'rpr_template',
                        	'type'      => 'image_select',
                        	'title'     => __( 'Choose a layout', 'recipepress-reloaded' ),
                        	'subtitle'  => sprintf (__( 'Layouts define how your recipes will look like. Choose one of the installed templates.', 'recipepress-reloaded'), ''), // or <a href="%s">create one yourself</a>.', 'recipepress-reloaded' ) , 'http://rp-reloaded.net/templates/create' ),),
                        	//'desc'      => sprintf (__( 'Templates define how your recipes will look like. Choose one of the installed templates.', 'recipepress-reloaded'), ''), // or <a href="%s">create one yourself</a>.', 'recipepress-reloaded' ) , 'http://rp-reloaded.net/templates/create' ),),
                        
                        	//Must provide key => value(array:title|img) pairs for radio options
                        	'options'   => rpr_admin_template_list(),
                        	'default'   => 'rpr_default',
                        	'width' => 300,
                        	'height' => 300
                      	),
                      	
                      	
	);
	
	if ($handle = opendir( $dirname )) {
		while (false !== ($file = readdir($handle))) {
			if( $file !='.' && $file !='..' && $file != '.svn' ) {
				if( file_exists($dirname . $file . '/settings.php') ){
					include_once( $dirname . $file . '/settings.php' );
				}
				
			}
		}
	}
	
	return $template_settings;
}

//=-=-=-=-=-=-= SHORTCODE GENERATOR =-=-=-=-=-=-=

function rpr_shortcode_generator_recipes_by_date()
{
    return rpr_shortcode_generator_recipes('date', 'DESC');
}

function rpr_shortcode_generator_recipes_by_title()
{
    return rpr_shortcode_generator_recipes('title', 'ASC');
}

function rpr_shortcode_generator_recipes($orderby, $order)
{
    $recipe_list = array();

    $args = array(
        'post_type' => 'rpr_recipe',
        'post_status' => 'publish',
        'orderby' => $orderby,
        'order' => $order,
        'no-paging' => true,
        'posts_per_page' => -1,
    );

    $recipes = get_posts( $args );
    foreach ( $recipes as $recipe ) {

        $recipe_list[] = array(
            'value' => $recipe->ID,
            'label' => get_recipe_title($recipe),
        );

    }

    if( $orderby == 'title' ) {
        usort($recipe_list, "compare_post_titles");
    }

    return $recipe_list;
}

function compare_post_titles($a, $b)
{
    return strcmp($a['label'], $b['label']);
}

function get_recipe_title( $recipe )
{
    $meta = get_post_custom($recipe->ID);

    if (!is_null($meta['recipe_title'][0]) && $meta['recipe_title'][0] != '') {
        return $meta['recipe_title'][0];
    } else {
        return $recipe->post_title;
    }
}


function rpr_shortcode_generator_taxonomies()
{
    $taxonomy_list = array();

    $args = array(
        'object_type' => array('recipe')
    );

    $taxonomies = get_taxonomies( $args, 'objects' );

    foreach ($taxonomies  as $taxonomy ) {

        if($taxonomy->name != 'rating') {
            $taxonomy_list[] = array(
                'value' => $taxonomy->name,
                'label' => $taxonomy->labels->name,
            );
        }
    }

    return $taxonomy_list;
}

function rpr_shortcode_generator_authors()
{
    $authors_list = array();
    $authors = array();

    $args = array(
        'post_type' => 'recipe',
        'no-paging' => true,
        'posts_per_page' => -1,
    );

    $recipes = get_posts( $args );
    foreach ( $recipes as $recipe ) {

        $user_id = $recipe->post_author;

        if(!in_array($user_id, $authors))
        {
            $authors[] = $user_id;

            $user = get_userdata($user_id);

            $authors_list[] = array(
                'value' => $user_id,
                'label' => $user->display_name,
            );
        }
    }

    return $authors_list;
}

//=-=-=-=-=-=-= WHITELIST =-=-=-=-=-=-=

VP_Security::instance()->whitelist_function('rpr_admin_latest_news_changelog');
VP_Security::instance()->whitelist_function('rpr_admin_recipe_slug_preview');
VP_Security::instance()->whitelist_function('rpr_admin_manage_tags');


VP_Security::instance()->whitelist_function('rpr_shortcode_generator_recipes_by_date');
VP_Security::instance()->whitelist_function('rpr_shortcode_generator_recipes_by_title');
VP_Security::instance()->whitelist_function('rpr_shortcode_generator_taxonomies');
VP_Security::instance()->whitelist_function('rpr_shortcode_generator_authors');
