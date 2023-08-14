<?php

namespace App\Http\Controllers;

use DB;
use Storage;
use Illuminate\Http\Request;
use App\Models\User;

class ResortController extends Controller
{
	public function create(Request $request)
	{

        try {

            DB::beginTransaction();

            $resort = DB::table('resort')->insertGetId([
                'resort_name' => $request->resort_name,
                'resort_desc' => $request->resort_desc,
                'resort_address' => $request->resort_address,
                'province' => $request->province,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'capture-status' => 0,
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
                    'description' => $row['description'],
                    'created_at' => now(),
                    'created_by' => Auth()->User()->id
                ]);
            }

            foreach($request->policies as $row) {
                // CREATE AMENITIES
                DB::table('resort_policy')->insert([
                    'resort_id' => $resort,
                    'description' => $row['description'],
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
        return DB::table('resort')->where('owner_id', Auth()->User()->id)->get()->map(function($value) {
            return collect($value)->merge([
                'amenities' => DB::table('resort_amenities')->where('resort_id', $value->id)->get(),
                'policies' => DB::table('resort_policy')->where('resort_id', $value->id)->get()
            ]);
        });
    }
}