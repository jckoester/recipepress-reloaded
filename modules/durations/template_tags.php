<?php

if (!function_exists('get_the_rpr_recipe_durations')) {

    /**
     * Renders the cook, prep and total time
     * 
     * @since 0.8.0
     * 
     * @param boolean $icons
     * @return string
     */
    function get_the_rpr_recipe_durations($icons = false) {
        /**
         *  Get the recipe id
         */
        $recipe_id = get_recipe_id();
        $recipe = get_post_custom($recipe_id);



        /**
         * Fix empty times 
         */
        if (!isset($recipe['rpr_recipe_prep_time'][0])) {
            $recipe['rpr_recipe_prep_time'][0] = 0;
        }
        if (!isset($recipe['rpr_recipe_cook_time'][0])) {
            $recipe['rpr_recipe_cook_time'][0] = 0;
        }
        if (!isset($recipe['rpr_recipe_passive_time'][0])) {
            $recipe['rpr_recipe_passive_time'][0] = 0;
        }
        /**
         * Return if no times are saved
         */
        if ($recipe['rpr_recipe_prep_time'][0] + $recipe['rpr_recipe_cook_time'][0] + $recipe['rpr_recipe_passive_time'][0] <= 0) {
            return;
        }

        /**
         *  Create an empty output string
         */
        $out = '';

        /**
         * Add the times in correct structured data format
         */
        $out .= '<div class="rpr_times">';
        $out .= '<dl>';

        if ($recipe['rpr_recipe_prep_time'][0] > 0) {
            $out .= '<dt>';
            if ($icons) {
                $out .= '<i class="fa fa-cog" title="' . __('Preparation: ', 'recipepress-reloaded') . '"></i>&nbsp;';
            } else {
                $out .= __('Preparation: ', 'recipepress-reloaded');
            }
            $out .= '</dt>';
            $out .= '<dd>' . rpr_format_time_hum(esc_attr($recipe['rpr_recipe_prep_time'][0])) . '</dd>';
        }
        if ($recipe['rpr_recipe_cook_time'][0] > 0) {
            $out .= '<dt>';
            if ($icons) {
                $out .= '<i class="fa fa-fire" title="' . __('Cooking: ', 'recipepress-reloaded') . '"></i>&nbsp;';
            } else {
                $out .= __('Cooking: ', 'recipepress-reloaded');
            }
            $out .= '</dt>';
            $out .= '<dd>' . rpr_format_time_hum(esc_attr($recipe['rpr_recipe_cook_time'][0])) . '</dd>';
        }
        $out .= '<dt>';
        if ($icons) {
            $out .= '<i class="fa fa-clock-o" title="' . __('Ready in: ', 'recipepress-reloaded') . '"></i>&nbsp;';
        } else {
            $out .= __('Ready in: ', 'recipepress-reloaded');
        }
        $out .= '</dt>';
        $out .= '<dd>' . rpr_format_time_hum(esc_attr($recipe['rpr_recipe_prep_time'][0]) + esc_attr($recipe['rpr_recipe_cook_time'][0]) + esc_attr($recipe['rpr_recipe_passive_time'][0])) . '</dd>';

        $out .= '</dl>';
        $out .= '</div>';
        /**
         * Return the rendered times data
         */
        return $out;
    }

}
if (!function_exists('the_rpr_recipe_durations')) {

    /**
     * Outputs the rendered times from above
     * 
     * @since 0.8.0
     * @param boolean $icons
     */
    function the_rpr_recipe_durations($icons = false) {
        echo get_the_rpr_recipe_durations($icons);
    }

}



// LEGACY template tags
if (!function_exists('get_the_rpr_recipe_times')) {

    /**
     * Outputs the rendered data
     * @since 0.9.0
     */
    function get_the_rpr_recipe_times() {
        return get_the_rpr_recipe_durations();
    }

}

if (!function_exists('the_rpr_recipe_times')) {

    /**
     * Outputs the rendered data
     * @since 0.9.0
     */
    function the_rpr_recipe_times() {
        echo get_the_rpr_recipe_durations();
    }

}

if (!function_exists('rpr_format_time_hum')) {

    /**
     * Helper function to format the times in a human readable way:
     * Formats a number of minutes to a human readable time string
     * 
     * @param int $min
     * @return string
     */
    function rpr_format_time_hum($min) {
        $hours = floor($min / 60);
        $minutes = $min % 60;
        if ($hours > 0 && $minutes > 0) {
            return sprintf('%1$d h %2$d min', $hours, $minutes);
        } elseif ($hours > 0 && $minutes === 0) {
            return sprintf('%d h', $hours);
        } else {
            return sprintf('%d min', $minutes);
        }
    }

}