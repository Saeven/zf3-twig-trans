<?php

declare(strict_types=1);

namespace CirclicalTwigTrans\Factory;

use CirclicalTwigTrans\Model\Twig\TransDefaultDomain;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class TransDefaultDomainFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new TransDefaultDomain($container->get('ZfcTwigRenderer'));
    }
}
