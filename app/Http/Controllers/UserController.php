<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PaymentMethod;
use App\Models\Bookmarks;

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

            Auth()->User()->update([
                'valid_doc' => $valid_doc_path,
                'approve_status' => 0
            ]);

            (new AdminController)->index();
         
            return response()->json([
                'authenticated' => true,
                'response' => 'Application sent to the admin. Please wait for approval',
                'data' => [
                	'valid_doc' => $valid_doc_path,
                	'approve_status' => 0
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

            $profile_picture_path = "";
            if ($request->hasFile('profile_picture')) {

                $file = $request->file('profile_picture');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); 
                $profile_picture_path = Storage::disk('public')->url($filename);

            } else {
                $profile_picture_path = $request->profile_picture;
                // throw new \Exception("Image is required", 1);
            }

            Auth()->User()->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'profile_picture' => $profile_picture_path,
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
                    'profile_picture' => $profile_picture_path,
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

    public function addPaymentMethod(Request $request)
    {
        try {

            $qr_code_path = "";
            if ($request->hasFile('qr_code')) {

                $file = $request->file('qr_code');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); 
                $qr_code_path = Storage::disk('public')->url($filename);

            } else {
                throw new \Exception("Image is required", 1);
            }

            PaymentMethod::insert([
                'payment_desc' => $request->payment_desc,
                'qr_code' => $qr_code_path,
                'created_by' => auth()->id(),
                'created_at' => now()
            ]);

            return response()->json([
                'authenticated' => true,
                'response' => 'New payment method successfully added',
                'data' => PaymentMethod::where('created_by', auth()->id())->where('archive', 0)->get(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }

    public function deletePaymentMethod(Request $request)
    {
        try {

            PaymentMethod::where('id', $request->payment_method_id)->update([
                'archive' => 1,
            ]);

            return response()->json([
                'authenticated' => true,
                'response' => 'Payment method successfully deleted',
                'data' => PaymentMethod::where('created_by', auth()->id())->where('archive', 0)->get(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'response' => $e->getMessage(),
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
                        'type' => 0,
                        'approve_status' => 2
                    ]
                ]);
            }

            (new AdminController)->index();

            

        } catch (\Exception $e) {
            return response()->json([
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
    }

    public function allBookmarks(){

        return DB::table('bookmarks')->where('created_by', auth()->id())->get();
        
    }


    public function addToBookmarks(Request $request){

        $check = Bookmarks::where('resort_id', $request->resort_id)->where('created_by', auth()->id())->first();

        if(empty($check)){
            DB::table('bookmarks')->insert([
                'resort_id' => $request->resort_id,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);
        }

        return response()->json(['response'=>"Added to bookmarks successfully."]);
        
    }

    public function removeToBookmarks(Request $request){

        Bookmarks::where('id', $request->bookmark_id)->delete();

        return response()->json(['response'=>"Removed to bookmarks successfully."]);
        
    }
}