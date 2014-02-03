<?php

namespace Digia\Core;

use Eloquent, Validator;

abstract class EntityTemplate extends Model 
{
    /**
     * Rules used for validating entity
     *
     * @var array
     */
    protected $validationRules = [];

    protected $validator;
}
