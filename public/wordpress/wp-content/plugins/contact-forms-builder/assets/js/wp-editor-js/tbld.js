(function() {
	tinymce.create('tinymce.plugins.wpdevartforms', {
 
		init : function(ed, url) {
		},
		createControl : function(n, cm) {
 
            if(n=='wpdevartforms'){
                var mlb = cm.createListBox('wpdevartforms', {
                     title: 'wpdevart Forms',
                     onselect : function(v) {
                     	if(tinyMCE.activeEditor.selection.getContent() == ''){
                            tinyMCE.activeEditor.selection.setContent( v )
                        }
                     }
                });
 
                for(var i in wpdevartforms_shortcodes)
                	mlb.add(wpdevartforms_shortcodes[i],wpdevartforms_shortcodes[i]);
 
                return mlb;
            }
            return null;
        }
 
 
	});
	tinymce.PluginManager.add('wpdevartforms', tinymce.plugins.wpdevartforms);
	
	setTimeout(function() {
		jQuery('.mce-widget.mce-btn').each(function() {
			var btn = jQuery(this);
			if (btn.attr('aria-label')=="wpdevart Forms")
				btn.find('span').css({padding:"10px 20px 10px 10px"});
		});
	},1000);
	
})();