<?php
$recipe_title = get_the_title( $recipe_post );
?>
<div class="rpr-container" itemscope itemtype="http://schema.org/Recipe" >

<!-- displaying these data is the job of the theme. Therefore they're hidden! -->
	<span class="rpr_title hidden" itemprop="name"><?php the_title(); ?></span>
  	<span class="rpr_author hidden" itemprop="author"><?php  the_author(); ?></span>
  	<span class="rpr_date hidden" itemprop="datePublished" content="<?php the_time( 'Y-m-d' ); ?>"><?php the_time( get_option('date_format') ); ?></span>
  
<!--  displaying the post image should be the job of the theme! -->
  	<!-- TODO: make nice! -->
  	<?php
  	$imgclass="hidden"; 
  	if( $this->option('recipe-header-image-display', '0' ) ) {$imgclass=""; }
    $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($recipe_post->ID), 'recipe-thumbnail' );
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
                <img <?php echo $imgclass; ?> itemprop="image" src="<?php echo $thumb_url; ?>" title="<?php echo $recipe_title;?>" />
            <?php } ?>
        </div>
    <?php } else { ?>
    <div class="recipe-header">
    <?php } ?>
    <!-- END TODO -->
    
    <!-- Taxonomies -->
    <?php the_recipe_category_bar(); ?>
    
    <!-- DESCRIPTION -->                  
    <span class="rpr_description" itemprop="description"><?php echo $recipe['rpr_recipe_description'][0]; ?></span>
    
    
    <div class="rpr-clear"></div>
    
    <!-- INGREDIENTS -->
    <?php
    $ingredients = unserialize($recipe['rpr_recipe_ingredients'][0]);
    if(!empty($ingredients))
    {
    ?>
    <h3><?php _e('Ingredients', 'recipe-press-reloaded' ); ?></h3>
    <?php if($recipe['rpr_recipe_servings'][0] != '') { ?>
        <?php _e( 'For: ', 'recipe-press-reloaded' ); ?>
        <span itemprop="recipeYield"><span class="recipe-information-servings"><?php echo $recipe['rpr_recipe_servings'][0]; ?></span> <span class="recipe-information-servings-type"><?php echo $recipe['rpr_recipe_servings_type'][0]; ?></span>
        </span>
        <?php } ?>
    <ul class="recipe-ingredients">
        <?php
        $out = '';
        $previous_group = '';
        foreach($ingredients as $ingredient) {

            if( isset( $ingredient['ingredient_id'] ) ) {
                $term = get_term($ingredient['ingredient_id'], 'ingredient');
                if ( $term !== null && !is_wp_error( $term ) ) {
                    $ingredient['ingredient'] = $term->name;
                }
            }

            if(isset($ingredient['group']) && $ingredient['group'] != $previous_group) {
                $out .= '<li class="group">' . $ingredient['group'] . '</li>';
                $previous_group = $ingredient['group'];
            }

            $out .= '<li itemprop="ingredients">';
            $out .= '<span class="recipe-ingredient-quantity-unit"><span class="recipe-ingredient-quantity" data-original="'.$ingredient['amount'].'">'.$ingredient['amount'].'</span> <span class="recipe-ingredient-unit">'.$ingredient['unit'].'</span></span>';


            $taxonomy = get_term_by('name', $ingredient['ingredient'], 'rpr_ingredient');

            $out .= ' <span class="recipe-ingredient-name">';

            $ingredient_links = $this->option('recipe_ingredient_links', 'archive_custom');
            
            $closing_tag = '';
            if (!empty($taxonomy) && $ingredient_links != 'disabled') {

                if( isset($ingredient['link']) && ( $ingredient_links == 'archive_custom' || $ingredient_links == 'custom' ) ) {
                    $custom_link = $ingredient['link'];//WPURP_Taxonomy_MetaData::get( 'ingredient', $taxonomy->slug, 'link' );
                } else {
                    $custom_link = false;
                }

                if($custom_link !== false && $custom_link !== '' && $custom_link !== NULL ) {
                    $out .= '<a href="'.$custom_link.'" class="custom-ingredient-link" target="'.$this->option('recipe_ingredient_custom_links_target', '_blank').'">';
                    $closing_tag = '</a>';
                } elseif($ingredient_links != 'custom') {
                    $out .= '<a href="'.get_term_link($taxonomy->slug, 'rpr_ingredient').'">';
                    $closing_tag = '</a>';
                } 
            }

            $out .= $ingredient['ingredient'];
            $out .= $closing_tag;
            $out .= '</span>';

            if($ingredient['notes'] != '') {
                $out .= ' ';
                $out .= '<span class="recipe-ingredient-notes">'.$ingredient['notes'].'</span>';
            }

            $out .= '</li>';
        }

        echo $out;
        ?>
    </ul>
    
    <!--- INSTRUCTIONS --->
    <?php
    $instructions = unserialize($recipe['rpr_recipe_instructions'][0]);
    if(!empty($instructions))
    {
        ?>
    <h3><?php _e( 'Instructions', 'recipe-press-reloaded' ); ?></h3>
    <!-- INFO LINE (Times) -->
    <?php the_recipe_time_bar(); ?>
    
    
    <ol class="recipe-instructions">
        <?php
        $out = '';
        $previous_group = '';
        foreach($instructions as $instruction) {
            if(isset($instruction['group']) && $instruction['group'] != $previous_group) {
                $out .= '</ol>';
                $out .= '<div class="instruction-group">' . $instruction['group'] . '</div>';
                $out .= '<ol class="recipe-instructions">';
                $previous_group = $instruction['group'];
            }

            $out .= '<li itemprop="recipeInstructions">';
            $instr_class="";
            if( isset($instruction['image']) && $this->option('recipe_images_clickable', '0') == 1 ) { $instr_class = "has_thumbnail"; }
            $out .= '<span class="recipe-instruction '.$instr_class.'">'.$instruction['description'].'</span>';

            if( isset($instruction['image']) ) {
                if($this->option('recipe_images_clickable', '0') == 1) {
                    $thumb = wp_get_attachment_image_src( $instruction['image'], 'thumbnail' );
                 
                } else{
                	$thumb = wp_get_attachment_image_src( $instruction['image'], 'large' );
                }

                $thumb_url = $thumb['0'];
                $full_img = wp_get_attachment_image_src( $instruction['image'], 'full' );
                $full_img_url = $full_img['0'];

                if($this->option('recipe_images_clickable', '0') == 1 && $thumb_url != "" ) {
                    $out .= '<a href="' . $full_img_url . '" rel="lightbox" title="' . $instruction['description'] . '">';
                    $out .= '<img src="' . $thumb_url . '" />';
                    $out .= '</a>';
                } else {
					if( $thumb_url != "" ){
                    	$out .= '<img src="' . $thumb_url . '" />';
                    }
                }
            }

            $out .= '</li>';
        }

        echo $out;
        ?>
    </ol>
    <?php } ?>
    <?php } ?>    
    
    <?php if( $recipe['rpr_recipe_notes'][0] ) { ?>
    <h3><?php _e( 'Recipe notes', 'recipe-press-reloaded' ); ?></h3>
    <div class="recipe-notes">
        <?php echo wpautop( $recipe['rpr_recipe_notes'][0] ); ?>
    </div>
    <?php } ?>
    
    
    <!-- RECIPE FOOTER -->
    <?php the_recipe_footer(); ?>

</div>
