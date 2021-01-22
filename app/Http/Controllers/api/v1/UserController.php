<?php

namespace App\Http\Controllers\api\v1;


use App\User as User;
use App\Merchant as Merchant;
use App\AddressBook as AddressBook;
use App\PasswordReset as PasswordReset;
use App\SuperAdmin as SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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

    // Registration Endpoint
    public function userRegistration(Request $req){


        $validator = Validator::make($req->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'phone_number' => 'required',
            'usertype' => 'required',
        ]);

        if($validator->passes()){

            $token = md5($req->firstname).uniqid();

            if($req->usertype == "customer"){

                // Check If email exist
                $existmail = User::where('email', $req->email)->get();

                if(count($existmail) > 0){
                    $resData = ['message' => 'Email address already exist!', 'status' => 201];
                    $status = 201;
                }
                else{
                    $user = User::create([
                        'user_id' => uniqid(),
                        'firstname' => $req->firstname,
                        'lastname' => $req->lastname,
                        'email' => $req->email,
                        'phone_number' => $req->phone_number,
                        'usertype' => $req->usertype,
                        'api_token' => $token,
                        'password' => Hash::make($req->password),
                    ]);

                    

                    $resData = ['data' => $user, 'message' => 'Registration successful', 'status' => 200, 'token' => $token];
                    $status = 200;
                }

            
            }

        elseif($req->usertype == "merchant"){

            // Check If email exist
            $existmail = Merchant::where('company', $req->company)->get();

            if(count($existmail) > 0){
                $resData = ['message' => 'An account with this business name already exist!', 'status' => 201];
                $status = 201;
            }
            else{



                if($req->file('avatar'))
                {
                //Get filename with extension
                $filenameWithExt = $req->file('avatar')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt , PATHINFO_FILENAME);
                // Get just extension
                $extension = $req->file('avatar')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore = rand().'_'.time().'.'.$extension;
                //Upload Image
                // $path = $req->file('file')->storeAs('public/uploads', $fileNameToStore);

                // $path = $req->file('avatar')->move(app()->basePath('../../profile/merchant/avatar/'), $fileNameToStore);
                $path = $req->file('avatar')->move(public_path('../../profile/merchant/avatar/'), $fileNameToStore);
                // $path = $req->file('avatar')->move(app()->basePath('../public/profile/merchant/avatar/'), $fileNameToStore);
                $avatar = "https://".$_SERVER['HTTP_HOST']."/profile/merchant/avatar/".$fileNameToStore;

                }
                else{
                    $avatar = 'noImage.png';
                }


                $user_id = uniqid();

                $userexist = User::where('email', $req->email)->get();

                if(count($userexist) > 0){
                    // DO Nothing
                }
                else{
                    $user = User::create([
                        'user_id' => $user_id,
                        'firstname' => $req->firstname,
                        'lastname' => $req->lastname,
                        'email' => $req->email,
                        'phone_number' => $req->phone_number,
                        'usertype' => $req->usertype,
                        'api_token' => $token,
                        'password' => Hash::make($req->password),
                    ]);
                }
                

                $merchant = Merchant::create([
                    'merchant_id' => $user_id,
                    'firstname' => $req->firstname,
                    'lastname' => $req->lastname,
                    'location' => $req->location,
                    'company' => $req->company,
                    'description' => $req->description,
                    'usertype' => $req->usertype,
                    'avatar' => $avatar
                ]);

                $resData = ['data' => $user, 'message' => 'Registration successful', 'status' => 200, 'token' => $token];
                $status = 200;

            }
        }


            
        }
        else{
            
            $error = implode(",",$validator->messages()->all());
            
            // $resData = ['message' => $validator->errors(), 'status' => 201];
            $resData = ['message' => $error, 'status' => 201];
            $status = 201;
        }



        return $this->returnJSON($resData, $status);

    }

    // Login Endpoint
    public function userLogin(Request $req){


        $validator = $req->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);



        if(!Auth::attempt($validator)){
                $code = 201;
                $resData = ['message' => 'Invalid login credential', 'status' => $code];
        }

        else{

            $token = Auth::user()->createToken('authToken')->accessToken;

            $getUser = User::where('email', $req->email)->get();

            // dd($input);

            // Check if password matchs

            if(Hash::check($req->password, $getUser[0]->password)){
                // Login

                if($getUser[0]->usertype == "customer"){

                    if($getUser[0]->status == "activate"){
                        $data = $getUser;
                        $code = 200;
                        $message = 'Login successful';
                    }
                    else{
                        $data = $getUser;
                        $code = 201;
                        $message = 'Account deactivated';
                    }

                    

                }
                elseif($getUser[0]->usertype == "merchant"){
                    // Get Merchant Info
                    $user = Merchant::where('merchant_id', $getUser[0]->user_id)->get();

                    if($user[0]->status == "activate"){
                        $data = $user;
                        $code = 200;
                        $message = 'Login successful';

                    }
                    else{
                        $data = $user;
                        $code = 201;
                        $message = 'Account deactivated';
                    }

                }

                // Update User API Token
                User::where('email', $req->email)->update(['api_token' => $token]);


                $resData = ['data' => $data, 'status' => $code, 'message' => $message, 'token' => $token];

            }
            else{
                $code = 201;
                $resData = ['message' => 'Invalid Username or Password', 'status' => $code];
            }
        }








        return $this->returnJSON($resData, $code);

    }


    // Merchant Login Auth

    public function merchantLogin(Request $req, Merchant $merchant){

    }



    // Super Admin Create and Login Auth

    public function adminLogin(Request $req){
        // Get Admin info
        $admin = SuperAdmin::where('username', $req->username)->orWhere('email', $req->username)->get();

        if(count($admin) > 0){
            // Hash Password
            if(Hash::check($req->password, $admin[0]->password)){
                // Login Successfully
                $token = md5($req->username).uniqid();
                $status = 200;

                SuperAdmin::where('username', $req->username)->orWhere('email', $req->username)->update(['api_token' => $token]);

                $resData = ['data' => $admin, 'status' => $status, 'message' => 'Login successful', 'token' => $token];
            }
            else{
                $resData = ['message' => 'Incorrect password', 'status' => 201];
                $status = 201;
            }
        }   
        else{
            $resData = ['message' => 'Username or Email does not match our record', 'status' => 201];
            $status = 201;
        }
        
        return $this->returnJSON($resData, $status);
    }


    



    // Password Reset Link
    public function resetLink(Request $req, PasswordReset $reset){
        
        $getuser = User::where('email', $req->email)->get();

        if(count($getuser) > 0){

            $token = str_random(32);
            // Generate token and insert to Password reset
            $reset->email = $req->email;
            $reset->token = $token;
            $reset->updated_at = date('Y-m-d h:i:s');

            $reset->save();

            $this->to = $req->email;
            $this->subject = "Password reset";
            $this->message = "<p>Hi ".$getuser[0]->firstname.",</p><br><p>To set up a new password to your Cliqmore account, click 'Reset Your Password' below, or use this link: ".$req->url."?token=".$token."</p> <br> <p>The link will expire in 24 hours. If nothing happens after clicking, copy and paste the link in your browser.</p>";

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
            User::where('email', $getuser[0]->email)->update(['password' => Hash::make($req->password)]);
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
        $getuser = User::where('email', $req->email)->get();

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




    // Start Update User Profile
    public function updateUserinfo(Request $req){


        $validator = Validator::make($req->all(), [
            'user_id' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'phone_number' => 'required'
        ]);

        if($validator->passes()){
            // Update User Profile

            // Get Usertype
            $getUser = User::where('user_id', $req->user_id)->get();

            if(count($getUser) > 0){
                // Update user
                if($getUser[0]->usertype == "customer"){

                    User::where('user_id', $req->user_id)->update(['firstname' => $req->firstname, 'lastname' => $req->lastname, 'sex' => $req->sex, 'phone_number' => $req->phone_number]);
                    
                }
                elseif($getUser[0]->usertype == "merchant"){

                    User::where('user_id', $req->user_id)->update(['firstname' => $req->firstname, 'lastname' => $req->lastname, 'sex' => $req->sex, 'phone_number' => $req->phone_number]);

                    Merchant::where('user_id', $req->user_id)->update(['firstname' => $req->firstname, 'lastname' => $req->lastname]);
                    
                }

                $resData = ['data' => $getUser, 'message' => 'Profile updated',  'status' => 200];
                $status = 200;

            }
            else{
                $resData = ['message' => 'Profile not found',  'status' => 201];
                $status = 201;
            }

                
            
        }
        else{

            $error = implode(",",$validator->messages()->all());

            $resData = ['message' => $error, 'status' => 201];
            // $resData = ['message' => 'Some required fields are missing', 'status' => 201];
            $status = 201;
        }



        return $this->returnJSON($resData, $status);
    }

    // End Update User Profile

    // Create Shipping Address
    public function shippingAddress(Request $req, AddressBook $add_address){

        // Check if address already exist
        $addup = $add_address->where('user_id', $req->user_id)->get();

        if(count($addup) > 0){
            $add_address->user_id = $req->user_id;
            $add_address->street_no = $req->street_no;
            $add_address->street_name = $req->street_name;
            $add_address->city = $req->city;
            $add_address->state = $req->state;
            $add_address->address_type = "added";

            $result = $add_address->save();

        }
        else{

            $add_address->user_id = $req->user_id;
            $add_address->street_no = $req->street_no;
            $add_address->street_name = $req->street_name;
            $add_address->city = $req->city;
            $add_address->state = $req->state;
            $add_address->address_type = "default";

            $result = $add_address->save();
        }


        if($result == true){
            $resData = ['message' => "Saved successfully", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "Something went wrong", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    // Update Shipping Address
    public function updateAddress(Request $req, $id){

        $address = AddressBook::where('id', $id)->update([
            'street_no' => $req->street_no,
            'street_name' => $req->street_name,
            'city' => $req->city,
            'state' => $req->state
        ]);


        if($address == 1){
            $resData = ['data' => $address, 'message' => "Updated successfully", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "Something went wrong", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    // Additional Address

    public function additionalAddress(Request $req, AddressBook $add_address){

        $add_address->user_id = $req->user_id;
        $add_address->street_no = $req->street_no;
        $add_address->street_name = $req->street_name;
        $add_address->city = $req->city;
        $add_address->state = $req->state;
        $add_address->address_type = "added";

        $result = $add_address->save();

        if($result == true){
            $resData = ['message' => "Saved successfully", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "Something went wrong", 'status' => 201];
            $status = 201;
        }
        

        return $this->returnJSON($resData, $status);
    }

    public function shipAddress(Request $req, $user_id){

        $getAddress = AddressBook::where('user_id', $user_id)->get();

        if(count($getAddress) > 0){
            $resData = ['data' => $getAddress, 'message' => "Success", 'status' => 200];
            $status = 200;
        }
        else{
            $resData = ['message' => "Address not found", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }

    // Delete Address
    public function deleteAddress(Request $req, $id){
        // Get User ID and locate address
        $getAddress = AddressBook::where('id', $id)->where('user_id', $req->user_id)->delete();

        $resData = ['message' => "Deleted successfully", 'status' => 200];
        $status = 200;
        

        return $this->returnJSON($resData, $status);
    }

    // Set or Change Default Address

    public function defaultAddress(Request $req, $id){
        
        $getAddress = AddressBook::where('id', $id)->where('user_id', $req->user_id)->get();

        if(count($getAddress) > 0){
            // Update Address
            if($req->address_type == "default"){
                AddressBook::where('id', $id)->where('user_id', $req->user_id)->update(['address_type' => $req->address_type]);
                AddressBook::where('user_id', $req->user_id)->where('id', '!=' ,$id)->update(['address_type' => 'added']);

                $resData = ['message' => "Success", 'status' => 200];
                $status = 200;
            }
            else{
                // Select default address
                $resData = ['message' => "Select default address", 'status' => 201];
                $status = 201;
            }


        }
        else{
            $resData = ['message' => "Address not found", 'status' => 201];
            $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }





    
}

