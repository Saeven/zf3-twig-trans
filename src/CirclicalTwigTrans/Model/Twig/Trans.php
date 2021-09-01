<?php

namespace CirclicalTwigTrans\Model\Twig;

use CirclicalTwigTrans\Model\Twig\Parser\TransParser;
use Twig\Token;
use Laminas\Mvc\I18n\Translator;
use ZfcTwig\Twig\Extension;
use ZfcTwig\View\TwigRenderer;

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
        $locale = $this->translator->getLocale();
        putenv('LANG=' . $locale);
        setlocale(LC_MESSAGES, $locale . '.utf-8');

        return [new TransParser()];
    }

    public function decideForFork(Token $token)
    {
        return $token->test(['plural', 'from', 'notes', 'endtrans']);
    }

    public function decideForEnd(Token $token)
    {
        return $token->test('endtrans');
    }

    public function getName(): string
    {
        return 'circlical-translator';
    }
}
