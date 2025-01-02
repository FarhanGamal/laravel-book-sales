<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Payment_method;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Contracts\Service\Attribute\Required;

class PaymentController extends Controller
{
    public function index(){
        $payments = Payment::all();
            return new OrderResource(true,  "Get All Resourse", $payments);
            
        return response()->json([
            "status" => true,
            "message" => "Get All Resourse",
            "Data" => $payments
        ], 200);
    }

    public function store(Request $request) {

        // 1. membuat validasi
        $validator = Validator::make($request->all(), [
            'order_id' => 'Required|exists:orders,id', 
            'payment_method_id' => 'Required|exists:payment_method,id', 
            // 'amount'=> 'required|numeric|min:0', 
            // 'status'=> 'required|string', 
            // 'staff_confirmed_by' => 'required|integer', 
            // 'staff_confirmed_at'=> 'required|timestamp',
        ]);

        // 2. melakukan cek data yang bermasalah
        if ($validator->fails()){
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ], 422);
        }

         // Ambil data user yang sedang login
         $user = auth('api')->user();

         // cek login user
         if (!$user) {
            return response()->json([
                'status' => true,
                'message' => 'Unauthorize!'
            ], 401);
        }

        // ambil data order
        $order = Order::find($request->order_id);
        $payment_method = Payment_method::find($request->payment_method_id);

        // ambil data amount
        $Amount = $order->totalAmount;

        // 3. membuat data genre
        $payment = Payment::create([
            'order_id' => $order, 
            'payment_method_id' => $payment_method, 
            'amount'=> $Amount, 
            'status'=> 'pending', 
            'staff_confirmed_by' => $request->staff_confirmed_by, 
            'staff_confirmed_at'=> $request->nstaff_confirmed_at,
        ]);

        // 4. memberi pesan berhasil
        return response()->json([
            "success" => true,
            "message" => "Resource added succesfully!",
            "data" => $payment
        ], 201);
    }

    public function show(string $id) {
        $payment = Payment::find($id);

        if(!$payment){
            return response()->json([
                "status" => false,
                "message" => "Resorce not found",
            ], 404);
        };

        return response()->json([
            "success" => true,
            "message" => "Get detail resource",
            "data" => $payment
        ], 200);
    }

    public function update(Request $request, string $id) {
        // cari data payment
        $payment = Payment::find($id);

        if (!$payment) {
          return response()->json([
            "success" => false,
            "message" => "Resource not found!"
          ], 404);
        }

        // membuat validasi
        $validator = Validator::make($request->all(), [
          'order_id' => 'Required|exists:orders,id', 
            'payment_method_id' => 'Required|exists:payment_method,id', 
            'amount'=> 'required|numeric|min:0', 
            'status'=> 'required|string', 
            'staff_confirmed_by' => 'required|integer', 
            'staff_confirmed_at'=> 'required|timestamp',
        ]);

        // melakukan cek data yang bermasalah
        if ($validator->fails()){
          return response()->json([
            "success" => false,
            "message" => $validator->errors()
          ], 422);
        }

        // $genre->update($request->only("name", "description"));

        $payment->update([
            'order_id' => $request->order_id, 
            'payment_method_id' => $request->payment_method_id, 
            'amount'=> $request->amount, 
            'status'=> $request->nstatusme, 
            'staff_confirmed_by' => $request->staff_confirmed_by, 
            'staff_confirmed_at'=> $request->nstaff_confirmed_atame,
        ]);

        return response()->json([
          "success" => true,
          "message" => "Resource updated successfully!",
          "data" => $payment
        ], 200);
    }

    public function destroy (string $id) {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                "succes" => false,
                "message" => "Resource not found!"
            ], 404);
        }

        $payment->delete();

        return response()->json([
            "success" => true,
            "messege" => "Resource deleted succesgully!",
        ],200);
    }



}