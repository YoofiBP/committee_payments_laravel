<?php


namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface ModelServiceInterface
{
    public function add(array $attributes);

    public function update(Model $model, array $data);

    public function delete(Model $user);

    public function all();

}
