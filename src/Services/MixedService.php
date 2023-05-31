<?php

namespace Blax\Wordpress\Services;

class Mixedservice
{
	public static function getAllFiles($directory)
	{
		$files = [];

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::SELF_FIRST
		);

		foreach ($iterator as $file) {
			if ($file->isFile()) {
				$files[] = $file->getPathname();
			}
		}

		return $files;
	}
}
