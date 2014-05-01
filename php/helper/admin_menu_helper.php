<?php
//=-=-=-=-=-=-= ADMIN =-=-=-=-=-=-=

function rpr_admin_latest_news_changelog()
{
    ob_start();
    include('changelog.html');
    $out = ob_get_contents();
    ob_end_clean();

    return $out;
}


function rpr_admin_recipe_slug_preview( $slug )
{
    return __( 'The recipe archive can be found at', 'recipe-press-reloaded' ) . ' <a href="'.site_url('/'.$slug.'/').'" target="_blank">'.site_url('/'.$slug.'/').'</a>';
}

function rpr_admin_manage_tags()
{
    return '<a href="'.admin_url('edit.php?post_type=rpr_recipe&page=rpr_taxonomies').'" class="button button-primary" target="_blank">'.__('Manage custom recipe tags', 'recipe-press-reloaded').'</a>';
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