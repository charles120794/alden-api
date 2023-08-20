<?php

namespace App\Http\Controllers;

use DB;
use Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resorts;
use App\Models\ResortRatings;

class ResortController extends Controller
{
    public function index()
    {
        try {
            return Resorts::with('createdUser')->where('is_for_rent', 1)->when(!empty(request()->search), function($query) {
                return $query->where('resort_name', 'like', '%' . request()->search. '%')
                    ->orWhere('resort_desc', 'like', '%' . request()->search. '%')
                    ->orWhere('resort_address', 'like', '%' . request()->search. '%')
                    ->orWhere('province', 'like', '%' . request()->search. '%')
                    ->orWhere('city', 'like', '%' . request()->search. '%')
                    ->orWhere('barangay', 'like', '%' . request()->search. '%');
            })->get()->map(function($value) {
                return collect($value)->merge([
                    'amenities' => DB::table('resort_amenities')->where('resort_id', $value->id)->get(),
                    'policies' => DB::table('resort_policy')->where('resort_id', $value->id)->get(),
                    'ratings' => ResortRatings::with('createdUser')->where('resort_id', $value->id)->get(),
                    'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->id)->avg('rating') ?? 0,
                    'feedback' => DB::table('resort_feedback')->where('resort_id', $value->id)->get(),
                    'images' => DB::table('resort_images')->where('resort_id', $value->id)->get(),
                    'pricing' => DB::table('resort_pricing')->where('resort_id', $value->id)->get(),
                    'reservation' => DB::table('resort_reservation')->where('resort_id', $value->id)->get(),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }

	public function create(Request $request)
	{

        try {

            DB::beginTransaction();

            $resort = DB::table('resort')->insertGetId([
                'resort_name' => $request->resort_name,
                'resort_desc' => $request->resort_desc,
                'resort_address' => $request->resort_address,
                'resort_price' => $request->resort_price,
                'province' => $request->province,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'capture_status' => 0,
                'is_for_rent' => 0,
                // 'capture_date'
                // 'vr_url'
                'created_at' => now(),
                'created_by' => Auth()->User()->id
            ]);

            foreach($request->amenities as $row) {
                // CREATE AMENITIES
                DB::table('resort_amenities')->insert([
                    'resort_id' => $resort,
                    'description' => $row['amenitiesTitle'],
                    'created_at' => now(),
                    'created_by' => Auth()->User()->id
                ]);
            }

            foreach($request->policies as $row) {
                // CREATE AMENITIES
                DB::table('resort_policy')->insert([
                    'resort_id' => $resort,
                    'description' => $row['policiesTitle'],
                    'created_at' => now(),
                    'created_by' => Auth()->User()->id
                ]);
            }

            DB::commit();

            return response()->json([
                'response' => 'Successfully created',
            ]);

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
	}

    public function getResortList()
    {
        return DB::table('resort')->get()->map(function($value) {
            return collect($value)->merge([
                'amenities' => DB::table('resort_amenities')->where('resort_id', $value->id)->get(),
                'policies' => DB::table('resort_policy')->where('resort_id', $value->id)->get(),
                'ratings' => DB::table('resort_rate')->where('resort_id', $value->id)->get(),
                'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->id)->avg('rating') ?? 0,
                'feedback' => DB::table('resort_feedback')->where('resort_id', $value->id)->get(),
                'images' => DB::table('resort_images')->where('resort_id', $value->id)->get(),
                'pricing' => DB::table('resort_pricing')->where('resort_id', $value->id)->get(),
                'reservation' => DB::table('resort_reservation')->where('resort_id', $value->id)->get(),
            ]);
        });
    }
}