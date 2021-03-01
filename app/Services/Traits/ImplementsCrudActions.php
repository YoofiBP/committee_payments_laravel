<?php


namespace App\Services\Traits;


use Illuminate\Database\Eloquent\Model;

trait ImplementsCrudActions
{
    public function update(Model $model, array $data){
        $model->fill($data)->save();
        return $model->refresh();
    }

    public function all(){
        return $this->model::all();
    }

    public function delete(Model $model){
        try{
            return $model->delete();
        } catch (\Throwable $err){
            throw $err;
        }
    }

    public function add(array $attributes){
        try{
            return $this->model::create($attributes);
        } catch (\Throwable $err){
            throw $err;
        }
    }

}
