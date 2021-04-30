<?php

namespace app\core\form;

use app\core\Model;

/**
 * Class InputField
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class InputField extends BaseField
{

    public string $type_of_field;

    // public Model $model;

    // public string $property_key;

    /**
     * Constructor
     */
    public function __construct(Model $model, string $property_key)
    {
        $this->type_of_field = self::TYPE_TEXT;
        // $this->model = $model;
        // $this->property_key = $property_key;
        parent::__construct($model,$property_key);
    }



    public function passwordField()
    {
        $this->type_of_field = self::TYPE_PASSWORD;
        return $this;
    }

    // public function textareaField()
    // {
    //     $this->type_of_field = self::TYPE_TEXTAREA;
    //     return $this;
    // }

    // public function formValue()
    // {
    //     return $this->type_of_field === self::TYPE_PASSWORD ? '******' : $this->model->{$this->property_key};
    // }

    public function renderInput(): string
    {
        // if($this->type_of_field == self::TYPE_TEXTAREA)
        // {
        //     return '<textarea type="%s" name="%s" value="%s" class="form-control %s"></textarea>';
        // }
        // return '<input type="%s" name="%s" value="%s" class="form-control %s">';

        return sprintf('<input type="%s" name="%s" value="%s" class="form-control %s">',
        $this->type_of_field,
        $this->property_key,
        $this->model->{$this->property_key},
        $this->model->hasError($this->property_key) ? 'is-invalid' : '',
    );
    }
}