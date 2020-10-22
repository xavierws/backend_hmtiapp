<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class Feed extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'caption' => $this->caption,
            'day_of_week' => $this->day_of_week,
            'image' => $this->transformImage(),
            'number_of_viewer' => $this->countViewer(),
//            'viewer' => $this->getViewer(),
            'created_at' => $this->created_at,
            'title'=>$this->title
        ];
    }

    /**
     * transform the image into base64 byte string
     *
     * @return array
     */
    protected function transformImage()
    {
        $feed = $this->find($this->id);

        $n = 0;
        $arrayOfImg = array();
        foreach ($feed->images as $image) {
            $filename = $image->filename;
//            $imgFile = Storage::get($filename);

//            $encodedImg = base64_encode($imgFile);

            $arrayOfImg[$n] = Storage::url($filename);
            $n++;
        }

        return $arrayOfImg;
    }

    /**
     * get all the feed's viewer name
     *
     * @return array
     */
    protected function getViewer()
    {
        $feed = $this->find($this->id);

        $n = 0;
        $arrayOfName = array();
        foreach ($feed->collegerProfiles as $collegerProfile) {
            $arrayOfName[$n] = $collegerProfile->name;
        }

        return $arrayOfName;
    }

    /**
     * get the number of viewer
     *
     * @return mixed
     */
    protected function countViewer()
    {
         return $this->find($this->id)->collegerProfiles->count();
    }
}
