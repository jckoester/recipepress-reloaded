<?php

/**
* helper functions to create lists off all or all active modules
*/

if( !function_exists( 'rpr_get_active_modules' ) ){
    function rpr_get_active_modules() {
      /**
       *  Get modules list from options
       */
      $modules = AdminPageFramework::getOption( 'rpr_options', array( 'modules' ));
      /**
       *  Create a list of active modules:
       */
      $active_modules = array();
      if( is_array( $modules ) and count( $modules ) > 0 ){
          foreach ( $modules as $mod =>$active){
              if( preg_match("/_active/", $mod) && $active == "1" ){
                  $mod = preg_replace( "/module_/", "", $mod);
                  $mod = preg_replace( "/_active/", "", $mod);
                  $prio = AdminPageFramework::getOption( 'rpr_options', array( 'modules', 'module_' . $mod . '_priority' ));
                  $active_modules[$prio . '_'. $mod] = $mod;
              }
          }
      }
      ksort($active_modules);
      return $active_modules;
    }
}

if( !function_exists( 'rpr_load_modules' ) ){
    function rpr_load_modules(){
      $active_modules = rpr_get_active_modules();
      $modules = array();
      foreach ( $active_modules as $active_module => $module_id ) {
          $filename = plugin_dir_path( dirname( __FILE__ ) ) . 'modules/' . strtolower( $module_id ) . '/module.php';

          if ( file_exists( $filename ) ) {
              require_once $filename;
              $classname = 'RPR_Module_' . $module_id;
              $modules[ $module_id ] = new $classname();
          }
      }
      return $modules;
    }
}

if( !function_exists( 'rpr_get_modules_list' )){
  function rpr_get_modules_list() {
    /**
     * First create a list of all globally available modules:
     */
    $dirname = WP_PLUGIN_DIR . '/recipepress-reloaded/modules/';

    $modules = array();

    return rpr_add_module_to_list($dirname, $modules);
  }
}

if( !function_exists( 'rpr_add_module_to_list') ){
  function rpr_add_module_to_list( $dirname, $modules=array() ){
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
                      $modules = rpr_get_modules_meta( $dirname, $file, $modules );
                  }
              }
          }
      }
      return $modules;
    }
}

if( !function_exists( 'rpr_get_modules_meta' ) ){
  function rpr_get_modules_meta( $dirname, $file, $modules=array() ) {
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
}
