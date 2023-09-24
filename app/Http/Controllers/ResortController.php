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

class ResortController extends Controller
{
    public function index()
    {
        try {
            return Resorts::with('createdUser')->where('is_for_rent', 1)->when(!empty(request()->search), function($query) {
                return $query->where('resort_name', 'like', '%' . request()->search. '%')
                    ->orWhere('resort_desc', 'like', '%' . request()->search. '%')->where('is_for_rent', 1)
                    ->orWhere('resort_address', 'like', '%' . request()->search. '%')->where('is_for_rent', 1)
                    ->orWhere('region', 'like', '%' . request()->search. '%')->where('is_for_rent', 1)
                    ->orWhere('province', 'like', '%' . request()->search. '%')->where('is_for_rent', 1)
                    ->orWhere('city', 'like', '%' . request()->search. '%')->where('is_for_rent', 1)
                    ->orWhere('barangay', 'like', '%' . request()->search. '%')->where('is_for_rent', 1);
            })->get()->map(function($value) {
                return collect($value)->merge([
                    'amenities' => DB::table('resort_amenities')->where('resort_id', $value->id)->get(),
                    'policies' => DB::table('resort_policy')->where('resort_id', $value->id)->get(),
                    'ratings' => ResortRatings::with('createdUser')->where('resort_id', $value->id)->get(),
                    'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->id)->avg('rating') ?? 0,
                    // 'feedback' => DB::table('resort_feedback')->where('resort_id', $value->id)->get(),
                    'images' => DB::table('resort_images')->where('resort_id', $value->id)->get(),
                    'images_vr' => DB::table('resort_vr_images')->where('resort_id', $value->id)->get(),
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

    public function indexShow(Request $request)
    {
        try {

            $resort = Resorts::with('createdUser')->where('is_for_rent', 1)->where('id', $request->resort_id)->firstOrFail();

            $resort->amenities = DB::table('resort_amenities')->where('resort_id', $request->resort_id)->get();
            $resort->policies = DB::table('resort_policy')->where('resort_id', $request->resort_id)->get();
            $resort->ratings = ResortRatings::with('createdUser')->where('resort_id', $request->resort_id)->get();
            $resort->ratings_avarage = DB::table('resort_rate')->where('resort_id', $request->resort_id)->avg('rating') ?? 0;
            // $resort->feedback = DB::table('resort_feedback')->where('resort_id', $request->resort_id)->get();
            $resort->images = DB::table('resort_images')->where('resort_id', $request->resort_id)->get();
            $resort->images_vr = DB::table('resort_vr_images')->where('resort_id', $request->resort_id)->get();
            $resort->pricing = DB::table('resort_pricing')->where('resort_id', $request->resort_id)->get();
            $resort->reservation = DB::table('resort_reservation')->where('resort_id', $request->resort_id)->get();

            return $resort;

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
                'resort_address_lon' => $this->getLatLngFromGoogleMapsURL($request->resort_address_url)['longitude'] ?? "",
                'resort_address_lat' => $this->getLatLngFromGoogleMapsURL($request->resort_address_url)['latitude'] ?? "",
                'resort_region' => $request->resort_region,
                'resort_province' => $request->resort_province,
                'resort_city' => $request->resort_city,
                'resort_barangay' => $request->resort_barangay,
                'region' => $request->resort_region_name,
                'province' => $request->resort_province_name,
                'city' => $request->resort_city_name,
                'barangay' => $request->resort_barangay_name,
                'capture_status' => 0,
                'is_for_rent' => 0,
                'capture_date_from' => date('Y-m-d', strtotime($request->capture_date[0]['startDate'])),
                'capture_date_to' => date('Y-m-d', strtotime($request->capture_date[0]['endDate'])),
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
                // CREATE POLICIES
                DB::table('resort_policy')->insert([
                    'resort_id' => $resort,
                    'description' => $row['policiesTitle'],
                    'created_at' => now(),
                    'created_by' => Auth()->User()->id
                ]);
            }

            foreach($request->pricing as $row) {
                // CREATE PRICING
                DB::table('resort_pricing')->insert([
                    'resort_id' => $resort,
                    'price_desc' => $row['description'],
                    'price' => $row['price'],
                    'downpayment_percent' => $row['downpayment_percent'],
                    'created_at' => now(),
                    'created_by' => Auth()->User()->id
                ]);
            }

            DB::commit();

            (new AdminController)->index();

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

    public function createReservation(Request $request)
    {
        try {
            DB::table('resort_reservation')->insert([
                'resort_id' => $request->resort_id,
                'pricing_id' => $request->pricing_id,
                'reserve_date' => date('Y-m-d', strtotime($request->reserve_date)),
                'ref_no' => $request->ref_no,
                'confirm_status' => 0, //pending reservation, owner need to confirm 
                'status' => 0, 
                'rate_status' => 0, 
                'created_at' => now(),
                'created_by' => Auth()->User()->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }

    public function confirmReservation(Request $request)
    {
        try {
            if($request->action == 'confirm'){
                Reservation::where('id', $request->reservation_id)->update([
                    'confirm_status' => 1, //owner confirmed 
                ]);
            }else{
                Reservation::where('id', $request->reservation_id)->update([
                    'confirm_status' => 2, //owner reject reservation 
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }


    //list of owner's resorts
    public function getResortList()
    {
        return DB::table('resort')->where('created_by', auth()->id())->get()->map(function($value) {
            return collect($value)->merge([
                'amenities' => DB::table('resort_amenities')->where('resort_id', $value->id)->get(),
                'policies' => DB::table('resort_policy')->where('resort_id', $value->id)->get(),
                'ratings' => DB::table('resort_rate')->where('resort_id', $value->id)->get(),
                'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->id)->avg('rating') ?? 0,
                // 'feedback' => DB::table('resort_feedback')->where('resort_id', $value->id)->get(),
                'images' => DB::table('resort_images')->where('resort_id', $value->id)->get(),
                'pricing' => DB::table('resort_pricing')->where('resort_id', $value->id)->get(),
                'reservation' => Reservation::with('userCreated', 'reservationPriceDesc')->where('resort_id', $value->id)->get(),
            ]);
        });
    }

    //list of owner's resorts that are yet to be captured
    public function getCaptureResortList()
    {
        return Resorts::with('createdUser')->where('capture_status', 0)->get()->map(function($value) {
            return collect($value)->merge([
                'amenities' => DB::table('resort_amenities')->where('resort_id', $value->id)->get(),
                'policies' => DB::table('resort_policy')->where('resort_id', $value->id)->get(),
                'ratings' => DB::table('resort_rate')->where('resort_id', $value->id)->get(),
                'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->id)->avg('rating') ?? 0,
                // 'feedback' => DB::table('resort_feedback')->where('resort_id', $value->id)->get(),
                'images' => DB::table('resort_images')->where('resort_id', $value->id)->get(),
                'pricing' => DB::table('resort_pricing')->where('resort_id', $value->id)->get(),
                'reservation' => DB::table('resort_reservation')->where('resort_id', $value->id)->get(),
            ]);
        });
    }

    //admin to upload resort's thumbnails and 360 images
    public function uploadResortImages(Request $request){
        try{

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
                'vr_url' => $request->vr_url,
            ]);

            return response()->json(['response' => "Image uploaded Successfully!",]);
            
        } catch (\Exception $e) {
            return response()->json(['response' => $e->getMessage(),]);
        }
        
    }

    public function reviewResort(Request $request){
        ResortRatings::insert([
            'resort_id' => $request->resort_id,
            'reservation_id' => $request->reservation_id,
            'rating' => $request->currentValue,
            'feedback' => $request->comment,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        Reservation::where('id', $request->reservation_id)->update([
            'rate_status' => 1,
        ]);

        return response()->json(['message'=>"Resort reviewed successfully."]);
        
    }

}