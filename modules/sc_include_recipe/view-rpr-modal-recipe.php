<?php
/**
 * The shortcode overlay view (aka the dialog itself) to insert recipe shortcodes.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
?>

<div id="rpr-modal-backdrop-scr" style="display: none"></div>
		<div id="rpr-modal-wrap-scr" class="wp-core-ui search-panel-visible" style="display: none">
		<form id="rpr-modal-form-scr" tabindex="-1">
		<?php wp_nonce_field( 'rpr-ajax-nonce', 'rpr_ajax_nonce', false ); ?>
		<div id="rpr-modal-title-scr">
			<?php _e( 'Insert recipe', 'recipepress-reloaded' ) ?>
			<button type="button" id="rpr-modal-close-scr"><span class="screen-reader-text"><?php _e( 'Close' ); ?></span></button>
	 	</div>
		<div id="rpr-modal-panel-scr">
			<input id="recipe-id-field" type="hidden" name="recipeid" />
			<input id="recipe-title-field" type="hidden" name="recipetitle" />
			
			<p class="howto"><?php _e( 'Choose the recipe you want to include from the list below or search for it.', 'recipepress-reloaded' ); ?></p>
			
			<div class="link-search-wrapper">
				<label>
					<span class="search-label"><?php _e( 'Search' ); ?></span>
					<input type="search" id="rpr-search-field" class="link-search-field" autocomplete="off" />
					<span class="spinner"></span>
				</label>
			</div>
			
			<div id="rpr-search-results" class="query-results" tabindex="0">
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
				</div>
				<div id="rpr-most-recent-results" class="query-results" tabindex="0">
					<div class="query-notice" id="query-notice-message">
						<em class="query-notice-default"><?php _e( 'No search term specified. Showing recent items.' ); ?></em>
						<em class="query-notice-hint screen-reader-text"><?php _e( 'Search or use up and down arrow keys to select an item.' ); ?></em>
					</div>
					<ul></ul>
					<div class="river-waiting">
						<span class="spinner"></span>
					</div>
				</div>
			<!--<a id="rpr-modal-scr-options-link"><i class="fa fa-caret-right"></i><?php _e( "Display options", 'recipepress-reloaded' ); ?> </a>-->
			<div id="rpr-modal-scr-options-panel" >
				<b><?php _e( "Display options:", 'recipepress-reloaded' ); ?></b>
				<ul id="rpr-modal-scr-options-list">
					<li>
						<input type="checkbox" id="rpr-embed-excerpt" name="embed-excerpt" value="embed-excerpt" />
						<label for="rpr-embed-excerpt"><span><?php _e( 'Embed excerpt only', 'recipepress-reloaded' ); ?></span></label>
					</li>
					<li>
						<input type="checkbox" id="rpr-embed-nodesc" name="embed-nodesc" value="embed-nodesc" />
						<label for="rpr-embed-nodesc"><span><?php _e( 'Embed <b>without</b> description', 'recipepress-reloaded' ); ?></span></label>
					</li>
				</ul>
			</div>
			
			<div id="rpr-modal-scr-new-recipe-panel">
				<?php printf(
				/* Translators: 1: Link tag opening and icon  2: Link tag closure */
					__( '%1sCreate a new recipe.%2s (This will open a new tab and you will need to return here for including the recipe', 'recipepress-reloaded' ),
					'<a href="'. admin_url() .'/post-new.php?post_type=rpr_recipe" target="_new" >' . '<i class="fa fa-plus-circle"></i>&nbsp;' ,
					'</a>'
				);
				?>
			</div>
		</div>
		
		<div class="submitbox">
			<div id="rpr-modal-cancel-scr">
				<a class="submitdelete deletion" href="#"><?php _e( 'Cancel' ); ?></a>
			</div>
			<div id="rpr-modal-update-scr">
				<input type="submit" value="<?php esc_attr_e( 'Include Shortcut', 'recipepress-reloaded' ); ?>" class="button button-primary" id="rpr-modal-submit-scr" name="rpr-link-submit">
			</div>
		</div>
		</form>
		</div>