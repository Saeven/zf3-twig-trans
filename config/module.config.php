<?php

use CirclicalTwigTrans\Model\Twig\Trans;
use CirclicalTwigTrans\Factory\TransFactory;

return [

    'service_manager' => [
        'factories' => [
            Trans::class => TransFactory::class,
        ],
    ],


    'zfctwig' => [
        'extensions' => [
            Trans::class,
        ],
    ],
];

