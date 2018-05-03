<?php

namespace CirclicalTwigTrans\Test;

use CirclicalTwigTrans\Model\Twig\Trans;
use Twig_Environment;
use Twig_Loader_Chain;
use Zend\Mvc\I18n\Translator;
use Zend\View\View;
use ZfcTwig\View\TwigRenderer;
use PHPUnit\Framework\TestCase;
use ZfcTwig\View\TwigResolver;
use CirclicalTwigTrans\Test\Loader\Filesystem;

class ExtensionTest extends TestCase
{

    private function getRenderer(array $variables = [])
    {
        $filesystem = new Filesystem('/', __DIR__ . '/Fixtures/twig');
        $filesystem->prependPath(__DIR__ . '/Fixtures/twig');
        $chain = new Twig_Loader_Chain();
        $chain->addLoader($filesystem);
        $environment = new Twig_Environment($chain);

        foreach ($variables as $key => $value) {
            $environment->addGlobal($key, $value);
        }

        $renderer = new TwigRenderer(new View, $chain, $environment, new TwigResolver($environment));
        $environment->addExtension(new \Twig_Extensions_Extension_I18n());
        $environment->addExtension(new Trans($renderer, $this->getTranslator()));

        return $renderer;
    }

    private function getTranslator()
    {
        require_once(__DIR__ . '/TestTranslator.php');

        return new Translator(new \TestTranslator());
    }

    public function testRenderBasic()
    {
        $content = $this->getRenderer()->render('/basic.twig');
        $this->assertInternalType('string', $content);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/twig/basic.twig', $content);
    }

    public function testRenderSimpleTrans()
    {
        $content = $this->getRenderer()->render('/simpletrans.twig');
        $this->assertInternalType('string', $content);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/result/simpletrans.txt', $content);
    }

    public function testRenderPluralTransSingular()
    {
        $content = $this->getRenderer(['ducks' => 1])->render('/trans-plural.twig');
        $this->assertInternalType('string', $content);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/result/trans-singular-1.txt', $content);
    }

    public function testRenderPluralTransPlural()
    {
        $content = $this->getRenderer(['ducks' => 2])->render('/trans-plural.twig');
        $this->assertInternalType('string', $content);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/result/trans-singular-2.txt', $content);
    }

    public function testRenderWithAttributes()
    {
        $content = $this->getRenderer()->render('/trans-with-notes.twig');
        $this->assertInternalType('string', $content);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/result/trans-with-notes.txt', $content);
    }

    public function testRenderWithVariable()
    {
        $content = $this->getRenderer()->render('/trans-with-variable.twig');
        $this->assertInternalType('string', $content);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/result/trans-with-variable.txt', $content);
    }

    public function testRenderInlineTrans()
    {
        $content = $this->getRenderer()->render('/trans-inline.twig');
        $this->assertInternalType('string', $content);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/result/trans-inline.txt', $content);
    }

    public function testRenderFromDomain()
    {
        /*
         * This behavior is driven by the Factory when the strap is invoked
         */
        bindtextdomain('domain', realpath(__DIR__) . '/language');
        bind_textdomain_codeset('domain', 'UTF-8');

        $content = $this->getRenderer()->render('/trans-with-domain.twig');
        $this->assertInternalType('string', $content);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/result/trans-with-domain.txt', $content);
    }

    public function testRenderPluralWithNotes()
    {
        $content = $this->getRenderer(['totalNotes' => 1])->render('/trans-plural-with-notes.twig');
        $this->assertInternalType('string', $content);
        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/result/trans-plural-with-notes.txt', $content);
    }

    public function testCanDecideForEnd()
    {

        $trans = new Trans($this->getRenderer(), $this->getTranslator());
        $token = new \Twig_Token(\Twig_Token::NAME_TYPE, 'endtrans', 0);
        $result = $trans->decideForEnd($token);
        $this->assertTrue($result);
    }

    public function testCanDecideForFork()
    {
        $trans = new Trans($this->getRenderer(), $this->getTranslator());
        $token = new \Twig_Token(\Twig_Token::NAME_TYPE, 'plural', 0);
        $result = $trans->decideForFork($token);
        $this->assertTrue($result);
    }
}