<?php

declare(strict_types=1);

namespace CirclicalTwigTrans\Model\Twig;

use CirclicalTwigTrans\Model\Twig\Parser\TransParser;
use Laminas\Mvc\I18n\Translator;
use Twig\Token;
use ZfcTwig\Twig\Extension;
use ZfcTwig\View\TwigRenderer;

use function putenv;
use function setlocale;

use const LC_MESSAGES;

class Trans extends Extension
{
    protected Translator $translator;

    public function __construct(TwigRenderer $renderer, Translator $translator)
    {
        parent::__construct($renderer);
        $this->translator = $translator;
    }

    public function getTokenParsers(): array
    {
        $locale = $this->translator->getLocale();
        putenv('LANG=' . $locale);
        setlocale(LC_MESSAGES, $locale . '.utf-8');

        return [new TransParser()];
    }

    public function decideForFork(Token $token): bool
    {
        return $token->test(['plural', 'from', 'notes', 'endtrans']);
    }

    public function decideForEnd(Token $token): bool
    {
        return $token->test('endtrans');
    }

    public function getName(): string
    {
        return 'circlical-translator';
    }
}
