<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\User as User;
use App\Orders as Orders;
use App\Cart as Cart;
use App\Checkout as Checkout;
use App\Products as Products;
use App\Favourite as Favourite;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // User Dashboard Endpoint

    // Fetch User 
    public function cartDetails(Request $req, $id){
        // Get full cart detail
        $cartDetail = DB::table('cart')
                    ->select(DB::raw('cart.id as cartId, cart.user_id, cart.product_id, cart.product_name as cartproductName, cart.price, cart.quantity as cartQuantity, cart.category as cartCategory, products.id as productId, products.merchant_id as merchantId, products.name as productName, products.rating, products.price as productPrice, products.description, products.avatar as image, products.category as productCategory, products.quantity as productQuantity, products.availablequantity, products.specification, products.about, products.features, products.whats_in_the_box, products.display, products.operating_system, products.warranty, products.sku'))
                    ->join('products', 'products.id', '=', 'cart.product_id')
                    ->where('user_id', $id)->where('status', 0)
                    ->orderBy('cart.created_at', 'DESC')->get();
        

        if(count($cartDetail) > 0){
            $resData = ['data' => $cartDetail, 'message' => 'Successfull',  'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => 'Cart is empty',  'status' => 201];
            $status = 201;
        }
        

        return $this->returnJSON($resData, $status);
    }

    public function addtoCart(Request $req, Cart $cart){
            // Add to cart
            // Get Product category
            $getProduct = Products::where('id', $req->product_id)->get();

            $cart->user_id = $req->user_id;
            $cart->product_id = $req->product_id;
            $cart->product_name = $req->product_name;
            $cart->price = $req->price;
            $cart->quantity = $req->quantity;
            $cart->category = $getProduct[0]->category;

            $cart->save();

            if($cart->save() == true){
                $resData = ['message' => 'Item Added to cart',  'status' => 200];
                $status= 200;
            }
            else{
                $resData = ['message' => 'Something went wrong',  'status' => 201];
                $status= 201;
            }

        return $this->returnJSON($resData, $status);
    }


    // Get cart count

    public function cartCount(Request $req, $user_id){

        $getcartcount = Cart::where('user_id', $user_id)->where('status', 0)->count();


        $resData = ['data' => $getcartcount, 'message' => 'success',  'status' => 200];
        $status= 200;

        return $this->returnJSON($resData, $status);
    }

    // Remove from Cart
    public function cartRemove(Request $req, $id){

        $delfromcart = Cart::where('id', $id)->delete();

        $resData = ['message' => 'deleted',  'status' => 200];
        $status= 200;

        return $this->returnJSON($resData, $status);
    }

    // Add Favourite

    public function addasFavorite(Request $req, Favourite $favourite){

            // Get Product category
            $getProduct = Products::where('id', $req->product_id)->get();

            $favourite->user_id = $req->user_id;
            $favourite->product_id = $req->product_id;
            $favourite->product_name = $req->product_name;
            $favourite->price = $req->price;
            $favourite->category = $getProduct[0]->category;

            $favourite->save();

            if($favourite->save() == true){
                $resData = ['message' => 'Item Added as favourite',  'status' => 200];
                $status= 200;
            }
            else{
                $resData = ['message' => 'Something went wrong',  'status' => 201];
                $status= 201;
            }

        return $this->returnJSON($resData, $status);

    }

    public function favouriteCount(Request $req, $user_id){

        $getcartcount = Favourite::where('user_id', $user_id)->count();


        $resData = ['data' => $getcartcount, 'message' => 'success',  'status' => 200];
        $status= 200;

        return $this->returnJSON($resData, $status);
    }

    // Remove from Cart
    public function favouriteRemove(Request $req, $id){

        $delfromcart = Favourite::where('id', $id)->delete();

        $resData = ['message' => 'deleted',  'status' => 200];
        $status= 200;

        return $this->returnJSON($resData, $status);
    }

    // Edit Cart Quantity

    public function editCart(Request $req, $id){
        $cartqty = Cart::where('id', $id)->get();

        if(count($cartqty) > 0){
            // Get Price
            
            $category = Products::where('id', $cartqty[0]->product_id)->get();

            $price = $category[0]->price * $req->quantity;
            // Update
            Cart::where('id', $id)->update(['price' => $price, 'quantity' => $req->quantity, 'category' => $category[0]->category]);

            $resData = ['message' => 'success',  'status' => 200];
            $status= 200;
        }
        else{
            $resData = ['message' => 'Item not found',  'status' => 201];
            $status= 201;
        }


        return $this->returnJSON($resData, $status);
    }

    // Get favourite endpoint
    public function favouriteDetails(Request $req, $id){
        $favDetail = DB::table('favourite')
                    ->select(DB::raw('favourite.id as favouriteId, favourite.user_id, favourite.product_id, favourite.product_name as favouriteproductName, favourite.price, favourite.category as favouriteCategory, products.id as productId, products.merchant_id as merchantId, products.name as productName, products.rating, products.price as productPrice, products.description, products.avatar as image, products.category as productCategory, products.quantity as productQuantity, products.availablequantity, products.specification, products.about, products.features, products.whats_in_the_box, products.display, products.operating_system, products.warranty, products.sku'))
                    ->join('products', 'products.id', '=', 'favourite.product_id')
                    ->where('user_id', $id)->where('status', 0)
                    ->orderBy('favourite.created_at', 'DESC')->get();

        

        if(count($favDetail) > 0){
            $resData = ['data' => $favDetail, 'message' => 'Successfull',  'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => 'Favourite is empty',  'status' => 201];
            $status = 201;
        }
        

        return $this->returnJSON($resData, $status);
    }




}