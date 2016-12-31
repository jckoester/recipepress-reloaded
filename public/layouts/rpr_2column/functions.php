<?php
// TOCOMMENT!!!
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

add_action( 'wp_enqueue_scripts', 'rpr_2column_styles'  );
add_action( 'wp_enqueue_scripts', 'rpr_2column_scripts'  );

function rpr_2column_styles( ){
	wp_enqueue_style( 'rpr_2column',  plugin_dir_url( __FILE__ ) . '/public.css', array (), '0.0.1', 'all' );
	wp_enqueue_style( 'rpr_2column_prn',  plugin_dir_url( __FILE__ ) . '/print.css', array (), '0.0.1', 'print' );
}

function rpr_2column_scripts( ){
	wp_enqueue_script( 'jquery-print', plugin_dir_url( __FILE__ ) . '/print.js', array('jquery'), '0.0.1' );
}


/**
 * Print link
 */
if ( !function_exists('get_the_recipe_print_link') ) {
	// a link to print only the recipe.
	function get_the_recipe_print_link() {
		$out = '';
		if ( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'printlink_display' ), false ) == true ){
			$out .= '<script>';
			$out .= 'var rpr_printarea="' . esc_attr( AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'printlink_class' ), '.rpr_recipe' ) ) . '";' ;
			$out .= '</script>';
			$out .= '<span class="print-link"></span>';
		}
		return $out;
	}
}

if ( !function_exists('the_recipe_print_link') ) {
	function the_recipe_print_link() {
		echo get_the_recipe_print_link();
	}
}
