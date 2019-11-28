<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Package extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
        // return [
        //     'title' => $request->title,
        //     "id" => 1,
        //     "title" => "Facilis irure in eni",
        //     "name" => "Ingrid Rios",
        //     "description" => "Minim officia molest sdf sd fsd fsdf sdsdf",
        //     "height" => 89,
        //     "width" => 96,
        //     "length" => 62,
        //     "weight"=> 26,
        //     "photo" => "1573832405_png",
        //     "email" => "qotusud@mailinator.net",
        //     "phone_number" => "640175328",
        //     "postcode_a" => "3025FF",
        //     "postcode_b" => "2020FF",
        //     "avg_confirmed" => 1,
        //     "show_hash" => "https://beta.vriendenkoerier.nl/edit/Facilis%20irure%20in%20eni?signature=f97fb940fa7ddea5280132ea106d34e0fd029b459d49fbf5827902cdcd29a836",
        //     "created_at" => "2019-11-15 15:40:05",
        //     "updated_at" => "2019-11-15 15:40:05"
        // ];
    }

    public function with($request)
    {
        return [
            'version' => '1.0.0',
            'author_url' => url('http://vriendenkoerier.nl')
        ];
    }
}
