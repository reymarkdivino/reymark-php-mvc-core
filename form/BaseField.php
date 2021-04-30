<?php

namespace app\core\form;

use app\core\Model;

/**
 * Class BaseField
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
abstract class BaseField 
{
    public const TYPE_TEXT = 'text';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';
    // public const TYPE_TEXTAREA = 'textarea';


    // this will force the child class to use the renderInput
    abstract public function renderInput(): string;

    // public Model $model;

    // public string $property_key;

     /**
     * Constructor
     */
    public function __construct(public Model $model, public string $property_key)
    {
        $this->model = $model;
        $this->property_key = $property_key;
    }

    // render input fields.
    public function __toString()
    {
        $constructed_output_field = sprintf('
                    <div class="form-group">
                        <label>%s</label>
                        %s
                        <div class="invalid-feedback">
                            %s
                        </div>
                    </div>
                ',
                $this->model->getLabel($this->property_key),
                $this->renderInput(),
                // $this->type_of_field,
                // $this->property_key,
                // $this->model->{$this->property_key},
                // $this->model->hasError($this->property_key) ? 'is-invalid' : '',
                $this->model->getFirstError($this->property_key)
            );

        return $constructed_output_field;
    }
}