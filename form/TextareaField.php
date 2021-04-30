<?php

namespace app\core\form;

/**
 * Class TextareaField
 * 
 * @author reymarkdivino <divinoreymark@gmail.com>
 * @package app\core
 */
class TextareaField extends BaseField
{


    // render or constract the Text Area Field.
    public function renderInput(): string
    {
        return sprintf('<textarea name="%s" class="form-control %s"></textarea>',
        $this->property_key,
        $this->model->hasError($this->property_key) ? 'is-invalid' : '',
    );
    }
}