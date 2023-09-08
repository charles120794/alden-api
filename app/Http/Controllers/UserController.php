<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
	public function updateToOwner(Request $request)
	{

        try {

            $valid_doc_path = "";

            if ($request->hasFile('valid_doc')) {

                $file = $request->file('valid_doc');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); // You can choose a storage disk here
                // You can also save the filename to a database if needed

                $valid_doc_path = Storage::disk('public')->url($filename);
            } else {
                throw new Exception("Image is required", 1);
            }

            $payment_qr_code_path = "";

            if ($request->hasFile('payment_qr_code')) {

                $file = $request->file('payment_qr_code');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); // You can choose a storage disk here
                // You can also save the filename to a database if needed

                $payment_qr_code_path = Storage::disk('public')->url($filename);
            } else {
                throw new Exception("Image is required", 1);
            }

            Auth()->User()->update([
                'valid_doc' => $valid_doc_path,
                'payment_qr_code' => $payment_qr_code_path,
                'type' => 1
            ]);
         
            return response()->json([
                'authenticated' => true,
                'response' => 'Successfully updated',
                'data' => [
                	'valid_doc' => $path,
                	'type' => 1
                ]
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
	}

    public function getAllUser(){
        try {

            $users = User::get();

            return response()->json([
                'authenticated' => true,
                'response' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
    }

    public function getAllPendingUser(){
        try {

            $users = User::where('type', 0)->get();

            return response()->json([
                'authenticated' => true,
                'response' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
    }
}