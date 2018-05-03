<?php

namespace CirclicalTwigTrans\Model\Twig;

use CirclicalTwigTrans\Model\Twig\Parser\TransParser;
use Zend\Mvc\I18n\Translator;
use ZfcTwig\Twig\Extension;
use ZfcTwig\View\TwigRenderer;
use Twig_Token;

class Trans extends Extension
{

    protected $translator;

    public function __construct(TwigRenderer $renderer, Translator $translator)
    {
        parent::__construct($renderer);
        $this->translator = $translator;
    }

    public function getTokenParsers()
    {
        if ($this->translator) {
            $locale = $this->translator->getLocale();
            putenv('LANG=' . $locale);
            setlocale(LC_MESSAGES, $locale . '.utf-8');
        }

        return [new TransParser()];
    }

    public function decideForFork(Twig_Token $token)
    {
        return $token->test(['plural', 'from', 'notes', 'endtrans']);
    }

    public function decideForEnd(Twig_Token $token)
    {
        return $token->test('endtrans');
    }

    public function getName()
    {
        return 'circlical-translator';
    }
}

 
