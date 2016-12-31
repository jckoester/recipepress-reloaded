<?php
/*
	Author: jan
	Template Name: RPR 2column
*/
?>

    
    <!-- DESCRIPTION -->                  
    <p class="rpr_description" itemprop="description"><?php echo $recipe['rpr_recipe_description'][0]; ?></p>
       
	<?php
	/** 
	 * Displaying the recipe title is normally done by the theme as post_title().
	 * However, if the recipe is embedded, we need to do it here.
	 */
	if( recipe_is_embedded() ){ ?>
		<h2 class="rpr_title"><?php echo get_the_title( $recipe_post ); ?></h2>
	<?php } ?>
	<?php
	the_rpr_recipe_image();
	?>
    <!-- Taxonomies -->
    <?php the_rpr_taxonomy_list( $recipe_post->ID ); ?>
    
   
    
	<div class="rpr-clear"></div>
