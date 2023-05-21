<?php

namespace Blax\Wordpress\Extendables;

class Plugin
{
    private static $instance = null;

    public static $plugin_name = null;
    public static $plugin_absolute_path = null;
    public static $plugin_loaded_classes = [];
    public static $plugin_translation_domain = null;

    /*
     * |--------------------------------------------------------------------------
     * | Constructs the plugin extendable
     * |--------------------------------------------------------------------------
     * |
     * | This method loads all classes from the plugin's composer autoload_classmap
     * | and invokes each class. This is useful for classes that need to be
     * | instantiated on plugin load.
     * |
     */
    function __construct()
    {
        if (self::$instance) return self::$instance;
        self::$instance = $this;

        static::$plugin_name = basename(__DIR__);
        static::$plugin_absolute_path = __DIR__;
        static::$plugin_translation_domain = basename(__DIR__);

        self::loadI18n();
        self::loadClasses();
    }

    /*
     * |--------------------------------------------------------------------------
     * | Returns a variable from the plugin's instance
     * |--------------------------------------------------------------------------
     * |
     * | This method returns a variable from the plugin's instance. This is useful
     * | for accessing variables from the plugin's instance.
     * |
     */
    public static function getVar($var, $default = null)
    {
        return self::$instance->$var ?? $default;
    }

    /*
     * |--------------------------------------------------------------------------
     * | Loads the plugin's i18n
     * |--------------------------------------------------------------------------
     * |
     * | This method loads the plugin's i18n. This is useful for translating
     * | strings in the plugin.
     * |
     */
    public static function loadI18n()
    {
        // TODO
    }

    /*
     * |----------------------------------------------------------------------- ---
     * | Loads and invokes each class
     * |--------------------------------------------------------------------------
     * |
     * | This method loads all classes from the plugin's composer autoload_classmap
     * | and invokes each class. This is useful for classes that need to be
     * | instantiated on plugin load.
     * |
     */
    public static function loadClasses()
    {
        $plugin_classes = (require static::$plugin_absolute_path . '/vendor/composer/autoload_classmap.php');
        foreach ($plugin_classes as $class => $path) {
            if (
                (strpos($class, '\\Includes') !== false && strpos($class, '\\Services') === false)
                || strpos($class, 'nterface') !== false
                || strpos($class, /* current namespace */ __NAMESPACE__) === false
            ) {
                unset($plugin_classes[$class]);
            }
        }

        foreach ($plugin_classes as $class => $path) {
            try {
                new $class();
                static::$plugin_loaded_classes[] = $class;
            } catch (\Throwable $th) {
            }
        }
    }

    /*
     * |--------------------------------------------------------------------------
     * | Logs a message
     * |--------------------------------------------------------------------------
     * |
     * | This method logs a message to the logfile in the plugin's root
     * |
     */
    public static function log()
    {
        // TODO
    }
}
