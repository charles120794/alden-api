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
}
