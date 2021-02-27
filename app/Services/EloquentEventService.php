<?php


namespace App\Services;


use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class EloquentEventService implements Interfaces\EventServiceInterface
{

    public function add(array $attributes)
    {
        try {
            return Event::create($attributes);
        } catch (\Throwable $err){
            throw $err;
        }

    }

    public function update(Model $event, array $data)
    {
        $event->fill($data)->save();
        return $event->refresh();
    }

    public function delete(Model $event)
    {
        try {
            return $event->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function all()
    {
       return Event::all();
    }
}
