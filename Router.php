<?php

namespace app\core;

use app\core\exception\NotFoundException;

/**
 * Class Router
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class Router
{
    /**
     * Declare the Request class as Typed Property for $request variables
     */
    public Request $request;

    /**
     * Declare the Response class as Typed Property for $response variables
     */
    public Response $response;

    


    // array of $_routes
    protected array $_routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $callback)
    {
        // The _routes should look like this:
        // 'get' => [
        //     '/' => callback_function,
        //     '/contact' => another_callback_function
        // ],
        // 'post' => [
        //     '/post_here' => callback_function
        // ]

        /**
         * @usage
         * $app->router->get(path: '/contact', callback: function(){
         *     return 'contact';
         *  });
         */
        // set/add all of the get routes with given $path to our $this->_routes array
        $this->_routes['get'][$path] = $callback;
    }
    public function post($path, $callback)
    {
        /**
         * @usage
         * $app->router->post(path: '/contact', callback: function(){
         *     return 'contact';
         *  });
         */
        // set/add all of the post routes with given $path to our $this->_routes array
        $this->_routes['post'][$path] = $callback;
    }

    public function resolved()
    {
        // Get the $path using getPath() function from Request class. 
        $path = $this->request->getPath();

        // Get the $method using getMethod() function from Request class.
        $method = $this->request->method();
        
        // In here we set the all of the routes that the users define on index.php
        /**
         * @usage
         * $app->router->get(path: '/contact', callback: function(){
         *     return 'contact';
         *  });
         * 
         * Notes: if the users forgot to set the router then return false
         */
        $callback = $this->_routes[$method][$path] ?? false;


        // So if the callback is false;
        if($callback === false)
        {
            /**
             * because we set the static $app variable as Application instance
             * we can now able to call it and use the Application other variable instance,
             * like response, request,
             */
            // Application::$app->response->setStatusCode(404);
            
            $this->response->setStatusCode(404);
            // return $this->renderContent('Router Not found. You should register a router for this route.');
            // return $this->renderView('_404');
            throw new NotFoundException();
        }


        // Check if the callback is string
        // then this should be a view, so lets renderView
        if(is_string($callback)) 
        {
            return Application::$app->view->renderView($callback);
        }


        /**
         * Check if the $callback is array
         * if array then set a new instance of class that are included on that array.
         */
        if(is_array($callback)) {
            /** @var \app\core\Controller $controller */

            // this means that we are calling/creating a new instance of $callback[0] class,
            // and set it back again into $callback[0]
            
            // $callback[0] = new $callback[0]();

            // lets set the variable $controller to new instance of that controller callback
            $controller = new $callback[0]();


            // We can get the new instance of that callback controller and use it fo later.
            Application::$app->controller = new $controller;

            // lets set the controller action for later use.
            // $callback[1] is a method name.

            Application::$app->controller->action = $callback[1];

            // set the $callback[0] to a new instance of $controller
            $callback[0] = $controller;

            // MIDDLEWARES CHECKER HERE:
            // lets iterate on our middlewares
            // and execute it.
            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            };


        }

        // call_user_func — Call the callback given by the first parameter
        // 
        // this will call the function that you set from 
        // callback params using router->get or router->post

        // return call_user_func($callback);


        // lets use "call_user_func_array" —> Call a callback with an array of parameters
        // Notes: this will call or trigger the _method from _controller and pass the _params value into that _method.
        return call_user_func($callback, $this->request, $this->response);
        

    }

}