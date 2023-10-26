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
            $reservation_list = Reservation::with('userCreated', 'resortInfo.createdUser', 'priceInfo')->get();
            $latest_review = ResortRatings::with('createdUser', 'resortInfo')->where('resort_owner_id', auth()->id())->latest()->first();
            $reservations_chart = DB::table('resort_reservation')
                                        ->join('resort', 'resort.id', '=', 'resort_reservation.resort_id')
                                        ->select('resort_name', DB::raw('count(*) as total'))
                                        ->groupBy('resort_id')
                                        ->get();

            return response()->json([
                'reservation_list' => $reservation_list,
                'latest_review' => $latest_review,
                'reservations_chart' => $reservations_chart,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }

    public function indexShow(Request $request)
    {
        try {

            $resort = Resorts::with('createdUser')->where('confirm_status', 0)->where('id', $request->resort_id)->firstOrFail();

            $resort->amenities = DB::table('resort_amenities')->where('resort_id', $request->resort_id)->get();
            $resort->policies = DB::table('resort_policy')->where('resort_id', $request->resort_id)->get();
            $resort->ratings = ResortRatings::with('createdUser')->where('resort_id', $request->resort_id)->get();
            $resort->ratings_avarage = DB::table('resort_rate')->where('resort_id', $request->resort_id)->avg('rating') ?? 0;
            $resort->images = DB::table('resort_images')->where('resort_id', $request->resort_id)->get();
            $resort->pricing = DB::table('resort_pricing')->where('resort_id', $request->resort_id)->get();
            $resort->reservation = DB::table('resort_reservation')->where('resort_id', $request->resort_id)->get();

            return $resort;

        } catch (\Exception $e) {
            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }

}