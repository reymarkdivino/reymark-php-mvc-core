<?php

namespace app\core;

/**
 * Class Session
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class Session 
{

    protected const _FLASH_KEY = 'flash_message';

    /**
     * Constructor
     */
    public function __construct()
    {
        session_start();

        // Take all flashMessages
        $flashMessages = $_SESSION[self::_FLASH_KEY] ?? [];

        // ref: https://www.php.net/manual/en/language.references.pass.php
        // by using "&" from the start of variable,
        // Only variables should be passed by reference
        foreach ($flashMessages as $key => &$flashMessage) {
            // Mark to be remove
            $flashMessage['remove'] = true;
        }

        $_SESSION[self::_FLASH_KEY] = $flashMessages;

        // echo '<pre>';
        // var_dump($_SESSION[self::_FLASH_KEY]);
        // echo '</pre>';
       
    }

    public function setFlash($key, $message)
    {
        $_SESSION[self::_FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::_FLASH_KEY][$key]['value'] ?? false;
    }

    public function __destruct()
    {
        // Take all flashMessages
        $flashMessages = $_SESSION[self::_FLASH_KEY] ?? [];

        // ref: https://www.php.net/manual/en/language.references.pass.php
        // by using "&" from the start of variable,
        // Only variables should be passed by reference
        foreach ($flashMessages as $key => &$flashMessage) {
            // Mark to be remove
            if($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }

        $_SESSION[self::_FLASH_KEY] = $flashMessages;

        // echo '<pre>';
        // var_dump($_SESSION[self::_FLASH_KEY]);
        // echo '</pre>';
    }

    /**
     * Set new session
     * @param $key
     * @param $value
     */
    public function set($key,$value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get the session using $key
     * @param $key
     */
    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    /**
     * Remove the session using $key
     * @param $key
     */
    public function remove($key)
    {
        unset($_SESSION[$key]);
    }
}