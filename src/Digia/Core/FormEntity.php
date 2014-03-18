<?php


/**
 * IDEAS
 */
// Eloquent getXXXAttribute
// __get Magic method
// incorporate uuid
// isBeingUpdated?

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
     * Attributes 
     *
     */
    protected $attributes = null;

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

    /**
     * Load the input 
     *
     * @param array $input;
     */
    public function load(array $input = [])
    {
        if (empty($input)) {
            $this->inputData = App::make('request')->all(); 
        } else {
            $this->inputData = $input;
        }

        return $this;
    }

    /**
     * Get the input
     *
     * @return array
     */
    public function getInput()
    {
        return $this->inputData; 
    }

    /**
     * Check if the form has attributes from the input data
     *
     * @param array $without
     *
     * @return boolean
     */
    public function hasAttributes(array $without = [])
    {
        $attributes = $this->getAttributes(); 

        if ( ! empty($without)) {
            array_map('unset', $attributes); 
        }

        return (count($attributes) > 0);
    }

    /** 
     * Get the attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->hasAttributesCache()) return $this->getAttributesCache();

        $fillable = $this->fillable;
        $input = $this->inputData;
        $attributes = [];

        foreach ($fillable as $field) {
            if (isset($input[$field]) && ! $this->isEmpty($input[$field])) {
                $attributes[$this->removeNamespace($field)] = $input[$field];
            }
        }

        if ( ! $this->hasAttributesCache()) $this->setAttributesCache($attributes);

        return $attributes;
    }

    protected function hasAttributesCache()
    {
        return ! is_null($this->attributes);
    }

    protected function setAttributesCache($attributes)
    {
        $this->attributes = $attributes; 
    }

    protected function getAttributesCache()
    {
        return $this->attributes; 
    }

    /**
     * Reset the attributes cache
     *
     * @return {object}
     */
    public function resetAttributesCache()
    {
        $this->attributes = null; 

        return $this;
    }

    /**
     * Remove the namespace from the field names
     *
     * @param array|string $attributes
     *
     * @return array|string
     */
    protected function removeNamespace($attributes)
    { 
        if (is_null($this->namespace)) return $attributes;

        $namespace = $this->namespace . '_';  

        if ( ! is_array($attributes)) return str_replace($namespace, '', $attributes);

        $cleaned = [];
        foreach ($attributes as $key => $value) {
            $key = str_replace($namespace, '', $key);
            $cleaned[$key] = $value;
        }

        return $cleaned;
    }

    protected function isEmpty($value)
    {
        if (is_null($value))
        {
            return true;
        }
        elseif (is_string($value) && trim($value) === '')
        {
            return true;
        } 

        return false;
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

        $this->validator = Validator::make($this->inputData, $this->validationRules, $this->validationMessages);

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
     * Check if the form input data has a uuid field
     *
     * @return boolean
     */
    public function hasId()
    {
        $attributes = $this->getAttributes();

        if ( ! isset($attributes['id'])) return false;

        return true;
    }

    /**
     * Get the form uuid from the input data
     *
     * @return string
     */
    public function getId()
    {
        $attributes = $this->getAttributes();

        if ( ! isset($attributes['id'])) return null;

        return $attributes['id'];
    }

}
