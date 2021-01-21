<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

use App\User as User;
use App\Merchant as Merchant;
use App\Orders as Orders;
use App\Cart as Cart;
use App\Checkout as Checkout;
use App\Products as Products;

class OrderController extends Controller
{
    public function myOrders(Request $req, $user_id){

        $orders = DB::table('orders')
                     ->select(DB::raw('users.id as userId, users.user_id, users.firstname, users.lastname, users.email, users.phone_number, products.id as productId, products.name as product_name, products.rating as rating, products.price as price, products.avatar as image, products.description as description, products.specification, products.about, products.features, products.whats_in_the_box, products.display, products.operating_system, products.warranty, products.sku, products.category, orders.id as orderId, orders.order_status as status, orders.quantity as quantity, orders.created_at'))
                     ->join('users', 'users.user_id', '=', 'orders.user_id')
                     ->join('products', 'products.id', '=', 'orders.product_id')
                     ->where('orders.user_id', $user_id)
                     ->orderBy('orders.created_at', 'DESC')->get();

        if(count($orders) > 0){
            $resData = ['data' => $orders, 'message' => "Success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No available orders", 'status' => 201];
            $status = 201;
        }


        return $this->returnJSON($resData, $status);
    }


    public function updateOrder(Request $req, $id){
        // Get Merchant ID
        $updateorder = Orders::where('id', $id)->where('merchant_id', $req->merchant_id)->update(['order_status' => $req->order_status]);
        
        $resData = ['message' => "Success", 'status' => 200];
        $status = 200;

        return $this->returnJSON($resData, $status);
    }

    public function merchantOrder(Request $req, $id){
        
        $merchantOrders = DB::table('orders')
                     ->select(DB::raw('products.id as productId, products.name as product_name, products.rating as rating, products.price as price, products.avatar as image, products.description as description, products.specification, products.about, products.features, products.whats_in_the_box, products.display, products.operating_system, products.warranty, products.sku, products.category, orders.id as orderId, orders.order_status as status, orders.quantity as quantity, orders.address, users.phone_number as phoneNumber, orders.created_at'))
                     ->join('products', 'products.id', '=', 'orders.product_id')
                     ->join('users', 'users.user_id', '=', 'orders.user_id')
                     ->where('orders.merchant_id', $id)
                     ->orderBy('orders.created_at', 'DESC')->get();

        if(count($merchantOrders) > 0){
            $resData = ['data' => $merchantOrders, 'message' => "Success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No available orders", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }
}

