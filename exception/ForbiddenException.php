<?php

namespace app\core\exception;

/**
 * Class ForbiddenException
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class ForbiddenException extends \Exception 
{
    protected $message = 'You don\'t have permission to access this page';
    protected $code = 403;
   
}