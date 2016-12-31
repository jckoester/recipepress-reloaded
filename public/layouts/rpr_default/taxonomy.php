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

if( $terms ){
	if( count( $terms ) > 0 ){
		// Create an index i to compare the number in the list and chreck for first and last item
		$i =0;
		
		// Create an empty array to take with the first letters of all headlines
		$letters = array();

		// Walk through all the terms to build alphabet navigation
		foreach( $terms as $term ){
			// Get term meta data
			$term_meta = get_term_meta( $term->term_id );
			
			// Skip ingredients withe meta 'use_in_list' == false
			if( !( $taxonomy === 'rpr_ingredient' && isset( $term_meta['use_in_list'] ) && $term_meta['use_in_list'] != 0 ) ){
				$title = ucfirst( $term->name );
				
				if( $headers ){
					// Add first letter headlines for easier navigation if set so
					
					// Get the first letter (without specisl chars)
					$first_letter = substr( normalize_special_chars ( $title ), 0, 1 );
					
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
				
				// Add the entry for the term:
				$out .= '<li><a href="'.get_term_link( $term ).'">';
		        $out .= $title;
		        $out .= '</a></li>';
				
				// increment the counter
				$i++;
			}
		}
		// Close the last list:
		$out .= '</ul>';
		
		// Output the rendered list
		echo '<a name="top"></a>';
		the_alphabet_nav_bar( $letters );
		echo $out;
		the_alphabet_nav_bar( $letters );

	} else {
		// No terms in this taxonomy
		_e( 'There are no terms in this taxonomy.', 'recipepress-reloaded' );
	}
} else {
	// Error: no taxonomy set
	_e( '<b>Error:</b> No taxonomy set for this list!', 'recipepress-reloaded' );
}
