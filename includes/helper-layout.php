<?php
/**
* Helper functions to handle the different layouts an do layout dependent rendering
*/

if( !function_exists( 'rpr_get_common_template_tags' )){
    function rpr_get_common_template_tags() {
      return plugin_dir_path( __DIR__ ) . 'public/rpr_template_tags.php';
    }
}

if( !function_exists( 'rpr_get_modules_template_tags') ){
  function rpr_get_modules_template_tags() {
    require_once 'helper-module-list.php';

    $modules = rpr_get_active_modules();
    $template_tags = array();

    foreach(  $modules as $module_id => $module ){
      $mod_includepath = plugin_dir_path( __DIR__ ) . 'modules/' . $module . '/template_tags.php';
      if(file_exists( $mod_includepath ) ){
        array_push( $template_tags, $mod_includepath );
      }
    }

    return $template_tags;
  }
}

if( !function_exists( 'rpr_get_the_layout' ) ){
  /**
	 * Get the path to the layout file depending on the layout options
	 *
	 * @since 0.8.0
	 * @return string
	 */
	function rpr_get_the_layout( $default = false ){
    if( $default ){
      $layout = 'rpr_default';
    } else {
		  // Get the layout chosen:
      $layout = AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'layout' ) , 'rpr_default' );
    }

		// calculate the includepath for the layout:
		// Check if a global or local layout should be used:
		if( strpos( $layout, 'local') !== false ){
			//Local layout
			$includepath = get_stylesheet_directory() . '/rpr_layouts/'. preg_replace('/^local\_/', '', $layout ) . '/';
		} else {
			//Global layout
			$includepath = plugin_dir_path( __DIR__ )  . 'public/layouts/' . $layout . '/';
		}

		return $includepath;
	}
}

if( !function_exists( 'rpr_render_recipe_content' )){
  function rpr_render_recipe_content( $recipe_post ) {
    // Get the modules
    require_once 'helper-module-list.php';
    $modules = rpr_load_modules();

		// Get the layout's includepath
		$includepath = rpr_get_the_layout() . 'recipe.php';

		if( !file_exists( $includepath ) ){
			// If the layout does not provide an recipe file, use the default one:
			// This NEVER should happen, but who knows...
			$includepath = rpr_get_the_layout(true) . 'recipe.php';
		}

		// Get the recipe data:
		$recipe = get_post_custom($recipe_post->ID);

                // Create structured data as json-LD
                // TODO: Date is missing!
                $json = array(
                    '@context' => 'http://schema.org',
                    '@type' => 'Recipe',
                    'name' => get_the_title( $recipe_post ),
                    'author' => get_the_author(),
                    'datePublished' => get_the_time( 'Y-m-d' )
                );

                // Number of comments
                if( get_comments_number() > 0 ){
                  $json['interactionStatistic'] = array(
                    '@type' => "InteractionCounter",
                    'interactionType'=> 'http://schema.org/Comment',
                    'userInteractionCount' =>  get_comments_number()
                  );
                }

                foreach ($modules as $module ){
                    if( is_a( $module, 'RPR_Module_Metabox' ) ){
                        $modjson = $module->get_structured_data( $recipe_post->ID, $recipe );
                        if( is_array($modjson) && count($modjson) > 0 ){
                            $json = array_merge($json, $modjson);
                        }
                    }
                }
                echo '<script type="application/ld+json">' . wp_json_encode($json) . '</script>';

		// Start rendering
		ob_start();

    rpr_include_template_tags();
		// Include the recipe template file:
                include( $includepath );
		// and render the content using that file:
		$content = ob_get_contents();

		// Finish rendering
		ob_end_clean();

		// return the rendered content:
		return $content;
	}
}

  /**
	 * Do the actual rendering using the excerpt.php file provided by the layout
	 *
	 * @since 0.8.0
	 * @param object $recipe_post
	 * @return string $content
	 */
if( !function_exists( 'rpr_render_recipe_excerpt' ) ){
  function rpr_render_recipe_excerpt( $recipe_post ) {

            // Return if we are on a single post page:
//            if( is_single() ) {return;}
		// Get the layot's includepath
		$includepath = rpr_get_the_layout() . 'excerpt.php';

    if( !file_exists( $includepath ) ){
      // If the layout does not provide an recipe file, use the default one:
      // This NEVER should happen, but who knows...
      $includepath = rpr_get_the_layout(true) . 'excerpt.php';
    }

		// Get the recipe data:
		$recipe = get_post_custom($recipe_post->ID);

		// Start rendering
		ob_start();
    // Include the common template tags:
    rpr_include_template_tags();
		// Include the excerpt file:
		include( $includepath);
		// and render the content using that file:
		$content = ob_get_contents();

		// Finish rendering
		ob_end_clean();

		// return the rendered content:
		return $content;

	}
}

if( !function_exists( 'rpr_include_template_tags' )){
  function rpr_include_template_tags( $mode='all') {
    // Include the common template tags:
    include_once( rpr_get_common_template_tags() );

    if( $mode != 'common'){
      // Include the module's template tags:
      $modules_tt = rpr_get_modules_template_tags();
      if( count($modules_tt) > 0 ){
        foreach( $modules_tt as $module ){
          include_once( $module );
        }
      }
    }
  }
}
