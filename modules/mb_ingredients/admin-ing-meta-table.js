/**
 * Ingredients MetaBox functions
 * According to the following recipe:
 * @link http://wordpress.stackexchange.com/questions/127088/how-can-i-use-the-built-in-wordpress-browse-link-functionality
 * 
 * @since 0.8.0
 */

var ing_table = (function($){
    'use strict';

/* PRIVATE METHODS
-------------------------------------------------------------- */
//add event listeners

    function _init() {
        // Add ingredient button
        $('#rpr-ing-add-row-ing').on('click', function(e){
            e.preventDefault();
            _add_ingredient_row();
            _update_ingredient_index();
        });
        // Add ingredient row on tab
        $('#recipe-ingredients .rpr-ing-note')
            .unbind('keydown')
            .last()
            .bind('keydown', function(e) {
                var keyCode = e.keyCode || e.which;

                if (keyCode === 9) {
                    e.preventDefault();
                    _add_ingredient_row();
                    _update_ingredient_index();
                }
            });
         // Add row button
        $('tbody .rpr-ing-add-row').on('click', function(e){
           var addbutton = $(this);
           e.preventDefault();
           if( addbutton.parent().parent().attr("class") == "rpr-ing-row" ){
               _add_ingredient_row( addbutton );
           } else {
               _add_ingredient_heading( addbutton );
           }
           _update_ingredient_index();
        });
        // Delete ingredient button
        $('.rpr-ing-remove-row').on('click', function(e){
            var delbutton = $(this);
            e.preventDefault();
            _delete_ingredient_row(delbutton);
            _update_ingredient_index();
            //_update_ingredient_delbuttons();
        });
        // Add ingredient heading
        $('#rpr-ing-add-row-grp').on('click', function(e){
            e.preventDefault();
            _add_ingredient_heading();
            _update_ingredient_index();
            //_update_ingredient_delbuttons();
        });
        $('#recipe_ingredients_meta_box tbody').sortable({
            opacity: 0.6,
            revert: true,
            cursor: 'move',
            handle: '.sort-handle',
            update: function() {
                //addRecipeIngredientOnTab();
                //calculateIngredientGroups();
                _update_ingredient_index();
                //_update_ingredient_delbuttons();
            }
        });
    }
    
    /* 
     * Add a new empty row to the ingredient table
     */
    function _add_ingredient_row( addbutton)
    {
        var last_row = $('#recipe-ingredients tbody tr:last');
        var last_ingredient = $('#recipe-ingredients tr.rpr-ing-row:last');
        
        if( addbutton ){
            last_row = addbutton.parent().parent();
            last_ingredient = last_row;
        }
        
        var clone_ingredient = last_ingredient.clone(true);

        clone_ingredient
            .insertAfter(last_row)
            .find('input').val('');

        last_ingredient.find('input').attr('placeholder','');
        clone_ingredient.find('.rpr-ing-del').show();

        clone_ingredient.find('.rpr-ing-amount input').focus();
    }
    
    function _delete_ingredient_row(delbutton){
        delbutton.parents( "tr" ).remove();
    }
    
    /** 
     * Add ingredient heading
     */
    function _add_ingredient_heading( addbutton ){
        var clone_from = $( '#recipe-ingredients tr.ingredient-group-stub' );
        var last_row = $( '#recipe-ingredients tbody tr:last' );
        if( addbutton ){
            last_row = addbutton.parent().parent();
        }
        var clone_group = clone_from.clone(true);

        clone_group
            .insertAfter(last_row)
            .removeClass('ingredient-group-stub')
            .removeClass('rpr-hidden')
            .addClass('ingredient-group')
            .find('input').val('').focus();


    }
    
    /**
     * Recalculate all index numbers on the ingredient list
     * @returns {none}
     */
    function _update_ingredient_index(){
        var rows = $( '#recipe-ingredients tbody').find( 'tr' ).not( '.rpr-hidden' );

        $( rows ).each( function( rowIndex ) {
            $( this ).find( 'input, select, textarea' ).each( function() {
        	var name = $( this ).attr( 'name' );
        	name = name.replace( /\[(\d+)\]/, '[' + (rowIndex +1 ) + ']');
		var id = $( this ).attr( 'id' );
		id = id.replace( /\_(\d+)/, '_' + (rowIndex + 1) );
        	$( this ).attr( 'name', name );
		$( this ).attr( 'id', id );
                if( $(this).attr('onfocus') ){
                    var onf = $(this).attr('onfocus');
                    onf = onf.replace( /\_(\d+)/, '_' + (rowIndex + 1) );
                    $(this).attr('onfocus', onf);
                }
            } );
            

            $( this ).find( '.rpr-ing-sort input.ingredients_sort' ).attr( 'value', rowIndex );
            $( this ).find( '.rpr-ing-del').show();
        } );
        //$( '#recipe-ingredients tbody').find( 'tr.rpr-ing-row:first .rpr-ing-del').hide();

    }
    
    function _update_ingredient_delbuttons(){
        $( '#recipe-ingredients tbody').find( 'tr.rpr-ing-row:first .rpr-ing-del').hide();
    }


/* PUBLIC ACCESSOR METHODS
-------------------------------------------------------------- */
return {
    init:       _init,
};

})(jQuery);

// Initialise
jQuery(document).ready(function($){
    'use strict';
    ing_table.init();
});
