<?php


namespace App\Services;


use App\Models\Event;
use App\Services\Traits\ImplementsCrudActions;
use App\Services\Interfaces\EventServiceInterface;

class EloquentEventService implements EventServiceInterface
{

    use ImplementsCrudActions;

    protected Event $model;

    public function __construct(Event $model){
        $this->model = $model;
    }

}
