<?php
/**
 *	This basically just exists to give us getter methods for protected properties of HTMLEditorConfig
 *	We "wrap" the HTMLEditorConfig object in an HTMLEditorConfig subclass so it has access to the protected $settings property
 *	This actually just stores the identifier rather than the object itself because we need to know the identifier for the JS.
 *	
 *	It also provides a copy() static method that duplicates editor configs for convenience
 *	
 */
class CustomHTMLEditorConfig extends HTMLEditorConfig {
	
	/**
	 *	The identifier string
	 */
	public $configIdentifier;
	
	
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
	 *	Creates a new config by cloning an existing one.  Just a helplful utility function
	 *	
	 *	@param string $newIdentifier	The ID of the new config we're creating
	 *	@param mixed HtmlEditorConfig|string $old	The old config we're copying, either as an ID string or the object itself
	 *	@param string $name Option friendly name for the new config.  If left blank, friendly_name is set to the new ID
	 *	
	 *	@return HTMLEditorConfig
	 */
	 /*
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
	*/
	
	static function copy($newIdentifier, $old, $name = null) {
	
		if (is_string($old)) {
		//	$old = CustomHTMLEditorConfig::get($old);
			$old = HTMLEditorConfig::get($old);
		}
		

	//	info(Config::inst()->get('HtmlEditorFieldConfig', 'configs'));
		
		
//		HTMLEditorConfig::$configs[$newIdentifier] = clone $old;
		$newConfig = HTMLEditorConfig::get($newIdentifier);
		
	//	$settings = $old->getSettings();
		foreach ($old->settings as $key => $value) {
			$newConfig->setOption($key, $value);
		}
		
		$plugins = $old->getPlugins();
		$newConfig->plugins = $plugins;

		$buttons = $old->buttons;


		$newConfig->buttons = $buttons;

		
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
	 *	@return HtmlEditorConfig
	 */
	function getConfig() {
		return HTMLEditorConfig::get($this->configIdentifier);
	}



	/**
	 *	Returns the buttons array of the wrapped config
	 *	
	 *	@return array
	 */
	function getButtons() {
		return $this->getConfig()->buttons;
	}


	/**
	 * Returns the plugins array of the wrapped config
	 *
	 * @return array
	 */
	function getPlugins() {
		return $this->getConfig()->plugins;
	}


	
}
