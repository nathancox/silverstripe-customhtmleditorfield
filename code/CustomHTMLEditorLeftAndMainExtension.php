<?php
/**
 * Extends LeftAndMain::init() to include all the HTMLEditorField configs in the initial page load
 */
class CustomHTMLEditorLeftAndMainExtension extends Extension
{
    public function init()
    {
        Requirements::javascript(basename(dirname(__DIR__)) . '/javascript/CustomHTMLEditorField.js');
        
        // this is mostly copied from LeftAndMain::init() but modified to apply to all configs, not just
        // the active one
        $availableConfigs = HtmlEditorConfig::get_available_configs_map();

        foreach ($availableConfigs as $identifier => $friendlyName) {
            $htmlEditorConfig = HtmlEditorConfig::get($identifier);

            $htmlEditorConfig->setOption('language', i18n::get_tinymce_lang());
            if (!$htmlEditorConfig->getOption('content_css')) {
                $cssFiles = array();
                $cssFiles[] = FRAMEWORK_ADMIN_DIR . '/css/editor.css';

                // Use theme from the site config
                if (class_exists('SiteConfig') && ($config = SiteConfig::current_site_config()) && $config->Theme) {
                    $theme = $config->Theme;
                } elseif (Config::inst()->get('SSViewer', 'theme_enabled') && Config::inst()->get('SSViewer', 'theme')) {
                    $theme = Config::inst()->get('SSViewer', 'theme');
                } else {
                    $theme = false;
                }

                if ($theme) {
                    $cssFiles[] = THEMES_DIR . "/{$theme}/css/editor.css";
                } elseif (project()) {
                    $cssFiles[] = project() . '/css/editor.css';
                }

                // Remove files that don't exist
                foreach ($cssFiles as $k => $cssFile) {
                    if (!file_exists(BASE_PATH . '/' . $cssFile)) {
                        unset($cssFiles[$k]);
                    }
                }

                $htmlEditorConfig->setOption('content_css', implode(',', $cssFiles));
            }
        }
    }
}
