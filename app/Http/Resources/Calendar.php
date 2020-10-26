<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Calendar extends JsonResource
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
//            'id' => $this->id,
//            'date' => $this->date,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at
            $this->date => $this->getdata(),
        ];
    }

    public function getdata()
    {
        $calendar = $this->find($this->id);
        $events = $calendar->events;

        return $events->toArray();
    }

}
