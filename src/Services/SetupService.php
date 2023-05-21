<?php

namespace Blax\Wordpress\Service;

class SetupService
{
    /*
     * |--------------------------------------------------------------------------
     * | Changes namespace of a PHP file
     * |--------------------------------------------------------------------------
     */
    public static function changeNamespaceOfFile($filePath, $newNamespace)
    {
        // Read the contents of the PHP file
        $fileContent = file_get_contents($filePath);

        // Replace the existing namespace with the new namespace
        $modifiedContent = preg_replace(
            '/^namespace\s+([^\s;]+)/m',
            'namespace ' . $newNamespace . ';',
            $fileContent
        );

        // Write the modified content back to the PHP file
        file_put_contents($filePath, $modifiedContent);
    }
}
