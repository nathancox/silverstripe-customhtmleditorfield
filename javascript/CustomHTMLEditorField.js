/**
 *  Overwrites the TinyMCE instantiation on HTMLEditorFields.  Allows you to set custom body classes for each editor.  This is done via the data-body-class attribute
 *  or by setBodyClass() on HTMLEditorField.  The body class is appended to the config's default.
 *  This is mostly copied from HTMLEditorField.js's textarea.htmleditor::redraw();
 */
(function($) {
    $.entwine('ss', function($) {
        $('textarea.htmleditor').entwine({

            redraw: function() {
                // Using textarea config ID from global config object (generated through HTMLEditorConfig PHP logic)
                var config = ssTinyMceConfig[this.data('config')], self = this, ed = this.getEditor();


                var customConfig = {};
                // if a custom body class was defined, append it to the default and merge it with the config
                if ($(this).data('body-class') && config) {
                    if (!config.body_class) {
                         config.body_class = '';
                    }
                    customConfig.body_class = config.body_class + ' ' + $(this).data('body-class');
                }
                // make sure not to change config so we don't affect other editors
                var customConfig = jQuery.extend({}, config, customConfig);



                ed.init(customConfig);

                // Create editor instance and render it.
                // Similar logic to adapter/jquery/jquery.tinymce.js, but doesn't rely on monkey-patching
                // jQuery methods, and avoids replicate the script lazyloading which is already in place with jQuery.ondemand.
                ed.create(this.attr('id'), customConfig);

                // enabling this causes errors when you open popups because it applied TinyMCE to the editor twice, I think
                //this._super();
            }

        });
    });
})(jQuery);
