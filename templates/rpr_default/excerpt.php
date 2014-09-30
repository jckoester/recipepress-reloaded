<?php
/*
	Author: jan
	Template Name: RPR Default
*/
// RPR Default Template - the excerpt


//$recipe_title = get_the_title( $recipe_post );
//the_post_thumbnail();
?>
   
    <!-- Taxonomies -->
    <?php the_recipe_taxonomy_bar( $recipe_post->ID ); ?>
    
    <!-- DESCRIPTION -->                  
    <span class="rpr_description" itemprop="description"><?php echo $recipe['rpr_recipe_description'][0]; ?></span>
    
    
    <div class="rpr-clear"></div>
    
    