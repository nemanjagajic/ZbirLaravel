<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CustomerResource::collection(Customer::all());
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
            'username' => 'required',
            'name' => 'required',
            'lastName' => 'required'
        ]);

        $customer = new Customer;
        $customer->username = $request->username;
        $customer->name = $request->name;
        $customer->lastName = $request->lastName;

        if ($customer->save()) {
            return new CustomerResource($customer);
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
        return new CustomerResource(Customer::findOrFail($id));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Customer::findOrFail($id)->delete();

        return response("Successfully deleted customer with id $id");
    }

    public function addBeers(Request $request)
    {
        $validatedData = $request->validate([
            'userId' => 'required',
            'beerId' => 'required'
        ]);

        $customer = Customer::findOrFail($request->userId);

        if ($request->count) {
            $customer->beers()->attach($request->beerId, ['count' => $request->count]);
        } else {
            $customer->beers()->attach($request->beerId);
        }

        return response("Beer with id {$request->beerId} successfully ordered");
    }
}
