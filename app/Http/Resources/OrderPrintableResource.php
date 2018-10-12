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
            'username' => Customer::find($this->customer_id) ? Customer::find($this->customer_id)->username : 'removed user',
            'beerName' => Beer::find($this->beer_id) ? Beer::find($this->beer_id)->name : 'removed beer',
            'count' => $this->count
        ];
    }
}