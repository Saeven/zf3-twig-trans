<?php

declare(strict_types=1);

namespace CirclicalTwigTrans\Exception;

use Exception;

class BlankTranslationException extends Exception
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
