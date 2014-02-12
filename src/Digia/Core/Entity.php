<?php

namespace Digia\Core;

use Eloquent, Validator;
use Digia\Core\Exception\NoValidationRulesException;
use Digia\Core\Exception\NoValidatorInstantiatedException;

abstract class Entity extends Eloquent 
{
    /**
     * Rules used for validating entity
     *
     * @var array
     */
    protected $validationRules = [];

    /**
     * Messages to use with validation rules/erros
     *
     * @param array
     */
    protected $validationMessages = [];

    /**
     * Validator
     *
     * @var Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * Validate and save the entity
     *
     * @param array $attributes
     * 
     * @return {mixed}
     */
    public function save(array $attributes = [])
    {
        if ( ! $this->isValid()) return false;

        return parent::save($attributes);
    }

    /**
     * Check if the entity attributes pass validation
     *
     * @return boolean
     */
    public function isValid()
    {
        if ( ! isset($this->validationRules)) {
            throw new NoValidationRulesException('No validation rule array defined on ' . get_called_class());
        }

        $this->validator = Validator::make($this->getAttributes(), $this->getPreparedRules(), $this->validationMessages);

        return $this->validator->passes();
    }

    /**
     * Get the prepared validation rules
     *
     * @return array
     */
    protected function getPreparedRules()
    {
         return $this->replaceIdsIfExists($this->validationRules);
    }

    /**
     * Replace the <id> string with the entities primary id integer
     *
     * @param array $rules
     *
     * @return array
     */
    protected function replaceIdsIfExists($rules)
    {
        $newRules = [];

        foreach ($rules as $key => $rule) {
            if (str_contains($rule, '<id>')) {
                $replacement = $this->exists ? $this->getAttribute($this->primaryKey) : '';

                $rule = str_replace('<id>', $replacement, $rule);
            }

            array_set($newRules, $key, $rule);
        }

        return $newRules;
    }

    public function getErrors()
    {
        if ( ! $this->validator) throw new NoValidatorInstantiatedException;

        return $this->validator->errors();
    }
}
