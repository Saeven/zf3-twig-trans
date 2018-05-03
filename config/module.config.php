<?php

use CirclicalTwigTrans\Factory\TransDefaultDomainFactory;
use CirclicalTwigTrans\Model\Twig\Trans;
use CirclicalTwigTrans\Factory\TransFactory;
use CirclicalTwigTrans\Model\Twig\TransDefaultDomain;

return [

    'service_manager' => [
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
        'factories' => [
            Trans::class => TransFactory::class,
            TransDefaultDomain::class => TransDefaultDomainFactory::class,
        ],
    ],


    'zfctwig' => [
        'extensions' => [
            Trans::class,
        ],
    ],
];

