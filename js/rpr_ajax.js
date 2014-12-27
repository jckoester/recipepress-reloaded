/* global ajaxurl, tinymce, wpLinkL10n, setUserSetting, wpActiveEditor */
var rprLink;

( function( $ ) {
	var editor, searchTimer, River, Query,
		inputs = {},
		rivers = {},
		isTouch = ( 'ontouchend' in document );
	
rprLink = {
	timeToTriggerRiver: 150,
	minRiverAJAXDuration: 200,
	riverBottomThreshold: 5,
	keySensitivity: 100,
	lastSearch: '',
	textarea: '',

	init: function() {
		//alert('test');
		
		inputs.wrap = $('#rpr-link-wrap');
		inputs.dialog = $( '#rpr-link' );
		inputs.backdrop = $( '#rpr-link-backdrop' );
		inputs.submit = $( '#rpr-link-submit' );
		inputs.close = $( '#rpr-link-close' );
		//Selector
		inputs.selector = $('#rpr-shortcode-selector');
		// URL
		inputs.id = $( '#recipe-id-field' );
		inputs.nonce = $( '#rpr_ajax_nonce' );
		// Secondary options
		inputs.title = $( '#recipe-title-field' );
		// Advanced Options
		inputs.taxonomy =$( '#recipe-taxonomy' );
		inputs.openInNewTab = $( '#link-target-checkbox' );
		inputs.search = $( '#rpr-search-field' );
		inputs.excerpt = $( '#rpr-embed-excerpt' );
		inputs.excerpt.prop("checked", false);
		
		// Shortcode Panes
		inputs.tax_wrap = $( '#rpr-taxonomy-panel');
		inputs.recipelist_wrap = $( '#rpr-recipelist-panel');
		inputs.recipe_wrap = $( '#rpr-recipe-panel');
		
		// Build Rivers
		rivers.search = new River( $( '#rpr-search-results' ) );
		rivers.recent = new River( $( '#rpr-most-recent-results' ) );
		rivers.elements = inputs.dialog.find( '.query-results' );

		// Get search notice text
		inputs.queryNotice = $( '#query-notice-message' );
		inputs.queryNoticeTextDefault = inputs.queryNotice.find( '.query-notice-default' );
		inputs.queryNoticeTextHint = inputs.queryNotice.find( '.query-notice-hint' );

		// Bind event handlers
		inputs.dialog.keydown( rprLink.keydown );
		inputs.dialog.keyup( rprLink.keyup );
		inputs.submit.click( function( event ) {
			event.preventDefault();
			rprLink.update();
		});
		inputs.close.add( inputs.backdrop ).add( '#rpr-link-cancel a' ).click( function( event ) {
			event.preventDefault();
			rprLink.close();
		});
		
		//Toggle view dependent on shortcode type
		inputs.selector.on('change', rprLink.toggleShortcodePanel);

		//Action whe a recipe is selected from the list...
		rivers.elements.on( 'river-select', rprLink.updateFields );

		// Display 'hint' message when search field or 'query-results' box are focused
		inputs.search.on( 'focus.rprLink', function() {
			inputs.queryNoticeTextDefault.hide();
			inputs.queryNoticeTextHint.removeClass( 'screen-reader-text' ).show();
		} ).on( 'blur.rprLink', function() {
			inputs.queryNoticeTextDefault.show();
			inputs.queryNoticeTextHint.addClass( 'screen-reader-text' ).hide();
		} );

		inputs.search.keyup( function() {
			var self = this;

			window.clearTimeout( searchTimer );
			searchTimer = window.setTimeout( function() {
				rprLink.searchInternalLinks.call( self );
			}, 500 );
		});
	},

	open: function( editorId ) {
		var ed;
		

		rprLink.range = null;

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
				editor = ed;inputs.tax_wrap.hide( );
				inputs.recipelist_wrap.hide( );
				inputs.recipe_wrap.show();
			} else {
				editor = null;
			}

			if ( editor && tinymce.isIE ) {
				editor.windowManager.bookmark = editor.selection.getBookmark();
			}
		}

		if ( ! rprLink.isMCE() && document.selection ) {
			this.textarea.focus();
			this.range = document.selection.createRange();
		}
		
		inputs.selector.val('rpr-recipe');
		inputs.tax_wrap.hide( );
		inputs.recipelist_wrap.hide( );
		inputs.recipe_wrap.show();
		inputs.wrap.show();
		inputs.backdrop.show();

		rprLink.refresh();
		$( document ).trigger( 'rprLink-open', inputs.wrap );
	},

	isMCE: function() {
		return editor && ! editor.isHidden();
	},

	refresh: function() {
		// Refresh rivers (clear links, check visibility)
		rivers.search.refresh();
		rivers.recent.refresh();

		if ( rprLink.isMCE() ) {
			rprLink.mceRefresh();
		} else {
			rprLink.setDefaultValues();
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
			inputs.submit.val( rprLinkL10n.update );
		
		// If there's no link, set the default values.
		} else {
			rprLink.setDefaultValues();
		}
	},

	close: function() {
		if ( ! rprLink.isMCE() ) {
			rprLink.textarea.focus();

			if ( rprLink.range ) {
				rprLink.range.moveToBookmark( rprLink.range.getBookmark() );
				rprLink.range.select();
			}
		} else {
			editor.focus();
		}

		inputs.backdrop.hide();
		inputs.wrap.hide();
		$( document ).trigger( 'rprLink-close', inputs.wrap );
	},

	/*getAttrs: function() {
		return {
			href: inputs.id.val(),
			title: inputs.title.val(),
			target: inputs.openInNewTab.prop( 'checked' ) ? '_blank' : ''
		};
	},*/

	update: function() {
		//if ( rprLink.isMCE() )
			rprLink.mceUpdate();
		//else
		//	rprLink.htmlUpdate();
	},

	//Build the shortcode here!
	htmlUpdate: function() {
		var attrs, html, begin, end, cursor, title, selection,
			textarea = rprLink.textarea;

		if ( ! textarea )
			return;
		
		var out="[";
		
		switch(inputs.selector.val) {
			case 'rpr-recipe':
				if( inputs.id.val=="" || inputs.title.val==""){
					return;
				}
				out+="rpr-recipe";
				alert(inputs.id.val);
				break;
		} 

		//attrs = rprLink.getAttrs();

		// If there's no href, return.
		if ( ! attrs.href || attrs.href == 'http://' )
			return;

		// Build HTML
		html = '<a href="' + attrs.href + '"';

		if ( attrs.title ) {
			title = attrs.title.replace( /</g, '&lt;' ).replace( />/g, '&gt;' ).replace( /"/g, '&quot;' );
			html += ' title="' + title + '"';
		}

		if ( attrs.target ) {
			html += ' target="' + attrs.target + '"';
		}

		html += '>';

		// Insert HTML
		if ( document.selection && rprLink.range ) {
			// IE
			// Note: If no text is selected, IE will not place the cursor
			//       inside the closing tag.
			textarea.focus();
			rprLink.range.text = html + rprLink.range.text + '</a>';
			rprLink.range.moveToBookmark( rprLink.range.getBookmark() );
			rprLink.range.select();

			rprLink.range = null;
		} else if ( typeof textarea.selectionStart !== 'undefined' ) {
			// W3C
			begin       = textarea.selectionStart;
			end         = textarea.selectionEnd;
			selection   = textarea.value.substring( begin, end );
			html        = html + selection + '</a>';
			cursor      = begin + html.length;

			// If no text is selected, place the cursor inside the closing tag.
			if ( begin == end )
				cursor -= '</a>'.length;

			textarea.value = textarea.value.substring( 0, begin ) + html +
				textarea.value.substring( end, textarea.value.length );

			// Update cursor position
			textarea.selectionStart = textarea.selectionEnd = cursor;
		}

		rprLink.close();
		textarea.focus();
	},

	mceUpdate: function() {
		var link;
			//attrs = rprLink.getAttrs();

		var out="";
		
		switch(inputs.selector.val()) {
			case 'rpr-recipe':
				if( inputs.id.val()=="" || inputs.title.val()==""){
					return;
				}
				out+="[rpr-recipe";
				out+=" id="+inputs.id.val();
				
				if( inputs.excerpt.prop("checked") == true ){
					out+= " excerpt=1";
				}
				out+=" ]";
				break;
			case 'rpr-tax-list':
				out+="[rpr-tax-list ";
				out+="tax=\""+inputs.taxonomy.val()+"\"]";
				break;
			case 'rpr-recipe-index':
				out+= "[rpr-recipe-index]";
				break;
			default:
				alert(inputs.selector.val());
				alert('error');
				return;
		} 

		rprLink.close();
		editor.focus();

		tinyMCE.activeEditor.execCommand('mceReplaceContent', false, out);
		/*if ( tinymce.isIE ) {
			editor.selection.moveToBookmark( editor.windowManager.bookmark );
		}

		link = editor.dom.getParent( editor.selection.getNode(), 'a[href]' );

		// If the values are empty, unlink and return
		if ( ! attrs.href || attrs.href == 'http://' ) {
			editor.execCommand( 'unlink' );
			return;
		}

		if ( link ) {
			editor.dom.setAttribs( link, attrs );
		} else {
			editor.execCommand( 'mceInsertLink', false, attrs );
		}

		// Move the cursor to the end of the selection
		editor.selection.collapse();*/
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
		inputs.submit.val( rprLinkL10n.save );
	},

	searchInternalLinks: function() {
		var t = $( this ), waiting,
			search = t.val();

		if ( search.length > 2 ) {
			rivers.recent.hide();
			rivers.search.show();

			// Don't search if the keypress didn't change the title.
			if ( rprLink.lastSearch == search )
				return;

			rprLink.lastSearch = search;
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
			rprLink.close();
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
		clearInterval( rprLink.keyInterval );
		rprLink[ fn ]();
		rprLink.keyInterval = setInterval( rprLink[ fn ], rprLink.keySensitivity );
		event.preventDefault();
	},

	keyup: function( event ) {
		var key = $.ui.keyCode;

		if ( event.which === key.UP || event.which === key.DOWN ) {
			clearInterval( rprLink.keyInterval );
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
	},
	
	toggleShortcodePanel: function( event ) {
		
		if( inputs.selector.val() == "rpr-tax-list"){
			inputs.tax_wrap.show( );
			inputs.recipelist_wrap.hide( );
			inputs.recipe_wrap.hide();
		} 
		
		if( inputs.selector.val() == "rpr-recipe"){
			inputs.tax_wrap.hide( );
			inputs.recipelist_wrap.hide( );
			inputs.recipe_wrap.show();
		}
		
		if( inputs.selector.val() == "rpr-recipe-index"){
			inputs.tax_wrap.hide( );
			inputs.recipelist_wrap.show( );
			inputs.recipe_wrap.hide();
		}
		
		event.preventDefault();
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
				delay = this.query.page == 1 ? 0 : rprLink.minRiverAJAXDuration,
				response = rprLink.delayedCallback( function( results, params ) {
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
						rprLinkL10n.noMatchesFound + '</em></span></li>';
				}
			} else {
				$.each( results, function() {
					classes = alt ? 'alternate' : '';
					classes += this.title ? '' : ' no-title';
					list += classes ? '<li class="' + classes + '">' : '<li>';
					list += '<input type="hidden" class="item-id" value="' + this.id + '" />';
					list += '<span class="item-title">';
					list += this.title ? this.title : rprLinkL10n.noTitle;
					list += '</span><span class="item-info">' + rprLinkL10n.recipe + '</span></li>';
					alt = ! alt;
				});
			}

			this.ul[ firstPage ? 'html' : 'append' ]( list );
		},
		maybeLoad: function() {
			var self = this,
				el = this.element,
				bottom = el.scrollTop() + el.height();

			if ( ! this.query.ready() || bottom < this.contentHeight.height() - rprLink.riverBottomThreshold )
				return;

			setTimeout(function() {
				var newTop = el.scrollTop(),
					newBottom = newTop + el.height();

				if ( ! self.query.ready() || newBottom < self.contentHeight.height() - rprLink.riverBottomThreshold )
					return;

				self.waiting.show();
				el.scrollTop( newTop + self.waiting.outerHeight() );

				self.ajax( function() {
					self.waiting.hide();
				});
			}, rprLink.timeToTriggerRiver );
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

	$( document ).ready( rprLink.init );
})( jQuery );