<?php

namespace App\Http\Controllers\api\v1;


use App\User as User;
use App\Orders as Orders;
use App\AddressBook as AddressBook;
use App\Cart as Cart;
use App\Checkout as Checkout;
use App\Products as Products;
use App\ProductSale as ProductSale;
use App\Payment as Payment;

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



}
