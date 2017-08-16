<?php
/**
 * The base class for modules to RecipePress reloaded
 *
 * @link       http://tech.cbjck.de/wp/rpr
 * @since      1.0.0
 *
 * @package    recipepress-reloaded
 */

abstract class RPR_Module{
	
	/**
	 * Include required files
	 * This method should be overwritten by the child classes
	 * It's here as a fallback
	 * @since 1.0.0
	 */
	abstract public function load_dependencies();
	
	/**
	 * Register all of the hooks related to the admin area functionality
     * of the module.
	 * @since 1.0.0
	 * @param RPR_Loader $loader
	 */
	abstract public function define_admin_hooks( $loader );
	
	/**
	 * Register all of the hooks related to the public area functionality
         * of the module.
	 * @since 1.0.0
	 * @param RPR_Loader $loader
	 */
	abstract public function define_public_hooks( $loader );
	
}