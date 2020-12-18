<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::group(['prefix' => '/'], function(){

	Route::post('register',  ['uses' => 'api\v1\UserController@userRegistration']);

	Route::post('login',  ['uses' => 'api\v1\UserController@userLogin']);

	Route::post('updateprofile',  ['uses' => 'api\v1\UserController@updateUserinfo']);

	Route::post('shippingaddress',  ['uses' => 'api\v1\UserController@shippingAddress']);

	
	Route::get('user/{user_id}', ['uses' => 'api\v1\DashboardController@userinformation']);




	Route::get('cart/details/{id}', ['uses' => 'api\v1\CartController@cartDetails']);

	Route::post('cart', ['uses' => 'api\v1\CartController@addtoCart']);


	//Merchant Products

	Route::delete('products/{id}',  ['uses' => 'api\v1\ProductController@delete']);

	Route::post('products/update/{id}',  ['uses' => 'api\v1\ProductController@update']);

	Route::post('products/{merchantID}',  ['uses' => 'api\v1\ProductController@create']);

	Route::get('products',  ['uses' => 'api\v1\ProductController@index']);
	
	Route::get('products/{id}',  ['uses' => 'api\v1\ProductController@fetch']);
	
	Route::get('products/list/count',  ['uses' => 'api\v1\ProductController@countAll']);
	
	Route::get('products/list/count/{merchant_id}',  ['uses' => 'api\v1\ProductController@count']);



	// Merchant Routes
	Route::post('merchants/{id}',  ['uses' => 'api\v1\MerchantController@update']);
	Route::get('merchants',  ['uses' => 'api\v1\MerchantController@index']);
	Route::get('merchants/{id}',  ['uses' => 'api\v1\MerchantController@fetch']);
	Route::get('merchants/list/count',  ['uses' => 'api\v1\MerchantController@count']);

	// Route::post('merchants',  ['uses' => 'api\v1\MerchantController@create']);
	// Route::delete('merchants/{id}',  ['uses' => 'api\v1\MerchantController@delete']);
	


	// PAYSTACK PAY
	Route::post('pay', ['uses' => 'api\v1\PaymentController@redirectToGateway']);

	// CALLBACK
	Route::get('payment/callback', ['uses' => 'api\v1\PaymentController@handleGatewayCallback']);

});



	