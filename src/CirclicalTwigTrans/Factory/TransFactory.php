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

        foreach ($config['translator']['translation_file_patterns'] as $translationConfig) {
            if (!array_key_exists(self::DOMAIN, $translationConfig)) {
                $translationConfig[self::DOMAIN] = self::DOMAIN_DEFAULT;
            }
            bindtextdomain($translationConfig[self::DOMAIN], realpath($translationConfig['base_dir']) . '/');
            bind_textdomain_codeset($translationConfig[self::DOMAIN], 'UTF-8');
        }

        textdomain('default');

        return new Trans(
            $container->get('ZfcTwigRenderer'),
            $container->get('translator')
        );
    }
}
