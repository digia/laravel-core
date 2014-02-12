<?php

namespace Digia\Core;

abstract class RepositoryTemplate 
{
    protected $model;

    public function __construct($model = null)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model; 
    }

    public function setModel($model)
    {
        $this->model = $model;    
    }

    public function getNew($attributes = [])
    {
        return $this->model->newInstance($attributes); 
    }
}
