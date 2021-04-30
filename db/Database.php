<?php

namespace app\core\db;

use app\core\Application;

/**
 * Class Database
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class Database 
{
    public \PDO $pdo;

    public $config;
    /**
     * Constructor
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';

        $this->pdo = new \PDO($dsn, $user, $password);

        // This will throw an error if something went wrong or any problem happen in our database.
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    }


    public function applyMigration()
    {
        // ____ CHECK FIRST IF THE ACTION IS ROLLBACK THEN ROLLBACK!!!
        /**
         * ref: https://www.igorkromin.net/index.php/2017/12/07/how-to-pass-parameters-to-your-php-script-via-the-command-line
         * ref: https://www.php.net/manual/en/function.getopt.php
         * @usage: php migartions.php --action=rollback
         * 
         */
        $cmd_args = getopt('', ["action:"]);
        
        // if cmd_args is not empty.
        if(!empty($cmd_args)) {
            // if action is equal to rollback, means lets rollback
            if($cmd_args["action"] === "rollback"){
                // do rollback
                $this->migrationsRollbackOnlyOne();
                exit;
            }
        }
        // ____ CHECK FIRST IF THE ACTION IS ROLLBACK THEN ROLLBACK!!!


        // ____ DO A MIGRATION HERE!!
        /**
         * If the migrations table are not exists create it first.
         */
        $this->createMigrationsTable();

        $appliedMigrations = $this->getAppliedMigrations();

        // scan the files from migrations folder,
        // this should return an array of file names
        $files = scandir(Application::$ROOT_DIR.'/migrations');



        // array_diff: Compares array against one or more other arrays and returns the values in array that are not present in any of the other arrays.
        $toApplyMigrations = array_diff($files, $appliedMigrations);


        // save the new migration in $newMigrations for late use.
        $newMigrations = [];

        

        foreach ($toApplyMigrations as $migration) {
            if($migration === "." || $migration === "..") {
                // continue the foreach loop
                continue;
            }
            
            // require the file migrations.
            require_once Application::$ROOT_DIR.'/migrations/'.$migration;

            // get the className of migration file
            // pathinfo: in here we will only get the FILENAME and not the extention .php,
            //  e.g. "m0001_initial.php" will now be "m0001_initial", so the class name should be m0001_initial
            $className = pathinfo($migration, PATHINFO_FILENAME);

            // Now we can create a new instance of migrations files from migrations folder, ang we can call the up or down method
            $newInstance_of_migration_class = new $className;

          
          
            $this->_log("Applying migration $migration");

            // call the up() method
            $newInstance_of_migration_class->up();

            $this->_log("Applied migration $migration");

            // save/add to array the new $migration
            $newMigrations[] = $migration;

        }

        // if the $newMigrations is not empty let save the migrations to database.
        if(!empty($newMigrations)) {
            $this->saveMigratioins($newMigrations);
        } else {
            $this->_log("All migrations are applied.");
        }
        // ____ DO A MIGRATION HERE!!

    }

    public function saveMigratioins(array $migrations) 
    {
        // BASIC INSERT.
        // foreach ($migrations as $migration) {
        //     $sql = "INSERT INTO `migrations` (`migration`) 
        //     VALUES ('$migration');";
        //     $this->pdo->exec($sql);
        // }

        // OR you can save using this:
       
        // Using array_map we can update the value of migratioins array and add something like:
        // e.g. m0001_initial.php then it should now be ('m0001_initial.php')
        $newMigrations = array_map(fn($m) => "('$m')",$migrations);

        // we can use implodde to array
        $str = implode(',', $newMigrations); // output: e.g. "('m0001_initial.php'),('m0002_something.php')"
        
        // lets insert the value,
        // this will insert all of the $str to `migration` 
        $statement = $this->pdo->prepare("INSERT INTO `migrations` (`migration`) VALUES $str;");

        $statement->execute();
    }

    
    public function prepare($sql)
    {
        return $this->pdo->prepare($sql);
    }

    protected function _log($message)
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }

    public function createMigrationsTable()
    {

        // Create a migrations table if not exists
        /**
         * @data `id` INT NOT NULL AUTO_INCREMENT
         * @data `migration` VARCHAR(255) NOT NULL
         * @data `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
         * @primary_key PRIMARY KEY  (`id`)
         */

        $sql = "CREATE TABLE IF NOT EXISTS `". $this->config['dbname']. "`.`migrations` (
            `id` INT NOT NULL AUTO_INCREMENT ,  
            `migration` VARCHAR(255) NOT NULL ,  
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,    
            PRIMARY KEY  (`id`)) ENGINE = InnoDB;";

        $this->pdo->exec($sql);
        
    }

    public function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }


    public function migrationsRollbackOnlyOne() {
        
         // LOGIC:
        // First we need to check from migration table if there is a migrated data
        
        // Get the last migration data:
        $statement = $this->pdo->prepare("SELECT * FROM `migrations` ORDER BY id DESC LIMIT 1");
        $statement->execute();

        $lastMigrations = $statement->fetchAll(\PDO::FETCH_ASSOC);
        


        // remove the row from that table.
        $statement = $this->pdo->prepare("DELETE FROM `migrations` WHERE `migrations`.`id` = " . $lastMigrations[0]['id']);
        $statement->execute();

        // require the file migrations first before creating a new instance of this migration.
        require_once Application::$ROOT_DIR.'/migrations/'.$lastMigrations[0]['migration'];

        // then use the name of that migration
        // notes: this will remove the .php from the migration name.
        $className = pathinfo($lastMigrations[0]['migration'], PATHINFO_FILENAME);

        // and create a new instance of that class, from migrations folder
        $newInstance_of_LAST_migration_class = new $className;


        $this->_log("Applaying Rollback migration $className");
        // then trigger the down() method.
        $newInstance_of_LAST_migration_class->down();

        $this->_log("Applied Rollback migration $className");
    }
    
}