<?php

declare(strict_types=1);

namespace CirclicalTwigTrans\Model\Twig;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Node;

class TransDefaultDomainNode extends Node
{
    public function __construct(AbstractExpression $expr, int $lineno = 0, ?string $tag = null)
    {
        parent::__construct(['expr' => $expr], [], $lineno, $tag);
    }

    public function getDomain(): string
    {
        return $this->getNode('expr')->getAttribute('value');
    }

    public function compile(Compiler $compiler)
    {
        // we are relying on the visitor
    }
}
