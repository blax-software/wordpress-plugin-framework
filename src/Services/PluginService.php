<?php

namespace Blax\Wordpress\Services;

class PluginService
{
    /*
     * |--------------------------------------------------------------------------
     * | Gets the plugin file
     * |--------------------------------------------------------------------------
     * | @return string - The absolute plugin file path
     * |--------------------------------------------------------------------------
     */
    public static function getPluginFile()
    {
        // go back until there is a file same named as directory
        $pluginFile = __DIR__;
        while (!file_exists($pluginFile . '/' . basename($pluginFile) . '.php')) {
            if (dirname($pluginFile) == $pluginFile) {
                break;
            }
            $pluginFile = dirname($pluginFile);
        }

        // if plugin file not found try to check php files for plugin description in head
        if ($pluginFile == dirname($pluginFile)) {

            $pluginFile = __DIR__;
            $pluginFile_found = false;

            while (!$pluginFile_found) {
                $files = scandir($pluginFile);

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
                    echo $pluginFile . '/' . $file . PHP_EOL;
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
        }

        return ($pluginFile . '/' . basename($pluginFile) . '.php');
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
        // TODO
    }
}
