<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Customer;
use App\Beer;

class OrderPrintableResource extends JsonResource
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
            'username' => Customer::findOrFail($this->customer_id)->username,
            'beerName' => Beer::findOrFail($this->beer_id)->name,
            'count' => $this->count
        ];
    }
}
