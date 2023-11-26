<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resorts;
use App\Events\AdminEvent;
use App\Events\ActivityLogEvent;

class AdminController extends Controller
{
    public function index()
    {

        try {

            $allUserCount = User::count();
            $userCount = User::where('type', 0)->count();
            $ownerCount = User::where('type', 1)->count();

            $allUserByDateCount = User::select(
                    DB::raw('DATE_FORMAT(`created_at`, "%Y-%m-%d") as created_date'), 
                    DB::raw('count(*) as total'))
                ->groupBy(DB::raw('DATE_FORMAT(`created_at`, "%Y-%m-%d")'))
                ->get();

            $allResortCount = Resorts::count();
            $allActiveResortCount = Resorts::where('is_for_rent', 1)->count();
            $allInactiveResortCount = Resorts::where('is_for_rent', 0)->count();
            $capturedResortCount = Resorts::where('capture_status', 1)->count();
            $allResortByDateCount = Resorts::select(
                    DB::raw('DATE_FORMAT(`created_at`, "%Y-%m-%d") as created_date'), 
                    DB::raw('count(*) as total'))
                ->groupBy(DB::raw('DATE_FORMAT(`created_at`, "%Y-%m-%d")'))
                ->get();

            $activityLog = (new ActivityLogController)->index();

            event(new AdminEvent([
                'allUserCount' => $allUserCount,
                'userCount' => $userCount,
                'ownerCount' => $ownerCount,
                'allUserByDateCount' => $allUserByDateCount,
                'allResortCount' => $allResortCount,    
                'allActiveResortCount' => $allActiveResortCount,
                'allInactiveResortCount' => $allInactiveResortCount,
                'capturedResortCount' => $capturedResortCount,
                'allResortByDateCount' => $allResortByDateCount,
            ]));
            

            event(new ActivityLogEvent($activityLog));

            

        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage(),
                // 'allUserCount' => $allUserCount,
                // 'userCount' => $userCount,
                // 'ownerCount' => $ownerCount,
                // 'allUserByDateCount' => $allUserByDateCount,
                // 'allResortCount' => $allResortCount,
                // 'allActiveResortCount' => $allActiveResortCount,
                // 'allInactiveResortCount' => $allInactiveResortCount,
                // 'capturedResortCount' => $capturedResortCount,
                // 'allResortByDateCount' => $allResortByDateCount,
                // 'activityLog' => $activityLog,
            ]);
        }
    }
}
