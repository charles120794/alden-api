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
                throw new \Exception("Image is required", 1);
            }

            $payment_qr_code_path = "";

            if ($request->hasFile('payment_qr_code')) {

                $file = $request->file('payment_qr_code');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); // You can choose a storage disk here
                // You can also save the filename to a database if needed
                $payment_qr_code_path = Storage::disk('public')->url($filename);
            } else {
                throw new \Exception("Image is required", 1);
            }

            Auth()->User()->update([
                'valid_doc' => $valid_doc_path,
                'payment_qr_code' => $payment_qr_code_path,
                'approve_status' => 0
            ]);
         
            return response()->json([
                'authenticated' => true,
                'response' => 'Successfully updated',
                'data' => [
                	'valid_doc' => $valid_doc_path,
                    'payment_qr_code' => $payment_qr_code_path,
                	'approve_status' => 1
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

    public function updateProfile(Request $request)
    {
        try {

            // $profile_picture_path = "";
            if ($request->hasFile('profile_picture')) {

                $file = $request->file('profile_picture');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); 
                $profile_picture_path = Storage::disk('public')->url($filename);
                Auth()->User()->update([
                'profile_picture' => $profile_picture_path
            ]);

            } else {
                $profile_picture_path = $request->profile_picture;
                // throw new \Exception("Image is required", 1);
            }

            $payment_qr_code_path = "";
            if ($request->hasFile('payment_qr_code')) {

                $file = $request->file('payment_qr_code');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); 
                $payment_qr_code_path = Storage::disk('public')->url($filename);

            } else {
                $payment_qr_code_path = $request->payment_qr_code;
                // throw new \Exception("Image is required", 1);
            }

            Auth()->User()->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                // 'profile_picture' => $profile_picture_path,
                'payment_qr_code' => $payment_qr_code_path,
                'updated_at' => now(),
            ]);
            
            return response()->json([
                'authenticated' => true,
                'response' => 'Successfully updated',
                'data' => [
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    // 'profile_picture' => $profile_picture_path,
                    'payment_qr_code' => $payment_qr_code_path,
                    'updated_at' => now(),
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

    public function getAllUser(Request $request)
    {
        try {

            $users = new User;

            if($request->has('approve_status')) {
                $users = $users->where('approve_status', 0);//pending users
            }

            $users = $users->get();

            return response()->json(
                $users
            );

        } catch (\Exception $e) {
            return response()->json([
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
    }

    public function getAllPendingUser()
    {
        try {

            $users = User::where('approve_status', 0)->get();

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

    public function approveUserToOwner(Request $request){
        try {

            if($request->action == 'approve'){
                User::where('id', $request->user_id)->update([
                    'type' => 1,
                    'approve_status' => 1
                ]);

                return response()->json([
                    'authenticated' => true,
                    'response' => 'Successfully updated',
                    'data' => [
                        'type' => 1,
                        'approve_status' => 1
                    ]
                ]);
            }else{
                User::where('id', $request->user_id)->update([
                    'approve_status' => 2
                ]);

                return response()->json([
                    'authenticated' => true,
                    'response' => 'Successfully updated',
                    'data' => [
                        'type' => 1,
                        'approve_status' => 2
                    ]
                ]);
            }

            

        } catch (\Exception $e) {
            return response()->json([
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
    }
}