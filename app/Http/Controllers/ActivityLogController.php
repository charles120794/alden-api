<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {

        try {

            return ActivityLog::with('userCreated')->orderBy('created_at', 'desc')->get();

        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }

    public function create(Request $request)
    {

        try {

            ActivityLog::insert([
                'activity' => $request->activity,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }
}
