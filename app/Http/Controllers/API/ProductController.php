<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if($request->search){
            $product = Product::where("name", $request->search)->paginate(5);
        }else{
            $product = Product::paginate(5);
        }
            $data = [
                "status" => "success",
                "message" => "successfully get product",
                "data" => $product
            ];  
        return response()->json($data, 200);
    }

    public function store(Request $request)
    {
        $validator =  Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'price' => 'required',
                'stock' => 'required|min:1',
            ]
        );
        if ($validator->fails()) {
            $data = [
                "status" => "fail",
                "message" => "Failed create product",
                "data" => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        try {
            Product::create([
                "name" => $request->name,
                "price" => $request->price,
                "stock" => $request->stock,
            ]);

            $data = [
                "status" => "success",
                "message" => "Successfully create Product",
            ];
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }

    }

    public function update(Request $request, $id)
    {
        
        $validator =  Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'price' => 'required',
                'stock' => 'required|min:1',
            ]
        );
        if ($validator->fails()) {
            $data = [
                "status" => "fail",
                "message" => "Failed create product",
                "data" => $validator->errors()
            ];
            return response()->json($data, 400);
        }

        try {
            $data = Product::where("id", $id)->first();
            if($data){
                $data->name = $request->name;
                $data->price = $request->price;
                $data->stock = $request->stock;
                $data->save();
                $respone = [
                    "status" => "fail",
                    "message" => "succesfully update product",
                    "data" => $data
                ];
            }else{
                $respone = [
                    "status" => "fail",
                    "message" => "Product not found",
                ];
            }
            return response()->json($respone, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 500);
        }

    }
}
