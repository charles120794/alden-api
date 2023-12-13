<?php

namespace App\Http\Controllers;

use DB;
use Storage;
use Illuminate\Http\Request;
use App\Http\Controllers\NotificationController;
use App\Models\CaptureRequest;
use App\Models\ResortRatings;
use App\Models\Resorts;

class CaptureRequestController extends Controller
{
    public function index(Request $request)
	{

        try {

            return CaptureRequest::with('resortInfo', 'userCreated')->orderBy('created_at', 'desc')->get()->map(function($value) {
                return collect($value)->merge([
                    'amenities' => DB::table('resort_amenities')->where('resort_id', $value->resort_id)->where('archive', 0)->get(),
                    'policies' => DB::table('resort_policy')->where('resort_id', $value->resort_id)->where('archive', 0)->get(),
                    'ratings' => ResortRatings::with('createdUser')->where('resort_id', $value->resort_id)->get(),
                    'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->resort_id)->avg('rating') ?? 0,
                    'images' => DB::table('resort_images')->where('resort_id', $value->resort_id)->where('archive', 0)->get(),
                    'images_vr' => DB::table('resort_vr_images')->where('resort_id', $value->resort_id)->where('archive', 0)->get(),
                    'pricing' => DB::table('resort_pricing')->where('resort_id', $value->resort_id)->where('archive', 0)->get(),
                    'reservation' => DB::table('resort_reservation')->where('resort_id', $value->resort_id)->get(),
                ]);
            });;

        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage(),
            ]);

        }
	}

    public function create(Request $request)
	{

        try {

            CaptureRequest::insert([
                'resort_id' => $request->resort_id,
                'capture_date_from' => date('Y-m-d', strtotime($request->capture_date_from)),
                'capture_date_to' => date('Y-m-d', strtotime($request->capture_date_to)),
                'capture_status' => 0,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            Resorts::where('id', $request->resort_id)->update([
                'capture_status' => 0,
                'capture_date_from' => date('Y-m-d', strtotime($request->capture_date_from)),
                'capture_date_to' => date('Y-m-d', strtotime($request->capture_date_to)),
            ]);

            $userName = auth()->user()->name;
            (new ActivityLogController)->create(new Request([
                'activity' => ("User $userName requested to 360 image capture a resort")
            ]));
            

            return response()->json([
                'status' => 'success',
                'response' => 'Successfully added to capture list',
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'response' => $e->getMessage(),
            ]);

        }
	}

    public function update(Request $request)
	{

        try {

            $countImages = DB::table('resort_images')->where('resort_id', $request->resort_id)->where('archive', 0)->count();
            $countVrImages = DB::table('resort_vr_images')->where('resort_id', $request->resort_id)->where('archive', 0)->count();

            $countImageInput = 0;
            $countVrImageInput = 0;

            if (
                (!$request->hasFile('resort_image') && !$request->hasFile('resort_vr_image')) ||
                (count(request()->file('resort_image'))==0 && count(request()->file('resort_vr_image'))==0)
            ){
                return response()->json([
                    'status' => 'error',
                    'response' => 'Invalid Action: Choose thumbnail or 360 images',
                ]);
            }

            if($request->hasFile('resort_image')){
                foreach(request()->file('resort_image') as $key => $file) {
                    $countImageInput += 1;
                }

                if($countImages == 0 && $countImageInput < 3){
                    return response()->json([
                        'status' => 'error',
                        'response' => "Resort should have at least 3 thumbnails. \nCurrent thumbnails: $countImages",
                    ]);
                }
            } 
            
            if($request->hasFile('resort_vr_image')){
                foreach(request()->file('resort_vr_image') as $key => $file) {
                    $countVrImageInput += 1;
                }

                if($countVrImages == 0 && $countVrImageInput < 3){
                    return response()->json([
                        'status' => 'error',
                        'response' => "Resort should have at least 3 360 images. \nCurrent thumbnails: $countVrImages",
                    ]);
                }    

            } 

            if($request->hasFile('resort_image')){

                foreach(request()->file('resort_image') as $key => $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public', $filename);
                    $getFileName = Storage::disk('public')->url($filename);
                    DB::table('resort_images')->insert([
                        'resort_id' => $request->resort_id,
                        'resort_image' => $getFileName,
                        'created_at' => now(),
                    ]);
                }

            } 


            if($request->hasFile('resort_vr_image')){

                foreach(request()->file('resort_vr_image') as $key => $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->storeAs('public', $filename);
                    $getFileName = Storage::disk('public')->url($filename);
                    DB::table('resort_vr_images')->insert([
                        'resort_id' => $request->resort_id,
                        'resort_vr_image' => $getFileName,
                        'created_at' => now(),
                    ]);
                }

            } 

            

            Resorts::where('id', $request->resort_id)->update([
                'is_for_rent' => 1,
                'capture_status' => 1,
                'captured_at' => now(),
                'updated_at' => now(),
            ]);

            CaptureRequest::where('id', $request->request_id)->update([
                'capture_status' => 1,
                'captured_at' => now(),
            ]);

            //notify owner
            (new NotificationController)->create(new Request([
                'resort_id' => $request->resort_id,
                'user_id' => $request->resort_owner,
                'message' => "Your resort is finally posted with a brilliant 360 images that serves as a virtual tour of the resort",
                'type' => 'RESORT_CAPTURED',
                'source' => auth()->id(),
            ]));

            (new ActivityLogController)->create(new Request([
                'activity' => ("A new resort has been uploaded with 360 images")
            ]));
            

            return response()->json([
                'status' => 'success',
                'response' => 'Image uploaded Successfully!',
            ]);

            
        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'response' => $e->getMessage(),
            ]);
            
        }
	}

    public function deleteImage(Request $request)
	{

        try {

            if($request->image_type == "image"){

                DB::table('resort_images')->where('id', $request->id)->update(['archive' => 1]);

            }

            if($request->image_type == "image_vr"){

                DB::table('resort_vr_images')->where('id', $request->id)->update(['archive' => 1]);

            }
            
            return response()->json([
                'response' => 'Successfully deleted',
            ]);
            
        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage(),
            ]);
            
        }
	}
}
