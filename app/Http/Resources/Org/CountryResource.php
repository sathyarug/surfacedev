<?php

namespace App\Http\Resources\Org;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            "country_id" => $this->country_id,
            "country_code" => $this->country_code,
            "country_description" => $this->country_description,
            "active_status" => $this->status
        ];
    }
}
