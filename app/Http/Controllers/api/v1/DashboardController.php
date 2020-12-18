<?php

namespace App\Http\Controllers\api\v1;


use App\User as User;
use App\Orders as Orders;
use App\Merchant as Merchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
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
    public function userinformation(Request $req, $user_id){

        $userInfo =  User::where('user_id', $user_id)->get();


        if(count($userInfo) > 0){

            $resData = ['data' => $userInfo, 'message' => 'Retrieved Information',  'status' => 200];
            $status = 200;
        }
        else{

            $resData = ['message' => "Profile information not found", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }

    public function merchantinformation(Request $req, $user_id){

        $userInfo =  Merchant::where('merchant_id', $user_id)->get();


        if(count($userInfo) > 0){

            $resData = ['data' => $userInfo, 'message' => 'Retrieved Information',  'status' => 200];
            $status = 200;
        }
        else{

            $resData = ['message' => "Merchant profile information not found", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }



    public function merchantbankinformation(Request $req, $user_id){

        $userInfo =  Merchant::where('merchant_id', $user_id)->get();


        if(count($userInfo) > 0){

            $bankname = $req->bankname;
            $accountnumber = $req->accountnumber;

            Merchant::where('merchant_id', $user_id)->update(['bankname' => $bankname, 'accountnumber' => $accountnumber]);

            $resData = ['message' => 'Success',  'status' => 200];
            $status = 200;
        }
        else{

            $resData = ['message' => "Merchant profile information not found", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }




}