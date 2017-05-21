<?php
/**
 * This class shouldn't be used directly, it's just here to allow other classes access to the
 * HTMLEditorField::editorConfig protect method by accessing it via a subclass.
 */
class CustomHTMLEditorField extends HTMLEditorField
{

    public static function set_editor_config($field, $identifier)
    {
        $field->editorConfig = $identifier;
    }

    public static function get_editor_config($field)
    {
        return $field->editorConfig;
    }
}
