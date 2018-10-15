<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Customer;
use App\Beer;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderPrintableResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Input;

class OrderController extends Controller
{
    public function index()
    {
        return OrderResource::collection(Order::orderBy('created_at', 'desc')->get());
    }

    public function getPrintableOrders()
    {
        $page = Input::get('page') && Input::get('page') > 0 ? Input::get('page') : 1;
        $showPerPage = Input::get('showPerPage') && Input::get('showPerPage') > 0 ? Input::get('showPerPage') : 5;

        $ordersResult = [];
        $orders = Order::orderBy('created_at', 'desc')->get();

        foreach ($orders as $order) {
            array_push($ordersResult, new OrderPrintableResource($order));
        }

        return $this->getSelectedPageResponse($ordersResult, $page, $showPerPage);
    }

    private function getSelectedPageResponse($items, $page, $showPerPage)
    {
        $offset = ($page - 1) * $showPerPage;

        $route = URL::route('ordersPrintable');
        $previous = null;
        $next = null;

        if ($page == 1) {
            $previous = false;
        } else {
            $previousPage = $page - 1;
            $previous = "$route?page=$previousPage&showPerPage=$showPerPage";
        }

        $lastPage = ceil(sizeof($items) / $showPerPage);
        if ($page == $lastPage) {
            $next = false;
        } else {
            $nextPage = $page + 1;
            $next = "$route?page=$nextPage&showPerPage=$showPerPage";
        }

        return response()->json([
            'orders' => array_slice($items, $offset, $showPerPage),
            'currentPage' => $page,
            'previous' => $previous,
            'next' => $next
        ]);
    }

    public function getMostOrderedBeers($numOfBeers)
    {
        $allBeers = Beer::all();
        $beersWithOrders = [];

        $numOfAllOrders = 0;
        foreach (Order::all() as $order) {
            $numOfAllOrders += $order->count;
        }

        foreach ($allBeers as $beer) {
            $orders = Order::all()->where('beer_id', $beer->id);
            $numOfOrders = 0;
            foreach ($orders as $order) {
                $numOfOrders += $order->count;
            }
            $beersWithOrders = $this->array_push_assoc($beersWithOrders, $beer->name, (int)(round($numOfOrders / $numOfAllOrders, 2) * 100));
        }

        arsort($beersWithOrders);
        return response()->json(array_slice($beersWithOrders, 0, $numOfBeers));
    }

    private function array_push_assoc($array, $key, $value)
    {
        $array[$key] = $value;
        return $array;
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

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();

        return response("Successfully deleted order with id $id");
    }
}
