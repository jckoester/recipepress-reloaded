var parentRecipe = '';

jQuery(document).ready(function() {

    jQuery(document).on('click', '.print-recipe', function(e) {
        e.preventDefault();

        var recipe = jQuery(this).parents('.rpr-container').clone(true);

        recipe.find('img').remove();

        var servings = recipe.find('input.adjust-recipe-servings').val();

        if(servings === undefined) {
            servings = recipe.find('input.advanced-adjust-recipe-servings').val();
        }

        if(servings !== undefined && servings != '') {
            recipe.find('.recipe-information-servings')
                .replaceWith(servings);
        }

        parentRecipe = recipe.html();

        window.open(rpr_pluginUrl + '/templates/'+ rpr_template +'/recipe_print.php');
    });
});