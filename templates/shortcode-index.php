<?php
if ( preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) ) {
     die('You are not allowed to call this page directly.');
}
/**
 * index-recipe.php - The Template for displaying all recipes.
 *
 * @package RecipePress Reloaded
 * @subpackage templates
 * @author dasmaeh
 * @copyright 2012
 * @access public
 * @since 0.1
 */
global $RECIPEPRESSOBJ;

//the complete alphabet:
$alphabet = range('A', 'Z');
//the part of the alphabet which is actually used
$alph=array();
$recs=array();
if ( $recipes->have_posts() ) :
    while ($recipes->have_posts()) : $recipes->the_post();
        $letter = strtoupper(substr(get_the_title(), 0, 1));
        if(!in_array($letter, $alph)):
            array_push($alph, $letter);
            $recs[$letter]=array('letter'=>$letter, 'posts'=>array());
        endif;
        array_push($recs[$letter]['posts'], array('title'=> get_the_title(), 'link'=>get_permalink()));
    endwhile; ?>
<?php endif;
//asort($recs);
?>

<!-- Alphebetical index for easy "jump to"-navigation-->
    <div id="nav-above">
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

    <div>
    <!-- The taxonomy-index:-->
    <?php if(count($recs)>0):?>
        <div class="content-area index">
            <?php $a=0; ?>
            <?php foreach($alph as $letter): ?>
	            <h2><a name="<?php printf($letter)?>" id="<?php printf($letter)?>"><?php printf($letter)?></a>&nbsp;<a href="#top"></a></h2>

	            <?php $a=$letter;//$terms = get_terms($tax->name, "name__like=$letter"); ?>
	            <?php if(count($recs[$a])>0):?>
                    <ul class="recipe-index-left">
		            <?php for($i=0; $i<ceil(count($recs[$a])/2); $i++):?>
                        <li>
                            <?php 
					        $ing=$recs[$a]['posts'][$i];
					        echo '<a href="'.$ing['link'].'">'.$ing['title'].'</a>';
				            ?>
                        </li>
        		    <?php endfor; ?>
                    </ul>
                    <?php if(count($recs[$a]['posts'])>1):?>
                    <ul class="recipe-index-right">
    		        <?php for($i=ceil(count($recs[$a])/2); $i<=count($recs[$a]); $i++):?>
                        <li>
                            <?php 
	        				$ing=$recs[$a]['posts'][$i];
					        echo '<a href="'.$ing['link'].'">'.$ing['title'].'</a>';
	        			?>
                        </li>
            		<?php endfor; ?>
                    </ul>
                    <?php endif; ?>
               	<?php endif; ?>
                <span class="clear"><!-- --></span>
                <?php $a++;?>
            <?php endforeach; ?>
        </div>
    <?php else:?>
        <div id="notfound">
            <p><?php _e("Sorry, no recipes found.", "recipe-press-reloaded");?></p>
        </div>
    <?php endif;?>
    </div>

    <span class="clear"><!-- --></span>

    <!-- Alphebetical index for easy "jump to"-navigation-->
    <div id="nav-below" style="clear:both;">
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
