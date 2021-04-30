<?php

namespace app\core\exception;

/**
 * Class NotFoundException
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class NotFoundException extends \Exception 
{
    protected $message = 'Page not found.';
    protected $code = 404;
   
}