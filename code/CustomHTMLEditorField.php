<?php
/**
 *	A subclass of HTMLEditorField that lets you set editor-specific body class names and TinyMCE configs
 *	
 */
class CustomHTMLEditorField extends HTMLEditorField {
	var $editorConfigID;
	
	
	/**
	 * @see TextareaField::__construct()
	 */
	public function __construct($name, $title = null, $value = '') {
		if(count(func_get_args()) > 3) Deprecation::notice('3.0', 'Use setRows() and setCols() instead of constructor arguments');
		
		// need to add the htmleditor class so the javascript will apply an editor to it
		$this->addExtraClass('htmleditor');
		
		// we now include the JS in an instance method instead of statically since it needs to know the config
		$this->includeJS();

		parent::__construct($name, $title, $value);
	}
	
	/**
	 * Stubbing out HTMLEditorField::include_js() since we're using includeJS() now (see below)
	 */
	public static function include_js() {
		return;
	}
	
	/**
	 *	Replaces the include_js() static method, and is called on __construct and when a new config id is set on this field
	 *	Main differences are that it includes CustomHTMLEditorField.js and calls generateJS on a CustomHTMLEditorConfig instead of a regular one
	 *	When a config has been output to JS we mark it in CustomHTMLEditorConfig::$included_configs so it's not re-added by later fields
	 */
	public function includeJS() {
		require_once 'tinymce/tiny_mce_gzip.php';
		
		if ($this->editorConfigID) {
			$configObj = $this->getEditorConfig();
			$configID = $this->editorConfigID;
		} else {
			$configID = HtmlEditorConfig::$current;
			$configObj = HtmlEditorConfig::get_active();
		}
		
		if(self::$use_gzip) {
			$internalPlugins = array();
			foreach($configObj->getPlugins() as $plugin => $path) if(!$path) $internalPlugins[] = $plugin;
			$tag = TinyMCE_Compressor::renderTag(array(
				'url' => THIRDPARTY_DIR . '/tinymce/tiny_mce_gzip.php',
				'plugins' => implode(',', $internalPlugins),
				'themes' => 'advanced',
				'languages' => $configObj->getOption('language')
			), true);
			preg_match('/src="([^"]*)"/', $tag, $matches);
			Requirements::javascript($matches[1]);

		} else {
			Requirements::javascript(MCE_ROOT . 'tiny_mce_src.js');
		}
		
		Requirements::javascript('customhtmleditor/javascript/CustomHTMLEditorField.js');
		
		if (!isset(CustomHTMLEditorConfig::$included_configs[$configID])) {
			$wrap = new CustomHTMLEditorConfig($configID);
			Requirements::customScript($wrap->generateJS(), 'htmlEditorConfig'.$configID);
			CustomHTMLEditorConfig::$included_configs[$configID] = $configID;
		}
	}
	
	/**
	 * 	Tells this field which HTMLEditorConfig to use by passing in the config's identifier
	 * 	If not config is set then the field will fall back to SilverStripe's default behaviour
	 * 	
	 *	@param string $identifier
	 */
	function setEditorConfig($identifier) {
		$this->editorConfigID = $identifier;
		$this->setAttribute('data-config-id', $identifier);
		$this->includeJS();
	}
	
	/**
	 * 	Get the identifier for this field's HTMLEditorConfig
	 * 	
	 *	@return string
	 */
	function getEditorConfigID() {
		return $this->editorConfigID;
	}
	
	/**
	 * 	Return This field's HTMLEditorConfig
	 * 	
	 *	@return HTMLEditorConfig
	 */
	function getEditorConfig() {
		return HTMLEditorConfig::get($this->editorConfigID);
	}
	
	
	/**
	 * 	Convenience method for setting body classes.  Just slightly nicer than using setAttribute().
	 * 	NOTE: the specified classes will be APPENDED to the config's body_class property, not replace it
	 * 	
	 *	@param string $classes
	 */
	function setBodyClass($classes) {
		$this->setAttribute('data-body-class', $classes);
	}
	
	/**
	 * 	Return This field's body class setting
	 * 	
	 *	@return string
	 */
	function getBodyClass() {
		$this->getAttribute('data-body-class');
	}
	
}