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
            'id' => $this->id,
            'title'=>$this->title,
            'caption' => $this->caption,
            'day_of_week' => $this->day_of_week,
            'image' => $this->transformImage(),
            'number_of_viewer' => $this->countViewer(),
//            'viewer' => $this->getViewer(),
            'created_at' => $this->created_at,
        ];
    }
}
