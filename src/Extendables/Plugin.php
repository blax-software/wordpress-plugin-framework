<?php

namespace Blax\Wordpress\Extendables;

abstract class Plugin
{
    private static $instance = null;

    public static $plugin_name = null;
    public static $plugin_absolute_path = null;
    public static $plugin_loaded_classes = [];
    public static $plugin_translation_domain = null;
    public static $plugin_namespace = null;

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

        $reflection = new \ReflectionClass(static::class);
        $plugin_file = $reflection->getFileName();
        $plugin_dir = dirname($plugin_file);

        // child namespace
        static::$plugin_namespace = static::$plugin_namespace ?? $reflection->getNamespaceName();
        static::$plugin_name = basename($plugin_dir);
        static::$plugin_absolute_path = $plugin_dir;
        static::$plugin_translation_domain = basename($plugin_dir);

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
                || strpos($class, /* current namespace */ static::$plugin_namespace) === false
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

    /*
     * |--------------------------------------------------------------------------
     * | Checks if is inside Wordpress environment
     * |--------------------------------------------------------------------------
     * |
     * | This method logs a message to the logfile in the plugin's root
     * |
     */
    public static function wordpress()
    {
        return [
            'is_plugin' => strpos(getcwd(), 'wp-content/plugins') !== false,
            'is_theme' => strpos(getcwd(), 'wp-content/themes') !== false,
            'is_loaded' => defined('ABSPATH'),
            'is_active' => function_exists('add_action'),
        ];
    }
}
