<?php

namespace Blax\Wordpress\Services;

class BuildService
{
    /*
     * |--------------------------------------------------------------------------
     * | Increment version number
     * |--------------------------------------------------------------------------
     * | @return bool
     * |--------------------------------------------------------------------------
     */
    public static function incrementVersion($new_version = null, $type = 'patch')
    {
        if ($new_version) {
            return PluginService::setVersion($new_version);
        } else {
            $version = explode('.', PluginService::getVersion());
            switch ($type) {
                case 'major':
                    $version[0]++;
                    $version[1] = 0;
                    $version[2] = 0;
                    break;
                case 'minor':
                    $version[1]++;
                    $version[2] = 0;
                    break;
                default:
                case 'patch':
                    $version[2]++;
                    break;
            }
            $version = implode('.', $version);
            return PluginService::setVersion($version);
        }
    }
}
