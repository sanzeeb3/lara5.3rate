(function($) {

	tinymce.PluginManager.add('wpdevart_forms', function( editor, url ) {
		
		
		var sh_tag = 'wpdevart_forms';

		//helper functions 
		function getAttr(s, n) {
			n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
			return n ?  window.decodeURIComponent(n[1]) : '';
		};
		function get_parentnode(url){
			new_url=url.split('/');
			new_url.pop();
			new_url=new_url.join('/')
			return new_url;
		}
		function html( cls, data ) {
			var placeholder = get_parentnode(url) + '/images/tinmce_content.png';
			
			data = window.encodeURIComponent( data );			
			return '<img src="' + placeholder + '" class="mceItem ' + cls + '" ' + 'data-sh-attr="' + data + '" data-mce-resize="false" data-mce-placeholder="1" />';
		}

		function replaceShortcodes( content ) {
			return content.replace( /\[wpdevart_forms([^\]]*)\]/g, function( all,attr) {
				return html( 'wp-wpdevart_forms', attr );
			});
		}

		function restoreShortcodes( content ) {
			return content.replace( /(?:<p(?: [^>]+)?>)*(<img [^>]+>)(?:<\/p>)*/g, function( match, image ) {
				var data = getAttr( image, 'data-sh-attr' );				
				if ( data ) {
					return '<p>[' + sh_tag + data + ']</p>';
				}
				return match;
			});
		}
		//add popup
		editor.addCommand('wpdevart_forms_popup', function(ui, v) {
			//setup defaults
		
            var   object_for_button = {};
			var   object_for_dialog = {};
		  
			object_for_button = {
				title: "Wpdevart Form Shortcode Creator", 
				file:  document.location.origin+ajaxurl + '/?action=wpdevart_forms_mce_ajax&id=0',   
				width: 300, 
				height: 200,   
				id : 'my-custom-wpdialog',
				inline: 1          
					
			};
		   object_for_dialog={
				editor: editor,  
				jquery: $,  								
				plugin_url : url
				//php_version: php_version   
			};		  
			editor.windowManager.open( object_for_button,  object_for_dialog);			
		});

		//add button
		editor.addButton('wpdevart_forms', {
			image : get_parentnode(url) + '/images/post_page_button.png',			
			tooltip: 'Insert Wpdevart From',
			onclick: function() {
				$.get(document.location.origin+ajaxurl + '/?action=wpdevart_forms_mce_ajax&id=0', function(data, status){});			
				editor.execCommand('wpdevart_forms_popup','',{});
			}
		});

		//replace from shortcode to an image placeholder
		editor.on('BeforeSetcontent', function(e){ 
			e.content = replaceShortcodes( e.content );
		});

		//replace from image placeholder to shortcode
		editor.on('GetContent', function(e){
			e.content = restoreShortcodes(e.content);
		});

		//open popup on placeholder on click
		editor.on('Click',function(e) {
			var cls  = e.target.className.indexOf('wp-wpdevart_forms');
			if ( e.target.nodeName == 'IMG' && e.target.className.indexOf('wp-wpdevart_forms') > -1 ) {
				var title = e.target.attributes['data-sh-attr'].value;
				title = window.decodeURIComponent(title);
				id = getAttr(title,'id');			  
				$.get(document.location.origin+ajaxurl + '/?action=wpdevart_forms_mce_ajax&id='+id, function(data, status){});
				var object_for_dialog = {};
				var object_for_button = {};			  
				object_for_dialog ={ord :getAttr(title,'ord'),
					title: "Wpdevart Form Shortcode Creator", 
					file: document.location.origin+ajaxurl + '/?action=wpdevart_forms_mce_ajax&id='+id,    
					width: 300, 
					height: 200,   
					id : 'my-custom-wpdialog',
					inline: 1          
					
				};
				object_for_button={
					editor: editor,  
					jquery: $,  										
					plugin_url : url
					//php_version: php_version   
				};
			  
				editor.windowManager.open( object_for_dialog,  object_for_button);
			}
		});
	});
})(jQuery);



