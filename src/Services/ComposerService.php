<?php

namespace Blax\Wordpress\Services;

class ComposerService
{
	public static $json = null;

	/*
     * |--------------------------------------------------------------------------
     * | Gets absolute path of composer.json
     * |--------------------------------------------------------------------------
     */
	public static function getAbsolutePath($absolute_path_to_composer = null)
	{
		return $absolute_path_to_composer ?? (dirname(PluginService::getPluginFile()) . '/composer.json');
	}

	/*
     * |--------------------------------------------------------------------------
     * | Gets json of composer.json
     * |--------------------------------------------------------------------------
     */
	public static function getJson($absolute_path_to_composer = null)
	{
		$path = static::getAbsolutePath($absolute_path_to_composer);
		$content = file_get_contents($path);

		$r = (static::$json = json_decode($content, true));
		if ($r == null) {
			throw new \Exception('Could not parse composer.json from: ' . $path);
		}
		return $r;
	}

	/*
     * |--------------------------------------------------------------------------
     * | Sets json of composer.json
     * |--------------------------------------------------------------------------
     */
	public static function setJson($json, $absolute_path_to_composer = null)
	{
		static::$json = $json;
		return (file_put_contents(static::getAbsolutePath($absolute_path_to_composer), json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false);
	}

	/*
     * |--------------------------------------------------------------------------
     * | Get the psr-4 namespace of composer.json
     * |--------------------------------------------------------------------------
     */
	public static function getNamespace($absolute_path_to_composer = null)
	{
		$json = static::getJson($absolute_path_to_composer);
		$psr = $json['autoload']['psr-4'];
		return array_keys($psr)[0];
	}

	/*
	 * |--------------------------------------------------------------------------
	 * | Set the psr-4 namespace of composer.json
	 * |--------------------------------------------------------------------------
	 */
	public static function setNamespace($namespace, $absolute_path_to_composer = null)
	{
		$json = static::getJson($absolute_path_to_composer);
		$value = array_values($json['autoload']['psr-4'])[0];
		$json['autoload']['psr-4'] = [$namespace => $value];
		return static::setJson($json, $absolute_path_to_composer);
	}
}
