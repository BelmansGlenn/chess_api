<?php

namespace App\Exception;


use Throwable;

class PlayerNotUserConnectedException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('This profile doesn\'t belong to you.', $code, $previous);
    }

}