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


	Route::post('resetlink',  ['uses' => 'api\v1\UserController@resetLink']);
	

	Route::post('passwordreset/{token}',  ['uses' => 'api\v1\UserController@changeresetPassword']);



	// Auth Routes

	Route::group(['middleware' => ['cliqmoretoken']], function () {
		

	Route::post('changepassword',  ['uses' => 'api\v1\UserController@changePassword']);

	Route::post('updateprofile',  ['uses' => 'api\v1\UserController@updateUserinfo']);

	Route::post('shippingaddress',  ['uses' => 'api\v1\UserController@shippingAddress']);
	
	Route::post('additionaladdress',  ['uses' => 'api\v1\UserController@additionalAddress']);
	
    Route::post('updateaddress/{id}',  ['uses' => 'api\v1\UserController@updateAddress']);

	Route::get('shipping/{user_id}',  ['uses' => 'api\v1\UserController@shipAddress']);

	Route::post('deleteaddress/{id}',  ['uses' => 'api\v1\UserController@deleteAddress']);

	Route::post('defaultaddress/{id}',  ['uses' => 'api\v1\UserController@defaultAddress']);


	Route::get('user/{user_id}', ['uses' => 'api\v1\DashboardController@userinformation']);


	Route::post('merchant/updatebank/{user_id}', ['uses' => 'api\v1\DashboardController@merchantbankinformation']);

	Route::get('cart/details/{id}', ['uses' => 'api\v1\CartController@cartDetails']);


	Route::get('favourite/details/{id}', ['uses' => 'api\v1\CartController@favouriteDetails']);

	Route::post('cart', ['uses' => 'api\v1\CartController@addtoCart']);

	Route::post('cartedit/{id}', ['uses' => 'api\v1\CartController@editCart']);

	Route::post('addfavourite', ['uses' => 'api\v1\CartController@addasFavorite']);

	Route::get('cartcount/{user_id}', ['uses' => 'api\v1\CartController@cartCount']);

	Route::get('favouritecount/{user_id}', ['uses' => 'api\v1\CartController@favouriteCount']);

	Route::post('removefromcart/{id}', ['uses' => 'api\v1\CartController@cartRemove']);

	Route::post('removefromfavourite/{id}', ['uses' => 'api\v1\CartController@favouriteRemove']);

	// Order Route
	Route::get('orders/{user_id}', ['uses' => 'api\v1\OrderController@myOrders']);

	Route::post('merchantorderupdate/{id}', ['uses' => 'api\v1\OrderController@updateOrder']);

	Route::get('merchantorders/{id}', ['uses' => 'api\v1\OrderController@merchantOrder']);

	//Merchant Products

	Route::post('products/delete/{id}',  ['uses' => 'api\v1\ProductController@delete']);

	Route::post('products/update/{id}',  ['uses' => 'api\v1\ProductController@update']);

	Route::post('products/{merchantID}',  ['uses' => 'api\v1\ProductController@create']);


	// Merchant Routes
	Route::post('merchants/{id}',  ['uses' => 'api\v1\MerchantController@update']);
	Route::post('merchantimageupload/{id}',  ['uses' => 'api\v1\MerchantController@merchantUploadimage']);
	Route::get('merchants',  ['uses' => 'api\v1\MerchantController@index']);
	Route::get('merchants/{id}',  ['uses' => 'api\v1\MerchantController@fetch']);
	Route::get('merchants/list/count',  ['uses' => 'api\v1\MerchantController@count']);


	Route::get('merchants/totalavailableproducts/{merchant_id}',  ['uses' => 'api\v1\MerchantController@totalProduct']);
	Route::get('merchants/totalsoldproducts/{merchant_id}',  ['uses' => 'api\v1\MerchantController@totalProductsold']);


		


		
	
		// Get Notification
		Route::get('notification/{id}', ['uses' => 'api\v1\MerchantController@notification']);
	
	
		Route::get('accountstatement/{id}', ['uses' => 'api\v1\MerchantController@accountStatement']);
	
	
		// Rating
		Route::post('product/rating', ['uses' => 'api\v1\ProductController@rateProducts']);
	
		
	
		// Bank Transactions and Money Transfers
	

	
		Route::post('validateaccountnumber', ['uses' => 'api\v1\PaymentController@validateAccountNumber']);
	
		



		
	});


	Route::group(['middleware' => ['superadmintoken']], function () {

		// Super Admin Route

		// Create Delivery fee
		
		Route::get('delivery/get', ['uses' => 'api\v1\SuperAdminController@getDeliveryfee']);
		Route::post('delivery/create', ['uses' => 'api\v1\SuperAdminController@createDeliveryfee']);
		Route::post('delivery/edit/{id}', ['uses' => 'api\v1\SuperAdminController@editDeliveryfee']);
		Route::post('delivery/delete/{id}', ['uses' => 'api\v1\SuperAdminController@deleteDeliveryfee']);

		// Transfer Money
		Route::post('createrecipient', ['uses' => 'api\v1\SuperAdminController@transferRecipient']);
	
		Route::post('transfermoney/{id}', ['uses' => 'api\v1\SuperAdminController@transferMoney']);

		Route::get('allorders',  ['uses' => 'api\v1\SuperAdminController@getallOrders']);
		Route::get('allpayments',  ['uses' => 'api\v1\SuperAdminController@getallPayments']);
		Route::get('merchantproducts',  ['uses' => 'api\v1\SuperAdminController@getallmerchantProducts']);
		Route::post('accountdeativate/{id}',  ['uses' => 'api\v1\SuperAdminController@accountDeactivation']);
		Route::post('accountactivate/{id}',  ['uses' => 'api\v1\SuperAdminController@accountActivation']);
		Route::get('allusers',  ['uses' => 'api\v1\SuperAdminController@getallUsers']);
		Route::get('allcustomers',  ['uses' => 'api\v1\SuperAdminController@getallcustomers']);
		Route::get('allusersbystatus',  ['uses' => 'api\v1\SuperAdminController@getallUsersbystatus']);
		Route::get('allmerchants',  ['uses' => 'api\v1\SuperAdminController@getallMerchants']);
		Route::get('allmerchantsbystatus',  ['uses' => 'api\v1\SuperAdminController@getallMerchantsbystatus']);
		Route::get('allsoldproducts',  ['uses' => 'api\v1\SuperAdminController@getallSoldproducts']);


			// Update Order Status
	Route::post('orderstatus/{id}',  ['uses' => 'api\v1\SuperAdminController@updateOrderstatus']);
	Route::get('productoutofstock',  ['uses' => 'api\v1\SuperAdminController@outofStock']);
	Route::get('merchantcount',  ['uses' => 'api\v1\SuperAdminController@allMerchantcount']);
	Route::get('categorycount',  ['uses' => 'api\v1\SuperAdminController@allCategorycount']);


	// Create admin
	Route::post('admin/create',  ['uses' => 'api\v1\SuperAdminController@adminCreate']);
	Route::post('admin/addcategory',  ['uses' => 'api\v1\SuperAdminController@addCategory']);
	Route::post('admin/editcategory/{id}',  ['uses' => 'api\v1\SuperAdminController@editCategory']);
	Route::post('admin/deletecategory/{id}',  ['uses' => 'api\v1\SuperAdminController@deleteCategory']);
	Route::post('admin/deletemanycategory',  ['uses' => 'api\v1\SuperAdminController@deleteManyCategory']);
		
	});


	Route::get('merchant/{user_id}', ['uses' => 'api\v1\DashboardController@merchantinformation']);



	// PAYSTACK PAY
		Route::post('pay', ['uses' => 'api\v1\PaymentController@redirectToGateway']);

		// CALLBACK
		Route::get('payment/callback', ['uses' => 'api\v1\PaymentController@handleGatewayCallback']);




	Route::post('admin/resetlink',  ['uses' => 'api\v1\SuperAdminController@resetLink']);

	Route::post('admin/passwordreset/{token}',  ['uses' => 'api\v1\SuperAdminController@changeresetPassword']);

	Route::post('admin/changepassword',  ['uses' => 'api\v1\SuperAdminController@changePassword']);



	Route::get('products',  ['uses' => 'api\v1\ProductController@index']);

	Route::get('products/{id}',  ['uses' => 'api\v1\ProductController@fetch']);

	Route::get('products/list/count',  ['uses' => 'api\v1\ProductController@countAll']);

	Route::get('products/list/count/{merchant_id}',  ['uses' => 'api\v1\ProductController@count']);
	
    Route::get('products/available/{id}',  ['uses' => 'api\v1\ProductController@productAvailable']);


	Route::get('products/list/{merchant_id}',  ['uses' => 'api\v1\ProductController@merchantproduct']);


	// Search product

	Route::get('search',  ['uses' => 'api\v1\ProductController@search']);

	// List Product Categories
	Route::get('categories',  ['uses' => 'api\v1\ProductController@listCategories']);


	
	Route::get('searchmerchantproduct/{merchant_id}',  ['uses' => 'api\v1\MerchantController@search']);
	Route::get('specificproduct/{merchant_id}',  ['uses' => 'api\v1\MerchantController@specificProduct']);
	Route::get('merchantsales/{merchant_id}',  ['uses' => 'api\v1\MerchantController@getallSales']);


	Route::get('merchantproductoutofstock/{merchantId}',  ['uses' => 'api\v1\MerchantController@productoutofStock']);
	Route::get('merchantproductbycategory/{merchantId}',  ['uses' => 'api\v1\MerchantController@productcategories']);
	Route::get('merchantswithproduct',  ['uses' => 'api\v1\MerchantController@merchantwithProduct']);

	// Route::post('merchants',  ['uses' => 'api\v1\MerchantController@create']);
	// Route::delete('merchants/{id}',  ['uses' => 'api\v1\MerchantController@delete']);

	Route::get('bankinformation', ['uses' => 'api\v1\PaymentController@saveBanks']);
	
	Route::get('allbanks', ['uses' => 'api\v1\PaymentController@getallBanks']);



	// Admin login
	Route::post('admin/login',  ['uses' => 'api\v1\UserController@adminLogin']);
	


});



