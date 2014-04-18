jQuery(document).ready(function() {
    jQuery('.rpr-edit-tag').on('click', function() {
        var tag = jQuery(this).data('tag');

        var singular = jQuery(this).parents('tr').find('.singular-name').text();
        var name = jQuery(this).parents('tr').find('.name').text();
        var slug = jQuery(this).parents('tr').find('.slug').text();

        jQuery('input#rpr_edit_tag_name').val(tag);
        jQuery('input#rpr_custom_taxonomy_singular_name').val(singular);
        jQuery('input#rpr_custom_taxonomy_name').val(name);
        jQuery('input#rpr_custom_taxonomy_slug').val(slug);

        jQuery('#rpr_editing_tag').text(tag);

        jQuery('.rpr_adding').hide();
        jQuery('.rpr_editing').show();
    });

    jQuery('#rpr_cancel_editing').on('click', function() {
        jQuery('input#rpr_edit_tag_name').val('');
        jQuery('input#rpr_custom_taxonomy_singular_name').val('');
        jQuery('input#rpr_custom_taxonomy_name').val('');
        jQuery('input#rpr_custom_taxonomy_slug').val('');

        jQuery('.rpr_adding').show();
        jQuery('.rpr_editing').hide();
    });
    
    jQuery('.rpr-delete-tag').on('click', function() {
        var tag = jQuery(this).data('tag');

        jQuery('input#rpr_delete_taxonomy_name').val(tag);
        
        jQuery('form#rpr_delete_taxonomy').submit();
    });

});