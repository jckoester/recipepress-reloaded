<?php

if (!function_exists('get_the_rpr_recipe_instructions_headline')) {

    /**
     * Renders the headline for instruction list.
     * Icons are optional, headline level depends on embedded or standalone
     * recipe
     *
     * @param boolean $icons
     * @return string
     */
    function get_the_rpr_recipe_instructions_headline($icons) {
        /**
         *  Create an empty output string
         */
        $out = '';

        /**
         * Add a third level heading for embedded recipes or a second level
         * heading for a standalone recipe
         */
        if (recipe_is_embedded()) {
            $out .= '<h3>';
        } else {
            $out .= '<h2>';
        }

        /**
         * Add icon if desired
         */
        if ($icons) {
            /**
             * @todo: Create an option for instructions icon
             */
            $out .= '<i class="fa fa-cogs"></i>&nbsp;';
        }

        $out .= $headline = AdminPageFramework::getOption( 'rpr_options', array( 'advanced', 'instructions_headline' ), __('Instructions', 'recipepress-reloaded') );

        if (recipe_is_embedded()) {
            $out .= '</h3>';
        } else {
            $out .= '</h2>';
        }
        /**
         * Return the rendered headline
         */
        return $out;
    }

}

if (!function_exists('the_rpr_recipe_instructions_headline')) {

    /**
     * Outputs the rendered headline
     *
     * @since 0.8.0
     * @param boolean $icons
     */
    function the_rpr_recipe_instructions_headline($icons = false) {
        echo get_the_rpr_recipe_instructions_headline($icons);
    }

}

if (!function_exists('get_the_rpr_recipe_instructions')) {

    /**
     * Render the instructions list
     *
     * @since 0.8.0
     * @return string
     */
    function get_the_rpr_recipe_instructions() {
        /**
         *  Get the recipe id
         */
        $recipe_id = get_recipe_id();
        $recipe = get_post_custom($recipe_id);

        /**
         *  Create an empty output string
         */
        $out = '<div class="rpr_instruction">';

        /**
         *  Get the instructions:
         */
        $instructions = unserialize($recipe['rpr_recipe_instructions'][0]);

        if (count($instructions) > 0) {

            /**
             * Loop over all the instructions
             */
            if (is_array($instructions)) {
                $i = 0;
                foreach ($instructions as $instruction) {
                    /**
                     * Check if the instruction is a grouptitle
                     */
                    if (isset($instruction['grouptitle'])) {
                        /**
                         * Render the grouptitle
                         */
                        $out .= rpr_render_instruction_grouptitle($instruction);
                    } else {

                        if ($i == 0) {
                            /**
                             * Start the list on the first item
                             */
                            $out .= '<ol class="rpr-instruction-list" >';
                        }
                        /**
                         * Render the instruction block
                         */
                        $out .= rpr_render_instruction_block($instruction);
                    }
                    $i++;
                }
            }
            /**
             * Close the list on the last item
             */
            $out .= '</ol>';
            
        } else {
            /**
             * Issue a warning, if there are no instructions for the recipe
             */
            $out .= '<p class="warning">' . __('No instructions could be found for this recipe.', 'recipepress-reloaded') . '</p>';
        }

        $out .= '</div>';

        /**
         * Return the rendered instructions list
         */
        return $out;
    }

}

if (!function_exists('rpr_render_instruction_grouptitle')) {

    /**
     * Render the grouptitle for a instruction group
     *
     * @since 0.8.0
     * @param array $instruction
     * @return string
     */
    function rpr_render_instruction_grouptitle($instruction) {
        /**
         *  Create an empty output string
         */
        $out = '';

        if ($instruction['sort'] == 0) {
            /**
             * Do not close the instruction list of the previous group if this is
             * the first group
             */
        } else {
            /**
             * Close the instruction list of the previous group
             */
            $out .= '</ol>';
        }

        /**
         * Create the headline for the instruction group
         */
        if (recipe_is_embedded()) {
            /**
             * Fourth level headline for embedded recipe
             */
            $out .= '<h4 class="rpr-instruction-group-title">' . esc_html($instruction['grouptitle']) . '</h4>';
        } else {
            /**
             * Third level headline for standalone recipes
             */
            $out .= '<h3 class="rpr-instruction-group-title">' . esc_html($instruction['grouptitle']) . '</h3>';
        }

        /**
         * Start the list for this ingredient group
         */
        $out .= '<ol class="rpr-instruction-list">';

        /**
         * Return the rendered output
         */
        return $out;
    }

}

if (!function_exists('rpr_render_instruction_block')) {

    /**
     * Render an instruction block
     *
     * @since 0.8.0
     * @param type $instruction
     * @return string
     */
    function rpr_render_instruction_block($instruction) {
        /**
         *  Create an empty output string
         */
        $out = '';

        /**
         * Start the line
         */
        $out .= '<li class="rpr-instruction">';

        /**
         * Determine the class for the instruction text depending on image options
         */
        if (isset($instruction['image']) && $instruction['image'] != '') {
            $instr_class = " has_thumbnail";
            $instr_class .= ' ' . esc_attr(AdminPageFramework::getOption('rpr_options', array('layout_general', 'images_instr_pos'), 'right'));
        } else {
            $instr_class = "";
        }

        /**
         * Render the instruction text
         */
        $out .= '<span class="rpr-recipe-instruction-text' . $instr_class . '">' . esc_html($instruction['description']) . '</span>';

        /**
         * Render the instruction step image
         */
        if (isset($instruction['image']) && $instruction['image'] != '') {
            /**
             * Get the image data
             */
            if (AdminPageFramework::getOption('rpr_options', array('layout_general', 'images_instr_pos'), 'right') === 'right') {
                $img = wp_get_attachment_image_src($instruction['image'], 'thumbnail');
            } else {
                $img = wp_get_attachment_image_src($instruction['image'], 'large');
            }

            /**
             * Get link target for clickable images:
             */
            if (AdminPageFramework::getOption('rpr_options', array('layout_general', 'images_link'), true) && is_array($img) && $img[0] != '') {
                $img_full = wp_get_attachment_image_src($instruction['image'], 'full');
                $out .= '<a class="rpr_img_link" href="' . esc_url($img_full[0]) . '" rel="lightbox" title="' . esc_html(substr($instruction['description'], 150)) . '">';
            }

            /**
             * Render the image
             */
            $out .= '<img class="';
            $out .= esc_attr(AdminPageFramework::getOption('rpr_options', array('layout_general', 'images_instr_pos'), 'right'));
            $out .= '" src="' . esc_url($img[0]) . '" width="' . esc_attr($img[1]) . '" height="' . esc_attr($img[2]) . '" />';

            /**
             * Close the link for clickable images
             */
            if (AdminPageFramework::getOption('rpr_options', array('layout_general', 'images_link'), true) && is_array($img) && $img[0] != '') {
                $out .= '</a>';
            }
        }

        /**
         * End the line
         */
        $out .= '</li>';

        /**
         * Return the rendered output
         */
        return $out;
    }

}
if (!function_exists('the_rpr_recipe_instructions')) {

    function the_rpr_recipe_instructions() {
        echo get_the_rpr_recipe_instructions();
    }

}