<?php

namespace CirclicalTwigTrans\Factory;

use CirclicalTwigTrans\Model\Twig\Trans;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TransFactory implements FactoryInterface
{
    const DOMAIN = 'text_domain';
    const DOMAIN_DEFAULT = 'default';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');

        foreach ($config['translator']['translation_file_patterns'] as $trcfg) {
            if (!array_key_exists(self::DOMAIN, $trcfg)) {
                $trcfg[self::DOMAIN] = self::DOMAIN_DEFAULT;
            }
            bindtextdomain($trcfg[self::DOMAIN], realpath($trcfg['base_dir']) . '/');
            bind_textdomain_codeset($trcfg[self::DOMAIN], 'UTF-8');
        }

        textdomain("default");

        return new Trans(
            $container->get('ZfcTwigRenderer'),
            $container->get('translator')
        );
    }
}
