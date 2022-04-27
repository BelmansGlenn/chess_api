<?php

namespace App\Exception;

use Throwable;

class RouteNotFoundException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('This route does\'nt exist.', $code, $previous);
    }


}