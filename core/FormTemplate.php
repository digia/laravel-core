<?php

namespace Digia\Core;

use Validator, App;

class FormTemplate 
{
    protected $inputData;
    protected $validationRules;
    protected $validationMessages;
    protected $attributeFields;
    protected $fieldNamespace;
    protected $uploadPath;

    public function __construct()
    {
        $this->inputData = App::make('request')->all();    
    }

}
