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

// Create an empty output variable.
$out = '';
?>

<?php if ( $posts && count( $posts ) > 0 ) : ?>
    <?php

		// Create an index i to compare the number in the list and check for first and last item.
		$i = 0;

		// Create an empty array to take with the first letters of all headlines.
		$letters = array();

		// Walk through all the terms to build alphabet navigation.
		foreach ( $posts as $post ) {
			if ( $headers ) {
				// Add first letter headlines for easier navigation if set so.

				// Get the first letter (without specisl chars).
				$first_letter = substr( normalize_special_chars( $post->post_title ), 0, 1 );

				// Check if we've already had a headline.
				if( ! in_array( $first_letter, $letters ) ) {
					// Close list of preceeding group.
					if ( $i !== 0 ) {
						$out .= '</ul>';
						$out .= '</div>';
					}

					// Checking if our counter is on even or odd number.
					$even_odd = ( $i % 2 === 0 ) ? 'even' : 'odd';

					// Create a headline.
					$out .= '<div class="index-info ' . $even_odd . '">';
					$out .= '<h2><a class="rpr_toplink" href="#top"><i class="fa fa-long-arrow-up"></i> </a><a name="' . $first_letter . '"></a>';
					$out .= strtoupper( $first_letter );
					$out .= '</h2>';

					// Start new list.
					$out .= '<ul class="rpr_taxlist">';

					// Add the letter to the list.
					$letters[] = $first_letter;
				}
			} else {
				// Start list before first item.
				if( $i === 0 ) {
					$out .= '<ul class="rpr_taxlist">';
				}
			}

			// Add the entry for the post.
				$out .= '<li><a href="' . get_permalink( $post->ID ) . '">';
		        $out .= $post->post_title;
		        $out .= '</a></li>';

			// increment the counter.
			$i++;
		}

		// Close the last list.
		$out .= '</ul>';
		$out .= '</div>';

    // Output the rendered list
    the_alphabet_nav_bar( $letters );
?>

<div class="index-container">
    <?php echo $out; ?>
</div>

<?php the_alphabet_nav_bar( $letters ); ?>

<?php else : ?>
    // No recipes.
 <?php _e( 'There are no recipes to display.', 'recipepress-reloaded' ); ?>
<?php endif; ?>