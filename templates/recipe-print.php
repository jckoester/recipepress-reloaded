<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * recipe-print.php - The Template for printing all recipes.
 *
 * @package RecipePress
 * @subpackage templates
 * @author GrandSlambert
 * @copyright 2009-2011
 * @access public
 * @since 1.2
 */
if ( !$template = $wp_query->query_vars['print'] ) {
     $template = $this->options['default-print-template'];
}

if ( get_option('permalink_structure') ) {
     $urldivider = '?';
} else {
     $urldivider = '&';
}
?>
<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <title>
    	<?php
			wp_title('&laquo;', true, 'right');
			if ( get_query_var('cpage') ) {
     			echo ' Page ' . get_query_var('cpage') . ' &laquo; ';
			}
			bloginfo('name');
		?>
	</title>
    <link rel="stylesheet" media="screen" type="text/css" href="<?php echo RPR_URL.'css/rpr-print.css'; ?>" />
    <link rel="stylesheet" media="print" type="text/css" href="<?php echo RPR_URL.'css/rpr-print-printer.css' ?>" />
    <script type="text/javascript">
    	function rpr_hide_image(){
    		document.getElementById("recipe-image").style.display="none";
    	}
    </script>
    <?php wp_head(); ?>
</head>
<body class="print-recipes">
	<div id="print-controls">
		<ul>
			<li><a href="javascript:rpr_hide_image();"><?php _e("Hide image", "recipe-press-reloaded"); ?></a></li>
			<li><a href="javascript:print();"><?php _e("Print", "recipe-press-reloaded"); ?></a></li>
			<li><a href="<?php the_permalink()?>" title="<?php printf(__('Return to %1$s', 'recipe-press-reloaded'), get_the_title()); ?>"><?php printf(__('Return to %1$s', 'recipe-press-reloaded'), get_the_title()); ?></a></li>
		</ul>
	</div>
	<div id="post-<?php the_ID(); ?>" class="recipe-print">
		<h1><?php the_title(); ?></h1>
    	<div class="recipe-header">      
        	<?php if ( function_exists('has_post_thumbnail') && has_post_thumbnail() ) : ?>
            	<div id="recipe-image" class="recipe-press-image align-left">
                	<?php the_post_thumbnail('recipe-press-image'); ?>
            	</div>
            	<span class="clear"><!-- --></span>
        	<?php endif; ?> 
        	<p class="recipe-notes"><?php the_recipe_introduction(array('length' => '5000')); ?></p>
        	<span class="clear"><!-- --></span>
        	<div class="recipe-meta-left">
        		<?php if ( use_recipe_courses() ): ?>
            		<span class="recipe-course">
                		<?php the_terms(get_the_id(), 'recipe-course');?>
            		</span>&nbsp;
        		<?php endif; ?>
    			<?php if ( use_recipe_servings() ): ?>
            		<span class="recipe-servings">
                		<?php _e("for", "recipe-press-reloaded"); ?>
                		<?php the_recipe_servings(); ?>
            		</span>
        		<?php endif; ?>
        		<?php if ( use_recipe_times ( ) && get_recipe_prep_time() != ""  ) : ?>
        			<div id="recipe-times-<?php the_ID(); ?>" class="recipe-times recipe-section-<?php the_id(); ?>">
            			<ul class="recipe-times-list">
                			<?php the_recipe_prep_time(array('type'=>'single')); ?>
                			<?php the_recipe_cook_time(array('type'=>'single')); ?>
                			<?php the_recipe_ready_time(array('type'=>'single')); ?>
            			</ul>
        			</div><!-- #recipe-details -->
        		<?php endif;?>
    		</div>
    		<div class="recipe-meta-right">
    			<?php if ( use_recipe_categories() ) :?>
            		<span class="recipe-category">
                		<?php _e('Posted in: ', 'recipe-press-reloaded');
                		the_terms(get_the_id(), 'recipe-category');?>
            		</span><br/>
        		<?php endif; ?>
        		<?php if ( use_recipe_cuisines() ): ?>
            		<span class="recipe-cuisine">
                		<?php _e('from: ', 'recipe-press-reloaded');
                		the_terms(get_the_id(), 'recipe-cuisine');?>
            		</span><br/>
        		<?php endif; ?>
        		<?php if ( use_recipe_seasons() ): ?>
            		<span class="recipe-season">
                		<?php _e('Season: ', 'recipe-press-reloaded');
                		the_terms(get_the_id(), 'recipe-season');?>
            		</span><br/>
	        	<?php endif; ?>
    		</div>
    	</div><!-- .recipe-header -->
    	<div class="recipe-content">
        	<h2><?php _e('Ingredients', 'recipe-press-reloaded'); ?> </h2>
        	<?php the_recipe_ingredients(); ?>

        	<h2 ><?php _e('Directions', 'recipe-press-reloaded'); ?></h2>
        	<?php the_recipe_directions(); ?>
    	</div><!-- .entry-content -->

	<?php do_action('after_recipe_content'); ?>
	</div>

	<?php wp_footer(); ?>
</body>
</html>

