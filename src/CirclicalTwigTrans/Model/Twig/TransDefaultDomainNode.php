<?php

namespace CirclicalTwigTrans\Model\Twig;

use Twig_Compiler;
use TWig_Node;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TransDefaultDomainNode extends Twig_Node
{
    public function __construct(\Twig_Node_Expression $expr, int $lineno = 0, string $tag = null)
    {
        parent::__construct(['expr' => $expr], [], $lineno, $tag);
    }

    public function getDomain(): string
    {
        return $this->getNode('expr')->getAttribute('value');
    }


    public function compile(Twig_Compiler $compiler)
    {
        // we are relying on the visitor
    }
}
