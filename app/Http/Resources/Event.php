<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Event extends JsonResource
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
            'calendar_id' => $this->calendar_id,
            'day' => $this->calendar->date,
            'contain' => [[
                'name' => $this->name,
                'category' => $this->category,
                'description' => $this->description,
                'background_color' => $this->background_color,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date
            ]]
        ];
    }

    protected function transformContain() {
        //
    }
}
