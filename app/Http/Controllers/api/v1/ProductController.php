<?php

namespace App\Http\Controllers\api\v1;


use App\User as User;
use App\Orders as Orders;
use App\Products as Products;
use App\Merchant as Merchant;
use App\ProductSpecification as ProductSpecification;
use App\ProductCategory as ProductCategory;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Input;

class ProductController extends Controller
{
    // User Dashboard Endpoint
    public function index(Request $req){
        $data = Products::where('availablequantity', '>', 0)->orderBy('created_at', 'DESC')->get()->groupBy('category');

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

    //Get this product
    public function fetch(Request $req, $id){
        $data = Products::where('id', $id)->orderBy('created_at', 'DESC')->get();

        if(count($data) > 0){
          $resData = ['data' => $data, 'message' => "Successfull", 'status' => 200];
          $status = 200;
        }
        else{
            $resData = ['message' => "Product not found or has been deleted by merchant", 'status' => 201];
            $status = 201;
        }

         
        return $this->returnJSON($resData, $status);
    } 


    // Get count of all products on the application
    public function countAll(){
        $getAll = Products::count();
        $resData = ['data' => $getAll, 'message' => "Successfull", 'status' => 200];
        $status = 200;
        return $this->returnJSON($resData, $status);
    } 


    // Get count of all merchant products on the application
    public function count($merchant_id){
        $getAll = Products::where('merchant_id', $merchant_id)->count();
        $resData = ['data' => $getAll, 'message' => "Successfull", 'status' => 200];
        $status = 200;
        return $this->returnJSON($resData, $status);
    }
    
    
    public function merchantproduct($merchant_id){
        $getAll = Products::where('merchant_id', $merchant_id)->get();
        $resData = ['data' => $getAll, 'message' => "Successfull", 'status' => 200];
        $status = 200;
        return $this->returnJSON($resData, $status);
    }

    //Create this product
    public function create(Request $req, $merchantID){


        $validator = Validator::make($req->all(), [
            'name' => 'required|unique:products',
            'price' => 'required',
        ]);

        if($validator->passes()){

          // $avatar = null;
          // $avatar2 = null;
          // $avatar3 = null;
          // $avatar4 = null;
          // $avatar5 = null;



            // if($req->hasFile('avatar')){

            //  $validator = Validator::make($req->all(), [
            //       'avatar'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //   ]);

            //  if($validator->passes()){
            //     $image = $req->file('avatar');
            //     $name = $req->name.".".$image->getClientOriginalExtension();
            //     $fileNameToStore = rand().'_'.time();
            //     $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //     $avatar = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //  }
            //  else{
            //     $resData = ['message' => $validator->errors(), 'status' => 201];
            //     $status = 201;
            //  }
              
                
            // }


            $fileToStore = "";



            if($req->hasFile('avatar')){


              if(count($req->file('avatar')) > 0)
              {


                  foreach($req->file('avatar') as $key => $value){

                      $name = md5($req->name).".".$value->getClientOriginalExtension();
                      $fileNameToStore = rand().'_'.time();
                      $path = $value->move(public_path('../../product/avatar/'.$fileNameToStore), $name);
                      $avatar = "https://".$_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;

                      $fileToStore .=  $avatar.",";

                  }
      
      
              }
            }
            else{
              $fileToStore = "";
            }


            


            // if($req->hasFile('avatar2')){

            //   $validator = Validator::make($req->all(), [
            //       'avatar2'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //   ]);

            //  if($validator->passes()){
            //     $image = $req->file('avatar2');
            //     $name = $req->name.".".$image->getClientOriginalExtension();
            //     $fileNameToStore = rand().'_'.time();
            //     $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //     $avatar2 = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //  }
            //  else{
            //     $resData = ['message' => $validator->errors(), 'status' => 201];
            //     $status = 201;
            //  }

                
            // }       

            // if($req->hasFile('avatar3')){

            //   $validator = Validator::make($req->all(), [
            //       'avatar3'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //   ]);

            //  if($validator->passes()){
            //     $image = $req->file('avatar3');
            //     $name = $req->name.".".$image->getClientOriginalExtension();
            //     $fileNameToStore = rand().'_'.time();
            //     $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //     $avatar3 = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //  }
            //  else{
            //     $resData = ['message' => $validator->errors(), 'status' => 201];
            //     $status = 201;
            //  }


            // } 

            // if($req->hasFile('avatar4')){

            //   $validator = Validator::make($req->all(), [
            //       'avatar4'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //   ]);

            //  if($validator->passes()){
            //     $image = $req->file('avatar4');
            //     $name = $req->name.".".$image->getClientOriginalExtension();
            //     $fileNameToStore = rand().'_'.time();
            //     $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //     $avatar4 = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //  }
            //  else{
            //     $resData = ['message' => $validator->errors(), 'status' => 201];
            //     $status = 201;
            //  }

            // } 

            // if($req->hasFile('avatar5')){

            //   $validator = Validator::make($req->all(), [
            //       'avatar5'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //   ]);

            //  if($validator->passes()){
            //     $image = $req->file('avatar5');
            //     $name = $req->name.".".$image->getClientOriginalExtension();
            //     $fileNameToStore = rand().'_'.time();
            //     $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //     $avatar5 = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //  }
            //  else{
            //     $resData = ['message' => $validator->errors(), 'status' => 201];
            //     $status = 201;
            //  }

                
            // } 


              $merchant = new Products();
              // return $merchant;
                  $merchant->merchant_id = $merchantID;
                  $merchant->name = $req->name;
                  $merchant->rating = $req->rating;
                  $merchant->price = $req->price;
                  $merchant->description = $req->description;
                  $merchant->specification = $req->specification;
                  $merchant->about = $req->about;
                  $merchant->features = $req->features;
                  $merchant->whats_in_the_box = $req->whats_in_the_box;
                  $merchant->display = $req->display;
                  $merchant->operating_system = $req->operating_system;
                  $merchant->warranty = $req->warranty;
                  $merchant->sku = $req->sku;
                  $merchant->category = $req->category;
                  $merchant->quantity = $req->quantity;
                  $merchant->availablequantity = $req->quantity;
                  $merchant->avatar = $fileToStore;
              $execute = $merchant->save();

              $resData = ['data' => $execute, 'message' => "Successfull", 'status' => 200];
              $status = 200;

        }
        else{
          $resData = ['message' => $validator->errors(), 'status' => 201];
          $status = 201;
        }


        return $this->returnJSON($resData, $status);
    }



    //Update product
    public function update(Request $req, $id){


      // Get Merchant
        $product = Products::where('id', $id)->get();

        if(count($product) > 0){
          // Get Merchant
          $merchant = Merchant::where('id', $product[0]->merchant_id)->get();

          $merchant_id = Merchant::where('merchant_id', $merchant[0]->merchant_id)->get();


        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'price' => 'required',
        ]);

        if($validator->passes()){


          $fileToStore = "";
          
          if($req->hasFile('avatar')){


            if(count($req->file('avatar')) > 0)
            {


                foreach($req->file('avatar') as $key => $value){

                    $name = md5($req->name).".".$value->getClientOriginalExtension();
                    $fileNameToStore = rand().'_'.time();
                    $path = $value->move(public_path('../../product/avatar/'.$fileNameToStore), $name);
                    $avatar = "https://".$_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;

                    $fileToStore .=  $avatar.",";

                }
                
                Products::where('id', $id)->where('merchant_id', $merchant_id[0]->id)->update(['avatar' => $fileToStore]);
    
            }
          }


            //     if($req->hasFile('avatar')){

            //       $validator = Validator::make($req->all(), [
            //           'avatar'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //       ]);

            //       if($validator->passes()){
            //         $image = $req->file('avatar');
            //         $name = $req->name.".".$image->getClientOriginalExtension();
            //         $fileNameToStore = rand().'_'.time();
            //         $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //         $avatar = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //           Products::where('id', $id)->where('merchant_id', $merchant_id[0]->id)->update(['avatar' => $avatar]);
            //      }
            //      else{
            //         $resData = ['message' => $validator->errors(), 'status' => 201];
            //         $status = 201;
            //      }



                
            // }
            // if($req->hasFile('avatar2')){

            //   $validator = Validator::make($req->all(), [
            //           'avatar2'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //       ]);

            //       if($validator->passes()){
            //         $image = $req->file('avatar2');
            //         $name = $req->name.".".$image->getClientOriginalExtension();
            //         $fileNameToStore = rand().'_'.time();
            //         $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //         $avatar2 = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //         Products::where('id', $id)->where('merchant_id', $merchant_id[0]->id)->update(['avatar2' => $avatar2]);
            //      }
            //      else{
            //         $resData = ['message' => $validator->errors(), 'status' => 201];
            //         $status = 201;
            //      }

                
            // }


            // if($req->hasFile('avatar3')){

            //   $validator = Validator::make($req->all(), [
            //           'avatar3'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //       ]);

            //       if($validator->passes()){
            //         $image = $req->file('avatar3');
            //         $name = $req->name.".".$image->getClientOriginalExtension();
            //         $fileNameToStore = rand().'_'.time();
            //         $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //         $avatar3 = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //         Products::where('id', $id)->where('merchant_id', $merchant_id[0]->id)->update(['avatar3' => $avatar3]);
            //      }
            //      else{
            //         $resData = ['message' => $validator->errors(), 'status' => 201];
            //         $status = 201;
            //      }

            // } 


            // if($req->hasFile('avatar4')){

            //   $validator = Validator::make($req->all(), [
            //           'avatar4'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //       ]);

            //       if($validator->passes()){
            //         $image = $req->file('avatar4');
            //         $name = $req->name.".".$image->getClientOriginalExtension();
            //         $fileNameToStore = rand().'_'.time();
            //         $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //         $avatar4 = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //         Products::where('id', $id)->where('merchant_id', $merchant_id[0]->id)->update(['avatar4' => $avatar4]);
            //      }
            //      else{
            //         $resData = ['message' => $validator->errors(), 'status' => 201];
            //         $status = 201;
            //      }

            // } 
            // if($req->hasFile('avatar5')){

            //   $validator = Validator::make($req->all(), [
            //           'avatar5'=>'mimes:jpeg,bmp,jpg,png|between:1, 6000',
            //       ]);

            //       if($validator->passes()){
            //         $image = $req->file('avatar5');
            //         $name = $req->name.".".$image->getClientOriginalExtension();
            //         $fileNameToStore = rand().'_'.time();
            //         $image->move(app()->basePath('../../product/avatar/'.$fileNameToStore), $name);
            //         $avatar5 = $_SERVER['HTTP_HOST']."/product/avatar/".$fileNameToStore."/".$name;
            //         Products::where('id', $id)->where('merchant_id', $merchant_id[0]->id)->update(['avatar5' => $avatar5]);
            //      }
            //      else{
            //         $resData = ['message' => $validator->errors(), 'status' => 201];
            //         $status = 201;
            //      }

            // }


              $execute = Products::where('id', $id)->where('merchant_id', $merchant_id[0]->id)->update(['name' => $req->name, 'rating' => $req->rating, 'price' => $req->price, 'description' => $req->description, 'specification' => $req->specification, 'about' => $req->about, 'features' => $req->features, 'whats_in_the_box' => $req->whats_in_the_box, 'display' => $req->display, 'operating_system' => $req->operating_system, 'warranty' => $req->warranty, 'sku' => $req->sku, 'category' => $req->category, 'quantity' => $req->quantity, 'availablequantity' => $req->quantity]);


              if($execute == 1){
                $resData = ['data' => $execute, 'message' => "Successfull", 'status' => 200];
                $status = 200;
              }
              else{
                $resData = ['message' => "Cannot update product", 'status' => 201];
                $status = 201;
              }




              }
              else{
                $resData = ['message' => "Product not found", 'status' => 201];
                $status = 201;
              }

        }


        else{
          $resData = ['message' => $validator->errors(), 'status' => 201];
          $status = 201;
        }

        return $this->returnJSON($resData, $status);

    }

    //Delete this product
    public function delete(Request $req, $id){

        // Get Merchant
        $product = Products::where('id', $id)->get();

        if(count($product) > 0){
          // Get Merchant
          $merchant_id = Merchant::where('id', $product[0]->merchant_id)->get();

            $data = Products::where('id', $id)->where('merchant_id', $merchant_id[0]->id)->delete();

            if($data == 1){
              $resData = ['data' => $data, 'message' => "Deleted", 'status' => 200];
              $status = 200;
            }
            else{
              $resData = ['message' => "Cannot delete item", 'status' => 201];
              $status = 201;
            }
        }
        else{
          $resData = ['message' => "Product not found", 'status' => 201];
          $status = 201;
        }


        return $this->returnJSON($resData, $status);

    }


    public function categories(Request $req, $category){

      // Get Product by Category
      $categories = Products::where('category', $category)->get();

      if(count($categories) > 0){
        $resData = ['data' => $categories, 'message' => "Success", 'status' => 200];
        $status = 200;
      }
      else{
        $resData = ['message' => "Item not found in category", 'status' => 201];
        $status = 201;
      }


      return $this->returnJSON($resData, $status);

    }
    


    public function search(Request $req){

      $category = $req->get('query');

      $searchQuery = trim($category);
      
     $requestData = ['name', 'rating', 'price', 'description', 'specification', 'about', 'features', 'whats_in_the_box', 'display', 'operating_system', 'warranty', 'sku', 'category'];

      $products = Products::where(function($q) use($requestData, $searchQuery) {
                            foreach ($requestData as $field)
                               $q->orWhere($field, 'like', "%{$searchQuery}%");
                    })->get();


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


    public function listCategories(Request $req){

        $allcategories = ProductCategory::orderBy('created_at', 'DESC')->get(['id', 'category']);


        if (count($allcategories)) {
          $resData = ['data' => $allcategories, 'message' => "Success", 'status' => 200];
          $status = 200;
        } 
        else {
          $resData = ['message' => "No result found", 'status' => 201];
          $status = 201;
        }

        return $this->returnJSON($resData, $status);
    }


    public function productAvailable(Request $req, $id){
      // Available Products
      $product = Products::where('id', $id)->get();

      if(count($product) > 0){
        $resData = ['data' => $product[0]->availablequantity, 'message' => "Success", 'status' => 200];
          $status = 200;
      }
      else{
        $resData = ['message' => "No result found", 'status' => 201];
        $status = 201;
      }

      return $this->returnJSON($resData, $status);
    }



}

