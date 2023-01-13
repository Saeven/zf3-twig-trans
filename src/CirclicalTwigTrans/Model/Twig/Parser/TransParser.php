<?php

declare(strict_types=1);

namespace CirclicalTwigTrans\Model\Twig\Parser;

use CirclicalTwigTrans\Model\Twig\TransNode;
use Twig\Error\SyntaxError;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\Node\TextNode;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

use function sprintf;

class TransParser extends AbstractTokenParser
{
    private const DECIDE_FORK = 'decideForFork';
    private const DECIDE_END = 'decideForEnd';

    /**
     * Parses a token and returns a node.
     */
    public function parse(Token $token): TransNode
    {
        $lineNumber = $token->getLine();
        $stream = $this->parser->getStream();
        $count = null;
        $plural = null;
        $notes = null;
        $textDomain = null;
        $initialBlock = false;

        if (!$stream->test(Token::BLOCK_END_TYPE)) {
            if ($stream->nextIf(Token::NAME_TYPE, 'from')) {
                $textDomain = $stream->expect(Token::STRING_TYPE)->getValue();
            } else {
                $body = $this->parser->getExpressionParser()->parseExpression();
                $initialBlock = true;
            }
        }

        if (!$initialBlock && $stream->test(Token::BLOCK_END_TYPE)) {
            $stream->expect(Token::BLOCK_END_TYPE);
            $body = $this->parser->subparse([$this, self::DECIDE_FORK]);
            $next = $stream->next()->getValue();

            if ('plural' === $next) {
                $count = $this->parser->getExpressionParser()->parseExpression();
                $stream->expect(Token::BLOCK_END_TYPE);
                $plural = $this->parser->subparse([$this, self::DECIDE_FORK]);

                if ('notes' === $stream->next()->getValue()) {
                    $stream->expect(Token::BLOCK_END_TYPE);
                    $notes = $this->parser->subparse([$this, self::DECIDE_END], true);
                }
            } elseif ('notes' === $next) {
                $stream->expect(Token::BLOCK_END_TYPE);
                $notes = $this->parser->subparse([$this, self::DECIDE_END], true);
            }
        }

        if (!$textDomain) {
            $stream->expect(Token::BLOCK_END_TYPE);
            $this->checkTransString($body, $lineNumber);
        } else {
            $stream->expect(Token::BLOCK_END_TYPE);
        }

        return new TransNode($body, $textDomain, $plural, $count, $notes, $lineNumber, $this->getTag());
    }

    public function decideForFork(Token $token): bool
    {
        return $token->test(['plural', 'notes', 'endtrans']);
    }

    public function decideForEnd(Token $token): bool
    {
        return $token->test('endtrans');
    }

    /**
     * Gets the tag name associated with this token parser.
     */
    public function getTag(): string
    {
        return 'trans';
    }

    protected function checkTransString(Node $body, int $lineno)
    {
        foreach ($body as $i => $node) {
            if ($node instanceof TextNode || ($node instanceof PrintNode && $node->getNode('expr') instanceof NameExpression)) {
                continue;
            }

            throw new SyntaxError(sprintf('The text to be translated with "trans" can only contain references to simple variables'), $lineno);
        }
    }
}
