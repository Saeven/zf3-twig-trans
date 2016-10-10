<?php

namespace CirclicalTwigTrans\Model\Twig\Parser;

use CirclicalTwigTrans\Model\Twig\TransNode;
use Twig_Token;
use Twig_Node;
use Twig_Node_Text;
use Twig_Node_Print;
use Twig_Node_Expression_Name;


class TransParser extends \Twig_Extensions_TokenParser_Trans
{

    const DECIDE_FORK = 'decideForFork';
    const DECIDE_END = 'decideForEnd';

    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $count = null;
        $plural = null;
        $notes = null;
        $text_domain = null;
        $initial_block = false;


        if (!$stream->test(Twig_Token::BLOCK_END_TYPE)) {
            if ($stream->nextIf(Twig_Token::NAME_TYPE, 'from')) {
                $text_domain = $stream->expect(Twig_Token::STRING_TYPE)->getValue();
            } else {
                $body = $this->parser->getExpressionParser()->parseExpression();
                $initial_block = true;
            }
        }

        if (!$initial_block && $stream->test(Twig_Token::BLOCK_END_TYPE)) {
            $stream->expect(Twig_Token::BLOCK_END_TYPE);
            $body = $this->parser->subparse([$this, self::DECIDE_FORK]);
            $next = $stream->next()->getValue();

            if ('plural' === $next) {
                $count = $this->parser->getExpressionParser()->parseExpression();
                $stream->expect(Twig_Token::BLOCK_END_TYPE);
                $plural = $this->parser->subparse([$this, self::DECIDE_FORK]);

                if ('notes' === $stream->next()->getValue()) {
                    $stream->expect(Twig_Token::BLOCK_END_TYPE);
                    $notes = $this->parser->subparse([$this, self::DECIDE_END], true);
                }
            } elseif ('notes' === $next) {
                $stream->expect(Twig_Token::BLOCK_END_TYPE);
                $notes = $this->parser->subparse([$this, self::DECIDE_END], true);
            }
        }

        if (!$text_domain) {
            $stream->expect(Twig_Token::BLOCK_END_TYPE);
            $this->checkTransString($body, $lineno);
        } else {
            $stream->expect(Twig_Token::BLOCK_END_TYPE);
        }

        return new TransNode($body, $text_domain, $plural, $count, $notes, $lineno, $this->getTag());
    }

    public function decideForFork(Twig_Token $token)
    {
        return $token->test(['plural', 'notes', 'endtrans']);
    }

    public function decideForEnd(Twig_Token $token)
    {
        return $token->test('endtrans');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     *
     * @return string
     */
    public function getTag()
    {
        return 'trans';
    }

    protected function checkTransString(Twig_Node $body, $lineno)
    {
        foreach ($body as $i => $node) {
            if ($node instanceof Twig_Node_Text || ($node instanceof Twig_Node_Print && $node->getNode('expr') instanceof Twig_Node_Expression_Name))
                continue;

            throw new \Twig_Error_Syntax(sprintf('The text to be translated with "trans" can only contain references to simple variables'), $lineno);
        }
    }
}
