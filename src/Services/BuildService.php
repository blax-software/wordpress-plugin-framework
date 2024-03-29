<?php

namespace Blax\Wordpress\Services;

class BuildService
{
	private static $instance;
	private static $zip;

	/*
     * |--------------------------------------------------------------------------
     * | construct
     * |--------------------------------------------------------------------------
     * | @return BuildService
     * |--------------------------------------------------------------------------
     */
	public function __construct()
	{
		if (static::$instance) return static::$instance;
		static::$instance = $this;
	}

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

	/*
     * |--------------------------------------------------------------------------
     * | Get current ZIP
     * |--------------------------------------------------------------------------
     * | @return ZipArchive
     * |--------------------------------------------------------------------------
     */
	public static function getZip()
	{
		if (!self::$zip) {
			self::$zip = new \ZipArchive();
			self::$zip->open(str_replace('.php', '.zip', PluginService::getPluginFile()), \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
		}
		return self::$zip;
	}

	/*
     * |--------------------------------------------------------------------------
     * | Adds file to ZIP
     * |--------------------------------------------------------------------------
     * | @return bool
     * |--------------------------------------------------------------------------
     */
	public static function addFileToZip($relative_path, $location_in_zip = null)
	{
		$adding = ($relative_path[0] == '/')
			? $relative_path
			: PluginService::getPluginDir() . $relative_path;

		if (!file_exists($adding)) {
			print('File ' . $adding . ' does not exist' . PHP_EOL);
			return false;
		}

		if (!$location_in_zip) {
			if (strpos($relative_path, PluginService::getPluginDir()) >= 0) {
				$location_in_zip = str_replace(PluginService::getPluginDir(), '', $relative_path);
			}
		}

		return self::getZip()->addFile($adding, $location_in_zip);
	}

	/*
     * |--------------------------------------------------------------------------
     * | Adds folder to ZIP
     * |--------------------------------------------------------------------------
     * | @return bool
     * |--------------------------------------------------------------------------
     */
	public static function addFolderToZip($relative_path)
	{
		$files = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator(PluginService::getPluginDir() . $relative_path),
			\RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($files as $name => $file) {
			// Skip directories (they would be added automatically)
			if (!$file->isDir()) {
				if (strpos($file->getRealPath(), 'node_modules') !== false) continue;

				$filePath = $file->getRealPath();
				$zipLocation = str_replace(PluginService::getPluginDir(), '', $file->getRealPath());

				self::getZip()->addFile($filePath, $zipLocation);
			}
		}
	}

	/*
     * |--------------------------------------------------------------------------
     * | Closes ZIP
     * |--------------------------------------------------------------------------
     * | @return bool
     * |--------------------------------------------------------------------------
     */
	public static function closeZip()
	{
		return self::getZip()->close();
	}
}
