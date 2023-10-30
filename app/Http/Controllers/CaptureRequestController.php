<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CaptureRequest;

class CaptureRequestController extends Controller
{
    public function index(Request $request)
	{

        try {

            return CaptureRequest::with('resortInfo', 'userCreated')->orderBy('created_at', 'desc')->get();

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

            CaptureRequest::where('id', $request->resort_id)->update([
                'captured_at' => now(),
            ]);
            

            return response()->json([
                'response' => 'Successfully updated to captured',
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
}
