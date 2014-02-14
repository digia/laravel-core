<?php

namespace Digia\Core;

use Validator, App;
use Illuminate\Support\MessageBag;
use Digia\Core\Exception\NoValidationRulesException;
use Digia\Core\Exception\NoValidatorInstantiatedException;

abstract class FormEntity
{
    /**
     * All of the input data from the request
     *
     * @var array
     */
    protected $inputData = [];

    /**
     * Validator
     *
     * @param Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Form validation rules
     *
     * @var array
     */
    protected $validationRules = [];

    /** 
     * Form validation messages
     *
     * @var array
     */
    protected $validationMessages = [];

    /**
     * Fields to keep form the input data
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Field namespace 
     *
     * @var string
     */
    protected $namespace = null;

    public function __construct()
    {
        $this->inputData = App::make('request')->all();    
    }

    /**
     * Check if the form has attributes from the input data
     *
     * @return boolean
     */
    public function hasAttributes()
    {
        $attributes = $this->getAttributes(); 

        return (count($attributes) > 0);
    }

    /** 
     * Get the attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $fillable = $this->fillable;
        $input = $this->inputData;
        $attributes = [];

        foreach ($fillable as $field) {
            if (isset($input[$field]) && ! empty($input[$field])) {
                $attributes[$this->removeNamespace($field)] = $input[$field];
            }
        }

        return $attributes;
    }

    /**
     * Get the input data
     *
     * @param string $key Get a single value from the input data
     *
     * @return array
     */
    public function getInputData($key = null)
    {
        if (is_null($key)) return $this->inputData;
        
        return $this->inputData[$key];
    }

    /**
     * Check if the attributes are valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $this->beforeValidation();

        if ( ! isset($this->validationRules)) {
            throw new NoValidationRulesException('No validation rules found on class ' . get_called_class());
        }

        $this->validator = Validator::make($this->getInputData(), $this->validationRules, $this->validationMessages);

        return $this->validator->passes();
    }

    protected function beforeValidation() {}

    /**
     * Get the validation errors with or without the field namespace
     *
     * @param boolean $stripNamespace
     *
     * @return Illuminate\Support\MessageBag
     */
    public function getErrors($stripNamespace = true)
    {
        if ( ! $this->validator) throw new NoValidatorInstantiatedException; 

        $errors = $this->validator->errors();

        if ( ! $stripNamespace) return $errors;

        $cleanedErrors = $this->removeNamespace($errors->toArray());

        return new MessageBag($cleanedErrors);
    }

    /**
     * Remove the namespace from the field names
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function removeNamespace(array $attributes)
    { 
        $namespace = $this->namespace . '_';  
        $cleaned = [];

        foreach ($attributes as $key => $value) {
            $key = str_replace($namespace, '', $key);
            $cleaned[$key] = $value;
        }

        return $cleaned;
    }

    /**
     * Get a value from the attributes and unset it
     *
     * @param string $key
     *
     * @return string
     */
    protected function pluck($key)
    {
        $data = $this->attributes[$key];
        
        unset($this->attributes[$key]);

        return $data;
    }

}
