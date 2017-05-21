<?php
/**
 *  This basically just exists to give us getter methods for protected properties of HTMLEditorConfig
 *  We "wrap" the HTMLEditorConfig object in an HTMLEditorConfig subclass so it has access to the
 *  protected $settings property
 *  This actually just stores the identifier rather than the object itself because we need to know
 *  the identifier for the JS.
 *
 *  It also provides a copy() static method that duplicates editor configs for convenience
 *
 */
class CustomHTMLEditorConfig extends HTMLEditorConfig
{

    /**
     *  The identifier string
     */
    public $configIdentifier;

    /**
     *  Constructor is passed the identifier of teh HTMLEditorConfig we want to wrap
     *
     *  @param string $identifier    Can't use an instance of HTMLEditorConfig to create
     *                               because we need to know the identifier
     */
    function __construct($identifier)
    {
        $this->configIdentifier = $identifier;
    }

    /**
     *  Used like HTMLEditorConfig::get() but returns the config wrapped in an CustomHTMLEditorConfig
     *
     *  @param string $identifier
     */
    public static function get($identifier = 'default')
    {
        return new CustomHTMLEditorConfig($identifier);
    }

    /**
     *  Creates a new config by cloning an existing one.  Just a helplful utility function
     *
     *  @param string $newIdentifier    The ID of the new config we're creating
     *  @param mixed HtmlEditorConfig|string $old   The old config we're copying,
     *         either as an ID string or the object itself
     *  @param string $name Option friendly name for the new config.  If left blank, friendly_name
     *         is set to the new ID
     *
     *  @return HTMLEditorConfig
     */
    public static function copy($newIdentifier, $old, $name = null)
    {

        if (is_string($old)) {
            $old = HTMLEditorConfig::get($old);
        }

        $newConfig = HTMLEditorConfig::get($newIdentifier);

        foreach ($old->settings as $key => $value) {
            $newConfig->setOption($key, $value);
        }

        $newConfig->plugins = $old->getPlugins();

        $newConfig->buttons = $old->buttons;


        if ($name) {
            $newConfig->setOption('friendly_name', $name);
        } else {
            $newConfig->setOption('friendly_name', $newIdentifier);
        }

        return $newConfig;
    }

    /**
     *  Returns the settings array of the wrapped config
     *
     *  @return array
     */
    public function getSettings()
    {
        return $this->getConfig()->settings;
    }

    /**
     *  Returns the wrapped config
     *
     *  @return HtmlEditorConfig
     */
    public function getConfig()
    {
        return HTMLEditorConfig::get($this->configIdentifier);
    }

    /**
     *  Returns the buttons array of the wrapped config
     *
     *  @return array
     */
    public function getButtons()
    {
        return $this->getConfig()->buttons;
    }

    /**
     * Returns the plugins array of the wrapped config
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->getConfig()->plugins;
    }
}
