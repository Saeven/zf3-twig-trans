<?php


/**
,,
`""*3b..											
     ""*3o.					  						11/11/13 12:40 PM
         "33o.			                  			S. Alexandre M. Lemaire
           "*33o.
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

use Zend\Mvc\I18n\Translator;

class Trans extends \ZfcTwig\Twig\Extension\ZfcTwig
{

    /**
     * @var TwigRenderer
     */
    protected $renderer;

    protected $translator;

    /**
     * Constructor.
     *
     * @param \ZfcTwig\View\Renderer\TwigRenderer $renderer
     * @param Translator $trans
     */
    public function __construct( \ZfcTwig\View\Renderer\TwigRenderer $renderer, Translator $trans)
    {
        $this->renderer     = $renderer;
        $this->translator   = $trans;
    }


    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array( new TransParser( $this->translator ) );
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'launchfire-translator';
    }
}

 