<?php

namespace App\Http\Resources\User;

use App\Http\Responses\DefaultResponse;
use Illuminate\Http\Resources\Json\JsonResource;



class RegisterResource extends JsonResource
{
    //PM4
    // use HasDefaultResponse;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'user' => [
                'id'   => $this->resource['user']['id'],
                'name' => $this->resource['user']['name'],
            ],
            'company' => [
                'id'   => $this->resource['company']['id'],
                'name' => $this->resource['company']['name'],
            ],
            'access_token' => $this->resource['token'],
        ];
    }
}
