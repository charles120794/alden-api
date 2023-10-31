<?php

namespace App\Http\Controllers;

use DB;
use Storage;
use Illuminate\Http\Request;
use App\Models\CaptureRequest;
use App\Models\ResortRatings;

class CaptureRequestController extends Controller
{
    public function index(Request $request)
	{

        try {

            return CaptureRequest::with('resortInfo', 'userCreated')->orderBy('created_at', 'desc')->get()->map(function($value) {
                return collect($value)->merge([
                    'amenities' => DB::table('resort_amenities')->where('resort_id', $value->resort_id)->get(),
                    'policies' => DB::table('resort_policy')->where('resort_id', $value->resort_id)->get(),
                    'ratings' => ResortRatings::with('createdUser')->where('resort_id', $value->resort_id)->get(),
                    'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->resort_id)->avg('rating') ?? 0,
                    'images' => DB::table('resort_images')->where('resort_id', $value->resort_id)->get(),
                    'images_vr' => DB::table('resort_vr_images')->where('resort_id', $value->resort_id)->get(),
                    'pricing' => DB::table('resort_pricing')->where('resort_id', $value->resort_id)->get(),
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
            

            return response()->json([
                'response' => 'Successfully added to capture list',
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage(),
            ]);

        }
	}

    public function update(Request $request)
	{

        try {
                                
            DB::table('resort_images')->where('resort_id', $request->resort_id)->delete();
            DB::table('resort_vr_images')->where('resort_id', $request->resort_id)->delete();

            // foreach($request->images_vr as $row) {
            //     // DELETE SPECIFIC IMAGES_VR
            //     if(isset($row['delete'])){
            //         $ans = json_decode($answer['resort_vr_image'],true);
            //         if(is_array($ans)){
            //             DB::table('resort_vr_images')->where('id', json_decode($row["id"]))->delete();
            //         }
            //     }
            // }

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

            Resorts::where('id', $request->resort_id)->update([
                'is_for_rent' => 1,
                'capture_status' => 1,
            ]);

            CaptureRequest::where('id', $request->request_id)->update([
                'capture_status' => 1,
                'captured_at' => now(),
            ]);
            

            return response()->json([
                'response' => 'Image uploaded Successfully!',
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage(),
            ]);
            
        }
	}

    //list of owner's resorts that are yet to be captured
    // public function getCaptureResortList()
    // {
    //     return Resorts::with('createdUser')->where('capture_status', 0)->get()->map(function($value) {
    //         return collect($value)->merge([
    //             'amenities' => DB::table('resort_amenities')->where('resort_id', $value->id)->get(),
    //             'policies' => DB::table('resort_policy')->where('resort_id', $value->id)->get(),
    //             'ratings' => DB::table('resort_rate')->where('resort_id', $value->id)->get(),
    //             'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->id)->avg('rating') ?? 0,
    //             'images' => DB::table('resort_images')->where('resort_id', $value->id)->get(),
    //             'pricing' => DB::table('resort_pricing')->where('resort_id', $value->id)->get(),
    //             'reservation' => DB::table('resort_reservation')->where('resort_id', $value->id)->get(),
    //         ]);
    //     });
    // }

    //admin to upload resort's thumbnails and 360 images
    // public function uploadResortImages(Request $request){
    //     try{

    //         foreach(request()->file('resort_image') as $key => $file) {
    //             $filename = time() . '_' . $file->getClientOriginalName();
    //             $file->storeAs('public', $filename);
    //             $getFileName = Storage::disk('public')->url($filename);
    //             DB::table('resort_images')->insert([
    //                 'resort_id' => $request->resort_id,
    //                 'resort_image' => $getFileName,
    //                 'created_at' => now(),
    //             ]);
    //         }

    //         foreach(request()->file('resort_vr_image') as $key => $file) {
    //             $filename = time() . '_' . $file->getClientOriginalName();
    //             $file->storeAs('public', $filename);
    //             $getFileName = Storage::disk('public')->url($filename);
    //             DB::table('resort_vr_images')->insert([
    //                 'resort_id' => $request->resort_id,
    //                 'resort_vr_image' => $getFileName,
    //                 'created_at' => now(),
    //             ]);
    //         }

    //         Resorts::where('id', $request->resort_id)->update([
    //             'is_for_rent' => 1,
    //             'capture_status' => 1,
    //         ]);

    //         return response()->json(['response' => "Image uploaded Successfully!",]);
            
    //     } catch (\Exception $e) {
    //         return response()->json(['response' => $e->getMessage(),]);
    //     }
        
    // }
}
