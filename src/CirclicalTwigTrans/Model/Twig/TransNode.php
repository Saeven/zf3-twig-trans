<?php

namespace CirclicalTwigTrans\Model\Twig;

use CirclicalTwigTrans\Model\Twig\Parser\TransParser;
use Twig_Node_Expression_Constant;
use Twig_Node_Expression_Filter;
use Twig_Node_Expression_Name;
use Twig_Node_Expression_TempName;
use Twig_Node_Print;
use Twig_Node_SetTemp;
use Twig_Compiler;
use Twig_Node;
use Twig_NodeInterface;
use Twig_Node_Expression;
use CirclicalTwigTrans\Exception\BlankTranslationException;

class TransNode extends Twig_Node
{
    private const TYPE_PLURAL = 'plural';
    private const TYPE_COUNT = 'count';
    private const TYPE_NAME = 'name';
    private const TYPE_DATA = 'data';

    private $domain;

    public function __construct(Twig_Node $body, $domain, Twig_NodeInterface $plural = null, Twig_Node_Expression $count = null, Twig_NodeInterface $notes = null, $line_number, $tag = null)
    {
        parent::__construct(
            [
                'count' => $count,
                'body' => $body,
                'plural' => $plural,
                'notes' => $notes,
            ],
            [],
            $line_number,
            $tag
        );

        $this->domain = $domain;
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return [new TransParser()];
    }


    /**
     * Compiles the node to PHP.
     *
     * @param Twig_Compiler $compiler A Twig_Compiler instance
     */
    public function compile(Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        /**
         * @var Twig_Node $msg
         * @var TWig_Node $msg1
         */
        try {
            [$msg, $vars] = $this->compileString($this->getNode('body'));

            if (null !== $this->getNode(self::TYPE_PLURAL)) {
                [$msg1, $vars1] = $this->compileString($this->getNode(self::TYPE_PLURAL));
                $vars = array_merge($vars, $vars1);
            }
        } catch (BlankTranslationException $x) {
            throw new \Exception($x->getMessage() . ' at line ' . $x->getCode() . ' in ' . $compiler->getFilename());
        }


        $isPlural = null !== $this->getNode(self::TYPE_PLURAL);
        if (!$this->domain) {
            $function = $isPlural ? 'ngettext' : 'gettext';
        } else {
            $function = $isPlural ? 'dngettext' : 'dgettext';
        }

        // handle notes
        if (null !== $notes = $this->getNode('notes')) {
            $message = trim($notes->getAttribute(self::TYPE_DATA));

            // line breaks are not allowed cause we want a single line comment
            $message = str_replace(["\n", "\r"], " ", $message);
            $compiler->write("// notes: {$message}\n");
        }

        if ($vars) {
            $compiler->write('echo strtr(' . $function . '(');

            if ($this->domain) {
                $compiler->repr($this->domain);
                $compiler->raw(', ');
            }

            $compiler->subcompile($msg);

            if (null !== $this->getNode(self::TYPE_PLURAL)) {
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
            if ($this->domain) {
                $compiler->repr($this->domain);
                $compiler->raw(', ');
            }

            $compiler->subcompile($msg);

            if (null !== $this->getNode(self::TYPE_PLURAL)) {
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
     * @param Twig_NodeInterface $body A Twig_NodeInterface instance
     *
     * @return array
     * @throws BlankTranslationException
     */
    protected function compileString(Twig_NodeInterface $body)
    {

        if ($body instanceof Twig_Node_Expression_Name || $body instanceof Twig_Node_Expression_Constant || $body instanceof Twig_Node_Expression_TempName) {
            if ($body instanceof Twig_Node_Expression_Constant && !trim($body->getAttribute('value'))) {
                throw new BlankTranslationException('You are attempting to translate an empty string', $body->getLine());
            }

            return [$body, []];
        }

        $vars = [];
        if (\count($body)) {
            $msg = '';

            foreach ($body as $node) {
                if (\get_class($node) === 'Twig_Node' && $node->getNode(0) instanceof Twig_Node_SetTemp) {
                    $node = $node->getNode(1);
                }

                if ($node instanceof Twig_Node_Print) {
                    $n = $node->getNode('expr');
                    while ($n instanceof Twig_Node_Expression_Filter) {
                        $n = $n->getNode('node');
                    }
                    $msg .= sprintf('%%%s%%', $n->getAttribute(self::TYPE_NAME));
                    $vars[] = new Twig_Node_Expression_Name($n->getAttribute(self::TYPE_NAME), $n->getLine());
                } else {
                    $msg .= $node->getAttribute(self::TYPE_DATA);
                }
            }
        } else {
            /** @var Twig_Node $body */
            if (!$body->hasAttribute(self::TYPE_DATA)) {
                throw new BlankTranslationException("You are attempting to translate a empty string", $body->getLine());
            }
            $msg = $body->getAttribute(self::TYPE_DATA);
        }

        if (!trim($msg)) {
            throw new BlankTranslationException("You are attempting to translate a blank string", $body->getLine());
        }

        return [new Twig_Node([new Twig_Node_Expression_Constant(trim($msg), $body->getLine())]), $vars];
    }

}