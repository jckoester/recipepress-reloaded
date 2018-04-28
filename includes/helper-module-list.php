<?php

/**
* helper functions to create lists off all or all active modules
*/

function get_modules_list() {
  /**
   * First create a list of all globally available modules:
   */
  $dirname = WP_PLUGIN_DIR . '/recipepress-reloaded/modules/';

  $modules = array();

  return add_module_to_list($dirname, $modules);
}

function add_module_to_list( $dirname, $modules=array() ){
    if ( is_dir( $dirname ) ){
        if ($handle = opendir( $dirname )) {
            // Walk through all folders in that directory:
            while (false !== ($file = readdir($handle))) {
                if( $file !='.' && $file !='..' && $file != '.svn' && $file != 'README.md' ) {
                    if( preg_match( "/plugin/", $dirname ) ){
                        $baseurl = plugins_url() . '/' . 'recipepress-reloaded' . '/modules/' . $file ;
                        $local = false;
                    }
                    $modules[$file] = array(
                        'path' => $dirname . $file,
                        'url' => $baseurl,
                        'id'    => $file
                    );
                    $modules = get_modules_meta( $dirname, $file, $modules );
                }
            }
        }
    }
    return $modules;
}


function get_modules_meta( $dirname, $file, $modules=array() ) {
    include $dirname . $file . '/module.conf.php';

    if( isset( $module_config['title'] ) ){
        $modules[$file]['title'] = sanitize_text_field( $module_config['title'] );
    }

    if( isset( $module_config['description'] ) ){
        $modules[$file]['description'] = sanitize_text_field( $module_config['description'] );
    }
    if( isset( $module_config['version'] ) ){
        $modules[$file]['version'] = sanitize_text_field( $module_config['version'] );
    }
    if( isset( $module_config['priority'] ) ){
        $modules[$file]['priority'] = sanitize_text_field( $module_config['priority'] );
    } else {
        $modules[$file]['priority'] = 0;
    }
    if( isset( $module_config['selectable'] ) ){
        $modules[$file]['selectable'] = sanitize_text_field( $module_config['selectable'] );
    } else {
        $modules[$file]['selectable'] = true;
    }
    if( isset( $module_config['category'] ) ){
        $modules[$file]['category'] = strtolower( sanitize_text_field( $module_config['category'] ) );
        switch ( $module_config['category'] ){
            case 'Metadata':
                $modules[$file]['icon'] = 'fa-tags';
                break;
            default :
                $modules[$file]['icon'] = 'fa-cogs';
                break;
        }
    }
    if( isset( $module_config['author'] ) ){
        $modules[$file]['author'] = sanitize_text_field( $module_config['author'] );
    }
    if( isset( $module_config['author_mail'] ) ){
        $modules[$file]['author_mail'] = sanitize_email( $module_config['author_mail'] );
    }
    if( isset( $module_config['author_url'] ) ){
        $modules[$file]['author_url'] = sanitize_text_field( $module_config['author_url'] );
    }
    if( isset( $module_config['doc_url'] ) ){
        $modules[$file]['doc_url'] = sanitize_text_field( $module_config['doc_url'] );
    }
    return $modules;
}
