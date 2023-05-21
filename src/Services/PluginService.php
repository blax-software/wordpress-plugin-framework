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
    public static function getPluginFile()
    {
        $pluginFile = static::getPluginDir();

        while (!file_exists($pluginFile . '/' . basename($pluginFile) . '.php')) {
            if (dirname($pluginFile) == $pluginFile) {
                break;
            }
            $pluginFile = dirname($pluginFile);
        }

        // if plugin file not found try to check php files for plugin description in head
        if ($pluginFile == dirname($pluginFile)) {

            $pluginFile = static::getPluginDir();
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
echo PluginService::getPluginFile();
