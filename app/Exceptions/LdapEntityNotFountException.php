<?php

namespace App\Exceptions;

class LdapEntityNotFountException extends \Exception
{
    public function __construct(string $message = "LDAP Object not found", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
