<?php

namespace CirclicalTwigTrans\Factory;

use CirclicalTwigTrans\Model\Twig\Trans;
use CirclicalTwigTrans\Model\Twig\TransDefaultDomain;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class TransDefaultDomainFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return Trans|object
     * @throws \RuntimeException
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new TransDefaultDomain($container->get('ZfcTwigRenderer'));
    }
}
