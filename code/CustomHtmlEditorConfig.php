<?php
/**
 *	This whole thing basically exists because there's no HTMLEditorConfig::getSettings().
 *	We "wrap" the HTMLEditorConfig object in an HTMLEditorConfig subclass so it has access to the protected $settings property
 *	This actually just stores the identifier rather than the object itself because we need to know the identifier for the JS.
 *	I'm going to feel really dumb is there's a simpler way to do this
 *	
 *	
 *	$footerConfig = CustomHTMLEditorConfig::copy('sidebar', 'cms', 'Sidebar content');
 *	$footerConfig->setButtonsForLine(2, array());
 *	$footerConfig->setButtonsForLine(3, array());
 *	
 *	$fields->addFieldToTab('Root.Test', $sidebarField = new CustomHTMLEditorField('SidebarText', 'Sidebar'));
 *	$sidebarField->setEditorConfig('sidebar');
 *	
 *	
 */
class CustomHTMLEditorConfig extends HtmlEditorConfig {
	/**
	 *	The identifier string
	 */
	var $configIdentifier;
	
	/**
	 *	Tracks which configs have been included in JS to save on double ups
	 */
	static $included_configs = array();
	
	/**
	 *	Constructor is passed the identifier of teh HTMLEditorConfig we want to wrap
	 *	
	 *	@param string $identifier	 Can't use an instance of HTMLEditorConfig to create because we need to know the identifier
	 */
	function __construct($identifier) {
		$this->configIdentifier = $identifier;
	}
	
	/**
	 *	Used like HTMLEditorConfig::get() but returns the config wrapped in an CustomHTMLEditorConfig
	 *	
	 *	@param string $identifier
	 */
	static function get($identifier = 'default') {
		return new CustomHTMLEditorConfig($identifier);
	}
	
	
	/**
	 *	Creates a new config by cloning an existing one
	 *	
	 *	@param string $newIdentifier	The ID of the new config we're creating
	 *	@param mixed HtmlEditorConfig|string $old	The old config we're copying, either as an ID string or the object itself
	 *	@param string $name Option friendly name for the new config.  If left blank, friendly_name is set to the new ID
	 *	
	 *	@return HTMLEditorConfig
	 */
	static function copy($newIdentifier, $old, $name = null) {
		if (is_string($old)) {
			$old = HTMLEditorConfig::get($old);
		}
		
		HTMLEditorConfig::$configs[$newIdentifier] = clone $old;
		$newConfig = HTMLEditorConfig::get($newIdentifier);
		if ($name) {
			$newConfig->setOption('friendly_name', $name);
		} else {
			$newConfig->setOption('friendly_name', $newIdentifier);
		}
		
		return $newConfig;
	}
	
	/**
	 *	Returns the settings array of the wrapped config
	 *	
	 *	@return array
	 */
	function getSettings() {
		return $this->getConfig()->settings;
	}
	
	/**
	 *	Returns the wrapped config
	 *	
	 *	@return array
	 */
	function getConfig() {
		return HTMLEditorConfig::get($this->configIdentifier);
	}


	/**
	 *	Does basically the same thing as calling the wrapped config's generateJS with an important difference:
	 *	It assigns each config to an associative JS array (keyed by identifier) called customTinyMceConfig instead
	 *	of overwriting ssTinyMceConfig each time.  This way the CustomHTMLEditorField can use a data- attribute to
	 *	pick which config to use.
	 *	
	 *	@param string javascript to be Required::javascript()ed by CustomHTMLEditorField
	 */
	function generateJS() {
		$config = $this->getConfig();
		$settings = $config->settings;
		
		// plugins
		$internalPlugins = array();
		$externalPluginsJS = '';
		foreach($config->plugins as $plugin => $path) {
			if(!$path) {
				$internalPlugins[] = $plugin;
			} else {
				$internalPlugins[] = '-' . $plugin;
				$externalPluginsJS .= sprintf(
					'tinymce.PluginManager.load("%s", "%s");' . "\n",
					$plugin,
					$path
				);
			}
		}
		$settings['plugins'] = implode(',', $internalPlugins);
		
		foreach ($config->buttons as $i=>$buttons) {
			$settings['theme_advanced_buttons'.$i] = implode(',', $buttons);
		}
		
		return "
if((typeof tinyMCE != 'undefined')) {
	$externalPluginsJS
	
	if (typeof customTinyMceConfig == 'undefined') {
		var customTinyMceConfig = [];
	}
	customTinyMceConfig['".$this->configIdentifier."'] = " . Convert::raw2json($settings) . ";
}
";
	}
	
	
	/**
	 *	Calls generateJS() but also Requires the JS.  Basically functionality copied and pasted from CustomHTMLEditorField::includeJS();
	 *	The goal is to let us attach custom configs to regular HTMLEditorConfig by setting the data-config-id attribute and calling this like
	 *	CustomHTMLEditorConfig::get('footer')->requireJavascript();
	 */
	function requireJavascript() {
		Requirements::javascript('customhtmleditorfield/javascript/CustomHTMLEditorField.js');
		if (!isset(CustomHTMLEditorConfig::$included_configs[$this->configIdentifier])) {
			Requirements::customScript($this->generateJS(), 'htmlEditorConfig'.$this->configIdentifier);
			CustomHTMLEditorConfig::$included_configs[$this->configIdentifier] = $this->configIdentifier;
		}
	}	

};
