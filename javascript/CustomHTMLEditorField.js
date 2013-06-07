/**
 *	Overwrites the TinyMCE instantiation on HTMLEditorFields.  Does two things:
 *	
 *	1) allows you to set custom body classes for each editor.  This is done via the data-body-class attribute 
 *	or by setBodyClass() on HTMLEditorField.  The body class is appended to the config's default.
 *	2) allow for per-editor TinyMCE configs setting the data-config-id attribute (done with setEdtorConfig on 
 *	HTMLEditoField)
 */
(function($) {
	$.entwine('ss', function($) {
		$('textarea.htmleditor').entwine({
			
			redraw: function() {

				var config, self = this, ed = this.getEditor();
				
				// if this editor has the data-config-id attribute set and customTinyMceConfigs is defined, use them to 
				// get the config.
				// otherwise use the default in ssTinyMceConfigs
				if ($(this).data('config-id') && typeof customTinyMceConfigs == 'object') {
					config = customTinyMceConfigs[$(this).data('config-id')];
				} 
				
				if (!config) {
					config = ssTinyMceConfig;
				}



				var callbackProperties = [
					'execcommand_callback',
					'handle_event_callbackEdit',
					'handle_node_change_callback',
					'init_instance_callback',
					'onchange_callback',
					'oninit',
					'onpageload',
					'remove_instance_callback',
					'save_callback',
					'setup',
					'setupcontent_callback',
					'urlconverter_callback'
				];
				
				var i = 0;
				var property;
				
				for (i = 0; i < callbackProperties.length; i++) {
					property = callbackProperties[i];
					
					if (typeof config[property] != 'undefined') {
						config.property = function() { eval(config[property]) };
					}
					
				}
				
				var customConfig = {};
				
				// if a custom body class was defined, append it to the default and merge it with the config
				if ($(this).data('body-class')) {
					if (!config.body_class) {
						 config.body_class = '';
					}
					customConfig.body_class = config.body_class + ' ' + $(this).data('body-class');
				}

				// make sure not to change config so we don't affect other editors
				var customConfig = jQuery.extend({}, config, customConfig);

				/*
					@todo: ed.init() is here in the default script but it causes editor fields to double up if I include it here
								for some reason
				 */
			//	ed.init(customConfig);

				// Create editor instance and render it.
				// Similar logic to adapter/jquery/jquery.tinymce.js, but doesn't rely on monkey-patching
				// jQuery methods, and avoids replicate the script lazyloading which is already in place with jQuery.ondemand.
				ed.create(this.attr('id'), customConfig, function() {
					// Delayed show because TinyMCE calls hide() via setTimeout on removing an element,
					// which is called in quick succession with adding a new editor after ajax loading new markup

					//storing the container object before setting timeout
					var redrawObj = $(ed.getInstance().getContainer());
					setTimeout(function() {
						redrawObj.show();
					}, 10);
				});
		
			}
			
		});
	});
})(jQuery);
	