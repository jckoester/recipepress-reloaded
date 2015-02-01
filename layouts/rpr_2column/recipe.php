<?php
/*
	Author: Jan Köster
	Author Mail: dasmaeh@rp-reloaded.net
	Author URL: www.cbjck.de
	Layout Name: RPR 2column
 	Version: 0.1
	Description: A layout with two columns.
*/
?>
<script>
var rpr_pluginUrl = '<?php echo $this->pluginUrl; ?>';
var rpr_template = '<?php echo $this->option( 'rpr_template', 'rpr_default' ); ?>';
</script>

<div class="rpr-container" itemscope itemtype="http://schema.org/Recipe" >

<?php if ( is_recipe_embedded() ){?>
	<h2 class="rpr_title"><?php echo get_the_title( $recipe_post ); ?></h2>
<?php } ?>
	<?php the_recipe_print_link(); ?>
 
	<!-- displaying these data is the job of the theme. Therefore they're hidden! -->
	<span class="rpr_title hidden" itemprop="name"><?php echo get_the_title( $recipe_post ); ?></span>
	
	<?php if( $this->get_option( 'recipe_author_display_in_recipe', 0) == '1' ){ ?>
		<?php _e( 'By:', $this->pluginName ); ?>&nbsp;<span class="rpr_author" itemprop="author"><?php  the_author_link(); ?></span>
	<?php } else { ?>
		<span class="rpr_author hidden" itemprop="author"><?php  the_author(); ?></span>	
	<?php } ?>
  	
  	<?php if( $this->get_option( 'recipe_time_display_in_recipe', 0) == '1' ){ ?>
  		<span class="rpr_date" itemprop="datePublished" content="<?php the_time( 'Y-m-d' ); ?>">(<?php the_time( get_option('date_format') ); ?>)</span>
  	<?php  } else { ?>
  		<span class="rpr_date hidden" itemprop="datePublished" content="<?php the_time( 'Y-m-d' ); ?>"><?php the_time( get_option('date_format') ); ?></span>
  	<?php } ?>
  	
	<!--  displaying the post image should be the job of the theme! -->
	<?php the_recipe_thumbnail( $recipe_post->ID ); ?>

    
    <!-- DESCRIPTION -->                  
    <p class="rpr_description" itemprop="description"><?php echo $recipe['rpr_recipe_description'][0]; ?></p>
       
    
    <div class="rpr_col1">
    	 <!-- INGREDIENTS -->
    	<h3>
    		<?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-shopping-cart"></i><?php }?><?php _e( 'Ingredients', $this->pluginName ); ?>
    	</h3>
   
		<?php the_recipe_servings_bar( $recipe_post->ID ); ?> 
    	<?php the_recipe_ingredient_list( array( 'ID' => $recipe_post->ID) ); ?>
    </div>
    <div class="rpr_col2">
    
    	<?php if( get_the_recipe_times( $recipe_post->ID ) != "" ){ ?>
    		<h3>
    		<?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-clock-o"></i><?php } ?><?php _e( 'Time', $this->pluginName ); ?></h3>
    		<?php the_recipe_times( $recipe_post->ID ); ?>
    	<?php } ?>
    	
    	<?php if( has_recipe_nutrition ($recipe_post->ID ) ) { ?>
    		<h3><?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-fire"></i><?php } ?><?php _e( 'Nutritional Information', $this->pluginName ); ?></h3>
    		<?php the_recipe_nutrition ($recipe_post->ID ); ?>
    	<?php } ?>
    	
    	<?php  if( $this->option( 'recipe_tags_use_wp_categories', 1) == '1' && $this->option( 'recipe_display_categories_in_recipe' , 1 ) == '1' && get_the_recipe_taxonomy_term_list( $recipe_post->ID, 'category' ) != '' ) {?>
    		<h3><?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-list-ul"></i><?php } ?><?php _e( 'Categories', $this->pluginName ); ?></h3>
    	<?php the_recipe_taxonomy_term_list( $recipe_post->ID, 'category' ); ?>
    	<?php } ?>
    	
    	<?php  if( $this->option( 'recipe_tags_use_wp_tags', 1) == '1' && $this->option( 'recipe_display_tags_in_recipe' , 1 ) == '1' && get_the_recipe_taxonomy_term_list( $recipe_post->ID, 'post_tag' ) != '' ) {?>
    		<h3><?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-tags"></i><?php } ?><?php _e( 'Tags', $this->pluginName ); ?></h3>
    	<?php the_recipe_taxonomy_term_list( $recipe_post->ID, 'post_tag' ); ?>
    	<?php } ?>
    	
    	<?php $taxonomies = get_option('rpr_taxonomies', array() );
    	
    	foreach( array_keys( $taxonomies ) as $tax ){
			if( $tax != 'rpr_ingredient' && get_the_recipe_taxonomy_term_list( $recipe_post->ID, $tax ) != '' ){
		?>
				<h3><?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa <?php echo $tax; ?>"></i><?php } ?><?php echo $taxonomies[$tax]['labels']['singular_name']; ?></h3>
		<?php 
				the_recipe_taxonomy_term_list( $recipe_post->ID, $tax );
			}
		}
    	?>
    </div>
    
   
   
    
	<div class="rpr-clear"></div>

    
    <!--- INSTRUCTIONS --->
    <h3><?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-cogs"></i><?php } ?><?php _e( 'Instructions', $this->pluginName ); ?></h3>
    <?php the_recipe_instruction_list( array( 'ID' => $recipe_post->ID ) ); ?>
    
    <!--  NOTES -->
    <?php if( isset( $recipe['rpr_recipe_notes'][0] ) && $recipe['rpr_recipe_notes'][0] != "" ) { ?>
    <h3><?php if( $this->get_option( 'recipe_icons_display', 0 ) == 1 ){?><i class="fa fa-paperclip"></i><?php }?><?php _e( 'Recipe notes', $this->pluginName ); ?></h3>
	<?php the_recipe_notes( array('ID' => $recipe_post->ID ) )?>
    <?php } ?>
    
    
    <!-- RECIPE FOOTER -->
    <?php //the_recipe_footer(); ?>

</div>