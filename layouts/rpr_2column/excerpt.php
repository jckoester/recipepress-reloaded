<?php
/*
	Author: jan
	Template Name: RPR 2column
*/
?>

    
    <!-- DESCRIPTION -->                  
    <p class="rpr_description" itemprop="description"><?php echo $recipe['rpr_recipe_description'][0]; ?></p>
       

    <!-- Taxonomies -->
    <?php the_recipe_taxonomy_bar( $recipe_post->ID ); ?>
    
    <!-- DESCRIPTION -->                  
    <span class="rpr_description" itemprop="description"><?php echo $recipe['rpr_recipe_description'][0]; ?></span>
    
   
   
    
	<div class="rpr-clear"></div>


</div>