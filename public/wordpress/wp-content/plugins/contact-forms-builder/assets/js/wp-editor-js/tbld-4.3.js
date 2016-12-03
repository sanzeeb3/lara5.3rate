(function() {
"use strict";   
	
	var ws_val = [];
	
	for(var i in wpdevartforms_shortcodes){
		ws_val[i] = {text: wpdevartforms_shortcodes[i], onclick : function() {
			//tinymce.execCommand('mceInsertContent', false, wpdevartforms_shortcodes[i]);
		}};
	}
	
	tinymce.PluginManager.add( 'wpdevartforms', function( editor, url ) {

		editor.addButton( 'wpdevartforms', {
			type: 'listbox',
			title: 'wpdevart Forms',			
			text: 'wpdevartforms',
			icon: false,
			onselect: function(e) {
				
				tinymce.execCommand('mceInsertContent', false, e.control.settings.text);
			}, 
			values: ws_val
 
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