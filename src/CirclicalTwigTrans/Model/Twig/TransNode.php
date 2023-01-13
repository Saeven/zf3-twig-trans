<?php

declare(strict_types=1);

namespace CirclicalTwigTrans\Model\Twig;

use CirclicalTwigTrans\Exception\BlankTranslationException;
use CirclicalTwigTrans\Model\Twig\Parser\TransParser;
use Exception;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Expression\TempNameExpression;
use Twig\Node\Node;
use Twig\Node\PrintNode;
use Twig\Node\SetNode;

use function array_merge;
use function count;
use function sprintf;
use function str_replace;
use function trim;

class TransNode extends Node
{
    private const TYPE_PLURAL = 'plural';
    private const TYPE_COUNT = 'count';
    private const TYPE_NAME = 'name';
    private const TYPE_DATA = 'data';

    private ?string $domain;

    private ?string $defaultDomain;

    public function __construct(Node $body, ?string $domain, ?Node $plural = null, ?AbstractExpression $count = null, ?Node $notes = null, int $lineNumber = 0, ?string $tag = null)
    {
        $nodes = [
            'body' => $body,
        ];

        if ($plural) {
            $nodes['plural'] = $plural;
        }

        if ($count) {
            $nodes['count'] = $count;
        }

        if ($notes) {
            $nodes['notes'] = $notes;
        }

        parent::__construct(
            $nodes,
            [],
            $lineNumber,
            $tag
        );

        $this->domain = $domain;
    }

    public function setDefaultDomain(?string $defaultDomain): void
    {
        $this->defaultDomain = $defaultDomain;
    }

    /**
     * Returns the token parser instances to add to the existing list.
     */
    public function getTokenParsers(): array
    {
        return [new TransParser()];
    }

    /**
     * Compiles the node to PHP.
     */
    public function compile(Compiler $compiler): void
    {
        $compiler->addDebugInfo($this);

        try {
            /** @var Node $msg */
            [$msg, $vars] = $this->compileString($this->getNode('body'));

            if ($this->hasNode(self::TYPE_PLURAL)) {
                /** * @var Node $msg1 */
                [$msg1, $vars1] = $this->compileString($this->getNode(self::TYPE_PLURAL));
                $vars = array_merge($vars, $vars1);
            }
        } catch (BlankTranslationException $x) {
            throw new Exception($x->getMessage() . ' at line ' . $x->getCode());
        }

        $isPlural = $this->hasNode(self::TYPE_PLURAL);
        $translationDomain = null;

        if ($this->defaultDomain) {
            $translationDomain = $this->defaultDomain;
        }

        if ($this->domain) {
            $translationDomain = $this->domain;
        }

        if ($translationDomain) {
            $function = $isPlural ? 'dngettext' : 'dgettext';
        } else {
            $function = $isPlural ? 'ngettext' : 'gettext';
        }

        // handle notes
        if ($this->hasNode('notes')) {
            $notes = $this->getNode('notes');
            $message = trim($notes->getAttribute(self::TYPE_DATA));

            // line breaks are not allowed cause we want a single line comment
            $message = str_replace(["\n", "\r"], " ", $message);
            $compiler->write("// notes: {$message}\n");
        }

        if ($vars) {
            $compiler->write('echo strtr(' . $function . '(');

            if ($translationDomain) {
                $compiler->repr($translationDomain);
                $compiler->raw(', ');
            }

            $compiler->subcompile($msg);

            if ($this->hasNode(self::TYPE_PLURAL)) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->getNode(self::TYPE_COUNT))
                    ->raw(')');
            }

            $compiler->raw('), array(');

            foreach ($vars as $var) {
                if (self::TYPE_COUNT === $var->getAttribute(self::TYPE_NAME)) {
                    $compiler
                        ->string('%count%')
                        ->raw(' => abs(')
                        ->subcompile($this->getNode(self::TYPE_COUNT))
                        ->raw('), ');
                } else {
                    $compiler
                        ->string('%' . $var->getAttribute(self::TYPE_NAME) . '%')
                        ->raw(' => ')
                        ->subcompile($var)
                        ->raw(', ');
                }
            }

            $compiler->raw("));\n");
        } else {
            $compiler->write('echo ' . $function . '(');
            if ($translationDomain) {
                $compiler->repr($translationDomain);
                $compiler->raw(', ');
            }

            $compiler->subcompile($msg);

            if ($this->hasNode(self::TYPE_PLURAL)) {
                $compiler
                    ->raw(', ')
                    ->subcompile($msg1)
                    ->raw(', abs(')
                    ->subcompile($this->getNode(self::TYPE_COUNT))
                    ->raw(')');
            }

            $compiler->raw(");\n");
        }
    }

    /**
     * @return array
     * @throws BlankTranslationException
     */
    protected function compileString(Node $body)
    {
        if ($body instanceof NameExpression || $body instanceof ConstantExpression || $body instanceof TempNameExpression) {
            if ($body instanceof ConstantExpression && !trim($body->getAttribute('value'))) {
                throw new BlankTranslationException('You are attempting to translate an empty string', $body->getTemplateLine());
            }

            return [$body, []];
        }

        $vars = [];
        if (count($body)) {
            $msg = '';

            foreach ($body as $node) {
                if ($node::class === Node::class && $node->getNode('0') instanceof SetNode) {
                    $node = $node->getNode('1');
                }

                if ($node instanceof PrintNode) {
                    $n = $node->getNode('expr');
                    while ($n instanceof FilterExpression) {
                        $n = $n->getNode('node');
                    }
                    $msg .= sprintf('%%%s%%', $n->getAttribute(self::TYPE_NAME));
                    $vars[] = new NameExpression($n->getAttribute(self::TYPE_NAME), $n->getTemplateLine());
                } else {
                    $msg .= $node->getAttribute(self::TYPE_DATA);
                }
            }
        } else {
            if (!$body->hasAttribute(self::TYPE_DATA)) {
                throw new BlankTranslationException('You are attempting to translate a empty string', $body->getTemplateLine());
            }
            $msg = $body->getAttribute(self::TYPE_DATA);
        }

        if (!trim($msg)) {
            throw new BlankTranslationException('You are attempting to translate a blank string', $body->getTemplateLine());
        }

        return [new Node([new ConstantExpression(trim($msg), $body->getTemplateLine())]), $vars];
    }
}
