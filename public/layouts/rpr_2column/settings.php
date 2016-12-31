<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Add Fields to custom taxonomies
 */
$oFactory->addSettingFields(
    array( 'layout', 'rpr_2column'),
    array(
        'field_id'      => 'icon_display',
        'type'          => 'checkbox',
        'title'         => __( 'Use icons' , 'recipepress-reloaded' ),
        'description'   => __( 'Icons not only look nice. They also can save you space. With this setting activated this layout will display <span class="admin_demo""><i class="fa fa-clock-o" title="ready in" ></i> 35min</span> instead of <span class="admin_demo"><span style="text-transform:uppercase; font-weight:bold;">Ready in: </span>35min</span>.' , 'recipepress-reloaded' ),
    ),
    array(
        'field_id'	=> 'printlink_display',
        'type'          => 'checkbox',
        'title'		=> __( 'Display print link', 'recipepress-reloaded' ),
        'tip'           => __( 'Adds a print link to your recipes. It\'s recommended to use one of the numerous print plugins for wordpress to include a print link to ALL of your posts.', 'recipepress-reloaded' ),
        'default'       => false
    ),
    array(
        'field_id'	=> 'printlink_class',
        'type'          => 'text',
        'title'		=> __( 'Print area class', 'recipepress-reloaded' ),
        'tip'           => __( 'Print links should only print an area of the page, usually a post. This is higly depending on wordpress theme you are using. Add here the class (prefixed by \'.\') or the id (prefixed by \'#\') of the printable area.', 'recipepress-reloaded' ),
        'default'       => '.rpr_recipe'
    )
);