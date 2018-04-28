/**
 * The jquery class handling the shortcode insertion for recipe listings
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */
var rprListingsSc;

( function( $ ) {
	var editor,
	    inputs = {},
	    isTouch = ( 'ontouchend' in document );
	
rprListingsSc = {
	timeToTriggerRiver: 150,
	minRiverAJAXDuration: 200,
	riverBottomThreshold: 5,
	keySensitivity: 100,
	lastSearch: '',
	textarea: '',

	init: function() {
		inputs.wrap = $('#rpr-modal-wrap-scl');
		inputs.dialog = $( '#rpr-modal-form-scl' );
		inputs.backdrop = $( '#rpr-modal-backdrop-scl' );
		inputs.submit = $( '#rpr-modal-submit-scl' );
		inputs.close = $( '#rpr-modal-close-scl' );
		// URL
		inputs.id = $( '#recipe-id-field' );
		inputs.nonce = $( '#rpr_ajax_nonce' );
		// Secondary options
		inputs.title = $( '#recipe-title-field' );
		// Advanced Options
		inputs.taxonomy =$( '#recipe-taxonomy' );
		
		// Bind event handlers
		inputs.dialog.keydown( rprListingsSc.keydown );
		inputs.dialog.keyup( rprListingsSc.keyup );
		inputs.submit.click( function( event ) {
			event.preventDefault();
			rprListingsSc.update();
		});
		inputs.close.add( inputs.backdrop ).add( '#rpr-modal-cancel-scl a' ).click( function( event ) {
			event.preventDefault();
			rprListingsSc.close();
		});
		$('body').on('click', '#recipe-taxonomy',  function( event ){
		    event.preventDefault();
		    $('input:radio[name=rpr-modal-scl-mode]')[0].checked = true;
		});
		
		/* Button to open the modal dialog */
		$('body').on('click', '#rpr-add-listings-button', function(event) {
		    editor_id = jQuery('#rpr-add-listings-button').attr( "data_editor" );
		    window.rprListingsSc.open( editor_id );
		});
	},

	open: function( editorId ) {
		var ed;
		

		rprListingsSc.range = null;

		if ( editorId ) {
			window.wpActiveEditor = editorId;
		}

		if ( ! window.wpActiveEditor ) {
			return;
		}

		this.textarea = $( '#' + window.wpActiveEditor ).get( 0 );

		if ( typeof tinymce !== 'undefined' ) {
			ed = tinymce.get( wpActiveEditor );

			if ( ed && ! ed.isHidden() ) {
				editor = ed;
			} else {
				editor = null;
			}

			if ( editor && tinymce.isIE ) {
				editor.windowManager.bookmark = editor.selection.getBookmark();
			}
		}

		if ( ! rprListingsSc.isMCE() && document.selection ) {
			this.textarea.focus();
			this.range = document.selection.createRange();
		}
		
		inputs.wrap.show();
		inputs.backdrop.show();

		rprListingsSc.refresh();
		$( document ).trigger( 'rprListingsSc-open', inputs.wrap );
	},

	isMCE: function() {
		return editor && ! editor.isHidden();
	},

	refresh: function() {
//		// Refresh rivers (clear links, check visibility)
//		rivers.search.refresh();
//		rivers.recent.refresh();

		if ( rprListingsSc.isMCE() ) {
			rprListingsSc.mceRefresh();
		} else {
			rprListingsSc.setDefaultValues();
		}

		if ( isTouch ) {
			// Close the onscreen keyboard
			inputs.id.focus().blur();
		} else {
			// Focus the URL field and highlight its contents.
			// If this is moved above the selection changes,
			// IE will show a flashing cursor over the dialog.
			inputs.id.focus()[0].select();
		}

	},

	mceRefresh: function() {
		var e;

		// If link exists, select proper values.
		if ( e = editor.dom.getParent( editor.selection.getNode(), 'A' ) ) {
			// Set URL and description.
			inputs.id.val( editor.dom.getAttrib( e, 'href' ) );
			inputs.title.val( editor.dom.getAttrib( e, 'title' ) );
			// Set open in new tab.
			inputs.openInNewTab.prop( 'checked', ( '_blank' === editor.dom.getAttrib( e, 'target' ) ) );
			// Update save prompt.
			inputs.submit.val( rprListingsScL10n.update );
		
		// If there's no link, set the default values.
		} else {
			rprListingsSc.setDefaultValues();
		}
	},

	close: function() {
		if ( ! rprListingsSc.isMCE() ) {
			rprListingsSc.textarea.focus();

			if ( rprListingsSc.range ) {
				rprListingsSc.range.moveToBookmark( rprListingsSc.range.getBookmark() );
				rprListingsSc.range.select();
			}
		} else {
			editor.focus();
		}

		inputs.backdrop.hide();
		inputs.wrap.hide();
		$( document ).trigger( 'rprListingsSc-close', inputs.wrap );
	},

	update: function() {
	    if ( rprListingsSc.isMCE() )
		rprListingsSc.mceUpdate();
	    else
		rprListingsSc.htmlUpdate();
	},

	//Build the shortcode here!
	htmlUpdate: function() {
	    var attrs, html, begin, end, cursor, title, selection,
		textarea = rprListingsSc.textarea;

	    if ( ! textarea )
		return;
		
	    var out="[";
		
	    sel = $( "input[name='rpr-modal-scl-mode']:checked" );
	    switch(sel.val()) {
		case 'rpr-tax-list':
		    out+="rpr-tax-list ";
		    out+="tax=\""+$( "#rpr-modal-form-scl select option:selected" ).val()+"\"";
		    break;
		case 'rpr-recipe-index':
		    out+= "rpr-recipe-index";
		    break;
	        default:
		    alert(sel.val());
		    alert('error');
		    return;
	    } 

	    out+="]\n";

		// Insert Shortcode
	    if ( document.selection && rprRecipeSc.range ) {
		// IE
		// Note: If no text is selected, IE will not place the cursor
		//       inside the closing tag.
		textarea.focus();
		rprRecipeSc.range.text = out;
	    } else if ( typeof textarea.selectionStart !== 'undefined' ) {
		// W3C
		begin       = textarea.selectionStart;
		end         = textarea.selectionEnd;
		selection   = textarea.value.substring( begin, end );
		cursor      = begin + out.length;
		textarea.value = textarea.value.substring( 0, begin ) + out +
				textarea.value.substring( end, textarea.value.length );
		// Update cursor position
		textarea.selectionStart = textarea.selectionEnd = cursor;
	    }

	    rprListingsSc.close();
	    textarea.focus();
	},

	mceUpdate: function() {
		var link;
		var out="[";
		sel = $( "input[name='rpr-modal-scl-mode']:checked" );
		switch(sel.val()) {
			case 'rpr-tax-list':
				out+="rpr-tax-list ";
				out+="tax=\""+$( "#rpr-modal-form-scl select option:selected" ).val()+"\"";
				break;
			case 'rpr-recipe-index':
				out+= "rpr-recipe-index";
				break;
			default:
				alert(sel.val());
				alert('error');
				return;
		} 
		out+="]<br/>";

		rprListingsSc.close();
		editor.focus();

		tinyMCE.activeEditor.execCommand('mceReplaceContent', false, out);
		
	},

	updateFields: function( e, li ) {
		inputs.id.val( li.children( '.item-id' ).val() );
		inputs.title.val( li.hasClass( 'no-title' ) ? '' : li.children( '.item-title' ).text() );
	},

	setDefaultValues: function() {
		// Set id to default
		inputs.id.val( '' );
		// Set description to default.
		inputs.title.val( '' );

		// Update save prompt.
		inputs.submit.val( rprListingsScL10n.save );
	},

	keydown: function( event ) {
		var fn, id,
			key = $.ui.keyCode;

		if ( key.ESCAPE === event.keyCode ) {
			rprListingsSc.close();
			event.stopImmediatePropagation();
		} else if ( key.TAB === event.keyCode ) {
			id = event.target.id;

			// wp-link-submit must always be the last focusable element in the dialog.
			// following focusable elements will be skipped on keyboard navigation.
			if ( id === 'wp-link-submit' && ! event.shiftKey ) {
				inputs.close.focus();
				event.preventDefault();
			} else if ( id === 'wp-link-close' && event.shiftKey ) {
				inputs.submit.focus();
				event.preventDefault();
			}
		}

		if ( event.keyCode !== key.UP && event.keyCode !== key.DOWN ) {
			return;
		}

		if ( document.activeElement &&
			( document.activeElement.id === 'link-title-field' || document.activeElement.id === 'url-field' ) ) {
			return;
		}

		fn = event.keyCode === key.UP ? 'prev' : 'next';
		clearInterval( rprListingsSc.keyInterval );
		rprListingsSc[ fn ]();
		rprListingsSc.keyInterval = setInterval( rprListingsSc[ fn ], rprListingsSc.keySensitivity );
		event.preventDefault();
	},

	keyup: function( event ) {
		var key = $.ui.keyCode;

		if ( event.which === key.UP || event.which === key.DOWN ) {
			clearInterval( rprListingsSc.keyInterval );
			event.preventDefault();
		}
	},

	delayedCallback: function( func, delay ) {
		var timeoutTriggered, funcTriggered, funcArgs, funcContext;

		if ( ! delay )
			return func;

		setTimeout( function() {
			if ( funcTriggered )
				return func.apply( funcContext, funcArgs );
			// Otherwise, wait.
			timeoutTriggered = true;
		}, delay );

		return function() {
			if ( timeoutTriggered )
				return func.apply( this, arguments );
			// Otherwise, wait.
			funcArgs = arguments;
			funcContext = this;
			funcTriggered = true;
		};
	}

};
	$( document ).ready( rprListingsSc.init );
})( jQuery );

