<?php
/**
 * @since 1.0.0
 */
class RPR_Module_PL_Update extends RPR_Module {

    /**
     * Load all files required for the module
     */
    public function load_module_dependencies() {
      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/helper-module-list.php';
      require_once dirname( __FILE__ ) . '/helper-update.php';

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the module.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_module_admin_hooks($loader) {
        if (is_a($loader, 'RPR_Loader')) {
          $loader->add_action( 'admin_init', $this, 'fix_dbversion' );
          $loader->add_action( 'admin_init', $this, 'check_migration' );
          $loader->add_action( 'admin_init', $this, 'rpr_do_migration' );
          $loader->add_action( 'admin_notices', $this, 'notice_migration' );
        }
    }

    /**
     * Register all of the hooks related to the public area functionality
     * of the modulinite.
     * @since 1.0.0
     * @param RPR_Loader $loader
     */
    public function define_module_public_hooks($loader) {

    }

    /**
     * Return the path to module's directory
     * @return string
     */
    public function get_path() {
        return dirname(__FILE__);
    }

    /**
  	 * Check if the database needs an update by comparing the dbversion of
  	 * plugin an database
  	 *
  	 * @since 0.8.0
  	 */
  	public function check_migration() {
  		// Update version information in database if necessary
  		if ( version_compare( get_option( 'rpr_version' ), RPR_VERSION, '<' ) ) {
  			// TODO: commented out for development, enable before release!
  			//update_option( 'rpr_version', RPR_VERSION );
  			update_option( 'rpr_version', '0.9.1');

  			// Update module settings each time the plugin gets updated!
  			$this->update_module_options();
  		}

  		if ( get_option( 'rpr_dbversion' ) && get_option( 'rpr_dbversion' ) < RPR_DBVER ) {
  			/**
  			 * Set Option to enable the update procedures
  			 */
  			update_option( 'rpr_update_needed', 1 );
  		}
  	}

  	public function update_module_options(){
  		$new_options = get_option( 'rpr_options' );

  		// Get a list of modules and loop over them
  		$modules = $this->get_modules_list();

  		foreach( $modules as $module ){
  			// Add priority settingg
  			$new_options['modules']['module_' . $module['id'] . '_priority'] = $module['priority'];
  			// Activate all non selectable modules
  			if( $module['selectable'] == false ){
  				$new_options['modules']['module_' . $module['id'] . '_active'] = 1;
  			}
  		}
  		update_option( 'rpr_options', $new_options );
  	}

    /**
  	 * Fix the database version information
  	 *
  	 * @since 0.9.0
  	 */
  	public function fix_dbversion() {
  		/*
  		 *  check for very old versions:
  		 */
  		if ( ! get_option( 'rpr_version_updated' ) ) {
  			// Check, if RPR < 0.5:
  			if ( is_array( get_option( 'rpr_options' ) ) ) {
  				update_option( 'rpr_version_updated', '0.3.0' );
  				//Check if RecipePress
  			} elseif ( is_array( get_option( 'recipe-press-options' ) ) ) {
  				update_option( 'rpr_version_updated', 'RecipePress' );
  			} else {
  				// RPR hasn't been installed previously.
  				return;
  			}
  		}

  		/*
  		 * Check if site was affected by the 0.8.x multisite update bug
  		 */
  		if ( get_option( 'rpr_version_updated' ) && get_option( 'rpr_dbversion' ) ) {
  			// If db version 5 is set but rpr_option still is there, migration did not trigger on previous update
  			if( version_compare( get_option( 'rpr_dbversion' ), '5', '=' ) && get_option( 'rpr_option' ) ) {
  				// Reset the dbversion to 4 (as it hasn't been updated to 5)
  				update_option( 'rpr_dbversion', '4' );
  				// Remove old version string 'rpr_version_updated'
  				delete_option( 'rpr_version_updated' );
  			}
  		} else {

  			/*
  			 * Check for older versions
  			 */
  			if ( get_option( 'rpr_version_updated' ) ) {
  				// Fix the dbversion option for old versions of recipepress reloaded
  				if ( version_compare( get_option( 'rpr_version_updated' ), '0.8.0', '<' ) ) {
  					update_option( 'rpr_dbversion', '4' );
  				}
  				if ( version_compare( get_option( 'rpr_version_updated' ), '0.7.12', '<' ) ) {
  					update_option( 'rpr_dbversion', '3' );
  				}
  				if ( version_compare( get_option( 'rpr_version_updated' ), '0.7.9', '<' ) ) {
  					update_option( 'rpr_dbversion', '2' );
  				}
  				if ( version_compare( get_option( 'rpr_version_updated' ), '0.7.7', '<' ) ) {
  					update_option( 'rpr_dbversion', '1' );
  				}
  				if ( get_option( 'rpr_version_updated' ) == "0.3.0" || get_option( 'rpr_version_updated' ) == 'RecipePress' ) {
  					update_option( 'rpr_dbversion', '0' );
  				}
  			}
  		}
  			// Remove old version string 'rpr_version_updated'
  			delete_option( 'rpr_version_updated' );
  	}

    /**
     * Create a database update notice
     * We don't want to do a database change without the user knowing about it
     * and having the chance to do a backup before.
     * @since 0.8.0
     */
    public function notice_migration() {
      if ( get_option( 'rpr_update_needed' ) == '1' ) {
        echo '<div class="updated"><p>';
        if ( get_option( 'rpr_dbversion' ) < 4 ) {
          printf( __( '%1s You are upgrading from an old version of RecipePress reloaded. A migration procedure is provided but it is not guaranteed it will work. Please make sure you have a backup you can roll back to.<br/> In case of problems file a bug report at the <a href="%2s" target="_blank">support forum</a> or file an issue at <a href="%3s" target="_blank">github</a>.<br/>', 'recipepress-reloaded' ), '<i class="fa fa-exclamation-triangle fa-3x" style="float:left; margin-right:6px; color:red;"></i>', 'https://wordpress.org/support/plugin/recipepress-reloaded', 'https://github.com/dasmaeh/recipepress-reloaded/issues' );
        }
        printf( __( 'The Recipe Press Reloaded database needs to upgraded. Make sure you have a backup before you proceed. | <a href="%1$s">Proceed</a>', 'recipepress-reloaded' ), '?post_type=rpr_recipe&rpr_do_migration=1' );
        echo "</p></div>";
      }
    }

    /**
     * Do the actual migration dependent on database version
     * @since 0.8.0
     */
    public function rpr_do_migration() {
      // Just to be sure, run the fix_dbversion()
      $this->fix_dbversion();
      $dbver = get_option( 'rpr_dbversion' );

      // Check if the user has actively allowed the update:
      if ( isset( $_GET['rpr_do_migration'] ) && $_GET['rpr_do_migration'] == '1' ) {
        // Do all the update tasks dependent on the version of the database
        if ( get_option( 'rpr_dbversion' ) <= '0' ) {
          // Migrate from the old RecipePress plugin
          rpr_update_from_rp();
        }
        if ( get_option( 'rpr_dbversion' ) <= '1' ) {
          // Migrate from db version 1
          rpr_update_from_1();
        }
        if ( get_option( 'rpr_dbversion' ) <= '3' ) {
          // Migrate from db version 3
          rpr_update_from_3();
        }
        if ( get_option( 'rpr_dbversion' ) <= '4' ) {
          // Migrate from db version 4
          rpr_update_from_4();
        }
        // Flush permalinks
        flush_rewrite_rules();

        // Mark update as done
        update_option( 'rpr_update_needed', 0 );
      }
    }
}
