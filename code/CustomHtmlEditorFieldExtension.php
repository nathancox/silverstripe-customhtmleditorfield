<?php
/**
 *	An extension of HTMLEditorField that lets you set editor-specific body class names and TinyMCE configs
 *
 */
class CustomHtmlEditorFieldExtension extends Extension {


	/**
	 * 	Kept for backwards compatibility.  As of SS3.4 you can pass a config identifier to HTMLEditorField::create();
	 *
	 *	@param string $identifier
	 */
	function setEditorConfig($identifier) {
		$this->owner->editorConfig = $identifier;
	}

	/**
	 * 	Get the identifier for this field's HTMLEditorConfig
	 *
	 *	@return string
	 */
	function getEditorConfigID() {
		return $this->owner->editorConfig;
	}

	/**
	 * 	Return this field's HTMLEditorConfig
	 *
	 *	@return HTMLEditorConfig
	 */
	function getEditorConfig() {
		return HTMLEditorConfig::get($this->getEditorConfigID());
	}


	/**
	 * 	Convenience method for setting body classes.  Just slightly nicer than using setAttribute().
	 * 	NOTE: the specified classes will be APPENDED to the config's body_class property, not replace it
	 *
	 *	@param string $classes
	 */
	function setBodyClass($classes) {
		$this->owner->setAttribute('data-body-class', $classes);
	}

	/**
	 * 	Return This field's body class setting
	 *
	 *	@return string
	 */
	function getBodyClass() {
		$this->owner->getAttribute('data-body-class');
	}

}
