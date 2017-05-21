<?php
/**
 *  An extension of HTMLEditorField that lets you set editor-specific body class names and TinyMCE configs
 */
class CustomHtmlEditorFieldExtension extends Extension
{
    /**
     *
     *  @param string $identifier
     */
    public function setEditorConfig($identifier)
    {
        CustomHTMLEditorField::set_editor_config($this->owner, $identifier);
    }

    /**
     *  Get the identifier for this field's HTMLEditorConfig
     *
     *  @return string
    */
    public function getEditorConfigID()
    {
        return CustomHTMLEditorField::get_editor_config($this->owner);
    }

    /**
     *  Return this field's HTMLEditorConfig
     *
     *  @return HTMLEditorConfig
    */
    public function getEditorConfig()
    {
        return HTMLEditorConfig::get($this->getEditorConfigID());
    }


    /**
     *  Convenience method for setting body classes.  Just slightly nicer than using setAttribute().
     *  NOTE: the specified classes will be APPENDED to the config's body_class property, not replace it
     *
     *  @param string $classes
     */
    public function setBodyClass($classes)
    {
        $this->owner->setAttribute('data-body-class', $classes);
    }

    /**
     *  Return This field's body class setting
     *
     *  @return string
     */
    public function getBodyClass()
    {
        $this->owner->getAttribute('data-body-class');
    }

}
