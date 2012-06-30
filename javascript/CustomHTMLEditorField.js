/**
 *	Example body class usage:
 *	$footerContentField->setAttribute('data-body-class', 'footer');
 *	
 */
 
 /**
 *	Overwrites the TinyMCE instantiation on HTMLEditorFields.  Does two things:
 *	
 *	1) allows you to set custom body classes for each editor.  This is done via the data-body-class attribute on an HTMLEditorField
 *	   or by setBodyClass() on CustomHTMLEditorField.  The body class is appended to the config's default
 *	2) allow for per-editor TinyMCE configs setting the data-config-id attribute (done with setEdtorConfig on CustomHTMLEditoField)
 */
(function($) {
	$.entwine('ss', function($) {
		$('textarea.htmleditor').entwine({
			
			redraw: function() {
				var config, self = this, ed = this.getEditor();
				
				// if this editor has the data-config-id attribute set and customTinyMceConfig is defined, use them to get the config.
				// otherwise use the default in ssTinyMceConfig
				if ($(this).data('config-id') && typeof customTinyMceConfig == 'object') {
					config = customTinyMceConfig[$(this).data('config-id')];
				} else {
					config = ssTinyMceConfig;
				}
				
				var customConfig = {};
				
				// if a custom body class was defined, append it to the default and merge it with the config
				if ($(this).data('body-class')) {
					customConfig.body_class = config.body_class + ' ' + $(this).data('body-class');
				}
				// make sure not to change config so we don't affect other editors
				var customConfig = jQuery.extend({}, config, customConfig);

				ed.init(customConfig);
				self.css('visibility', '');
				
				ed.create(this.attr('id'), customConfig, function() {
					self.css('visibility', 'visible');
				});
			}
			
		});
	});
})(jQuery);
	