<?php

namespace CirclicalTwigTrans\Test;

use CirclicalTwigTrans\Module;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{

    public function testCanGetModuleConfig()
    {
        $module = new Module();
        $this->assertEquals($module->getConfig(), include __DIR__ . '/../../../config/module.config.php');
    }
}