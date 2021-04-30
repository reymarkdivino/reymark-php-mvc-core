<?php

namespace app\core;

/**
 * Class Model
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
abstract class Model 
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'matched';
    public const RULE_UNIQUE = 'unique';

    /**
     * Constructor
     */
    public function __construct()
    {
        
    }

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            // First, Check if the $key are existing property 
            // of our custom model class(e.g. RegisterModel class.)
            // We use $this on property_exists function because this current model will be,
            //  extends in our custom model class(e.g. RegisterModel class.)
            // 
            if(property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    // abstract methods will force child class from implementing this method.
    abstract public function rules(): array;

    public function labels(): array
    {
        return [];
    }

    public function getLabel($property_key)
    {
        return $this->labels()[$property_key] ?? $property_key;
    }

    public array $errors = [];

    public function validate()
    {
        // foreach ($this->rules() as $key => $value) {
        //     # code...
        // }

        // e.g. sample Return from array rules() should look like, in a custom model(e.g. RegisterModel class)
        // return [
        //     'firstname' => [self::RULE_REQUIRED],
        //   'lastname' => [self::RULE_REQUIRED],
        //    'email' => [
        //        self::RULE_REQUIRED,
        //        self::RULE_EMAIL, 
        //        [self::RULE_UNIQUE, 'class' => self::class, 'columnName' => 'email'] 
        //    ],
        //    'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min'=>8],[self::RULE_MAX, 'max'=>24]],
        //    'passwordConfirm' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match'=>'password']],
        // ]

        // So e.g. 'firstname' => [self::RULE_REQUIRED],
        // then the 'firstname' is $property_key,
        // the [self::RULE_REQUIRED] is $rules, and the $rules can be array like: e.g. [self::RULE_REQUIRED, [self::RULE_MIN, 'min'=>8],[self::RULE_MAX, 'max'=>24]]
        foreach ($this->rules() as $property_key => $rules) {

            // lets get the value of that property
            // e.g. if $property_key == firstname then
            // the $this->firstname will be set as $value
            $value = $this->{$property_key};
            
            foreach($rules as $rule) {
                // GETTING A RULE NAME
                $ruleName = $rule;
                
                // if the ruleName is not string
                if(!is_string($ruleName)) {

                    // this should be an array,
                    // so lets take the actual rule name
                    $ruleName = $rule[0];

                }

                // NOW LETS CHECK.

                // if the $ruleName is equal self::RULE_REQUIRED, and the value is empty then:
                if($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($property_key, self::RULE_REQUIRED);
                }


                // if the $ruleName is equal self::RULE_EMAIL, and the value is not valid email then:
                if($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($property_key, self::RULE_EMAIL);
                }


                // if the $ruleName is equal self::RULE_MIN, and the value length is less than $rule['min'] (e.g. 8), then:
                if($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($property_key, self::RULE_MIN, $rule);
                }

                // if the $ruleName is equal self::RULE_MAX, and the value length is more than $rule['min'] (e.g. 24), then:
                if($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($property_key, self::RULE_MAX, $rule);
                }


                // if the $ruleName is equal self::RULE_MAX, and the value length is more than $rule['min'] (e.g. 24), then:
                if($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($property_key, self::RULE_MATCH, $rule);
                }

                // if the $ruleName is equal self::RULE_UNIQUE
                if($ruleName === self::RULE_UNIQUE) {
                    // get the class name
                    $className = $rule['class'];
                    // get the column name that will be unique
                    $uniqueColumnName = $rule['columnName'] ?? $property_key;
                    // get the table name from that classname instance.
                    $tableName = $className::tableName();

                    // do a query statement: query sql, then bindValue, then execute
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueColumnName = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();

                    // fetch the record.
                    $record = $statement->fetchObject();
                    // if there is already record then add new Error
                    if($record) {
                        // so the {field} are not exist on $rule so we can define directly here
                        // and pass the $property_key/colomnName
                        $this->addErrorForRule($property_key, self::RULE_UNIQUE, ['field' => $this->getLabel($property_key)] );
                    }
                }

            }
        }

        // if $this->errors then return TRUE
        return empty($this->errors);
    }

    public function addErrorForRule(string $property_key, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }

        $this->errors[$property_key][] = $message;
    }

    public function addError(string $property_key, string $message)
    {
        $this->errors[$property_key][] = $message;
    }

    public function errorMessages()
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be a valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}',
            self::RULE_UNIQUE => 'Record with this {field} already exists',
        ];
    }

    public function hasError($property_key)
    {
        /**
         * Check if the $property_key is existing or have a value from errors
         * then return true, else return false.
         */
        return $this->errors[$property_key] ?? false;
    }

    public function getFirstError($property_key)
    {
        return $this->errors[$property_key][0] ?? false;
    }
}