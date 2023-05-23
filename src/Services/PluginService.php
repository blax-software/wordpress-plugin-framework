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
        $pluginFile = ($path ?? static::getPluginDir());
        $pluginFile_found = false;

        $debug_output = function ($message) {
            echo PHP_EOL . $message;
        };

        while (!file_exists($pluginFile . '/' . basename($pluginFile) . '.php')) {
            $debug_output('Try: ' . $pluginFile);
            if (dirname($pluginFile) == $pluginFile) {
                break;
            }
            $pluginFile = dirname($pluginFile);
            $debug_output('New: ' . $pluginFile);
        }

        // if plugin file not found try to check php files for plugin description in head
        if ($pluginFile == dirname($pluginFile)) {

            $pluginFile = ($path ?? static::getPluginDir());
            $pluginFile_found = false;


            while (!$pluginFile_found) {
                $files = scandir($pluginFile);
                $debug_output('Scan: ' . $pluginFile);

                // exclude files starting with .
                $files = array_filter($files, function ($file) {
                    if ($file[0] == '.') {
                        return false;
                    }

                    if (is_dir($file)) {
                        return false;
                    }

                    if (strpos($file, '.php') === false) {
                        return false;
                    }

                    return true;
                });


                foreach ($files as $file) {
                    $debug_output('Check: ' . $file);
                    if (strpos($file, '.php') !== false) {
                        $fileContent = file_get_contents($pluginFile . '/' . $file);
                        if (
                            strpos($fileContent, 'Plugin Name:') !== false
                            && __FILE__ != $pluginFile . '/' . $file
                        ) {
                            $pluginFile_found = true;
                            $pluginFile = $pluginFile . '/' . $file;
                            break;
                        }
                        unset($fileContent);
                    }
                }

                $pluginFile = dirname($pluginFile);

                if ($pluginFile == dirname($pluginFile)) {
                    break;
                }
            }

            $debug_output('Found: ' . $pluginFile);

            $pluginFile_found = file_exists($pluginFile . '/' . basename($pluginFile) . '.php');

            return ($pluginFile_found)
                ? ($pluginFile . '/' . basename($pluginFile) . '.php')
                : false;
        }
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

        $version = preg_match('/\* Version: \d+\.\d+\.\d+/', $plugin_file_content, $matches);

        return (!$version)
            ? '0.0.0'
            : $version;
    }

    /*
     * |--------------------------------------------------------------------------
     * | Set current plugin version number
     * |--------------------------------------------------------------------------
     * | @return bool
     * |--------------------------------------------------------------------------
     * // TODO test
     */
    public static function setVersion($version)
    {
        $plugin_file_content = file_get_contents(static::getPluginFile());

        $version = preg_replace('/(\* Version:)(\s+)(\d+\.\d+\.\d+)/', '$1 $2' . $version, $plugin_file_content);

        return (!$version)
            ? '0.0.0'
            : $version;
    }
}
