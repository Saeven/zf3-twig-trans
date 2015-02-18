<?php

/**
,,
`""*3b..											
     ""*3o.
         "33o.			                  			S. Alexandre M. Lemaire
           "*33o.                                   alemaire@circlical.com
              "333o.
                "3333bo...       ..o:
                  "33333333booocS333    ..    ,.
               ".    "*3333SP     V3o..o33. .333b
                "33o. .33333o. ...A33333333333333b
          ""bo.   "*33333333333333333333P*33333333:
             "33.    V333333333P"**""*"'   VP  * "l
               "333o.433333333X
                "*3333333333333AoA3o..oooooo..           .b
                       .X33333333333P""     ""*oo,,     ,3P
                      33P""V3333333:    .        ""*****"
                    .*"    A33333333o.4;      .
                         .oP""   "333333b.  .3;
                                  A3333333333P
                                  "  "33333P"
                                      33P*"
		                              .3"
                                     "
                                     
                                     
*/

namespace CirclicalTwigTrans\Factory;

use CirclicalTwigTrans\Model\Twig\Trans;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TransFactory implements FactoryInterface
{
    const DOMAIN = 'text_domain';

    public function createService( ServiceLocatorInterface $serviceLocator )
    {
        $config     = $serviceLocator->get('config');
        $trcfg      = $config['translator']['translation_file_patterns'][0];

        bindtextdomain($trcfg[self::DOMAIN], realpath( $trcfg['base_dir'] ) . '/');
        textdomain($trcfg[self::DOMAIN]);
        bind_textdomain_codeset($trcfg[self::DOMAIN], 'UTF-8');

        return new Trans(
            $serviceLocator->get('ZfcTwigRenderer'),
            $serviceLocator->get('translator')
        );
    }
}