<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


use App\User as User;
use App\Merchant as Merchant;
use App\Orders as Orders;
use App\Products as Products;
use App\ProductSpecification as ProductSpecification;
use App\ProductCategory as ProductCategory;
use App\AddressBook as AddressBook;
use App\Cart as Cart;
use App\Checkout as Checkout;
use App\ProductSale as ProductSale;
use App\Payment as Payment;
use App\SuperAdmin as SuperAdmin;

class SuperAdminController extends Controller
{
    public function getallOrders(Request $req){

        $allorders = DB::table('orders')
                    ->select(DB::raw('users.id as userId, users.user_id, users.firstname, users.lastname, users.email, users.phone_number, products.id as productId, products.name as product_name, products.rating as rating, products.price as price, products.avatar as image, products.description as description, products.specification, products.about, products.features, products.whats_in_the_box, products.display, products.operating_system, products.warranty, products.sku, products.category, orders.order_status as status, orders.quantity as quantity, users.status as accountStatus, orders.id as orderId'))
                    ->join('users', 'users.user_id', '=', 'orders.user_id')
                    ->join('products', 'products.id', '=', 'orders.product_id')
                    ->orderBy('orders.created_at', 'DESC')->get();

        if(count($allorders) > 0){
            $resData = ['data' => $allorders, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No order information", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    public function updateOrderstatus(Request $req, $id){

        // Get order
        $getOrder = Orders::where('id', $id)->get();

        if(count($getOrder) > 0){
            // Update Record
            Orders::where('id', $id)->update(['order_status' => $req->order_status]);

            $resData = ['message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No order record", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    public function getallPayments(Request $req){

        $allpayments = DB::table('payment')
                    ->select(DB::raw('users.id as userId, users.user_id, users.firstname, users.lastname, users.email, users.phone_number, payment.id as paymentId, payment.transaction_id, payment.amount, products.id as productId, products.name as productName, products.avatar as image, products.description as description, products.specification, products.about, products.features, products.category,  users.status as accountStatus'))
                    ->join('users', 'users.email', '=', 'payment.customer_id')
                    ->join('products', 'products.id', '=', 'payment.product_id')
                    ->orderBy('payment.created_at', 'DESC')->get();

        if(count($allpayments) > 0){
            $resData = ['data' => $allpayments, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No payment information", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    public function getallmerchantProducts(Request $req){

        $allmerchantproducts = DB::table('products')
                    ->select(DB::raw('merchants.id as merchantId, merchants.merchant_id as merchantuserId, merchants.firstname, merchants.lastname, users.user_id, users.email, users.phone_number, merchants.location as address, merchants.company, merchants.description, merchants.avatar as merchantLogo, merchants.bankname, merchants.accountnumber, products.id as productId, products.name as productName, products.avatar as image, products.description as description, products.specification, products.about, products.features, products.category, products.quantity, products.availablequantity, users.status as accountStatus'))
                    ->join('merchants', 'merchants.id', '=', 'products.merchant_id')
                    ->join('users', 'users.user_id', '=', 'merchants.merchant_id')
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


    public function accountDeactivation(Request $req, $id){

        $getUser = User::where('id', $id)->get();

        if(count($getUser) > 0){
            // Deactivate

            if($getUser[0]->usertype == "customer"){
                User::where('id', $id)->update(['status' => 'deactivate']);

            }
            else{
                User::where('id', $id)->update(['status' => 'deactivate']);
                Merchant::where('merchant_id', $getUser[0]->user_id)->update(['status' => 'deactivate']);

            }

            $resData = ['message' => "Account deactivated", 'status' => 200];
            $status = 200;

        }
        else{
            $resData = ['message' => "This user is not found", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }

    public function accountActivation(Request $req, $id){

        $getUser = User::where('id', $id)->get();

        if(count($getUser) > 0){
            // Deactivate
            User::where('id', $id)->update(['status' => 'activate']);

            $resData = ['message' => "Account activated", 'status' => 200];
            $status = 200;

        }
        else{
            $resData = ['message' => "This user is not found", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    public function getallUsers(Request $req){
        
        $getUser = User::orderBy('created_at', 'DESC')->get();

        if(count($getUser) > 0){
            $resData = ['data' => $getUser, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No available users", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    public function getallcustomers(Request $req){
        
        $getUser = User::where('usertype', 'customer')->orderBy('created_at', 'DESC')->get();

        if(count($getUser) > 0){
            $resData = ['data' => $getUser, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No available users", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }

    public function getallMerchants(Request $req){

        $getUser = DB::table('merchants')
                ->select(DB::raw('merchants.id, merchants.merchant_id as merchantuserId, merchants.firstname, merchants.lastname, users.user_id, users.email, users.phone_number, merchants.location as address, merchants.company, merchants.description, merchants.avatar as merchantAvatar, merchants.logo as merchantLogo, users.status as accountStatus, merchants.bankname, merchants.accountnumber'))
                ->join('users', 'users.user_id', '=', 'merchants.merchant_id')
                ->orderBy('merchants.created_at', 'DESC')->get();

                // ->join('products', 'products.merchant_id', '=', 'merchants.id')


        if(count($getUser) > 0){
            
            $resData = ['data' => $getUser, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No available users", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }

    public function allMerchantcount(){
        $allmechant = Merchant::count();

        $resData = ['data' => $allmechant, 'message' => "success", 'status' => 200];
        $status = 200;

        return $this->returnJSON($resData, $status);
    }


    public function allCategorycount(){
        $allcategory = ProductCategory::count();

        $resData = ['data' => $allcategory, 'message' => "success", 'status' => 200];
        $status = 200;

        return $this->returnJSON($resData, $status);
    }


    public function getallUsersbystatus(Request $req){
        
        $getUser = User::orderBy('created_at', 'DESC')->get()->groupBy('status');

        if(count($getUser) > 0){
            $resData = ['data' => $getUser, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No available users", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }

    public function getallMerchantsbystatus(Request $req){

        $getUser = Merchant::orderBy('created_at', 'DESC')->get()->groupBy('status');

                // ->join('products', 'products.merchant_id', '=', 'merchants.id')


        if(count($getUser) > 0){
            
            $resData = ['data' => $getUser, 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No available users", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    public function getallSoldproducts(Request $req){

        $getSold = DB::table('merchants')
                ->select(DB::raw('merchants.id, merchants.merchant_id as merchantuserId, merchants.firstname, merchants.lastname, users.user_id, users.email, users.phone_number, merchants.location as address, merchants.company, merchants.description, merchants.avatar as merchantAvatar, merchants.logo as merchantLogo, merchants.bankname, merchants.accountnumber, products.quantity as quantityUploaded, products.availablequantity as quantityAvailable, users.status as accountStatus, product_sale.product_id as soldproductId, product_sale.quantity as soldQuantity, products.avatar as productImage, products.name as productName, products.rating, products.price as productPrice, products.description, products.specification, products.about, products.features, products.category'))
                ->join('users', 'users.user_id', '=', 'merchants.merchant_id')
                ->join('products', 'products.merchant_id', '=', 'merchants.id')
                ->join('product_sale', 'product_sale.product_id', '=', 'products.id')
                ->orderBy('merchants.created_at', 'DESC')->get();

        if(count($getSold) > 0){
            $resData = ['data' => array('soldproduct' => $getSold, 'soldproductcount' => count($getSold)), 'message' => "success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No sold products", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    public function adminCreate(Request $req, SuperAdmin $super){

        $getAdmin = $super->where('email', $req->email)->get();

        if(count($getAdmin) > 0){
            $resData = ['message' => "Account exist with the email address", 'status' => 201];
            $status = 201;
        }
        else{
            $username = explode(" ", $req->name);

            $user_name = $username[0]."".mt_rand(100, 999);
            $super->name = $req->name;
            $super->username = $user_name;
            $super->email = $req->email;
            $super->password = Hash::make($req->password);
            $super->role = $req->role;
    
            $super->save();
    
            // Send Mail
    
            $this->to = $req->email;
            $this->subject = "Account Created";
            $this->message = "<p>Hi ".$req->name.",</p><br><p>Your account is set up as an admin with cliqmore.<hr> <p>Login details:</p> <br> <p>Username: ".$user_name."</p><p>Password: ".$req->password."</p>.";
    
            $this->sendMail($this->to, $this->subject);

            // Get Admin

            $newreg = $super::where('email', $req->email)->first();
    
    
            $resData = ['data' => $newreg, 'message' => "success", 'status' => 200, 'token' => md5($user_name)];
            $status = 200;
        }



        return $this->returnJSON($resData, $status);
    }


    public function addCategory(Request $req, ProductCategory $category){
        // Check if Category Exist
        $checkexist = $category->where('category', $req->category_name)->get();

        if(count($checkexist) > 0){
            // Already exist
            $resData = ['message' => "This category is already created", 'status' => 201];
            $status = 201;
        }
        else{
            // DO insert
            $category->category = $req->category_name;

            $category->save();

            $resData = ['message' => "success", 'status' => 200];
            $status = 200;
        }

        return $this->returnJSON($resData, $status);
    }


    // Edit Category
    public function editCategory(Request $req, ProductCategory $category, $id){

        // Check if Category Exist
        $checkexist = $category->where('id', $id)->get();

        if(count($checkexist) > 0){
            // Update Category
            $category->where('id', $id)->update(['category' =>  $req->category_name]);

            $resData = ['message' => "Success", 'status' => 200];
            $status = 200;

        }
        else{
            //    Category not found

            $resData = ['message' => "This category is not found", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);

    }


    // Delete Category
    public function deleteCategory(Request $req, ProductCategory $category, $id){

        $checkexist = $category->where('id', $id)->delete();

        $resData = ['message' => "Success", 'status' => 200];
        $status = 200;

        return $this->returnJSON($resData, $status);

    }


    // All out of stcok products
    public function outofStock(){

        $productoutofstock = Products::where('availablequantity', '<=', 0)->get();

        if(count($productoutofstock) > 0){

            $resData = ['data' => array('productsoutofstock' => $productoutofstock, 'productsoutofstockcount' => count($productoutofstock)), 'message' => "Success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "No record", 'status' => 201];
            $status = 201;
        }


        return $this->returnJSON($resData, $status);
    }

}
