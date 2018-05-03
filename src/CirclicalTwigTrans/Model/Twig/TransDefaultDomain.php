<?php

namespace CirclicalTwigTrans\Model\Twig;

use CirclicalTwigTrans\Model\Twig\Parser\TransDefaultDomainTokenParser;
use ZfcTwig\Twig\Extension;
use ZfcTwig\View\TwigRenderer;

class TransDefaultDomain extends Extension
{

    protected $translator;

    public function __construct(TwigRenderer $renderer)
    {
        parent::__construct($renderer);
    }

    public function getTokenParsers()
    {
        return [new TransDefaultDomainTokenParser()];
    }

    public function getNodeVisitors()
    {
        return [
            new TranslationDefaultDomainNodeVisitor(),
        ];
    }

    public function getName()
    {
        return 'circlical-translatordomain';
    }
}

 
