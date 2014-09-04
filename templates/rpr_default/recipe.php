<?php
/*
	Author: jan
	Template Name: RPR Default
*/
?>
<script>
var rpr_pluginUrl = '<?php echo $this->pluginUrl; ?>';
var rpr_template = '<?php echo $this->option( 'rpr_template', 'rpr_default' ); ?>';
</script>
<?php
// RPR Default Template
// Check if we are in single mode or shortcode mode (aka included recipe)
if( get_post_type( ) != 'rpr_recipe' ){
	$mode = 'shortcode';
} else {
	$mode = 'single';
}


$recipe_title = get_the_title( $recipe_post );
//the_post_thumbnail();
?>
<div class="rpr-container" itemscope itemtype="http://schema.org/Recipe" >

<?php if ( $mode == 'shortcode' ){?>
	<h2 class="rpr_title"><?php echo get_the_title( $recipe_post ); ?></h2>
<?php } ?>
<?php the_recipe_print_link(); ?>
 
<!-- displaying these data is the job of the theme. Therefore they're hidden! -->
	<span class="rpr_title hidden" itemprop="name"><?php echo get_the_title( $recipe_post ); ?></span>
  	<span class="rpr_author hidden" itemprop="author"><?php  the_author(); ?></span>
  	<span class="rpr_date hidden" itemprop="datePublished" content="<?php the_time( 'Y-m-d' ); ?>"><?php the_time( get_option('date_format') ); ?></span>
  
<!--  displaying the post image should be the job of the theme! -->
<?php
$imgclass="hidden"; 
if( $this->option('recipe_display_image', '0' ) || $mode=='shortcode' ) {$imgclass=""; }

// Get the image URL:
$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($recipe_post->ID), 'large' );
$thumb_url = $thumb['0'];

if(!is_null($thumb_url)) {
	$full_img = wp_get_attachment_image_src( get_post_thumbnail_id($recipe_post->ID), 'full' );
    $full_img_url = $full_img['0'];
    ?>
    <div class="recipe-header has-image">
        <div class="recipe-header-image">
            <?php
            if($this->option('recipe_images_clickable', '0') == 1) {
            ?>
            <a href="<?php echo $full_img_url; ?>" rel="lightbox" title="<?php echo $recipe_title;?>">
                <img class="<?php echo $imgclass; ?>" itemprop="image" src="<?php echo $thumb_url; ?>" title="<?php echo $recipe_title;?>" />
            </a>
            <?php } else { ?>
                <img class="<?php echo $imgclass; ?>" itemprop="image" src="<?php echo $thumb_url; ?>" title="<?php echo $recipe_title;?>" />
            <?php } ?>
        </div>
<?php 
} else { ?>
    <div class="recipe-header">
<?php } ?>

    
    <!-- Taxonomies -->
    <?php the_recipe_taxonomy_bar( $recipe_post->ID ); ?>
    
    <!-- DESCRIPTION -->                  
    <span class="rpr_description" itemprop="description"><?php echo $recipe['rpr_recipe_description'][0]; ?></span>
    
    
    <div class="rpr-clear"></div>
    
    <!--  NUTROTIONAL INFORMATION -->
    <?php if( has_recipe_nutrition( $recipe_post->ID ) ) {
    	the_recipe_nutrition($recipe_post->ID );
    } ?>
    <!-- INGREDIENTS -->
    <h3><?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-shopping-cart"></i> <?php }?><?php _e('Ingredients', $this->pluginName ); ?></h3>
   
	<?php the_recipe_servings_bar( $recipe_post->ID ); ?> 
    <?php the_recipe_ingredient_list( array( 'ID' => $recipe_post->ID) ); ?>
    
  
    
    <!--- INSTRUCTIONS --->
    <h3><?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-cogs"></i> <?php } ?><?php _e( 'Instructions', $this->pluginName ); ?></h3>
    <!-- INFO LINE (Times) -->
    <?php the_recipe_times( $recipe_post->ID ); ?>
    <?php the_recipe_instruction_list( array( 'ID' => $recipe_post->ID ) ); ?>
    
    <!--  NOTES -->
    <?php if( isset( $recipe['rpr_recipe_notes'][0] ) ) { ?>
    <h3><?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-paperclip"></i> <?php }?><?php _e( 'Recipe notes', $this->pluginName ); ?></h3>
	<?php the_recipe_notes( array('ID' => $recipe_post->ID ) )?>
    <?php } ?>
    
    
    <!-- RECIPE FOOTER -->
    <?php the_recipe_footer(); ?>
</div>
</div>