<?php

declare(strict_types=1);

namespace CirclicalTwigTrans\Model\Twig;

use CirclicalTwigTrans\Model\Twig\Parser\TransDefaultDomainTokenParser;
use ZfcTwig\Twig\Extension;
use ZfcTwig\View\TwigRenderer;

class TransDefaultDomain extends Extension
{
    public function __construct(TwigRenderer $renderer)
    {
        parent::__construct($renderer);
    }

    public function getTokenParsers(): array
    {
        return [
            new TransDefaultDomainTokenParser(),
        ];
    }

    public function getNodeVisitors(): array
    {
        return [
            new TranslationDefaultDomainNodeVisitor(),
        ];
    }

    public function getName(): string
    {
        return 'circlical-translatordomain';
    }
}
