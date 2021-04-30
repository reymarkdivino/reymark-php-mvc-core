<?php

namespace app\core;

use app\core\middlewares\BaseMiddleware;
/**
 * Class Controller
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class Controller 
{
    // layout use for view,
    // default: main
    public string $layout = 'main';

    // action: this is a method/function to be called.
    public string $action = '';
    
    /**
     * @var \app\core\middlewares\BaseMiddleware[]
     */
    protected array $middlewares = [];

    public function render($view, $params = [])
    {
        return Application::$app->view->renderView($view, $params); 
    }

    /**
     * Set the layout
     * @param $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Register a middleware to be use later.
     */
    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Get the middlewares that has been set.
     * @return \app\core\middlewares\BaseMiddleware[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}