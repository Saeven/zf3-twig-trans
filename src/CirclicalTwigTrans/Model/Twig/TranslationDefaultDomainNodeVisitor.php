<?php


namespace CirclicalTwigTrans\Model\Twig;

use Twig_Environment;
use Twig_NodeInterface;

class TranslationDefaultDomainNodeVisitor implements \Twig_NodeVisitorInterface
{

    private $defaultDomain;

    /**
     * Called before child nodes are visited.
     *
     * @return Twig_NodeInterface The modified node
     */
    public function enterNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if ($domain = $this->getDefaultDomain($node)) {
            $this->defaultDomain = $domain;
        }

        if ($node instanceof TransNode) {
            $node->setDefaultDomain($this->defaultDomain);
        }

        return $node;
    }

    private function getDefaultDomain(\Twig_NodeInterface $node)
    {
        if ($node instanceof TransDefaultDomainNode) {
            return $node->getDomain();
        }

        return null;
    }


    /**
     * Called after child nodes are visited.
     *
     * @return Twig_NodeInterface|false The modified node or false if the node must be removed
     */
    public function leaveNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Module) {
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