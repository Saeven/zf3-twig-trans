<?php

namespace CirclicalTwigTrans\Exception;


class BlankTranslationException extends \Exception
{

    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }


}