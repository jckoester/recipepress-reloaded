<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class RPR_Module_Demo extends RPR_Module {

	/**
	 * Load all files required for the module
	 */
	public function load_dependencies() {
	}
	
	/**
	 * Register all of the hooks related to the admin area functionality
     * of the module.
	 * @since 1.0.0
	 * @param RPR_Loader $loader
	 */
	public function define_admin_hooks( $loader ){
		if( is_a( $loader, 'RPR_Loader' ) ){
			echo "Got a valid loader";
		}
	}
	
	/**
	 * Register all of the hooks related to the public area functionality
     * of the module.
	 * @since 1.0.0
	 * @param RPR_Loader $loader
	 */
	public function define_public_hooks( $loader ){
		if( is_a( $loader, 'RPR_Loader' ) ){
			echo "Got a valid loader";
		}
	}
}