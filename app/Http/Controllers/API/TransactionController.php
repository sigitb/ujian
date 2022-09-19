<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function transaction(Request $request)
    {
        $validator =  Validator::make(
            $request->all(),
            [
                'reference_id' => 'required',
                'amount' => 'required|numeric',
                'product' => 'required',
            ]
        );
        if ($validator->fails()) {
            $data = [
                "status" => "fail",
                "message" => "Failed register",
                "data" => $validator->errors()
            ];
            return response()->json($data, 400);
        }
        $data = Http::post("https://sandbox.saebo.id/api/v1/payments", [
            "reference_id" => $request->reference_id,
            "amount" => $request->amount,
            "product" => $request->nama_product
        ]);

        $product = Product::where("reference_number", $data->reference_number)->fisrt();
        if($data->response_code == "2009900"){
            if($product){
                $product->status = "SUCCESS";
                $product->save();
                $respone = [
                    "status" => "success",
                    "message" => "successfully transaction",
                ];
            }else{
                $respone = [
                    "status" => "failed",
                    "message" => "transaction not found",
                ];
            }

        }

        if($data->response_code == "5009901"){
            $product->status = "FAILED";
            $product->save();
            $respone = [
                "status" => "fail",
                "message" => "Failed transaction",
            ];
        }
        
        return response()->json($respone, 200);

    }  
    
    public function index(Request $request)
    {
        if ($request->search) {
            $product = Transaction::where("status", $request->search)->paginate(5);
        } else {
            $product = Transaction::paginate(5);
        }
        $data = [
            "status" => "success",
            "message" => "successfully get transaction",
            "data" => $product
        ];
        return response()->json($data, 200);
    }
}
