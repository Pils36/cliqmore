<?php

namespace App\Http\Controllers\api\v1;


use App\User as User;
use App\Merchant as Merchant;
use App\Orders as Orders;
use App\AddressBook as AddressBook;
use App\Cart as Cart;
use App\Checkout as Checkout;
use App\Products as Products;
use App\ProductSale as ProductSale;
use App\Payment as Payment;
use App\Banks as Banks;
use App\Notification as Notification;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


use Paystack;

class PaymentController extends Controller
{

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $req)
    {

        // return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {


        // Get User Information and Update Payment Details



        $paymentDetails = Paystack::getPaymentData();


        // dd($paymentDetails['data']['metadata']['custom_fields'][0]['return_url']);

        // failed_url

        if($paymentDetails['data']['gateway_response'] == "Successful"){
            $user = User::where('email', $paymentDetails['data']['customer']['email'])->get();

            


            if(count($user) > 0){
                // Get Cart Info
                $cartinfo = Cart::where('user_id', $user[0]->user_id)->where('status', 0)->get();
    
                if(count($cartinfo) > 0){
    
                    foreach($cartinfo as $key => $value){
    
                        $amount = $paymentDetails['data']['amount'] / 100;
    
                    
                        $merchant = Products::where('id', $value->product_id)->get();

    
                        // Get Address
                        $address = AddressBook::where('id', $paymentDetails['data']['metadata']['custom_fields'][0]['address'])->get();
    
                        if(count($address) > 0){
                            $myaddress = $address[0]->street_no.' '.$address[0]->street_name.' '.$address[0]->city.' '.$address[0]->state;
                        }
                        else{
                            $myaddress = '';
                        }
    
                        $this->paymentMade($paymentDetails['data']['reference'], $paymentDetails['data']['customer']['email'], $value->product_id, $merchant[0]->merchant_id, $amount);

                        $this->productSales($value->user_id, $value->product_id);
    
    
                        $this->productQuantity($value->product_id, $value->quantity);
    
                        $this->ordersMade($value->user_id, $value->product_id, 'card payment', $amount, 'open', $merchant[0]->merchant_id, $value->quantity, $myaddress);

                        $this->notifyMerchant($value->product_id, $merchant[0]->merchant_id, $amount, $user[0]->firstname, $user[0]->lastname, $merchant[0]->name, $user[0]->email);

                        $this->updateCartinfo($value->user_id, $value->product_id);

    
    
                    }
    
                    
    
    
    
                    return redirect($paymentDetails['data']['metadata']['custom_fields'][0]['return_url']);
    
                    // $resData = ['data' => 'https://cliqmore.com/shop', 'message' => "Success", 'status' => 200];
                    // $status = 200;
    
                }
    
    
                else{
                    $resData = ['message' => "User cart is empty", 'status' => 201];
                    $status = 201;
                }
    
            }
            else{
                $resData = ['message' => "User information is missing", 'status' => 201];
                $status = 201;
            }
        }
        else{
            return redirect($paymentDetails['data']['metadata']['custom_fields'][0]['failed_url']);
        }

        return $this->returnJSON($resData, $status);
    }

        // Fetch User 
    public function billingInformation(Request $req){

        return $this->returnJSON($resData, $status);
    }


    // Receive Cart info:: user_id, product_id and update cart status = 1
    public function updateCartinfo($user_id, $product_id){

        Cart::where('user_id', $user_id)->where('product_id', $product_id)->delete();
    }

    // Insert product_id info to merchant product sale
    public function productSales($user_id, $product_id){

        // Cart info and get product info
       $info = Cart::where('user_id', $user_id)->where('product_id', $product_id)->get();

       if(count($info) > 0){
            // get product

            $myproduct = Products::where('id', $product_id)->get();

            if(count($myproduct) > 0){
                // Insert
                ProductSale::insert(['product_id' => $product_id, 'merchant_id' => $myproduct[0]->merchant_id, 'price' => $info[0]->price, 'quantity' => $info[0]->quantity]);
            }
       }
       else{
            // Do nothing

       }


    }


    public function notifyMerchant($product_id, $merchant_id, $amount, $firstname, $lastname, $productname, $email){
        // Insert to Notification Table
        Notification::insert(['merchant_id' => $merchant_id, 'activity' => $productname.' order made by '.$firstname.' '.$lastname, 'purchase' => $amount]);

        // get Merchant

        $merch = Merchant::where('id', $merchant_id)->get();

        if(count($merch) > 0){ 

            $userMech = User::where('user_id', $merch[0]->merchant_id)->get();

            // Send merchant mail for new purchase and to confirm order

            $this->to = $userMech[0]->email;
            $this->subject = "New product ordered from Cliqmore";
            $this->message = "<p>Hello ".$userMech[0]->firstname.",</p><br><p>You have a product order on cliqmore. </p><br> <p> Product Name: <b>".$productName."</b></p> <p> Customer: <b>".$firstname." ".$lastname."</b></p> <p> Purchase Amount: <b>".$amount."</b></p> <p> Date and Time: <b>".date('d/m/Y h:i a')."</b></p> <br> <p>Kindly acknowledge order purchase.</p> <p>Thank you</p>";

            $this->sendMail($this->to, $this->subject);

        }
        else{

            // Send to Cliqmore

            $this->to = "info@cliqmore.com";
            $this->subject = "New product ordered from Cliqmore";
            $this->message = "<p>Hello ".$userMech[0]->firstname.",</p><br><p>Theres a new product purchase on cliqmore and can not be identified to a merchant. </p><br> <p> Product Name: <b>".$productName."</b></p> <p> Customer: <b>".$firstname." ".$lastname."</b></p> <p> Customer Email: <b>".$email."</b></p> <p> Amount Paid: <b>".$amount."</b></p> <p> Date and Time: <b>".date('d/m/Y h:i a')."</b></p> <br> <p>Kindly contact them on what to do</p>. <p>Thank you</p>";

            $this->sendMail($this->to, $this->subject);

        }


    }


    public function paymentMade($transaction_id, $customer_id, $product_id, $merchant_id, $amount){
        Payment::insert(['transaction_id' => $transaction_id, 'customer_id' => $customer_id, 'product_id' => $product_id, 'merchant_id' => $merchant_id, 'amount' => $amount]);

    }


    public function ordersMade($user_id, $product_id, $payment_method, $amount, $order_status, $merchant_id, $quantity, $address){
        Orders::insert(['user_id' => $user_id, 'product_id' => $product_id, 'payment_method' => $payment_method, 'amount' => $amount, 'order_status' => $order_status, 'merchant_id' => $merchant_id, 'quantity' => $quantity, 'address' => $address]);
    }



    // Reduce products count in store
    public function productQuantity($id, $quantity){
        $prod = Products::where('id', $id)->get();

        if(count($prod) > 0){
            // Update Product Availability
            $available = $prod[0]->availablequantity - $quantity;

            Products::where('id', $id)->update(['availablequantity' => $available]);
        }

    }


    // Get Banks Information
    public function getallBanks(){
        $allbanks = Banks::orderBy('name', 'DESC')->get();

        $resData = ['data' => $allbanks,'message' => "success", 'status' => 200];
        $status = 200;


        return $this->returnJSON($resData, $status);

    }


    // Save Banks
    public function saveBanks(Request $req){

        $this->url = "https://api.flutterwave.com/v3/banks/NG";

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer FLWSECK_TEST-SANDBOXDEMOKEY-X"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $resData = json_decode($response);


        foreach($resData->data as $key => $value){

            $this->insertBank($value->id, $value->code, $value->name);
        }

        $resData = ['data' => $resData->data,'message' => "saved!", 'status' => 200];
        $status = 200;

        return $this->returnJSON($resData, $status);

    }

    // Validate Account Number
    public function validateAccountNumber(Request $req){

        $this->url = "https://api.paystack.co/bank/resolve?account_number=".$req->account_number."&bank_code=".$req->bank_code;


        $curl = curl_init();
  
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer sk_test_0530c6a0c35ebd6f6e5150c13c3f9ff7e28e1c77",
            "Cache-Control: no-cache",
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {

          $resData = ['data' => [], 'message' => "cURL Error #:" . $err, 'status' => 201];
          $status = 201;


        } else {
            $resData = ['data' => json_decode($response), 'status' => 200];
            $status = 200;
        }

        return $this->returnJSON($resData, $status);


    }


    public function insertBank($id, $code, $name){
        // Get if exist
        Banks::updateOrInsert(['bankid' => $id], ['bankid' => $id, 'code' => $code, 'name' => $name]);

        
    }




}
