<?php
//=-=-=-=-=-=-= ADMIN =-=-=-=-=-=-=

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
    return __( 'The recipe archive can be found at', 'recipepress-reloaded' ) . ' <a href="'.site_url('/'.$slug.'/').'" target="_blank">'.site_url('/'.$slug.'/').'</a>';
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

				array_push( $templates, 
					 array(
						'value' => $file,
						'label' => $params['Template Name'],
						'img' => WP_PLUGIN_URL . '/recipepress-reloaded/templates/' . $file . '/screenshot.png',
					)
				);
			}
		}
	}
	return $templates;
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