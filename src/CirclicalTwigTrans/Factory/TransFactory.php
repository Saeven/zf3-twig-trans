<?php

namespace CirclicalTwigTrans\Factory;

use CirclicalTwigTrans\Model\Twig\Trans;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class TransFactory implements FactoryInterface
{
    private const DOMAIN_KEY = 'text_domain';

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
        $config = $container->get('config');
        $domainList = [];

        foreach ($config['translator']['translation_file_patterns'] as $translationConfig) {

            $domain = $translationConfig[self::DOMAIN_KEY];

            if (!array_key_exists(self::DOMAIN_KEY, $translationConfig)) {
                throw new \RuntimeException("The domain for configuration " . ($translationConfig['namespace'] ?? 'unknown') . ' was not set.  Every module must have a distinct ' . self::DOMAIN_KEY . 'set');
            }

            if (\in_array($domain, $domainList, true)) {
                throw new \RuntimeException("The domain with name '$domain' was configured more than once. Unlike with Zend MVC's translator, a domain cannot span two .mo files with gettext. Give each translation a unique domain.");
            }

            $domainList[] = $translationConfig[self::DOMAIN_KEY];
            bindtextdomain($translationConfig[self::DOMAIN_KEY], realpath($translationConfig['base_dir']));
            bind_textdomain_codeset($translationConfig[self::DOMAIN_KEY], 'UTF-8');
        }

        return new Trans(
            $container->get('ZfcTwigRenderer'),
            $container->get('translator')
        );
    }
}
