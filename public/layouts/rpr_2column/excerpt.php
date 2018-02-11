<?php
/*
	Author: jan
	Template Name: RPR 2column
*/
?>

    
    <!-- DESCRIPTION -->                  
    <p class="rpr_description">
        <?php
            if(function_exists( 'the_rpr_recipe_description' ) && get_the_rpr_recipe_description() != null ){
                the_rpr_recipe_description();
            } ?>
    </p>
       
	<?php
	/** 
	 * Displaying the recipe title is normally done by the theme as post_title().
	 * However, if the recipe is embedded, we need to do it here.
	 */
	if( recipe_is_embedded() ){ ?>
		<h2 class="rpr_title"><?php echo get_the_title( $recipe_post ); ?></h2>
	<?php 
            if(function_exists( 'the_rpr_recipe_image' ) ){
                the_rpr_recipe_image();
            }
        }
	?>
    <!-- Taxonomies -->
    <?php the_rpr_taxonomy_list( $recipe_post->ID ); ?>
    
   
    
	<div class="rpr-clear"></div>
