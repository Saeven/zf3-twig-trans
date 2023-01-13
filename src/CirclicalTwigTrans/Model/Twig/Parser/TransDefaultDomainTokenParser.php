<?php

declare(strict_types=1);

namespace CirclicalTwigTrans\Model\Twig\Parser;

use CirclicalTwigTrans\Model\Twig\TransDefaultDomainNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

class TransDefaultDomainTokenParser extends AbstractTokenParser
{
    public function parse(Token $token): TransDefaultDomainNode
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(Token::BLOCK_END_TYPE);

        return new TransDefaultDomainNode($expr, $token->getLine(), $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'trans_default_domain';
    }
}
