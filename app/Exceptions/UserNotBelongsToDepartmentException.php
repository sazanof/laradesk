<?php

namespace App\Exceptions;

use Exception;

class UserNotBelongsToDepartmentException extends Exception
{
    protected $message = 'User does not belongs to department';
}
