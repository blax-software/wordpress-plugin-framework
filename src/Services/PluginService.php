<?php

namespace Blax\Wordpress\Services;

class PluginService
{
    public static $plugin_file;

    /*
     * |--------------------------------------------------------------------------
     * | Gets the plugin dir
     * |--------------------------------------------------------------------------
     * | @return string - The absolute plugin  dir
     * |--------------------------------------------------------------------------
     */
    public static function getPluginDir()
    {
        $pluginFile = __DIR__;

        // if pluginFile contains /vendor remove it and all characters after
        if (strpos($pluginFile, '/vendor') !== false) {
            $pluginFile = substr($pluginFile, 0, strpos($pluginFile, '/vendor'));
        }

        return $pluginFile;
    }

    /*
     * |--------------------------------------------------------------------------
     * | Gets the plugin file
     * |--------------------------------------------------------------------------
     * | @return string - The absolute plugin file path
     * |--------------------------------------------------------------------------
     */
    public static function getPluginFile($path = null)
    {
        $plugin_dir = ($path ?? static::getPluginDir());

        // show files inside plugin dir
        $files = scandir($plugin_dir);

        // remove all files not ending with .php
        foreach ($files as $key => $file) {
            if (strpos($file, '.php') === false) {
                unset($files[$key]);
            }
        }

        // get first file same named as plugin dir
        $same_name_file = ($plugin_dir) . '/' . basename($plugin_dir) . '.php';

        // if file exists 
        if (file_exists($same_name_file)) {
            return $same_name_file;
        }

        // read all $files and check if any file contains "Plugin Name:"
        foreach ($files as $file) {
            $file_content = file_get_contents($plugin_dir . '/' . $file);
            if (strpos($file_content, 'Plugin Name:') !== false) {
                return $plugin_dir . '/' . $file;
            }
        }

        return false;
    }

    /*
     * |--------------------------------------------------------------------------
     * | Gets the plugin file content
     * |--------------------------------------------------------------------------
     * | @return string - The content
     * |--------------------------------------------------------------------------
     */
    public static function getPluginFileContent()
    {
        return file_get_contents(static::getPluginFile());
    }

    /*
     * |--------------------------------------------------------------------------
     * | Sets the plugin file content
     * |--------------------------------------------------------------------------
     * | @return bool - The success
     * |--------------------------------------------------------------------------
     */
    public static function setPluginFileContent($content)
    {
        static::getPluginFileContent();
        return file_put_contents(static::getPluginFile(), $content);
    }


    /*
     * |--------------------------------------------------------------------------
     * | Gets the absolute plugin class
     * |--------------------------------------------------------------------------
     * | @return string - The absolute plugin class
     * |--------------------------------------------------------------------------
     */
    public static function getPluginClass()
    {
        $plugin_file = static::getPluginFile();

        $regex = '/(?<=class\s).+(?=\sextends\s)/';
        preg_match($regex, static::getPluginFileContent(), $matches);

        return SetupService::getNamespaceOfFile($plugin_file) . '\\' . $matches[0];
    }


    /*
     * |--------------------------------------------------------------------------
     * | Get current plugin version number
     * |--------------------------------------------------------------------------
     * | @return string
     * |--------------------------------------------------------------------------
     */
    public static function getVersion()
    {
        $regex = '/(?<=Version:).+(?=\n|\s)/';
        preg_match($regex, static::getPluginFileContent(), $matches);

        return str_replace(' ', '', $matches[0]);
    }

    /*
     * |--------------------------------------------------------------------------
     * | Set current plugin version number
     * |--------------------------------------------------------------------------
     * | @return bool
     * |--------------------------------------------------------------------------
     */
    public static function setVersion($new_version)
    {
        $plugin_file_content = static::getPluginFileContent();

        $regex = '/(?<=Version:).+(?=\n|\s)/';
        preg_match($regex, $plugin_file_content, $matches);

        $whitespaces = explode(static::getVersion(), $matches[0])[0];

        $plugin_file_content = preg_replace($regex, $whitespaces . $new_version, $plugin_file_content, 1);

        return file_put_contents(static::getPluginFile(), $plugin_file_content);
    }

    /* 
     * |--------------------------------------------------------------------------
     * | Get plugin meta
     * |--------------------------------------------------------------------------
     * | @return string
     * |--------------------------------------------------------------------------
     */
    public static function getPluginMeta($meta_key)
    {
        $regex = '/(?<=' . $meta_key . ':).+(?=\n|\s)/';
        preg_match($regex, static::getPluginFileContent(), $matches);

        return trim($matches[0]);
    }

    /*
     * |--------------------------------------------------------------------------
     * | Set plugin meta
     * |--------------------------------------------------------------------------
     * | @return bool
     * |--------------------------------------------------------------------------
     */
    public static function setPluginMeta($meta_key, $meta_value)
    {
        $plugin_file_content = static::getPluginFileContent();

        $regex = '/(?<=' . $meta_key . ':).+(?=\n|\s)/';
        preg_match($regex, $plugin_file_content, $matches);

        $whitespaces = str_replace(static::getPluginMeta($meta_key), '', $matches[0]);

        $plugin_file_content = preg_replace($regex, $whitespaces . $meta_value, $plugin_file_content, 1);

        return file_put_contents(static::getPluginFile(), $plugin_file_content);
    }
}
