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
use App\DeliveryFee as DeliveryFee;
use App\PasswordReset as PasswordReset;
use App\Notification as Notification;

class SuperAdminController extends Controller
{
    public function getallOrders(Request $req){

        $allorders = DB::table('orders')
                    ->select(DB::raw('users.id as userId, users.user_id, users.firstname, users.lastname, users.email, users.phone_number, orders.address, products.id as productId, products.name as product_name, products.rating as rating, products.price as price, products.avatar as image, products.description as description, products.specification, products.about, products.features, products.whats_in_the_box, products.display, products.operating_system, products.warranty, products.sku, products.category, orders.order_status as status, orders.quantity as quantity, users.status, orders.id as orderId'))
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
                    ->select(DB::raw('users.id as userId, users.user_id, users.firstname, users.lastname, users.email, users.phone_number, payment.id as paymentId, payment.transaction_id, payment.amount, products.id as productId, products.name as productName, products.avatar as image, products.description as description, products.specification, products.about, products.features, products.category,  users.status'))
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
                    ->select(DB::raw('merchants.id as merchantId, merchants.merchant_id as merchantuserId, merchants.firstname, merchants.lastname, users.user_id, users.email, users.phone_number, merchants.location as address, merchants.company, merchants.description, merchants.avatar as merchantLogo, merchants.bankname, merchants.accountnumber, merchants.wallet_amount, products.id as productId, products.name as productName, products.avatar as image, products.description as description, products.specification, products.about, products.features, products.category, products.quantity, products.availablequantity, users.status'))
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

            if($getUser[0]->usertype == "customer"){
                User::where('id', $id)->update(['status' => 'activate']);

            }
            else{
                User::where('id', $id)->update(['status' => 'activate']);
                Merchant::where('merchant_id', $getUser[0]->user_id)->update(['status' => 'activate']);

            }

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
                ->select(DB::raw('users.id as id, merchants.id as merchantId, merchants.merchant_id as merchantuserId, merchants.firstname, merchants.lastname, users.user_id, users.email, users.phone_number, merchants.location as address, merchants.company, merchants.description, merchants.avatar as merchantAvatar, merchants.logo as merchantLogo, users.status, merchants.bankname, merchants.accountnumber, merchants.wallet_amount'))
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
                ->select(DB::raw('merchants.id, merchants.merchant_id as merchantuserId, merchants.firstname, merchants.lastname, users.user_id, users.email, users.phone_number, merchants.location as address, merchants.company, merchants.description, merchants.avatar as merchantAvatar, merchants.logo as merchantLogo, merchants.bankname, merchants.accountnumber, merchants.wallet_amount, products.quantity as quantityUploaded, products.availablequantity as quantityAvailable, users.status, product_sale.product_id as soldproductId, product_sale.quantity as soldQuantity, products.avatar as productImage, products.name as productName, products.rating, products.price as productPrice, products.description, products.specification, products.about, products.features, products.category'))
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


    public function deleteManyCategory(Request $req, ProductCategory $category){


        if(count($req->id) > 0){
            foreach($req->id as $item){

                $category->where('id', $item)->delete();
        
            }

            $resData = ['message' => "Deleted", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "Nothing selected", 'status' => 201];
            $status = 201;
        }
        

        return $this->returnJSON($resData, $status);

    }

    // GEt Delivery Fee

    public function getDeliveryfee(Request $req, DeliveryFee $delivery){

       $resp = $delivery->orderBy('location', 'DESC')->get();

       if(count($resp) > 0){
        $resData = ['data' => $resp, 'message' => "Success", 'status' => 200];
        $status = 200;
       }
       else{
        $resData = ['message' => "No record", 'status' => 201];
        $status = 201;
       }

        return $this->returnJSON($resData, $status);

    }

    // Create Delivery Fee
    public function createDeliveryfee(Request $req, DeliveryFee $delivery){

        $delivery->updateOrInsert(['location' => $req->location], ['location' => $req->location, 'fee' => $req->fee]);

        $resData = ['message' => "Success", 'status' => 200];
        $status = 200;

        return $this->returnJSON($resData, $status);

    }

    public function editDeliveryfee(Request $req, DeliveryFee $delivery, $id){

        // Check if delivery Exist
        $checkexist = $delivery->where('id', $id)->get();

        if(count($checkexist) > 0){
            // Update delivery
            $delivery->where('id', $id)->update(['location' =>  $req->location, 'fee' => $req->fee]);

            $resData = ['message' => "Success", 'status' => 200];
            $status = 200;

        }
        else{
            //    Category not found

            $resData = ['message' => "No record", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);

    }


    // Delete Category
    public function deleteDeliveryfee(Request $req, DeliveryFee $delivery, $id){

        $delete = $delivery->where('id', $id)->delete();

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


    // Create a transfer recipient

    public function transferRecipient(Request $req){

        $this->url = "https://api.paystack.co/transferrecipient";

        $this->curldata = array(
            'type' => "nuban",
            'name' => $req->name,
            'account_number' => $req->account_number,
            'bank_code' => $req->bank_code,
            'currency' => "NGN"
        );


        $fields_string = http_build_query($this->curldata);
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $this->url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Authorization: Bearer sk_test_0530c6a0c35ebd6f6e5150c13c3f9ff7e28e1c77",
          "Cache-Control: no-cache",
        ));
        
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
        
        //execute post
        $result = curl_exec($ch);

        $response = json_decode($result);

        $resData = ['data' => $response->data, 'status' => 200];
        $status = 200;

        return $this->returnJSON($resData, $status);


    }

    // Transfer the Money
    public function transferMoney(Request $req, $id){

        /*

        Retrying a transfer

        If there is an error with the transfer request, kindly retry the transaction with the same reference in order to avoid double crediting. If a new reference is used, the transfer would be treated as a new request.

        */ 


        $getMerchant = Merchant::where('id', $id)->get();

        if(count($getMerchant) > 0){

            if($getMerchant[0]->wallet_amount >= $req->amount){

                $this->url = "https://api.paystack.co/transfer";

                $this->curldata = array(
                    'source' => "receivable",
                    'amount' => $req->amount,
                    'recipient' => $req->recipient_code,
                    'reason' => $req->reason
                );
        
        
        
                $fields_string = http_build_query($this->curldata);
                //open connection
                $ch = curl_init();
                
                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, $this->url);
                curl_setopt($ch,CURLOPT_POST, true);
                curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                  "Authorization: Bearer sk_test_0530c6a0c35ebd6f6e5150c13c3f9ff7e28e1c77",
                  "Cache-Control: no-cache",
                ));
                
                //So that curl_exec returns the contents of the cURL; rather than echoing it
                curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
                
                //execute post
                $result = curl_exec($ch);
                
                $response = json_decode($result);
        
        
                    $wallet = $getMerchant[0]->wallet_amount - $req->amount;
        
                    Merchant::where('id', $id)->update(['wallet_amount' => $wallet]);
        
                    $activity = "Withdraw NGN ".$req->amount;
        
                    // Update Account Statement
                    $this->notifyMerchant($id, $activity, $req->amount);
                    
                    $resData = ['data' => $response, 'status' => 200];
                    $status = 200;
            }
            else{
                $resData = ['message' => 'Insufficient fund', 'status' => 201];
                $status = 201;
            }

        }
        else{

            $resData = ['message' => 'Cannot identify recipient', 'status' => 201];
            $status = 201;

        }

        return $this->returnJSON($resData, $status);

    }


    public function notifyMerchant($merchant_id, $activity, $amount){

        // Insert to Notification Table
        Notification::insert(['merchant_id' => $merchant_id, 'activity' => $activity, 'purchase' => $amount, 'status' => 'debit']);
    }


        // Password Reset Link
        public function resetLink(Request $req, PasswordReset $reset){
        
            $getuser = SuperAdmin::where('email', $req->email)->get();
    
            if(count($getuser) > 0){
    
                $token = str_random(32);
                // Generate token and insert to Password reset
                $reset->email = $req->email;
                $reset->token = $token;
                $reset->updated_at = date('Y-m-d h:i:s');
    
                $reset->save();
    
                $this->to = $req->email;
                $this->subject = "Password reset";
                $this->message = "<p>Hi ".$getuser[0]->name.",</p><br><p>To set up a new password to your Cliqmore account, click 'Reset Your Password' below, or use this link: ".$req->url."?token=".$token."</p> <br> <p>The link will expire in 24 hours. If nothing happens after clicking, copy and paste the link in your browser.</p>";
    
                $this->sendMail($this->to, $this->subject);
    
                $resData = ['message' => "Reset link generated ", 'status' => 200];
                $status = 200;
            }
            else{
                $resData = ['message' => "Record not found", 'status' => 201];
                $status = 201;
            }
    
            return $this->returnJSON($resData, $status);
    
        }
    
        // Change Reset Password
        public function changeresetPassword(Request $req, $token){
            
            $getuser = PasswordReset::where('token', $token)->get();
    
            if(count($getuser) > 0){
                // Change Password / update Password
                SuperAdmin::where('email', $getuser[0]->email)->update(['password' => Hash::make($req->password)]);
                // Remove Reset Link
                PasswordReset::where('token', $token)->delete();
    
                $resData = ['message' => "Password reset", 'status' => 200];
                $status = 200;
            }
            else{
                $resData = ['message' => "Token expired", 'status' => 201];
                $status = 201;
            }
    
            return $this->returnJSON($resData, $status);
    
        }
    
    
        // Change Password
        public function changePassword(Request $req){
            $getuser = SuperAdmin::where('email', $req->email)->get();
    
            if(count($getuser) > 0){
                // Change Password / update Password
                User::where('email', $getuser[0]->email)->update(['password' => Hash::make($req->password)]);
    
                $resData = ['message' => "Password updated", 'status' => 200];
                $status = 200;
            }
            else{
                $resData = ['message' => "Record not found", 'status' => 201];
                $status = 201;
            }
    
            return $this->returnJSON($resData, $status);
    
        }




}
