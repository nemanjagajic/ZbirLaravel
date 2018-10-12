<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Beer;
use App\Http\Resources\BeerResource;

class BeerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BeerResource::collection(Beer::orderBy('created_at','desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required'
        ]);

        $beer = new Beer;
        $beer->name = $request->name;
        $beer->price = $request->price;
        if ($request->onStock) {
            $beer->onStock = $request->onStock;
        }

        if ($beer->save()) {
            return new BeerResource($beer);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new BeerResource(Beer::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'onStock' => 'required|boolean'
        ]);

        $beer = Beer::findorFail($id);
        $beer->update(['onStock' => $request->onStock]);

        return new BeerResource($beer);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Beer::findorFail($id)->delete();

        return response("Successfully deleted beer with id $id");
    }
}
