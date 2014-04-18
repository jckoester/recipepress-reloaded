<?php
$notes = get_post_meta( $recipe->ID, "rpr_recipe_notes", true );
$options = array(
		'textarea_rows' => 4
);

//if(isset($wpurp_user_submission)) {
    $options['media_buttons'] = false;
//}

wp_editor( $notes, 'rpr_recipe_notes',  $options );
?>