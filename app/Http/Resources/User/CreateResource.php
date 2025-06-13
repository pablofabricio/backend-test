<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class CreateResource extends JsonResource
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
            'id'    => $this->resource['id'],
            'name'  => $this->resource['name'],
            'email' => $this->resource['email'],
            'type'  => $this->resource['type'],
        ];
    }
}
