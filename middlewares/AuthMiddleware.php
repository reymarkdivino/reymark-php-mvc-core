<?php

namespace app\core\middlewares;

use app\core\Application;
use app\core\exception\ForbiddenException;

/**
 * Class BaseMiddleware
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core\middlewares
 */
class AuthMiddleware extends BaseMiddleware
{

    public array $action = [];

    public function __construct(array $actions = [])
    {
        $this->actions = $actions; 
    }

    public function execute()
    {
        // echo "AuthMiddleware has been triggered.";
        // Check if the user is Guest
        // and if the user is Guest, we are blocking the user for accessing page that need to be authenticated.

        if(Application::isGuest()) {
            // if $this->actions is empty == TRUE OR
            // if the pplication::$app->controller->action are existing on $this->actions == TRUE
            // then lets throw forbidden execption
            // Notes: 'action' is the method/fucntion that has been triggered by the request.
            if(empty($this->actions) || in_array(in_array(Application::$app->controller->action, $this->actions), $this->actions)){
                throw new ForbiddenException();
            }
        }
    }
}