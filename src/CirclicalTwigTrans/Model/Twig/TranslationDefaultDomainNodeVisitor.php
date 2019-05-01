<?php


namespace CirclicalTwigTrans\Model\Twig;

use Twig\Environment;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig_NodeInterface;

class TranslationDefaultDomainNodeVisitor implements \Twig_NodeVisitorInterface
{

    private $defaultDomain;

    /**
     * Called before child nodes are visited.
     */
    public function enterNode(Node $node, Environment $env): Node
    {
        if ($domain = $this->getDefaultDomain($node)) {
            $this->defaultDomain = $domain;
        }

        if ($node instanceof TransNode) {
            $node->setDefaultDomain($this->defaultDomain);
        }

        return $node;
    }

    private function getDefaultDomain(Node $node)
    {
        if ($node instanceof TransDefaultDomainNode) {
            return $node->getDomain();
        }

        return null;
    }


    /**
     * Called after child nodes are visited.
     */
    public function leaveNode(Node $node, Environment $env)
    {
        if ($node instanceof ModuleNode) {
            $this->defaultDomain = null;
        }

        return $node;
    }

    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return int The priority level
     */
    public function getPriority()
    {
        return 0;
    }
}