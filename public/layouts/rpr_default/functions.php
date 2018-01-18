<?php
// TOCOMMENT!!!
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

add_action( 'wp_enqueue_scripts', 'rpr_default_styles'  );
add_action( 'wp_enqueue_scripts', 'rpr_default_scripts'  );

function rpr_default_styles( ){
	wp_enqueue_style( 'rpr_default',  plugin_dir_url( __FILE__ ) . '/public.css', array (), '0.0.1', 'all' );

	if( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'print_button_link' ) ) ){
		wp_enqueue_style( 'rpr_default_prn',  plugin_dir_url( __FILE__ ) . '/print.css', array (), '0.0.1', 'print' );
	}
}

function rpr_default_scripts( ){
	$print_data = array(
        'print_area' => esc_attr( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'printlink_class' ), '.rpr_recipe' )),
        'no_print_area' => esc_attr( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'no_printlink_class' ), '.no-print' )),
        'print_css' => plugin_dir_url( __FILE__ ) . 'print.css'
	);

	if( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'print_button_link' ) ) ){
		wp_enqueue_script( 'rpr-print-js', plugin_dir_url(dirname(__FILE__)) . '../js/rpr-print.js', array ( 'jquery' ), '1.5.1', true );
		wp_enqueue_script( 'rpr-print-opt',  plugin_dir_url( __FILE__ ) . 'print.js', array (), '0.0.1', true );
		wp_localize_script('rpr-print-opt', 'print_options', $print_data);
	}	
}


/**
 * Print link
 */
if ( !function_exists('get_the_recipe_print_link') ) {
	// a link to print only the recipe.
	function get_the_recipe_print_link() {
		$out = '';
		if ( AdminPageFramework::getOption( 'rpr_options', array( 'layout_general', 'print_button_link' ) ) ){
			$out .= '<span class="print-link">';
			$out .= '<a href="#print"><i class="fa fa-print"></i> ';
			$out .= __('Print', 'recipepress_reloaded');
			$out .= '</a>';
			$out .= '</span>';
		}
		return $out;
	}
}

if ( !function_exists('the_recipe_print_link') ) {
	function the_recipe_print_link() {
		echo get_the_recipe_print_link();
	}
}
