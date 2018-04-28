/**
 * The jquery class handling the shortcode insertion for recipes
 *
 * @since      0.8.0
 * @package    recipepress-reloaded
 * @subpackage recipepress-reloaded/admin
 * @author     Jan Köster <rpr@cbjck.de>
 */

var rprRecipeSc;

( function( $ ) {
	var editor, searchTimer, River, Query,
		inputs = {},
		rivers = {},
		isTouch = ( 'ontouchend' in document );
	
rprRecipeSc = {
	timeToTriggerRiver: 150,
	minRiverAJAXDuration: 200,
	riverBottomThreshold: 5,
	keySensitivity: 100,
	lastSearch: '',
	textarea: '',

	init: function() {
		inputs.wrap = $('#rpr-modal-wrap-scr');
		inputs.dialog = $( '#rpr-modal-form-scr' );
		inputs.backdrop = $( '#rpr-modal-backdrop-scr' );
		inputs.submit = $( '#rpr-modal-submit-scr' );
		inputs.close = $( '#rpr-modal-close-scr' );
		// URL
		inputs.id = $( '#recipe-id-field' );
		inputs.nonce = $( '#rpr_ajax_nonce' );
		// Secondary options
		inputs.title = $( '#recipe-title-field' );
		// Advanced Options
		inputs.optlink = $( '#rpr-modal-scr-options-link' );
		inputs.opticon = $( '#rpr-modal-scr-options-link i' );
		inputs.optpanel = $( '#rpr-modal-scr-options-panel' );
		inputs.taxonomy =$( '#recipe-taxonomy' );
		inputs.openInNewTab = $( '#link-target-checkbox' );
		inputs.search = $( '#rpr-search-field' );
		inputs.excerpt = $( '#rpr-embed-excerpt' );
		inputs.excerpt.prop("checked", false);
		inputs.nodesc = $( '#rpr-embed-nodesc' );
		inputs.nodesc.prop("checked", false);
		
		// Build Rivers
		rivers.search = new River( $( '#rpr-search-results' ) );
		rivers.recent = new River( $( '#rpr-most-recent-results' ) );
		rivers.elements = inputs.dialog.find( '.query-results' );

		// Get search notice text
		inputs.queryNotice = $( '#query-notice-message' );
		inputs.queryNoticeTextDefault = inputs.queryNotice.find( '.query-notice-default' );
		inputs.queryNoticeTextHint = inputs.queryNotice.find( '.query-notice-hint' );

		// Bind event handlers
		inputs.dialog.keydown( rprRecipeSc.keydown );
		inputs.dialog.keyup( rprRecipeSc.keyup );
		inputs.submit.click( function( event ) {
			event.preventDefault();
			rprRecipeSc.update();
		});
		inputs.close.add( inputs.backdrop ).add( '#rpr-modal-cancel-scr a' ).click( function( event ) {
			event.preventDefault();
			rprRecipeSc.close();
		});
		
		//Action whe a recipe is selected from the list...
		rivers.elements.on( 'river-select', rprRecipeSc.updateFields );

		// Display 'hint' message when search field or 'query-results' box are focused
		inputs.search.on( 'focus.rprRecipeSc', function() {
			inputs.queryNoticeTextDefault.hide();
			inputs.queryNoticeTextHint.removeClass( 'screen-reader-text' ).show();
		} ).on( 'blur.rprRecipeSc', function() {
			inputs.queryNoticeTextDefault.show();
			inputs.queryNoticeTextHint.addClass( 'screen-reader-text' ).hide();
		} );

		inputs.search.keyup( function() {
			var self = this;

			window.clearTimeout( searchTimer );
			searchTimer = window.setTimeout( function() {
				rprRecipeSc.searchInternalLinks.call( self );
			}, 500 );
		});
		
		/* Button to open dialog */
		$('body').on('click', '#rpr-add-recipe-button', function(event) {
		    editor_id = jQuery('#rpr-add-recipe-button').attr( "data_editor" );
		    window.rprRecipeSc.open( editor_id );
		});
	},

	open: function( editorId ) {
	    var ed;
		
		rprRecipeSc.range = null;

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

		if ( ! rprRecipeSc.isMCE() && document.selection ) {
			this.textarea.focus();
			this.range = document.selection.createRange();
		}

		inputs.wrap.show();
		inputs.backdrop.show();

		rprRecipeSc.refresh();
		$( document ).trigger( 'rprRecipeSc-open', inputs.wrap );
	},

	isMCE: function() {
		return editor && ! editor.isHidden();
	},

	refresh: function() {
		// Refresh rivers (clear links, check visibility)
		rivers.search.refresh();
		rivers.recent.refresh();

		if ( rprRecipeSc.isMCE() ) {
			rprRecipeSc.mceRefresh();
		} else {
			rprRecipeSc.setDefaultValues();
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

		// Load the most recent results if this is the first time opening the panel.
		if ( ! rivers.recent.ul.children().length ) {
			rivers.recent.ajax();
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
			inputs.submit.val( rprRecipeScL10n.update );
		
		// If there's no link, set the default values.
		} else {
			rprRecipeSc.setDefaultValues();
		}
	},

	close: function() {
		if ( ! rprRecipeSc.isMCE() ) {
			rprRecipeSc.textarea.focus();

			if ( rprRecipeSc.range ) {
				rprRecipeSc.range.moveToBookmark( rprRecipeSc.range.getBookmark() );
				rprRecipeSc.range.select();
			}
		} else {
			editor.focus();
		}

		inputs.backdrop.hide();
		inputs.wrap.hide();
		$( document ).trigger( 'rprRecipeSc-close', inputs.wrap );
	},

	update: function() {
		if ( rprRecipeSc.isMCE() )
			rprRecipeSc.mceUpdate();
		else
			rprRecipeSc.htmlUpdate();
	},

	//Build the shortcode here!
	htmlUpdate: function() {
	    var attrs, html, begin, end, cursor, title, selection,
	    textarea = rprRecipeSc.textarea;

	    if ( ! textarea )
		return;
		
	    var out="[";
		
	    if( inputs.id.val=="" || inputs.title.val==""){
		return;
	    }
	
	    out+="rpr-recipe";
	    out+=" id="+inputs.id.val();
				
	    if( inputs.excerpt.prop("checked") == true ){
		out+= " excerpt=1";
	    }
	    if( inputs.nodesc.prop("checked") == true ){
		out+= " nodesc=1";
	    }
	
	    out+="]\n";
	    
	    // Insert shortcode
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

	    rprRecipeSc.close();
	    textarea.focus();
	},

	mceUpdate: function() {
	    var out="";

	    if( inputs.id.val()=="" || inputs.title.val()==""){
		return;
	    }
	
	    out+="[rpr-recipe";
	    out+=" id="+inputs.id.val();
				
	    if( inputs.excerpt.prop("checked") === true ){
		out+= " excerpt=1";
	    }
	    if( inputs.nodesc.prop("checked") === true ){
		out+= " nodesc=1";
	    }
	    out+=" ]<br/>";

	    rprRecipeSc.close();
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
		inputs.submit.val( rprRecipeScL10n.save );
	},

	searchInternalLinks: function() {
		var t = $( this ), waiting,
			search = t.val();

		if ( search.length > 2 ) {
			rivers.recent.hide();
			rivers.search.show();

			// Don't search if the keypress didn't change the title.
			if ( rprRecipeSc.lastSearch == search )
				return;

			rprRecipeSc.lastSearch = search;
			waiting = t.parent().find('.spinner').show();

			rivers.search.change( search );
			rivers.search.ajax( function() {
				waiting.hide();
			});
		} else {
			rivers.search.hide();
			rivers.recent.show();
		}
	},

	next: function() {
		rivers.search.next();
		rivers.recent.next();
	},

	prev: function() {
		rivers.search.prev();
		rivers.recent.prev();
	},

	keydown: function( event ) {
		var fn, id,
			key = $.ui.keyCode;

		if ( key.ESCAPE === event.keyCode ) {
			rprRecipeSc.close();
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
		clearInterval( rprRecipeSc.keyInterval );
		rprRecipeSc[ fn ]();
		rprRecipeSc.keyInterval = setInterval( rprRecipeSc[ fn ], rprRecipeSc.keySensitivity );
		event.preventDefault();
	},

	keyup: function( event ) {
		var key = $.ui.keyCode;

		if ( event.which === key.UP || event.which === key.DOWN ) {
			clearInterval( rprRecipeSc.keyInterval );
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
	
	River = function( element, search ) {
		var self = this;
		this.element = element;
		this.ul = element.children( 'ul' );
		this.contentHeight = element.children( '#link-selector-height' );
		this.waiting = element.find('.river-waiting');

		this.change( search );
		this.refresh();

		$( '#rpr-link .query-results, #rpr-link #link-selector' ).scroll( function() {
			self.maybeLoad();
		});
		element.on( 'click', 'li', function( event ) {
			self.select( $( this ), event );
		});
	};
	
	$.extend( River.prototype, {
		refresh: function() {
			this.deselect();
			this.visible = this.element.is( ':visible' );
		},
		show: function() {
			if ( ! this.visible ) {
				this.deselect();
				this.element.show();
				this.visible = true;
			}
		},
		hide: function() {
			this.element.hide();
			this.visible = false;
		},
		// Selects a list item and triggers the river-select event.
		select: function( li, event ) {
			var liHeight, elHeight, liTop, elTop;

			if ( li.hasClass( 'unselectable' ) || li == this.selected )
				return;

			this.deselect();
			this.selected = li.addClass( 'selected' );
			// Make sure the element is visible
			liHeight = li.outerHeight();
			elHeight = this.element.height();
			liTop = li.position().top;
			elTop = this.element.scrollTop();

			if ( liTop < 0 ) // Make first visible element
				this.element.scrollTop( elTop + liTop );
			else if ( liTop + liHeight > elHeight ) // Make last visible element
				this.element.scrollTop( elTop + liTop - elHeight + liHeight );

			// Trigger the river-select event
			this.element.trigger( 'river-select', [ li, event, this ] );
		},
		deselect: function() {
			if ( this.selected )
				this.selected.removeClass( 'selected' );
			this.selected = false;
		},
		prev: function() {
			if ( ! this.visible )
				return;

			var to;
			if ( this.selected ) {
				to = this.selected.prev( 'li' );
				if ( to.length )
					this.select( to );
			}
		},
		next: function() {
			if ( ! this.visible )
				return;

			var to = this.selected ? this.selected.next( 'li' ) : $( 'li:not(.unselectable):first', this.element );
			if ( to.length )
				this.select( to );
		},
		ajax: function( callback ) {
			var self = this,
				delay = this.query.page == 1 ? 0 : rprRecipeSc.minRiverAJAXDuration,
				response = rprRecipeSc.delayedCallback( function( results, params ) {
					self.process( results, params );
					if ( callback )
						callback( results, params );
				}, delay );

			this.query.ajax( response );
		},
		change: function( search ) {
			if ( this.query && this._search == search )
				return;

			this._search = search;
			this.query = new Query( search );
			this.element.scrollTop( 0 );
		},
		process: function( results, params ) {
			var list = '', alt = true, classes = '',
				firstPage = params.page == 1;

			if ( ! results ) {
				if ( firstPage ) {
					list += '<li class="unselectable no-matches-found"><span class="item-title"><em>' +
						rprRecipeScL10n.noMatchesFound + '</em></span></li>';
				}
			} else {
				$.each( results, function() {
					classes = alt ? 'alternate' : '';
					classes += this.title ? '' : ' no-title';
					list += classes ? '<li class="' + classes + '">' : '<li>';
					list += '<input type="hidden" class="item-id" value="' + this.id + '" />';
					list += '<span class="item-title">';
					list += this.title ? this.title : rprRecipeScL10n.noTitle;
					list += '</span><span class="item-info">' + rprRecipeScL10n.recipe + '</span></li>';
					alt = ! alt;
				});
			}

			this.ul[ firstPage ? 'html' : 'append' ]( list );
		},
		maybeLoad: function() {
			var self = this,
				el = this.element,
				bottom = el.scrollTop() + el.height();

			if ( ! this.query.ready() || bottom < this.contentHeight.height() - rprRecipeSc.riverBottomThreshold )
				return;

			setTimeout(function() {
				var newTop = el.scrollTop(),
					newBottom = newTop + el.height();

				if ( ! self.query.ready() || newBottom < self.contentHeight.height() - rprRecipeSc.riverBottomThreshold )
					return;

				self.waiting.show();
				el.scrollTop( newTop + self.waiting.outerHeight() );

				self.ajax( function() {
					self.waiting.hide();
				});
			}, rprRecipeSc.timeToTriggerRiver );
		}
	});

	
	Query = function( search ) {
		this.page = 1;
		this.allLoaded = false;
		this.querying = false;
		this.search = search;
	};

	$.extend( Query.prototype, {
		ready: function() {
			return ! ( this.querying || this.allLoaded );
		},
		ajax: function( callback ) {
			var self = this,
				query = {
					action : 'rpr_get_results',
					page : this.page,
					'rpr_ajax_nonce' : inputs.nonce.val()
				};

			if ( this.search )
				query.search = this.search;

			this.querying = true;

			$.post( ajaxurl, query, function( r ) {
				self.page++;
				self.querying = false;
				self.allLoaded = ! r;
				callback( r, query );
			}, 'json' );
		}
	});

	$( document ).ready( rprRecipeSc.init );
})( jQuery );