<?php

namespace CirclicalTwigTrans\Model\Twig;

use CirclicalTwigTrans\Model\Twig\Parser\TransParser;
use Zend\Mvc\I18n\Translator;
use ZfcTwig\Twig\Extension;
use ZfcTwig\View\TwigRenderer;
use Twig_Token;

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
    public function __construct(TwigRenderer $renderer, Translator $translator = null)
    {
        $this->renderer = $renderer;
        $this->translator = $translator;
    }


    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        // best place to set locale I could find, because of how the module loader works
        // translator is optional to facilitate extraction, use Factory to create in production
        if ($this->translator) {
            $locale = $this->translator->getLocale();
            putenv('LANG=' . $locale);
            setlocale(LC_MESSAGES, $locale . ".utf-8");
        }

        return [new TransParser($this->translator)];
    }

    public function decideForFork(Twig_Token $token)
    {
        return $token->test(['plural', 'from', 'notes', 'endtrans']);
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

 
