<?php

namespace CirclicalTwigTrans\Model\Twig\Parser;

namespace CirclicalTwigTrans\Model\Twig\Parser;

use CirclicalTwigTrans\Model\Twig\TransDefaultDomainNode;
use Twig_Token;

/**
 * Token Parser for the 'trans_default_domain' tag.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class TransDefaultDomainTokenParser extends \Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $expr = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

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