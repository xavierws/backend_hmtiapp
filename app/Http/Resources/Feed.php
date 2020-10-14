<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class Feed extends JsonResource
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
            'caption' => $this->caption,
            'day_of_week' => $this->day_of_week,
            'image' => $this->transformImage(),
            'created_at' => $this->created_at
        ];
    }

    protected function transformImage()
    {
        $feed = $this->find($this->id);

        $n = 0;
        $arrayOfImg = array();
        foreach ($feed->images as $image){
            $filename = $image->filename;
            $imgFile =  Storage::get($filename);

            $encodedImg = base64_encode($imgFile);

            $arrayOfImg[$n] = $encodedImg;
            $n++;
        }

        return $arrayOfImg;
    }
}
