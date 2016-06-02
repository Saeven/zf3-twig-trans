<?php

/**
,,
`""*3b..
     ""*3o.					  						11/11/13 1:51 PM
         "33o.			                  			S. Alexandre M. Lemaire
           "*33o.
              "333o.
                "3333bo...       ..o:
                  "33333333booocS333    ..    ,.
               ".    "*3333SP     V3o..o33. .333b
                "33o. .33333o. ...A33333333333333b
          ""bo.   "*33333333333333333333P*33333333:
             "33.    V333333333P"**""*"'   VP  * "l
               "333o.433333333X
                "*3333333333333AoA3o..oooooo..           .b
                       .X33333333333P""     ""*oo,,     ,3P
                      33P""V3333333:    .        ""*****"
                    .*"    A33333333o.4;      .
                         .oP""   "333333b.  .3;
                                  A3333333333P
                                  "  "33333P"
                                      33P*"
		                              .3"
                                     "


*/


namespace CirclicalTwigTrans\Model\Twig;

use Zend\Mvc\I18n\Translator;

class MVCTransParser extends \Twig_Extensions_TokenParser_Trans
{
    protected $translator;

    public function __construct( Translator $t )
    {
        $this->translator = $t;
    }

    /**
     * Parses a token and returns a node.
     *
     * @param \Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(\Twig_Token $token)
    {
        $lineno     = $token->getLine();
        $stream     = $this->parser->getStream();
        $count      = null;
        $plural     = null;

        if (!$stream->test(\Twig_Token::BLOCK_END_TYPE))
        {
            $body = $this->parser->getExpressionParser()->parseExpression();
        }
        else
        {
            $stream->expect(\Twig_Token::BLOCK_END_TYPE);
            $body = $this->parser->subparse(array($this, 'decideForFork'));

            if ('plural' === $stream->next()->getValue()) {
                $count = $this->parser->getExpressionParser()->parseExpression();
                $stream->expect(\Twig_Token::BLOCK_END_TYPE);
                $plural = $this->parser->subparse(array($this, 'decideForEnd'), true);
            }
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        $this->checkTransString($body, $lineno);

        $t = new MvcTransNode( $body, $plural, $count, null,  $lineno, $this->getTag() );
        $t->setTranslator( $this->translator );
        return $t;
    }

}