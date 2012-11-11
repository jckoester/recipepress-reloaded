<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}

/**
 * recipe-taxonomy.php - The Template for displaying all recipe categories.
 *
 * @package RecipePress Reloaded
 * @subpackage templates
 * @author dasmaeh
 * @copyright 2012
 * @access public
 * @since 1.0
 */
/* Make sure we have some terms to list */
if ( !is_array($terms) ) {
     foreach ( $this->options['taxonomies'] as $key => $taxonomy ) {
          if ( $post->ID == $taxonomy['page'] ) {
               $tax = $key;
          }
     }

     $terms = get_terms($taxonomy, array('parent' => 0));
}

//the complete alphabet:
$alphabet = range('A', 'Z');
//the part of the alphabet which is actually used
$alph=array();
foreach($terms as $term):
    if(!in_array(substr($term->name, 0, 1), $alph)):
        array_push($alph, strtoupper(substr($term->name, 0, 1)));
    endif;
endforeach;
?>

<!-- Alphebetical index for easy "jump to"-navigation-->
<div id="nav-above" class="article_links">
    <h1 class="assistive-text"><?php __('Alphabetical index navigation', 'recipe-press-reloaded');?></h1>
    <ul class="alphabet">
	    <?php foreach($alphabet as $letter): ?>
            <?php if(in_array($letter, $alph)):?>
		        <li><a href="#<?php printf($letter) ?>"><?php printf($letter) ?></a></li>
            <?php else: ?>
                <li class="empty"><?php printf($letter) ?></li>
            <?php endif; ?>
	    <?php endforeach; ?>
    </ul>
</div>

<span class="clear"><!-- --></span>

<!-- The taxonomy-index:-->
<?php if(count($terms)>0):?>
    <div class="content-area index">
        <?php foreach($alph as $letter): ?>
            <h2><a name="<?php printf($letter)?>" id="<?php printf($letter)?>"><?php printf($letter)?></a>&nbsp;<a href="#top"></a></h2>
	            <?php $terms = get_terms($taxonomy, "name__like=$letter"); ?>
	            <?php if(count($terms)>0):?>
                    <ul class="recipe-index-left">
		            <?php for($i=0; $i<ceil(count($terms)/2); $i++):?>
                        <li>
                            <?php 
					        $ing=$terms[$i];
					        echo '<a href="'.get_term_link($ing->slug, $ing->taxonomy).'">'.$ing->name.'</a>';
				            ?>
                        </li>
        		    <?php endfor; ?>
                    </ul>
                    <?php if(count($terms)>1):?>
                    <ul class="recipe-index-right">
    		        <?php for($i=ceil(count($terms)/2); $i<=count($terms)/2; $i++):?>
                        <li>
                            <?php 
	        				$ing=$terms[$i];
	        				echo '<a href="'.get_term_link($ing->slug, $ing->taxonomy).'">'.$ing->name.'</a>';
	        			?>
                        </li>
            		<?php endfor; ?>
                    </ul>
                    <?php endif; ?>
               	<?php endif; ?>
                <span class="clear"><!-- --></span>
            <?php endforeach; ?>
        </div>
<?php else:?>
    <div id="notfound">
        <p><?php _e("Sorry, no recipes found.", "recipe-press-reloaded");?></p>
    </div>
<?php endif;?>
    
<span class="clear"><!-- --></span>


<!-- Alphebetical index for easy "jump to"-navigation-->
<div id="nav-below" class="cleared">
    <h1 class="assistive-text"><?php __('Alphabetical index navigation', 'recipe-press-reloaded');?></h1>
    <ul class="alphabet">
	    <?php foreach($alphabet as $letter): ?>
            <?php if(in_array($letter, $alph)):?>
		        <li><a href="#<?php printf($letter) ?>"><?php printf($letter) ?></a></li>
            <?php else: ?>
                <li class="empty"><?php printf($letter) ?></li>
            <?php endif; ?>
	    <?php endforeach; ?>
    </ul>
</div>

<div class="cleared"></div>
