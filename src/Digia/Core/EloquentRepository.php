<?php

namespace Digia\Core;

use Illuminate\Database\Eloquent\Model;
use Digia\Core\Exception\EntityNotFoundException;
use Digia\Core\Exception\SaveTypeNotFoundException;

abstract class EloquentRepository 
{
    protected $model;

    public function __construct(Model $model)
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

    public function getAll()
    {
        return $this->model->all(); 
    }

    public function getAllWithTrash()
    {
        return $this->model->withTrashed()->all(); 
    }

    public function getAllOnlyTrash()
    {
       return $this->model->onlyTrashed()->all(); 
    }

    public function getAllPaginated($count)
    {
        return $this->model->paginated($count); 
    }

    public function getById($id)
    {
        return $this->model->find($id); 
    }

    public function requireById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function requireByIdWithTrashed($id)
    {   
        return $this->model->withTrashed()->findOrFail($id);    
    }

    /**
     * Get a new model
     *
     * @param array $attributes
     *
     * @return instanceOf Illuminate\Database\Eloquent\Model
     */
    public function getNew($attributes = [])
    {
        return $this->model->newInstance($attributes); 
    }

    /**
     * Save data to the database
     *
     * @param array|Illuminate\Database\Eloquent\Model $data
     *
     * @return instanceOf Illuminate\Database\Eloquent\Model
     */
    public function save($data)
    {
        if ($data instanceOf Model) {
            return $this->storeEloquentModel($data);
        } elseif (is_array($data)) {
            return $this->storeArray($data);
        }

        throw new SaveTypeNotFoundException;
    }

    /**
     * Store the passed in eloquent model
     *
     * @param instanceOf Illuminate\Database\Eloquent\Model
     *
     * @return instanceOf Illuminate\Database\Eloquent\Model
     */
    protected function storeEloquentModel($model)
    {
        if ($model->getDirty()) {
           return $model->save(); 
        } else { 
            return $model->touch();
        }
    }

    /**
     * Save array data to a new eloquent model
     *
     * @param array $data
     *
     * @return instanceOf Illuminate\Database\Eloquent\Model
     */
    protected function storeArray($data)
    {
        $model = $this->getNew($data); 

        return $this->storeEloquentModel($model);
    }

    /**
     * Delete the passed in model
     *
     * @param instanceOf Illuminate\Database\Eloquent\Model
     *
     * @return boolean
     */
    public function delete($model)
    {
        return $model->delete(); 
    }

    /**
     * Check for a duplicate model within the database
     * 
     * @param array $data
     * @param       $model
     * @param array $fillable
     *
     * @return null| instanceOf Illuminate\Database\Eloquent\Model
     */
    public function hasDuplicate(array $data)
    {
        $model = $this->getNew();

        foreach ($this->model->getFillable() as $column) {
            if (isset($data[$column])) $model = $model->where($column, $data[$column]);
        }

        return $model->first();
    }
}
