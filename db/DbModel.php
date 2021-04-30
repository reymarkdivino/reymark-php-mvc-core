<?php

namespace app\core\db;

use app\core\Model;
use app\core\Application;

/**
 * Class DbModel
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
abstract class DbModel extends Model 
{
    

    /**
     * Constructor
     */
    public function __construct()
    {
        
    }

    abstract public static function tableName(): string;
    abstract public static function primaryKey(): string;
    abstract public function columnNames(): array;
    

    public function save()
    {
        $tableName = $this->tableName();
        $columnNames = $this->columnNames();

        // this is a sample insert into data:
        // $sql = "INSERT INTO `users` (
        //     -- `user_id`, 
        //     `email`, 
        //     `firstname`, 
        //     `lastname`, 
        //     `status`, 
        //     -- `created_at`, 
        //     `password`) VALUES (
        //         -- NULL, 
        //         'zxczxc@gmail.com', 
        //         'MyFirstname', 
        //         'MyLastname', 
        //         '1', 
        //         -- current_timestamp(), 
        //         'HelloWorld')";

        // BUT we will use more best practice:

        // updating the array with new value like:
        // e.g. "firstname" will now be "`firstname`"
        // so that we can use it for colum names later in statement.
        $params_for_column_names = array_map(fn($column) => "`$column`", $columnNames);

        $imploded_comlumn_names = implode(',', $params_for_column_names);

        // updating the array with new value like:
        // e.g. "firstname" will now be ":firstname"
        // so that we can use it for pdo statement bind later.
        $params_for_statement_binding = array_map(fn($column) => ":$column", $columnNames);

        $imploded_values_names = implode(',', $params_for_statement_binding);

        $sql = "INSERT INTO `". $tableName ."` (" . $imploded_comlumn_names . ") 
        VALUES($imploded_values_names)";
        
       
        $statement = self::prepare($sql);

        // echo '<pre>';
        // var_dump($statement, $sql, $imploded_values_names, $imploded_comlumn_names);
        // echo '</pre>';

        // Lets bind the value now
        foreach ($columnNames as $columnName) {
            $statement->bindValue(":$columnName", $this->{$columnName});
        }

        echo '<pre>';
        var_dump($statement, $sql, $imploded_values_names, $imploded_comlumn_names);
        echo '</pre>';

        // After we bind the value, we can now safely execute the statement
        $statement->execute();

        return true;

    }

    public static function findOne($where) // [email => test@gmail.com, firstname => Rey]
    {
        $tableName = static::tableName();
        $columnNames = array_keys($where);

        // SELECT * FROM `$tableName` WHERE `email` = :email AND `firstname` = :firstname

        // sql_partial will output something like: `email` = :email AND `firstname` = :firstname
        $sql_partial = implode("AND ", array_map(fn($colum_name) => "`$colum_name` = :$colum_name", $columnNames));

        $sql = "SELECT * FROM `$tableName` WHERE ".$sql_partial;

        // then prepare the sql query
        $statement = self::prepare($sql);
        
        // bind the vaue
        foreach ($where as $key => $value) {
            $statement->bindValue(":$key",$value);
        }
        // then execute the statement
        $statement->execute();
        
        return $statement->fetchObject(static::class);

    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}