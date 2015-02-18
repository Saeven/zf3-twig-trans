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

namespace CirclicalTwigTrans\Model\Twig;

use CirclicalTwigTrans\Model\Twig\Parser\TransParser;
use Zend\Mvc\I18n\Translator;
use ZfcTwig\Twig\Extension;
use ZfcTwig\View\TwigRenderer;

class Trans extends Extension
{

    /**
     * @var TwigRenderer
     */
    protected $renderer;


    /**
     * @var Translator
     */
    protected $translator;


    /**
     * Constructor.
     *
     * @param TwigRenderer $renderer
     */
    public function __construct( TwigRenderer $renderer, Translator $translator )
    {
        $this->renderer     = $renderer;
        $this->translator   = $translator;
    }


    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        // best place to set locale I could find, because of how the module loader works
        $locale = $this->translator->getLocale();
        putenv( 'LANG=' . $locale );
        setlocale( LC_ALL, $locale );

        return array( new TransParser( $this->transaltor ) );
    }

    public function decideForFork(Twig_Token $token)
    {
        return $token->test(array('plural', 'from', 'notes', 'endtrans'));
    }

    public function decideForEnd(Twig_Token $token)
    {
        return $token->test('endtrans');
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'circlical-translator';
    }
}

 