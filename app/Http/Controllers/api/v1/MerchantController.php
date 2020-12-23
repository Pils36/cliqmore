<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Merchant as Merchant;
use App\Products as Products;
use App\ProductCategory as ProductCategory;

class MerchantController extends Controller
{
    // Get all merchant on the application
    public function index(){
        $getAll = Merchant::all();

        // $getAll = DB::table('merchants')
        // ->select(DB::raw('merchants.*, products.*'))
        // ->join('products', 'products.merchant_id', '=', 'merchants.id')
        // ->selectRaw('count(products.merchant_id)')
        // ->groupBy('merchants.id')
        // ->orderBy('merchants.created_at', 'DESC')->get();



        $resData = ['data' => $getAll, 'message' => "Successfull", 'status' => 200];
        $status = 200;
        return $this->returnJSON($resData, $status);
    }    
    // Get this merchant
    public function fetch(Request $req, $id){
        $getAll = Merchant::where('id', $id)->get();
        $resData = ['data' => $getAll, 'message' => "Successfull", 'status' => 200];
        $status = 200;
        return $this->returnJSON($resData, $status);
    }   
    // Get count of all merchant on the application
    public function count(){
        $getAll = Merchant::count();
        $resData = ['data' => $getAll, 'message' => "Successfull", 'status' => 200];
        $status = 200;
        return $this->returnJSON($resData, $status);
    }

    //Create new merchant
    public function create(Request $req){
        // $this->validate($req, [
        //     'location' => 'required',
        //     'company' => 'required|unique:merchants',
        //     'avatar' => 'required',
        // ]);

        $validator = Validator::make($req->all(), [
            'location' => 'required',
            'company' => 'required|unique:merchants',
            // 'avatar'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
        ]);

        if($validator->passes()){
                // $avatar = null;

            // if($req->hasFile('avatar')){

            //   $image = $req->file('avatar');
            //   $name = $req->company.".".$image->getClientOriginalExtension();
            //   $fileNameToStore = rand().'_'.time();
            //   // Get just extension
            //   $image->move(app()->basePath('../../profile/merchant/avatar/'.$fileNameToStore), $name);
            //   $avatar = $_SERVER['HTTP_HOST']."/profile/merchant/avatar/".$fileNameToStore."/".$name;
            // }

            $merchant = new Merchant();
                $merchant->firstname = $req->firstname;
                $merchant->lastname = $req->lastname;
                $merchant->location = $req->location;
                $merchant->company = $req->company;
                $merchant->description = $req->description;
                // $merchant->avatar = $avatar;
            $execute = $merchant->save();
            $resData = ['data' => $execute, 'message' => "Successfull", 'status' => 200];
            $status = 200;
            
            return $this->returnJSON($resData, $status);
        }
        else{
            // $resData = ['message' => $validator->errors(), 'status' => 201];
            $resData = ['message' => 'Some required fields are missing', 'status' => 201];
            $status = 201;
        }


    
    }


    // MErchant Upload Avatar and Logo

    public function merchantUploadimage(Request $req, $id){

            $avatar = null;
            $logo = null;
            if($req->hasFile('avatar')){

                $image = $req->file('avatar');
                $name = uniqid().".".$image->getClientOriginalExtension();
                $fileNameToStore = rand().'_'.time();
                // Get just extension
                // $image->move(app()->basePath('../../profile/merchant/avatar/'.$fileNameToStore), $name);
                $path = $image->move(public_path('../../profile/merchant/avatar/'.$fileNameToStore), $name);
                $avatar = "https://".$_SERVER['HTTP_HOST']."/profile/merchant/avatar/".$fileNameToStore."/".$name;

                Merchant::where('merchant_id', $id)->update(['avatar' => $avatar]);
              }
              

            if($req->hasFile('logo')){

                $image = $req->file('logo');
                $name = uniqid().".".$image->getClientOriginalExtension();
                $fileNameToStore = rand().'_'.time();
                // Get just extension
                // $image->move(app()->basePath('../../profile/merchant/logo/'.$fileNameToStore), $name);
                $path = $image->move(public_path('../../profile/merchant/logo/'.$fileNameToStore), $name);
                $logo = "https://".$_SERVER['HTTP_HOST']."/profile/merchant/logo/".$fileNameToStore."/".$name;

                Merchant::where('merchant_id', $id)->update(['logo' => $logo]);
              }
        
            $resData = ['message' => "Upload successfull", 'status' => 200];
            $status = 200;
            
            return $this->returnJSON($resData, $status);
    }



    //Update merchant
    public function update(Request $req, $id){

        $merchant_id = Merchant::where('id', $id)->get();


        $validator = Validator::make($req->all(), [
            'location' => 'required',
            // 'avatar' => 'mimes:jpeg,bmp,jpg,png|between:1, 6000',
        ]);

        if($validator->passes()){
            // if($req->hasFile('avatar')){
              
            //   $image = $req->file('avatar');
            //   $name = $req->company.".".$image->getClientOriginalExtension();
            //   $fileNameToStore = rand().'_'.time();
            //   $image->move(app()->basePath('../../profile/merchant/avatar/'.$fileNameToStore), $name);
            //   $avatar = $_SERVER['HTTP_HOST']."/profile/merchant/avatar/".$fileNameToStore."/".$name;

            //     Merchant::where('id', $id)->where('merchant_id', $merchant_id[0]->merchant_id)->update(['avatar' => $avatar]);
            // }
            $execute = Merchant::where('id', $id)->update(['firstname' => $req->firstname, 'lastname' => $req->lastname, 'location' => $req->location, 'company' => $req->company, 'description' => $req->description]);

            if($execute == 1){
              $resData = ['data' => $execute, 'message' => "Successfull", 'status' => 200];
              $status = 200;
            }
            else{
              $resData = ['message' => "Cannot update information", 'status' => 201];
              $status = 201;
            }
        }
        else{

            // $resData = ['message' => $validator->errors(), 'status' => 201];
            $resData = ['message' => 'Some required fields are missing', 'status' => 201];
            $status = 201;

        }



        
        
        return $this->returnJSON($resData, $status);
    }

    // Delete merchant on the application
    public function delete(Request $req, $id){
        $delete = Merchant::where('id', $id)->delete();
        $resData = ['data' => $delete, 'message' => "Deleted", 'status' => 200];
        $status = 200;
        return $this->returnJSON($resData, $status);
    }


    // Get The total product in store for specific merchant

    public function totalProduct(Request $req, $merchant_id){
        $productinstore = Products::where('merchant_id', $merchant_id)->where('availablequantity', '>', 0)->orderBy('created_at', 'DESC')->get();
        $totalProduct = Products::where('merchant_id', $merchant_id)->where('availablequantity', '>', 0)->orderBy('created_at', 'DESC')->count();

        $data = array('productsinstore' => $productinstore, 'totalproductcount' => $totalProduct);

        $resData = ['data' => $data, 'message' => "success", 'status' => 200];

        $status = 200;
        return $this->returnJSON($resData, $status);

    }

    public function totalProductsold(Request $req, $merchant_id){
        $productsold = DB::table('product_sale')->distinct('product_sale.product_id')
                ->select(DB::raw('products.id as product_id, products.name, products.rating, products.price, products.avatar, products.description'))
                ->join('products', 'products.id', '=', 'product_sale.product_id')
                ->where('product_sale.merchant_id', $merchant_id)
                ->orderBy('product_sale.created_at', 'DESC')->get();

        $totalProduct = DB::table('product_sale')
        ->select(DB::raw('products.id as product_id, products.name, products.rating, products.price, products.avatar, products.description'))
        ->join('products', 'products.id', '=', 'product_sale.product_id')
        ->where('product_sale.merchant_id', $merchant_id)
        ->orderBy('product_sale.created_at', 'DESC')->count();

        $data = array('productssold' => $productsold, 'salescount' => $totalProduct);

        $resData = ['data' => $data, 'message' => "success", 'status' => 200];

        $status = 200;
        return $this->returnJSON($resData, $status);

    }

    // Search merchant products
    public function search(Request $req, $merchant_id){
        $category = $req->get('query');

        $searchQuery = trim($category);
        
        $requestData = ['name', 'rating', 'price', 'description', 'specification', 'about', 'features', 'whats_in_the_box', 'display', 'operating_system', 'warranty', 'sku', 'category'];

        $products = Products::where(function($q) use($requestData, $searchQuery) {
                                foreach ($requestData as $field)
                                $q->orWhere($field, 'like', "%{$searchQuery}%");
                        })->where('merchant_id', $merchant_id)->get();


        if (count($products)) {
            $resData = ['data' => $products, 'message' => "Success", 'status' => 200];
            $status = 200;
        } 
        else {
            $resData = ['message' => "No result found", 'status' => 201];
            $status = 201;
        }
        


        return $this->returnJSON($resData, $status);
    }


    public function specificProduct(Request $req, $merchant_id){
        $data = Products::where('merchant_id', $merchant_id)->orderBy('created_at', 'DESC')->get()->groupBy('category');

        if(count($data) > 0){
          $resData = ['data' => $data, 'message' => "Successfull", 'status' => 200];
          $status = 200;
        }
        else{
            $resData = ['message' => "No available products yet", 'status' => 201];
            $status = 201;
        }


        
        return $this->returnJSON($resData, $status);
    }


    public function getallSales(Request $req, $merchant_id){

        $getSold = DB::table('merchants')->distinct('product_sale.product_id')
                ->select(DB::raw('products.id as productId, products.quantity as quantityUploaded, products.availablequantity as quantityAvailable, users.status as merchantaccountStatus, product_sale.product_id as soldproductId, product_sale.quantity as soldQuantity, products.avatar as productImage, products.name as productName, products.rating, products.price as productPrice, products.description, products.category'))
                ->join('users', 'users.user_id', '=', 'merchants.merchant_id')
                ->join('products', 'products.merchant_id', '=', 'merchants.id')
                ->join('product_sale', 'product_sale.product_id', '=', 'products.id')
                ->where('product_sale.merchant_id', $merchant_id)
                ->orderBy('merchants.created_at', 'DESC')->get();

        if(count($getSold) > 0){
            $resData = ['data' => $getSold, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No sold products", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    public function productoutofStock(Request $req, $merchantId){

        $merchantProduct = Products::where('merchant_id', $merchantId)->where('availablequantity', '<=', 0)->get();

        if(count($merchantProduct) > 0){
            $resData = ['data' => array('productsoutofstock' => $merchantProduct, 'productsoutofstockcount' => count($merchantProduct)), 'message' => "Success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No record", 'status' => 201];
            $status = 201;
        }


        return $this->returnJSON($resData, $status);
    }


    public function productcategories(Request $req, $merchantId){

        $merchantProductcategory = Products::where('merchant_id', $merchantId)->get()->groupBy('category');

        if(count($merchantProductcategory) > 0){
            $resData = ['data' => $merchantProductcategory, 'message' => "Success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No record", 'status' => 201];
            $status = 201;
        }


        return $this->returnJSON($resData, $status);
    }


    public function merchantwithProduct(){

        $allmerchantproducts = DB::table('products')->distinct('merchants.id')
                    ->select(DB::raw('merchants.id, merchants.merchant_id, merchants.firstname, merchants.lastname, merchants.location as address, merchants.company, merchants.description, merchants.avatar as merchantLogo, merchants.bankname, merchants.accountnumber'))
                    ->join('merchants', 'merchants.id', '=', 'products.merchant_id')
                    ->orderBy('merchants.created_at', 'DESC')->get();

        if(count($allmerchantproducts) > 0){
            $resData = ['data' => $allmerchantproducts, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No record", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    // Get Notifications
    public function notification($id){
        $notification = DB::table('notification')
                ->where('merchant_id', $id)
                ->orderBy('created_at', 'DESC')->get();

        if(count($notification) > 0){
            $resData = ['data' => $notification, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{

            $resData = ['message' => "No new notification", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }

}

