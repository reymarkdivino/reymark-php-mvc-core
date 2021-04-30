<?php

namespace app\core;

/**
 * Class Request
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class Request 
{
  
    /**
     * Constructor
     */
    public function __construct()
    {
      
    }

    /**
     * This will return the path.
     * e.g. user navigate to "http://localhost:8080/users" then it will return '/users'
     * or if "http://localhost:8080/users?id=1" then still it will return '/users'
     */
    public function getPath()
    {
        // echo "<pre>";
        // var_dump($_SERVER);
        // echo "</pre>";

        /**
         * Lets get the REQUEST_URI using $_SERVER['REQUEST_URI']
         * e.g. if the user navigate to your site "http://localhost:8080/users"
         * then the REQUEST_URI value will be: "/users"
         * or if "http://localhost:8080/users?id=1" then the 
         * REQUEST_URI value will be: "/users?id=1"
         * 
         * Note: Request '/' if REQUEST_URI is empty or null
         */
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        
        /**
         * Find the ? character position from path url.
         * e.g. "/users?id=1" will return the position as int(6)
         * 
         * Notes: if there is no ? then it will return false
         */
        $position = strpos($path, '?');
        
        // if the $position is false then return the $path
        if($position === false) {
            return $path;
        }
        
        /**
         * The substr() function returns a part of a string.
         * so using substr we are taking the string from 0 to $posision of "?" from path.
         * e.g. "/users?id=1" then we will take the "/users"
         */
        $from_position = 0;
        $to_position = $position;
        $path = substr($path, $from_position, $to_position);
        // e.g. this will return the path "/users"
        return $path;
    }

    /**
     * This will return string 'get' or 'post' if the request is GET or POST
     */
    public function method()
    {
        // The strtolower() function converts a string to lowercase.   
        // So this will return 'get' or 'post'
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() === 'get';
    }

    public function isPost()
    {
        return $this->method() === 'post';
    }

    /**
     * handling POST, or GET data
     */
    public function getBody()
    {
        $body = [];
        if($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                // Lets sanitize the '$_GET' data here:
                // remove some invalid special chars from input GET $key and put it into $body[$key]
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                // Lets sanitize the '$_POST' data here:
                // remove some invalid special chars from input POST $key and put it into $body[$key]
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}