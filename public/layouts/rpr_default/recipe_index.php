<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Render a list of all terms of this taxonomy
 * @todo: create a multicolumn layout
 */

// Create an empty output variable
$out = '';

if( $posts && count( $posts ) > 0 ){
		// Create an index i to compare the number in the list and chreck for first and last item
		$i =0;
		
		// Create an empty array to take with the first letters of all headlines
		$letters = array();

		// Walk through all the terms to build alphabet navigation
		foreach( $posts as $post ){
			if( $headers ){
				// Add first letter headlines for easier navigation if set so
					
				// Get the first letter (without specisl chars)
				$first_letter = substr( normalize_special_chars ( $post->post_title ), 0, 1 );
					
				// Check if we've already had a headline
				if( !in_array( $first_letter, $letters ) ){
					// Close list of preceeding group:
					if( $i != 0 ){
						$out .= '</ul>';
					}
					// Create a headline
					$out .= '<h2><a class="rpr_toplink" href="#top">&uarr;</a><a name="'.$first_letter.'"></a>';
		            $out .= strtoupper($first_letter);
		            $out .= '</h2>';
						
					// Start new list
					$out .= '<ul class="rpr_taxlist">';
						
					// Add the letter to the list
					array_push( $letters, $first_letter );
				}
			} else {
				// Start list before first item
				if( $i === 0 ){
					$out .= '<ul class="rpr_taxlist">';
				}
			}
				
			// Add the entry for the post:
				$out .= '<li><a href="'.get_permalink( $post->ID ).'">';
		        $out .= $post->post_title;
		        $out .= '</a></li>';
				
			// increment the counter
			$i++;
		}
		// Close the last list:
		$out .= '</ul>';
		
		// Output the rendered list
		echo '<a name="top"></a>';
		the_alphabet_nav_bar( $letters );
		echo $out;
		the_alphabet_nav_bar( $letters );
} else {
	// No recipes
	_e( 'There are no recipes to display.', 'recipepress-reloaded' );
}
