<?php

namespace App\Http\Controllers;

use DB;
use Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resorts;
use App\Models\ResortRatings;
use App\Models\Reservation;
use App\Models\Notification;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        try {

            return Reservation::with('userCreated', 'resortInfo.createdUser', 'priceInfo')->get();
            
        } catch (\Exception $e) {
            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }

    public function ownderDashboard(Request $request)
    {
        try {
            $reservation_list = Reservation::with('userCreated', 'resortInfo.createdUser', 'priceInfo')->get();
            $review_list = ResortRatings::with('createdUser', 'resortInfo')->where('resort_owner_id', auth()->id())->orderBy('created_at', 'desc')->get();
            $reservations_chart = DB::table('resort_reservation')
                                        ->where('resort_owner_id', auth()->id())
                                        ->join('resort', 'resort.id', '=', 'resort_reservation.resort_id')
                                        ->select('resort_name as name', DB::raw('count(*) as value'))
                                        ->groupBy('resort_name')
                                        ->get();

            return response()->json([
                'reservation_list' => $reservation_list,
                'review_list' => $review_list,
                'reservations_chart' => $reservations_chart,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }

}