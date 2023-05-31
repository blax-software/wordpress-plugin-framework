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
	public static function replaceNamespaceOfFile($filePath, $oldNamespace, $newNamespace, $replace_use = false)
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

		if ($replace_use) {
			// TODO
		}

		// Write the modified content back to the PHP file
		file_put_contents($filePath, $modifiedContent);

		return true;
	}

	/*
     * |--------------------------------------------------------------------------
     * | Replace the use of a namespace
     * |--------------------------------------------------------------------------
     */
	public static function replaceUseNamespaceOfFile($filePath, $oldNamespace, $newNamespace)
	{
		// Read the contents of the PHP file
		$fileContent = file_get_contents($filePath);

		if (substr($oldNamespace, -1) == '\\') {
			$oldNamespace = substr($oldNamespace, 0, -1);
		}
		if (substr($newNamespace, -1) == '\\') {
			$newNamespace = substr($newNamespace, 0, -1);
		}

		$oldNamespace = str_replace('\\', '\\\\', $oldNamespace);
		$newNamespace = str_replace('\\', '\\\\', $newNamespace);

		$modifiedContent = $fileContent;

		// use
		$modifiedContent = preg_replace(
			'/use[\s]+' . $oldNamespace . '/m',
			'use ' . $newNamespace . '',
			$modifiedContent
		);

		$modifiedContent = preg_replace(
			'/use \\\\' . $oldNamespace . '/m',
			'use \\' . $newNamespace . '',
			$modifiedContent
		);

		$modifiedContent = preg_replace(
			'/extends[\s]\\\\' . $oldNamespace . '/m',
			'extends \\' . $newNamespace . '',
			$modifiedContent
		);

		$modifiedContent = preg_replace(
			'/extends[\s]+' . $oldNamespace . '/m',
			'extends ' . $newNamespace . '',
			$modifiedContent
		);

		// Write the modified content back to the PHP file
		file_put_contents($filePath, $modifiedContent);

		return true;
	}
}
