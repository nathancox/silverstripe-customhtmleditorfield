<?php
/**
 * Extends LeftAndMain::init() to include all the HTMLEditorField configs in the initial page load
 */
class CustomHTMLEditorLeftAndMainExtension extends Extension {


	function init() {
		Requirements::javascript(basename(dirname(__DIR__)) . '/javascript/CustomHTMLEditorField.js');
		CustomHTMLEditorLeftAndMainExtension::include_js();
	}


	/**
	 * This basically merges HtmlEditorField::include_js() and HTMLEditorConfig::generateJS() to output all
	 * configuration sets to a customTinyMceConfigs javascript array.
	 * This is output in addition to the standard ssTinyMceConfig because a) we can't stop the default output
	 * with extensions; and b) the default setting is still used for any HTMLEditorField that doesn't specify
	 * it's own config.
	 *
	 * Calls Requirements::javascript() to load the scripts.
	 */
	public static function include_js() {
		require_once 'tinymce/tiny_mce_gzip.php';


		$availableConfigs = HtmlEditorConfig::get_available_configs_map();


		$pluginsForTag = array();
		$languages = array();
		//$allConfigs = array();
		$settingsJS = '';
		$externalPluginsForJS = array();

		$activeConfig = HtmlEditorConfig::get_active();

		foreach ($availableConfigs as $identifier => $friendlyName) {
			$configObj = CustomHtmlEditorConfig::get($identifier);
			$internalPluginsForJS = array();

			$configObj->getConfig()->setOption('language', i18n::get_tinymce_lang());
			if(!$configObj->getConfig()->getOption('content_css')) {
				$configObj->getConfig()->setOption('content_css', $activeConfig->getOption('content_css'));
			}


			$settings = $configObj->getSettings();


			foreach($configObj->getPlugins() as $plugin => $path) {
				if(!$path) {
					$pluginsForTag[$plugin] = $plugin;
					$internalPluginsForJS[$plugin] = $plugin;
				} else {
					$internalPluginsForJS[$plugin] = '-' . $plugin;
					$externalPluginsForJS[$plugin] = sprintf(
						'tinymce.PluginManager.load("%s", "%s");' . "\n",
						$plugin,
						$path
					);
				}
			}

			$language = $configObj->getConfig()->getOption('language');
			if ($language) {
				$languages[$language] = $language;
			}


			$settings['plugins'] = implode(',', $internalPluginsForJS);

			$buttons = $configObj->getButtons();
			foreach ($buttons as $i=>$buttons) {
				$settings['theme_advanced_buttons'.$i] = implode(',', $buttons);
			}

			$settingsJS .= "customTinyMceConfigs['" .$identifier. "'] = " . Convert::raw2json($settings) . ";\n";
		}



		if(HtmlEditorField::$use_gzip) {
			$tag = TinyMCE_Compressor::renderTag(array(
				'url' => THIRDPARTY_DIR . '/tinymce/tiny_mce_gzip.php',
				'plugins' => implode(',', $pluginsForTag),
				'themes' => 'advanced',
				'languages' => implode(',', $languages)
			), true);
			preg_match('/src="([^"]*)"/', $tag, $matches);
			Requirements::javascript($matches[1]);
		} else {
			Requirements::javascript(MCE_ROOT . 'tiny_mce_src.js');
		}

		$externalPluginsJS = implode('', $externalPluginsForJS);

		$script = <<<JS
			if((typeof tinyMCE != 'undefined')) {
				{$externalPluginsJS}

				if (typeof customTinyMceConfigs == 'undefined') {
					var customTinyMceConfigs = [];
				}
				{$settingsJS}
			}

JS;

			Requirements::customScript($script, 'htmlEditorConfigs');
	}





}
