<?php

if (!function_exists('get_the_rpr_recipe_ingredients_headline')) {

    /**
     * Renders the headline for ingredient list.
     * Icons are optional, headline level depends on embedded or standalone
     * recipe
     *
     * @param boolean $icons
     * @return string
     */
    function get_the_rpr_recipe_ingredients_headline($icons) {
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
            $out .= '<i class="' . esc_attr(AdminPageFramework::getOption('rpr_options', array('tax_builtin', 'ingredients', 'icon_class'), 'fa fa-shopping-cart')) . '"></i>&nbsp;';
        }

        $out .= __('Ingredients', 'recipepress-reloaded');

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

if (!function_exists('the_rpr_recipe_ingredients_headline')) {

    /**
     * Outputs the rendered headline
     *
     * @since 0.8.0
     * @param boolean $icons
     */
    function the_rpr_recipe_ingredients_headline($icons = false) {
        echo get_the_rpr_recipe_ingredients_headline($icons);
    }

}

if (!function_exists('get_the_rpr_recipe_ingredients')) {

    /**
     * Renders the ingredient list
     *
     * @since 0.8.0
     * @param boolean $icons
     * @return string
     */
    function get_the_rpr_recipe_ingredients($icons = false) {
        /**
         *  Get the recipe id
         */
        $recipe_id = get_recipe_id();
        $recipe = get_post_custom($recipe_id);

        /**
         *  Create an empty output string
         */
        $out = '';

        /**
         *  Get the ingredients:
         */
        $ingredients = unserialize($recipe['rpr_recipe_ingredients'][0]);

        if (count($ingredients) > 0) {
            /**
             * Loop over all the ingredients
             */
            $i = 0;
            if (is_array($ingredients)) {
                foreach ($ingredients as $ingredient) {
                    /**
                     * Check if the ingredient is a grouptitle
                     */
                    if (isset($ingredient['grouptitle'])) {

                        /**
                         * Render the grouptitle
                         */
                        $out .= rpr_render_ingredient_grouptitle($ingredient);
                    } else {
                        /**
                         * Start the list on the first item
                         */
                        if ($i == 0) {
                            //if( isset( $ingredient['sort'] ) && $ingredient['sort'] == 1 ){
                            $out .= '<ul class="rpr-ingredient-list" >';
                        }
                        /**
                         * Render the ingredient line
                         */
                        $out .= rpr_render_ingredient_line($ingredient);
                        /**
                         * Close the list on the last item
                         */
                        if (isset($ingredient['sort']) && $ingredient['sort'] == count($ingredients)) {
                            $out .= '</ul>';
                        }
                    }
                    $i++;
                }
            }
            /**
             * Close the list on the last item
             */
            $out .= '</ul>';
        } else {
            /**
             * Issue a warning, if there are no ingredients for the recipe
             */
            $out .= '<p class="warning">' . __('No ingredients could be found for this recipe.', 'recipepress-reloaded') . '</p>';
        }


        /**
         * Return the rendered ingredient list
         */
        return $out;
    }

}
if (!function_exists('rpr_render_ingredient_grouptitle')) {

    /**
     * Renders the ingredient group headline
     *
     * @since 0.8.0
     * @param array $ingredient
     * @return string
     */
    function rpr_render_ingredient_grouptitle($ingredient) {
        /**
         *  Create an empty output string
         */
        $out = '';

        if ($ingredient['sort'] === 0) {
            /**
             * Do not close the ingredient list of the previous group if this is
             * the first group
             */
        } else {
            /**
             * Close close the ingredient list of the previous group
             */
            $out .= '</ul>';
        }

        /**
         * Create the headline for the ingredient group
         */
        if (recipe_is_embedded()) {
            /**
             * Fourth level headline for embedded recipe
             */
            $out .= '<h4 class="rpr-ingredient-group-title">' . esc_html($ingredient['grouptitle']) . '</h4>';
        } else {
            /**
             * Third level headline for standalone recipes
             */
            $out .= '<h3 class="rpr-ingredient-group-title">' . esc_html($ingredient['grouptitle']) . '</h3>';
        }

        /**
         * Start the list for this ingredient group
         */
        $out .= '<ul class="rpr-ingredient-list">';

        /**
         * Return the rendered output
         */
        return $out;
    }

}

if (!function_exists('rpr_render_ingredient_line')) {

    /**
     * Render the actual ingredient line
     *
     * @since 0.8.0
     * @param array $ingredient
     * @return string
     */
    function rpr_render_ingredient_line($ingredient) {
        /**
         * Get the term object for the ingredient
         */
        if (isset($ingredient['ingredient_id']) && get_term_by('id', $ingredient['ingredient_id'], 'rpr_ingredient')) {
            $term = get_term_by('id', $ingredient['ingredient_id'], 'rpr_ingredient');
        } else {
            $term = get_term_by('name', $ingredient['ingredient'], 'rpr_ingredient');
        }

        /**
         *  Create an empty output string
         */
        $out = '';

        /**
         * Start the line
         */
        $out .= '<li class="rpr-ingredient">';

        /**
         * Render amount
         */
        $out .= '<span class="recipe-ingredient-quantity">' . esc_html($ingredient['amount']) . '</span> ';

        /**
         * Render the unit
         */
        $out .= '<span class="recipe-ingredient-unit">' . esc_html($ingredient['unit']) . '</span> ';

        /**
         * Render the ingredient link according to the settings
         */
        if (AdminPageFramework::getOption('rpr_options', array('tax_builtin', 'ingredients', 'link_target'), 2) == 0) {
            /**
             * Set no link
             */
            $closing_tag = '';
        } elseif (AdminPageFramework::getOption('rpr_options', array('tax_builtin', 'ingredients', 'link_target'), 2) == 1) {
            /**
             * Set link to archive
             */
            $out .= '<a href="' . get_term_link($term->slug, 'rpr_ingredient') . '">';
            $closing_tag = '</a>';
        } elseif (AdminPageFramework::getOption('rpr_options', array('tax_builtin', 'ingredients', 'link_target'), 2) == 2) {
            /**
             * Set custom link if available, link to archive if not
             */
            if (isset($ingredient['link']) && $ingredient['link'] != '') {
                $out .= '<a href="' . esc_url($ingredient['link']) . '" target="_blank">';
                $closing_tag = '</a>';
            } else {
                $out .= '<a href="' . get_term_link($term->slug, 'rpr_ingredient') . '">';
            }

            $closing_tag = '</a>';
        } else {
            /**
             * Set custom link if available, no link if not
             */
            if (isset($ingredient['link']) && $ingredient['link'] != '') {
                $out .= '<a href="' . esc_url($ingredient['link']) . '" target="_blank" >';
                $closing_tag = '</a>';
            } else {
                $closing_tag = '';
            }
        }

        /**
         * Render the ingredient name
         */
        if (isset($ingredient['amount']) && $ingredient['amount'] > 1 && $ingredient['unit'] === '' && AdminPageFramework::getOption('rpr_options', array('tax_builtin', 'ingredients', 'auto_plural'), 0)) {
            /**
             * Use plural if amount > 1
             */
            if (get_term_meta($term->term_id, 'plural', true) != '') {
                $out .= '<span name="rpr-ingredient-name" >' . esc_html(get_term_meta($term->term_id, 'plural', true)) . '</span>';
            } else {
                $out .= '<span name="rpr-ingredient-name" >' . $term->name . __('s', 'recipepress-reloaded') . '</span>';
            }
        } else {
            /**
             * Use singular
             */
            $out .= '<span name="rpr-ingredient-name" >' . $term->name . '</span>';
        }

        $out .= $closing_tag;

        /**
         * Render the ingredient note
         */
        if (isset($ingredient['notes']) && $ingredient['notes'] != '') {
            $out .= '<span class="rpr-ingredient-note">';
            /**
             * Add the correct separator as set in the options
             */
            if (AdminPageFramework::getOption('rpr_options', array('tax_builtin', 'ingredients', 'comment_sep'), 0) == 0) {
                /**
                 * No separator
                 */
                $out .= ' ';
            } elseif (AdminPageFramework::getOption('rpr_options', array('tax_builtin', 'ingredients', 'comment_sep'), 0) == 1) {
                /**
                 * Brackets
                 */
                $out .= __(' (', 'reciperess-reloaded');
                $closing_tag = __(')', 'recipepress-reloaded');
            } else {
                /**
                 * comma
                 */
                $out .= __(', ', 'recipepress-reloaded');
                $closing_tag = '';
            }
            $out .= esc_html($ingredient['notes']) . $closing_tag . '</span>';
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
if (!function_exists('the_rpr_recipe_ingredients')) {

    /**
     * Outputs the ingredient list rendered above
     *
     * @since 0.8.0
     * @param boolean $icons
     */
    function the_rpr_recipe_ingredients($icons = false) {
        echo get_the_rpr_recipe_ingredients($icons);
    }

}
