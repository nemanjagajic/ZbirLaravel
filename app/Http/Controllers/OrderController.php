<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Customer;
use App\Beer;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderPrintableResource;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::all());
    }

    public function addOrder(Request $request)
    {
        $validatedData = $request->validate([
            'userId' => 'required',
            'beerId' => 'required'
        ]);

        $customer = Customer::findOrFail($request->userId);

        // Fail if beer doesn't exist
        Beer::findOrFail($request->beerId);

        if ($request->count) {
            $customer->beers()->attach($request->beerId, ['count' => $request->count]);
        } else {
            $customer->beers()->attach($request->beerId);
        }

        return response("Beer with id {$request->beerId} successfully ordered");
    }

    public function getOrdersByCustomer($customerId)
    {
        $result = [];
        $orders = Order::all();

        foreach ($orders as $order) {
            if ($order->customer_id == $customerId) {
                array_push($result, new OrderResource($order));
            }
        }

        return $result;
    }

    public function getPrintableOrders()
    {
        $response = [];
        $orders = Order::all();

        foreach ($orders as $order) {
            array_push($response, new OrderPrintableResource($order));
        }

        return $response;
    }

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();

        return response("Successfully deleted order with id $id");
    }
}
