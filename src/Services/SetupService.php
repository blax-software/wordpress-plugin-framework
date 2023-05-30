<?php

namespace Blax\Wordpress\Services;

class SetupService
{
	/*
     * |--------------------------------------------------------------------------
     * | Changes namespace of a PHP file
     * |--------------------------------------------------------------------------
     * | @param string $filePath
     * | @param string $newNamespace
     * | @return bool
     * |--------------------------------------------------------------------------
     * |
     * | Only changes the namespace of the PHP file if the namespace is found
     * |
     */
	public static function changeNamespaceOfFile($filePath, $newNamespace)
	{
		// Read the contents of the PHP file
		$fileContent = file_get_contents($filePath);

		// Replace the existing namespace with the new namespace
		$modifiedContent = preg_replace(
			'/^namespace\s+([^\s;]+)/m',
			'namespace ' . $newNamespace . '',
			$fileContent,
			1
		);

		// Write the modified content back to the PHP file
		file_put_contents($filePath, $modifiedContent);

		return true;
	}

	/*
     * |--------------------------------------------------------------------------
     * | Get the namespace of a PHP file
     * |--------------------------------------------------------------------------
     */
	public static function getNamespaceOfFile($filePath)
	{
		// Read the contents of the PHP file
		$fileContent = file_get_contents($filePath);

		// Get the namespace from the PHP file
		preg_match('/^namespace\s+([^\s;]+)/m', $fileContent, $matches);

		// Return the namespace
		return $matches[1];
	}

	/*
     * |--------------------------------------------------------------------------
     * | Gets the namespace of composer.json
     * |--------------------------------------------------------------------------
     */
	public static function getNamespaceFromComposer($absolute_path_to_composer = null)
	{
		return ComposerService::getNamespace($absolute_path_to_composer);
	}

	/*
     * |--------------------------------------------------------------------------
     * | Replace the namespace of a PHP file
     * |--------------------------------------------------------------------------
     */
	public static function replaceNamespaceOfFile($filePath, $oldNamespace, $newNamespace)
	{
		// Read the contents of the PHP file
		$fileContent = file_get_contents($filePath);

		$oldNamespace = str_replace('\\', '\\\\', $oldNamespace);
		$newNamespace = str_replace('\\', '\\\\', $newNamespace);

		$modifiedContent = preg_replace(
			'/^namespace\s+' . $oldNamespace . '/m',
			'namespace ' . $newNamespace . '',
			$fileContent,
			1
		);

		// Write the modified content back to the PHP file
		file_put_contents($filePath, $modifiedContent);

		return true;
	}
}
