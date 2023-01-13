<?php

declare(strict_types=1);

namespace CirclicalTwigTrans;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
