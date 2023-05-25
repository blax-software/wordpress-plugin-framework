<?php

namespace Blax\Wordpress\Services;

class PluginService
{
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
     * | Get current plugin version number
     * |--------------------------------------------------------------------------
     * | @return string
     * |--------------------------------------------------------------------------
     */
    public static function getVersion()
    {
        $plugin_file_content = file_get_contents(static::getPluginFile());

        $regex = '/(?<=Version:).+(?=\n|\s)/';
        preg_match($regex, $plugin_file_content, $matches);

        return str_replace(' ', '', $matches[0]);
    }

    /*
     * |--------------------------------------------------------------------------
     * | Set current plugin version number
     * |--------------------------------------------------------------------------
     * | @return bool
     * |--------------------------------------------------------------------------
     * // TODO test
     */
    public static function setVersion($new_version)
    {
        $plugin_file_content = file_get_contents(static::getPluginFile());
        $current_version = static::getVersion();

        $regex = '/(?<=Version:).+(?=\n|\s)/';
        preg_match($regex, $plugin_file_content, $matches);

        $whitespaces = explode($current_version, $matches[0])[0];

        $plugin_file_content = preg_replace($regex, $whitespaces . $new_version, $plugin_file_content, 1);

        return file_put_contents(static::getPluginFile(), $plugin_file_content);
    }
}
