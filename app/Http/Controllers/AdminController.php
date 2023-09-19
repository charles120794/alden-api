<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resorts;
use App\Events\AdminEvent;

class AdminController extends Controller
{
    public function index()
    {

        try {

            $allUserCount = User::count();
            $userCount = User::where('type', 0)->count();
            $ownerCount = User::where('type', 1)->count();
            $allResortCount = Resorts::count();
            $allActiveResortCount = Resorts::where('is_for_rent', 1)->count();
            $allInactiveResortCount = Resorts::where('is_for_rent', 0)->count();
            $capturedResortCount = Resorts::where('capture_status', 1)->count();

            event(new AdminEvent([
                'allUserCount' => $allUserCount,
                'userCount' => $userCount,
                'ownerCount' => $ownerCount,
                'allResortCount' => $allResortCount,
                'allActiveResortCount' => $allActiveResortCount,
                'allInactiveResortCount' => $allInactiveResortCount,
                'capturedResortCount' => $capturedResortCount,
            ]));

            // return response()->json([
            //     'allUserCount' => $allUserCount,
            //     'userCount' => $userCount,
            //     'ownerCount' => $ownerCount,
            //     'allResortCount' => $allResortCount,
            //     'allActiveResortCount' => $allActiveResortCount,
            //     'allInactiveResortCount' => $allInactiveResortCount,
            //     'capturedResortCount' => $capturedResortCount,
            // ]);

        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }
}
