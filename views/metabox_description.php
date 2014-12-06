<?php
$description = get_post_meta( $recipe->ID, "rpr_recipe_description", true );
$options = array(
    'textarea_rows' => 4
);

//if(isset($wpurp_user_submission)) {
    $options['media_buttons'] = true;
//}

wp_editor( $description, 'rpr_recipe_description',  $options );
?>