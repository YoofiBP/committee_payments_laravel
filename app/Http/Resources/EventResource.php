<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'venue' => $this->venue,
            'event_date' => $this->event_date,
            'flyer' => $this->flyer,
            'total_contribution' => $this->when(Auth::user() !== null && Auth::user()->isAdministrator(), $this->total_contribution)
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode(200);
    }
}
