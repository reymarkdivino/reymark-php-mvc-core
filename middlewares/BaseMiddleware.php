<?php

namespace app\core\middlewares;

/**
 * Class BaseMiddleware
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core\middlewares
 */
abstract class BaseMiddleware 
{
    abstract public function execute();
}