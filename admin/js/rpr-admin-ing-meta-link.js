/**
 * Ingredients MetaBox functions
 * According to the following recipe:
 * @link http://wordpress.stackexchange.com/questions/127088/how-can-i-use-the-built-in-wordpress-browse-link-functionality
 * 
 * @since 0.8.0
 */

var link_btn_ing = (function($){
    'use strict';
    var _link_sideload = true; //used to track whether or not the link dialogue actually existed on this page, ie was wp_editor invoked.

/* PRIVATE METHODS
-------------------------------------------------------------- */
//add event listeners

function _init() {
    $('body').on('click', '.rpr-ing-add-link', function(event) {
        var button = $(this);
        window.console.log( button);
        var link_val_container=button.siblings('.rpr_recipe_ingredients_link');
        //var link_val_container = $('#recipe_ingredient_0');
        
        _addLinkListeners( link_val_container );
        _link_sideload = false;

        // load existing data for editing
        wpLink.setDefaultValues = function () {
            $('#wp-link-url').val(link_val_container.val());
        };

        if ( typeof wpActiveEditor != 'undefined') {
            wpLink.open();
            wpLink.textarea = $(link_val_container);
        } else {
            window.wpActiveEditor = true;
            _link_sideload = true;
            wpLink.open();
            wpLink.textarea = $(link_val_container);
        }
        return false;
    });
    
    $('body').on('click', '.rpr-ing-del-link', function(event) {
        var delbutton = $(this);
        var addbutton = delbutton.siblings('.rpr-ing-add-link');
        var link_val_container=delbutton.siblings('.rpr_recipe_ingredients_link');
        
        link_val_container.val('');
        addbutton.removeClass('has-link');
        delbutton.addClass('rpr-hidden');
    });

}

/* LINK EDITOR EVENT HACKS
-------------------------------------------------------------- */
function _addLinkListeners( link_val_container ) {
    $('body').on('click', '#wp-link-submit', function(event) {
        var linkAtts = wpLink.getAttrs();
        //var link_val_container = $('#recipe_ingredient_0');
        link_val_container.val(linkAtts.href);
        
        // change icon color and make delete link button visible
        var addbutton = link_val_container.siblings('.rpr-ing-add-link');
        var delbutton = link_val_container.siblings('.rpr-ing-del-link');
        delbutton.removeClass("rpr-hidden");
        addbutton.addClass("has-link");
        /**
         * Prevent the link from being added to an editor field
         * @link http://stackoverflow.com/questions/33156478/how-to-prevent-wordpress-built-in-browse-link-entering-the-data-in-wp-editor
         */ 
        var $frame = $('#content_ifr'),
        $added_links = $frame.contents().find("a[data-mce-href]");

        $added_links.each(function(){
            if ($(this).attr('href') === linkAtts.href) {
                $(this).remove();
            }
        });
    
        _removeLinkListeners();
        return false;
    });

    $('body').on('click', '#wp-link-cancel', function(event) {
        _removeLinkListeners();
        return false;
    });
}

function _removeLinkListeners() {
    if(_link_sideload){
        if ( typeof wpActiveEditor != 'undefined') {
            wpActiveEditor = undefined;
        }
    }

    wpLink.close();
    wpLink.textarea = $('html');//focus on document

    $('body').off('click', '#wp-link-submit');
    $('body').off('click', '#wp-link-cancel');
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
 link_btn_ing.init();
});