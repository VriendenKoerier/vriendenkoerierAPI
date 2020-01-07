<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Packet extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'deliverer_id' => $this->deliverer_id,
            'title' => $this->title,
            'description' => $this->description,
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length,
            'weight' => $this->weight,
            'photo' => $this->photo,
            'postcode_a' => $this->postcode_a,
            'postcode_b' => $this->postcode_b,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        //niet laten zien
        // "contact"
        // "adres_a"
        // "adres_b"
        // "avg_confirmed"
        // "show_hash"
        // "deny_hash"
    }

    public function with($request)
    {
        return [
            'version' => '1.0.0',
            'author_url' => url('http://vriendenkoerier.nl')
        ];
    }
}
