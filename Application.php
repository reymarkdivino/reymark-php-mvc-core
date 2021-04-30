<?php

namespace app\core;

use app\core\db\Database;
use app\core\db\DbModel;

/**
 * Class Application
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class Application 
{
    public static string $ROOT_DIR;

    /**
     * Declare User Custom Class, so that we can use that custom user class 
     * inside on this Application class.
     */
    public string $userCustomClass;

    /**
     * Declare the Router class as Typed Property for $router variables
     */
    public Router $router;
    
    // default layout for layoutContent from router
    public string $layout = 'main';

    /**
     * Declare the Request class as Typed Property for $request variables
     */
    public Request $request;

    /**
     * Declare the Response class as Typed Property for $response variables
     */
    public Response $response;

    /**
     * Declare the Session class as Typed Property for $session variables
     */
    public Session $session;

    /**
     * Declare the Application class as Typed Property for $app variables
     */
    public static Application $app;

    /**
     * Declare the Controller class as Typed Property for $controller variables
     * Notes: ? before the declaring of class means this can be NULL,
     * and we can set the initial value to null
     */
    public ?Controller $controller = null;

    /**
     * Declare the Database class as Typed Property for $db variables
     */
    public Database $db;

    /**
     * Declare the DbModel class as Typed Property for $user variables
     * Notes: ? before the declaring of class means this can be NULL
     * and we can set the initial value to null
     */
    public ?DbModel $user;

     /**
     * Declare the View class as Typed Property for $view variables
     */
    public View $view;

    /**
     * $config
     */
    public $config;

    /**
     * Constructor
     */
    public function __construct(string $rootPath, array $config)
    {
        // set the static $ROOT_DIR into rootPath
        self::$ROOT_DIR = $rootPath;

        // you can set the $app variable to $this/or Application instance.
        self::$app = $this;


        // Create a new Router class instance for $this->request
        $this->request = new Request();

        // Create a new Response class instance for $this->response
        $this->response = new Response();

        // Create a new Response class instance for $this->session
        $this->session = new Session();

        // Create a new Router class instance for $this->router
        // I need to access the request instance in my Router,
        // so I've pass the $this->request into Router args.
        $this->router = new Router($this->request,$this->response);


        // Create a new Response class instance for $this->session
        $this->view = new View();

        // Database Config
        $this->db = new Database($config['db']);
        
        // Set the $this->config as $config['db']
        $this->config = $config['db'];

        
        /**
         * NOTES: USING THE CODE BELOW APPROACH,
         * WHEN WE NAVIGATE TO ANY POINT OF APPLICATION
         * THEN WE CAN ABLE TO FETCH THE USER
         */
        // ____ CODE FETCH USER.____ 

        // Set the userClass as the value of $config['userClass']
        // e.g. $config['userClass'] = \app\models\User::class
        $this->userCustomClass = $config['userCustomClass'];
        
        // get the primaryValue from current user session. -> this will be a user ID
        $primaryValue = $this->session->get('user');
        
        // check if the session user are existing
        if($primaryValue) {

            // lets get the primaryKey from our custom user class.
            $primaryKey = $this->userCustomClass::primaryKey();

            // fetch the user, using findOne() method from userCustomClass
            $this->user = $this->userCustomClass::findOne([$primaryKey=>$primaryValue]);
            
        } else {
            // if the user session are not exists set the $this->user to null.
            $this->user = null;
        }
        // NOTES: You can also create/set your own CustomClass and pass it to $config on public\index.php,
        // and you can use that CustomClass inside core Classes. e.g. Application. Like we did on 'userCustomClass'
        // ____ CODE FETCH USER.____ 

        // echo '<pre>';
        // var_dump($this->user);
        // echo '</pre>';

    }

    /**
     * This will resolved the router
     */
    public function run()
    {
        /**
         * After the router has been set then the resolved function
         * will do his job to resolved all of the route
         */
        try {
            echo $this->router->resolved();
        } catch (\Exception $e) {
            $this->response->setStatusCode(403);
            echo $this->view->renderView('_error', [
                'exception' => $e,
            ]);
        }
       
    }

    /**
     * @params \app\core\Controller $controller
     */
    public function setController(\app\core\Controller $controller): void
    {
        $this->controller = $controller;
    }
    
    /**
     * @return \app\core\Controller
     */
    public function getController(): \app\core\Controller
    {
        return $this->controller;
    }

    
    /**
     * Login ~
     * Save a user in a session
     * @param \app\core\DbModel $user
     */
    public function login(DbModel $user)
    {
        // lets take the id of the user and set it in session as user identifier.

        // get the DbModel $user instance and set it to $this->user
        $this->user = $user;
        // get the primaryKey
        $primaryKey = $user->primaryKey();
        // get the value of that primaryKey
        $primaryValue = $user->{$primaryKey};
        // set the session of that primaryKey value,
        // e.g. `user_id` == "123_user_ID", so the 'user' session will be "123_user_ID"
        $this->session->set('user',$primaryValue);
        return true;
    }

    /**
     * Logout the user.
     * 
     */
    public function logout()
    {
        // set the $this->user to null
        $this->user = null;
        // remove the session named 'user'
        $this->session->remove('user');
    }

    /**
     * If user is Guest, Means not authenticated.
     */
    public static function isGuest()
    {
        // if the user does not exists. (!self::$app->user)
        // this means that the user is Guest. then return TRUE
        return !self::$app->user;
    }
    
}