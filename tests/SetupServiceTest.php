<?php

namespace Blax\Wordpress\Tests;

use Blax\Wordpress\Services\SetupService;

class SetupServiceTest extends \PHPUnit\Framework\TestCase
{
    public function test_get_and_change_namespaces()
    {
        // get current namespace
        $originalNamespace = SetupService::getNamespaceOfFile(__FILE__);
        $artificialNamespace = 'Blax\Artificial';

        // assert current namespace
        $this->assertEquals('Blax\Wordpress\Tests', $originalNamespace);

        $this->assertTrue(SetupService::changeNamespaceOfFile(__FILE__, $artificialNamespace));
        $this->assertEquals($artificialNamespace, SetupService::getNamespaceOfFile(__FILE__));
        $this->assertTrue(SetupService::changeNamespaceOfFile(__FILE__, $originalNamespace));
    }
}
