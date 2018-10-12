<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Customer;
use App\Beer;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderPrintableResource;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::orderBy('created_at','desc')->get());
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

        $insertedOrderId = -1;
        if ($request->count) {
            $insertedOrderId = DB::table('beer_customer')->insertGetId([
                'beer_id' => $request->beerId,
                'customer_id' => $request->userId,
                'count' => $request->count,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            $insertedOrderId = DB::table('beer_customer')->insertGetId([
                'beer_id' => $request->beerId,
                'customer_id' => $request->userId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return new OrderPrintableResource(Order::findOrFail($insertedOrderId));
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
        $orders = Order::orderBy('created_at','desc')->get();

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
