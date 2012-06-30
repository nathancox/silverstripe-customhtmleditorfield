<?php
/**
 *	A subclass of HTMLEditorField that lets you set editor-specific body class names and TinyMCE configs
 *	
 */
class CustomHtmlEditorFieldExtension extends Extension {
	
	public function __construct() {
		parent::__construct();
	}
	
	
	public function includeJS() {
		require_once 'tinymce/tiny_mce_gzip.php';
		
		if ($configID = $this->getEditorConfigID()) {
			$configObj = $this->getEditorConfig();
		} else {
			$configID = HtmlEditorConfig::$current;
			$configObj = HtmlEditorConfig::get_active();
		}
		
		if(HtmlEditorField::$use_gzip) {
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
		
		
		$wrap = new CustomHTMLEditorConfig($configID);
		$wrap->requireJavascript();
		
	//	Requirements::javascript('customhtmleditor/javascript/CustomHTMLEditorField.js');
	}
	
	
	
	/**
	 * 	
	 *	@param string $identifier
	 */
	function setEditorConfig($identifier) {
		$this->owner->setAttribute('data-config-id', $identifier);
		$this->includeJS();
	}
	
	/**
	 * 	Get the identifier for this field's HTMLEditorConfig
	 * 	
	 *	@return string
	 */
	function getEditorConfigID() {
		return $this->owner->getAttribute('data-config-id');
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