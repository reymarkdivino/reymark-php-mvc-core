<?php

namespace app\core;

/**
 * Class View
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class View 
{
    public string $title = '';


    /**
     * $param is a array of data that will be available to views.
     */
    public function renderView($view, $params = [])
    {
        // $viewContent content are the content of $view.php
        $viewContent = $this->__renderOnlyView($view, $params);

        // $layoutContent are the content custom_layout.php with place holder {{content}}
        $layoutContent = $this->_layoutContent();

        // Now lets replace the string "{{content}}" content from $layoutContent,
        // and replace it with $viewContent that came from $view.php
        $final_content = str_replace('{{content}}', $viewContent, $layoutContent);

        // then return it to user.
        return $final_content;
    } 

    /**
     * Getting the main.php and put it into buffering.
     * then return that buffer.
     */
    protected function _layoutContent()
    {
        // we can get the Controller layout.
        $layout = Application::$app->layout;
        // Check if the instance of controller are exists
        if(Application::$app->controller){
            // if exists then lets get the layout of that controller
            $layout = Application::$app->controller->layout;
        }

        /**
         * ob_start(); This function will turn output buffering on. 
         * While output buffering is active no output is sent from the script (other than headers), i
         * nstead the output is stored in an internal buffer.
         */

        // its like output caching. it means that all of your script on ob_start() block 
        // will not be sent to user client.
        ob_start(); // -- Ob Start --
        
        // including the main.php
        // include_once Application::$ROOT_DIR."/views/layouts/main.php";

        // OR
        // including custom layout
        include_once Application::$ROOT_DIR."/views/layouts/$layout.php";


        // ob_get_clean — Get current buffer contents and delete current output buffer
        return ob_get_clean(); // -- Ob END --
    }


    
    /**
     * @param $view // will be the view file (e.g. profile ) -> means profile.php
     * @param $params, array of $data that will be available to the vew for later use.
     */
    protected function __renderOnlyView($view, $params = []) {

        // Converting the name of params into variable name,
        // and set the value of that newly variable name.
        foreach ($params as $data_name => $data_value) {
            // $key/data_name = this will be evaluated as a name variable
            // this means that the $key/data_name will be variable name
            // e.g if $key = name then it will be $name.
            // and then the $name variable value will be $value.
            // its like: ${$data_name} = $data_value;
            $$data_name = $data_value;
        }

        // uncomment the code below to see the data available for use.
        // echo '<pre>AVAILABLE DATA TO BE USE:<br/>';
        // var_dump($this);
        // echo '<pre>';

        ob_start();
        include_once Application::$ROOT_DIR."/views/$view.php";
        return ob_get_clean(); // -- Ob END --
    }


    public function renderContent($viewContent)
    {
        // $layoutContent content are the main.php
        $layoutContent = $this->_layoutContent();
        
         // Now lets replace the string "{{content}}" content from $layoutContent,
        // and replace it with $viewContent that came from variable $viewContent
        $final_content = str_replace('{{content}}', $viewContent, $layoutContent);

        // then return it to user.
        return $final_content;
        
    } 


}