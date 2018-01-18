<?php

/**
 * Add Fields to custom taxonomies
 */
$oFactory->addSettingFields(
    array( 'layout', 'rpr_default'),
    array(
        'field_id'      => 'icon_display',
        'type'          => 'checkbox',
        'title'         => __( 'Use icons' , 'recipepress-reloaded' ),
        'description'   => __( 'Icons not only look nice. They also can save you space. With this setting activated this layout will display <span class="admin_demo""><i class="fa fa-clock-o" title="ready in" ></i> 35min</span> instead of <span class="admin_demo"><span style="text-transform:uppercase; font-weight:bold;">Ready in: </span>35min</span>.' , 'recipepress-reloaded' ),
    ),
    array(
        'field_id'	=> 'printlink_class',
        'type'          => 'text',
        'title'		=> __( 'Print area class', 'recipepress-reloaded' ),
        'tip'           => __( 'Print links should only print an area of the page, usually a post. This is higly depending on wordpress theme you are using. Add here the class (prefixed by \'.\') or the id (prefixed by \'#\') of the printable area.', 'recipepress-reloaded' ),
        'default'       => '.rpr_recipe'
    ),
    array(
        'field_id'	=> 'no_printlink_class',
        'type'          => 'text',
        'title'		=> __( 'Do not print area class', 'recipepress-reloaded' ),
        'tip'           => __( 'Enter the class or ID of areas or elements that should not be printed. This is highly depending on the wordpress theme you are using. Add here the class (prefixed by \'.\') or the id (prefixed by \'#\') of the area not to be printed. Separate multiple entries with commas (\',\').', 'recipepress-reloaded' ),
        'default'       => '.no-print'
    )
);