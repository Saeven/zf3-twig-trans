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

class MvcTransNode extends \Twig_Extensions_Node_Trans
{


    /**
     * @var Zend\Mvc\I18n\Translator\Translator
     */
    protected $translator;

    public function setTranslator( Translator $t )
    {
        $this->translator = $t;
    }

    /**
     * Compiles the node to PHP.
     *
     * @param \Twig_Compiler $compiler
     */
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        list($msg, $vars) = $this->compileString($this->getNode('body'));

        if (null !== $this->getNode('plural')) {
            list($msgp, $vars1) = $this->compileString($this->getNode('plural'));
            $vars = array_merge($vars, $vars1);
        }

        $is_plural  = null === $this->getNode('plural') ? false : true;
        $function   = null === $this->getNode('plural') ? 'gettext' : 'ngettext';

        if ($vars) {

            $compiler->raw('echo strtr(' . $function . '(' );

            if( $is_plural )
            {
                $t  = $this->translator->translate(
                    $msg->nodes[0]->getAttribute('value')
                );

                if( is_array( $t ) )
                {
                    $s = $t[0];
                    $p = $t[1];
                }
                else
                {
                    $s = $t;
                    $p = $msgp->nodes[0]->getAttribute('value');
                }

                $compiler
                    ->repr( $s )
                    ->raw(',' )
                    ->repr( $p )
                    ->raw(', abs(')
                    ->subcompile($this->getNode('count'))
                    ->raw(')');
            }
            else
            {
                $compiler->repr( $this->translator->translate( $msg->nodes[0]->getAttribute('value') ) );
            }
            $compiler->raw('), array(');

            foreach ($vars as $var) {
                if ('count' === $var->getAttribute('name')) {
                    $compiler
                        ->string('%count%')
                        ->raw(' => abs(')
                        ->subcompile($this->getNode('count'))
                        ->raw(') ')
                    ;
                } else {
                    $compiler
                        ->string('%'.$var->getAttribute('name').'%')
                        ->raw(' => ')
                        ->subcompile($var)
                        ->raw(', ')
                    ;
                }
            }

            $compiler->raw("));\n");

        } else {

            $srcnode = $is_plural ? $msgp : $msg;
            $compiler->write('echo ');
            $compiler->repr( $this->translator->translate( $srcnode->getAttribute('value') ) );
            $compiler->write(';' );

        }
    }

}