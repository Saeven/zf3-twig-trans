<?php

final class TestTranslator extends \Zend\Mvc\I18n\DummyTranslator
{
    public function getLocale(): string
    {
        return 'en_US';
    }
}

