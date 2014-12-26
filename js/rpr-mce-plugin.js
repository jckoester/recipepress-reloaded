/*
 * Add a button to TinyMCE to include RPR shortcodes
 */


(function() {
	tinymce.create('tinymce.plugins.rpr_mce_plugin', {
        init: function(editor, url) {
            editor.addButton('rpr_mce_plugin', {
            	title: 'RPR Shortcodes',
                icon: 'rpr-icon-mce',
                onclick: function() {
                	tinyMCE.activeEditor.execCommand('RPR_Link'); 
                }
            });
            
            editor.addCommand( 'RPR_Link', function() {
        		window.rprLink.open( editor.id );
        	});

            data = {};
        },
        createControl: function(n, cm) {
            return null;
        },
        getInfo: function() {
            return {
                longname : 'RPR Shortcode Plugin',
                author : 'Jan Koester',
                authorurl : 'http://rp-reloaded.net',
                infourl : 'http://rp-reloaded.net',
                version : "1.0"
            };
        },
    	
    });
	
    tinymce.PluginManager.add('rpr_mce_plugin', tinymce.plugins.rpr_mce_plugin);
})();