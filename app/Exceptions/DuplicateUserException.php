<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class DuplicateUserException extends Exception
{
    protected $message, $code;

    public function __construct($message = "User already exists", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = $message;
        $this->code = $code;
    }

    public function render(){
        return response()->json(["message" => $this->message], $this->code);
    }
}
