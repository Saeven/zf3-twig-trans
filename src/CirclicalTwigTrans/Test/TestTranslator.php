<?php

final class TestTranslator extends \Laminas\Mvc\I18n\DummyTranslator
{
    public function getLocale(): string
    {
        return 'en_US';
    }
}

