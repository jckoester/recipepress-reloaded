<div id="rpr-link-backdrop" style="display: none"></div>
		<div id="rpr-link-wrap" class="wp-core-ui search-panel-visible" style="display: none">
		<form id="rpr-link" tabindex="-1">
		<?php wp_nonce_field( 'rpr-ajax-nonce', 'rpr_ajax_nonce', false ); ?>
		<div id="link-modal-title">
			<?php _e( 'Insert RecipePress reloaded Shortcode' ) ?>
			<button type="button" id="rpr-link-close"><span class="screen-reader-text"><?php _e( 'Close' ); ?></span></button>
	 	</div>
		<div id="link-selector">
			<div id="shortcode-select">
				<div>
	 				<label><span><?php _e('Select the type of shortcode to embed:', 'recipepress-reloaded'); ?></span></label>
	 				<select id="rpr-shortcode-selector">
	 					<option selected value="rpr-recipe"><?php _e('Embed recipe', 'recipepress-reloaded'); ?></option>
	 					<option value="rpr-tax-list"><?php _e('Embed taxonomy index', 'recipepress-reloaded'); ?></option>
	 					<option value="rpr-recipe-index"><?php _e('Embed recipe index', 'recipepress-reloaded'); ?></option>
	 				</select>
	 			</div>
	 		</div>
	 		<div id="rpr-taxonomy-panel" style="display:none;">
	 			<div>
	 				<label><span><?php _e('Taxonomy', 'recipepress-reloaded' ); ?></span></label>
	 				<select id="recipe-taxonomy">
	 					<?php 
	 						$taxonomies = get_option('rpr_taxonomies');
							
							foreach($taxonomies as $id=>$tax){?>
								<option value="<?php echo $id; ?>" <?php if($id=='rpr_ingredient'){echo 'selected';} ?>><?php echo $tax['labels']['singular_name']; ?></option>
							<?php
							}
	 					?>
	 				</select>
	 			</div>
	 		</div>
	 		<div id="embed-excerpt-div">
	 			<input type="checkbox" id="rpr-embed-excerpt" name="embed-excerpt" value="embed-excerpt" />
	 			<label for="rpr-embed-excerpt"><span><?php _e( 'Embed excerpt only', 'recipepress-reloaded' ); ?></span></label>
	 		</div>
	 		<div id="rpr-recipelist-panel" style="display:none;"></div>
			<div id="rpr-recipe-panel">
				
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
			</div>
		</div>
		<div class="submitbox">
			<div id="rpr-link-cancel">
				<a class="submitdelete deletion" href="#"><?php _e( 'Cancel' ); ?></a>
			</div>
			<div id="rpr-link-update">
				<input type="submit" value="<?php esc_attr_e( 'Include Shortcut', 'recipepress-reloaded' ); ?>" class="button button-primary" id="rpr-link-submit" name="rpr-link-submit">
			</div>
		</div>
		</form>
		</div>