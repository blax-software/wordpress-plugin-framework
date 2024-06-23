<?php

namespace Blax\Wordpress\Services;

class LoggerService
{
	public static $logger_path = null;

	/*
	 * |--------------------------------------------------------------------------
	 * | Gets the log file
	 * |--------------------------------------------------------------------------
	 * | @return string - The absolute log file path
	 * |--------------------------------------------------------------------------
	 */
	public static function getLogFile($file_name = null)
	{
		$dir = PluginService::getPluginDir();

		if ($file_name) {
			// if folder logs does not exist create it
			if (!file_exists($dir . '/logs')) {
				try{
					ob_start();
					if(!@mkdir($dir . '/logs')){
						throw new \Exception();
					}
				}catch(\Exception $e){
					static::showAdminError('['.PluginService::getPluginMeta('name').'] Could not create logs folder with permissions 777. Please create it manually in the root of the plugin.');
				}finally{
					ob_end_clean();
				}
			}

			// if file does not end with .log add to it
			if (strpos($file_name, '.log') === false) {
				$file_name .= '.log';
			}

			if (!file_exists($dir . '/logs/' . $file_name)) {
				@file_put_contents($dir . '/logs/' . $file_name, '');
			}
			return $dir . '/logs/' . $file_name;
		} else {
			if (!file_exists($dir . '/log.log')) {
				@file_put_contents($dir . '/log.log', '');
			}
			return $dir . '/log.log';
		}
	}

	/*
	 * |--------------------------------------------------------------------------
	 * | Sets the channel
	 * |--------------------------------------------------------------------------
	 */
	public static function channel($channel = null)
	{
		static::$logger_path = static::getLogFile($channel);
		return new static;
	}

	/*
	 * |--------------------------------------------------------------------------
	 * | Logs a message to the log file
	 * |--------------------------------------------------------------------------
	 */
	public static function log($info, $context = [])
	{
		$path = static::$logger_path ?? static::getLogFile();

		$log_prefix = '[' . date('Y-m-d H:i:s') . '] ';

		$log = $log_prefix . $info . ($context ? ' ' . json_encode($context) : '') . PHP_EOL;

		@file_put_contents($path, $log, FILE_APPEND);
	}

	/*
	 * |--------------------------------------------------------------------------
	 * | Clears current channel or channel from argument
	 * |--------------------------------------------------------------------------
	 */
	public static function clear($channel = null)
	{
		$path = static::$logger_path ?? static::getLogFile($channel);

		@file_put_contents($path, '');

		return new static;
	}

	/*
	 * |--------------------------------------------------------------------------
	 * | Gets current log content
	 * |--------------------------------------------------------------------------
	 */
	public static function getContent($channel = null)
	{
		$path = static::$logger_path ?? static::getLogFile($channel);

		return @file_get_contents($path);
	}

	/*
	 * |--------------------------------------------------------------------------
	 * | Show admin error message
	 * |--------------------------------------------------------------------------
	 */
	public static function showAdminError($message)
	{
		return add_action('admin_notices', function() use ($message) {
			echo '<div class="notice notice-error is-dismissible">
					<p>' . esc_html($message) . '</p>
				  </div>';
		});
	}
}
