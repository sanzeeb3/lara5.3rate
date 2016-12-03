(function() {
"use strict";   
	
	var wpda_form_val = [];
	
	for(var i in wpdevartforms_shortcodes){
		wpda_form_val[i] = {text: wpdevartforms_shortcodes[i], onclick : function() {
			//tinymce.execCommand('mceInsertContent', false, wpdevartforms_shortcodes[i]);
		}};
	}
	
	tinymce.PluginManager.add( 'wpdevartforms', function( editor, url ) {

		editor.addButton( 'wpdevartforms', {
			type: 'listbox',
			title: 'wpdevart Forms',			
			text: 'wpdevart Forms',
			icon: false,
			onselect: function(e) {
				tinymce.execCommand('mceInsertContent', false, e.control['_text']);
			}, 
			values: wpda_form_val
 
		});
	});
	
	setTimeout(function() {
		jQuery('.mce-widget.mce-btn').each(function() {
			var btn = jQuery(this);
			if (btn.attr('aria-label')=="wpdevart Forms")
				btn.find('span').css({padding:"10px 20px 10px 10px"});
		});
	},1000);
 
})();