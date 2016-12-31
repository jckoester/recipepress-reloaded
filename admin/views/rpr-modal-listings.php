<?php
/**
 * The shortcode overlay view (aka the dialog itself) to insert listings shortcodes.
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
?>
<div id="rpr-modal-backdrop-scl" style="display: none"></div>

<div id="rpr-modal-wrap-scl" class="wp-core-ui search-panel-visible" style="display: none">
	<form id="rpr-modal-form-scl" tabindex="-1">
		<?php wp_nonce_field( 'rpr-ajax-nonce', 'rpr_ajax_nonce', false ); ?>
		<div id="rpr-modal-title-scl">
			<?php _e( 'Insert recipe listing', 'recipepress-reloaded' ) ?>
			<button type="button" id="rpr-modal-close-scl"><span class="screen-reader-text"><?php _e( 'Close' ); ?></span></button>
	 	</div>
		<div id="rpr-modal-panel-scl">
			<ul id="rpr-modal-scl-mode">
				<li>
					<input type="radio" selected="selected" value="rpr-tax-list" id="rpr-modal-scl-mode-tax" name="rpr-modal-scl-mode" />
					<label for="rpr-modal-scl-mode-tax"><b><?php _e('Embed taxonomy index', 'recipepress-reloaded'); ?></b></label>
					<div id="rpr-taxonomy-panel">
						<label><span><?php _e('Taxonomy', 'recipepress-reloaded' ); ?></span></label>
						<select id="recipe-taxonomy">
							<?php 
							/**
							 * add builtin taxonomies to the list:
							 * (Categories and tags are left out as those are global to wp)
							?>
							<!--<option value="category" ><?php _e( "Category"); ?></option>
							<option value="tag" ><?php _e( "Tag"); ?></option>-->
							<?php
							if( is_array( AdminPageFramework::getOption( 'rpr_options', array('tax_builtin' ) ) ) ){
								foreach( AdminPageFramework::getOption( 'rpr_options', array('tax_builtin') ) as $tax ){
									if( isset( $tax['id'] ) ) {
										?>
										<option value="<?php echo $tax['id']; ?>" ><?php echo $tax['singular']; ?></option>
									<?php
									}
								}
							}
							/**
							 * add builtin taxonomies to the list:
							 */
							if( is_array( AdminPageFramework::getOption( 'rpr_options', array('tax_custom' ) ) ) ){
								foreach( AdminPageFramework::getOption( 'rpr_options', array('tax_custom') ) as $tax ){
									if( isset( $tax['slug'] ) ) {
										?>
										<option value="<?php echo $tax['slug']; ?>" ><?php echo $tax['singular']; ?></option>
									<?php
									}
								}
							} ?>
						</select>
					</div>
				</li>
				<li>
					<input type="radio" value="rpr-recipe-index" name="rpr-modal-scl-mode" id="rpr-modal-scl-mode-ind"/>
					<label for="rpr-modal-scl-mode-ind"><b><?php _e('Embed recipe index', 'recipepress-reloaded'); ?></b></label>
				</li>
			</ul>
		</div>
		<div class="submitbox">
			<div id="rpr-modal-cancel-scl">
				<a class="submitdelete deletion" href="#"><?php _e( 'Cancel' ); ?></a>
			</div>
			<div id="rpr-modal-update-scl">
				<input type="submit" value="<?php esc_attr_e( 'Include Shortcut', 'recipepress-reloaded' ); ?>" class="button button-primary" id="rpr-modal-submit-scl" name="rpr-modal-submit-scl">
			</div>
		</div>
		</form>
		</div>