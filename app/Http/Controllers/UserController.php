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

            $userName = auth()->user()->name;
            (new ActivityLogController)->create(new Request([
                'activity' => ("User $userName registered to be an owner")
            ]));


            (new AdminController)->index();
         
            return response()->json([
                'status' => 'success',
                'authenticated' => true,
                'response' => 'Application sent to the admin. Please wait for approval',
                'data' => [
                	'valid_doc' => $valid_doc_path,
                	'approve_status' => 0
                ]
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
	}

    public function updateProfile(Request $request)
    {
        try {
            $mResponse = 'Successfully updated';

            if($request->email != auth()->user()->email){
                 // Check if the email already exists
                $existingUser = User::where('email', $request->email)->first();

                if ($existingUser) {
                    return response()->json([
                        'status' => 'error',
                        'response' => 'Email is already in use',
                    ]);
                }


                Auth()->User()->update(['email_verified_at' => null]);
                $mResponse = 'Successfully updated. Please verify new email';

            }

            

            $profile_picture_path = "";
            if ($request->hasFile('profile_picture')) {

                $file = $request->file('profile_picture');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); 
                $profile_picture_path = Storage::disk('public')->url($filename);

            } else {
                $profile_picture_path = $request->profile_picture;
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
                'status' => "success",
                'authenticated' => true,
                'response' => $mResponse,
                'data' => [
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'contact_no' => $request->contact_no,
                    'profile_picture' => $profile_picture_path,
                    'updated_at' => now(),
                    'email_verified_at' => auth()->user()->email_verified_at,
                ]
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => "error",
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

            $userName = auth()->user()->name;
            (new ActivityLogController)->create(new Request([
                'activity' => ("Owner $userName added a new payment method")
            ]));

            return response()->json([
                'status' => 'success',
                'authenticated' => true,
                'response' => 'New payment method successfully added',
                'data' => PaymentMethod::where('created_by', auth()->id())->where('archive', 0)->get(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
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

            $userName = auth()->user()->name;
            (new ActivityLogController)->create(new Request([
                'activity' => ("Owner $userName deleted a payment method")
            ]));

            return response()->json([
                'status' => 'success',
                'authenticated' => true,
                'response' => 'Payment method successfully deleted',
                'data' => PaymentMethod::where('created_by', auth()->id())->where('archive', 0)->get(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'response' => $e->getMessage(),
            ]);
        }
    }

    public function getAllUser(Request $request)
    {
        try {

            return User::get();

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

            $owner = User::where('id', $request->user_id);
            $ownerName = User::find($request->user_id);

            if($request->action == 'approve'){
                $owner->update([
                    'type' => 1,
                    'approve_status' => 1
                ]);

                (new ActivityLogController)->create(new Request([
                    'activity' => ("User $ownerName->name has been approved to be an owner")
                ]));

                //notify user
                (new NotificationController)->create(new Request([
                    'user_id' => $request->user_id,
                    'message' => "Your application to be an owner has been approved",
                    'type' => 'CONFIRMED_OWNER',
                    'source' => auth()->id(),
                ]));

                return response()->json([
                    'authenticated' => true,
                    'response' => 'Successfully updated',
                    'data' => [
                        'type' => 1,
                        'approve_status' => 1
                    ]
                ]);


            }else{
                $owner->update([
                    'approve_status' => 2
                ]);

                (new ActivityLogController)->create(new Request([
                    'activity' => ("User $ownerName->name has been rejected to be an owner")
                ]));

                //notify user
                (new NotificationController)->create(new Request([
                    'user_id' => $request->user_id,
                    'message' => "Your application to be an owner has been rejected",
                    'type' => 'REJECT_OWNER',
                    'source' => auth()->id(),
                ]));

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

        return Bookmarks::with('resortInfo.images')->where('created_by', auth()->id())->get();
        
    }


    public function updateBookmarks(Request $request){

        if($request->action=='add'){

             $check = Bookmarks::where('resort_id', $request->resort_id)->where('created_by', auth()->id())->first();

            if(empty($check)){
                Bookmarks::insert([
                    'resort_id' => $request->resort_id,
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                ]);
            }

        }else{

            Bookmarks::where('resort_id', $request->resort_id)->where('created_by', auth()->id())->delete();

        }

        return response()->json([
            'authenticated' => true,
            'response'=>"Bookmarks updated successfully.",
            'data'=>Bookmarks::with('resortInfo.images')->where('created_by', auth()->id())->get(),
        ]);
    }
}