<?php
/*
	Author: jan
	Template Name: RPR Default
*/
// RPR Default Template - the excerpt


//$recipe_title = get_the_title( $recipe_post );
//the_post_thumbnail();
?>
   
   
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

	$icons = (boolean) AdminPageFramework::getOption( 'rpr_options', array( 'layout', 'rpr_default', 'icon_display' ), false);
        the_rpr_taxonomy_list( $icons, true ,'&nbsp;/&nbsp;', true );
	?>
                
    <div class="rpr-clear"></div>
	<?php
		the_rpr_recipe_description();
		
	?>
        
    <div class="rpr-clear"></div>
    
    