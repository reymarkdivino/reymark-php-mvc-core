<?php

namespace app\core\form;

use app\core\Model;

/**
 * Class Form
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class Form 
{
    

    public static function begin($action, $method)
    {
        // return '<form action="" method="">';
        // https://www.php.net/manual/en/function.sprintf.php
        echo sprintf('<form action="%s" method="%s">',$action,$method);

        // return a new instance of the form
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, $property_key)
    {
        // return new instance of Field with model, and property key
        return new InputField($model, $property_key);
    }
}