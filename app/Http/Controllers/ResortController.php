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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CaptureRequestController;

class ResortController extends Controller
{
    public function index()
    {
        try {
            return Resorts::with('createdUser')->when(!empty(request()->search), function($query) {
                return $query->where('resort_name', 'like', '%' . request()->search. '%')
                    ->orWhere('resort_desc', 'like', '%' . request()->search. '%')
                    ->orWhere('resort_address', 'like', '%' . request()->search. '%')
                    ->orWhere('region', 'like', '%' . request()->search. '%')
                    ->orWhere('province', 'like', '%' . request()->search. '%')
                    ->orWhere('city', 'like', '%' . request()->search. '%')
                    ->orWhere('barangay', 'like', '%' . request()->search. '%');
            })->get()->map(function($value) {
                return collect($value)->merge([
                    'amenities' => DB::table('resort_amenities')->where('resort_id', $value->id)->where('archive', 0)->get(),
                    'policies' => DB::table('resort_policy')->where('resort_id', $value->id)->where('archive', 0)->get(),
                    'ratings' => ResortRatings::with('createdUser')->where('resort_id', $value->id)->get(),
                    'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->id)->avg('rating') ?? 0,
                    'images' => DB::table('resort_images')->where('resort_id', $value->id)->where('archive', 0)->get(),
                    'images_vr' => DB::table('resort_vr_images')->where('resort_id', $value->id)->where('archive', 0)->get(),
                    'pricing' => DB::table('resort_pricing')->where('resort_id', $value->id)->where('archive', 0)->get(),
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

            $resort->amenities = DB::table('resort_amenities')->where('resort_id', $request->resort_id)->where('archive', 0)->get();
            $resort->policies = DB::table('resort_policy')->where('resort_id', $request->resort_id)->where('archive', 0)->get();
            $resort->ratings = ResortRatings::with('createdUser')->where('resort_id', $request->resort_id)->get();
            $resort->ratings_avarage = DB::table('resort_rate')->where('resort_id', $request->resort_id)->avg('rating') ?? 0;
            $resort->images = DB::table('resort_images')->where('resort_id', $request->resort_id)->where('archive', 0)->get();
            $resort->images_vr = DB::table('resort_vr_images')->where('resort_id', $request->resort_id)->where('archive', 0)->get();
            $resort->pricing = DB::table('resort_pricing')->where('resort_id', $request->resort_id)->where('archive', 0)->get();
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

            $business_permit_path = "";
            if ($request->hasFile('business_permit')) {

                $file = $request->file('business_permit');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); 

                $business_permit_path = Storage::disk('public')->url($filename);
            } else {
                throw new \Exception("Image is required", 1);
            }

            $resort = DB::table('resort')->insertGetId([
                'resort_name' => $request->resort_name,
                'resort_desc' => $request->resort_desc,
                'resort_address' => $request->resort_address,
                'resort_region' => $request->resort_region,
                'resort_province' => $request->resort_province,
                'resort_city' => $request->resort_city,
                'resort_barangay' => $request->resort_barangay,
                'region' => $request->resort_region_name,
                'province' => $request->resort_province_name,
                'city' => $request->resort_city_name,
                'barangay' => $request->resort_barangay_name,
                'business_permit' => $business_permit_path,
                'capture_status' => 0,
                'is_for_rent' => 0,
                'capture_date_from' => date('Y-m-d', strtotime($request->capture_date_from)),
                'capture_date_to' => date('Y-m-d', strtotime($request->capture_date_to)),
                'created_at' => now(),
                'created_by' => Auth()->User()->id
            ]);

            

            foreach($request->amenities as $row) {
                // CREATE AMENITIES
                if(isset($row['amenitiesTitle'])){
                    $ans = json_decode($answer['amenitiesTitle'],true);
                    if(is_array($ans)){
                        DB::table('resort_amenities')->insert([
                            'resort_id' => $resort,
                            'description' => json_decode($row["amenitiesTitle"]),
                            'created_at' => now(),
                            'created_by' => Auth()->User()->id
                        ]);
                    }
                }
                
            }

            foreach($request->policies as $row) {
                // CREATE POLICIES
                if(isset($row['policiesTitle'])){
                    $ans = json_decode($answer['policiesTitle'],true);
                    if(is_array($ans)){
                        DB::table('resort_policy')->insert([
                            'resort_id' => $resort,
                            'description' => $row['policiesTitle'],
                            'created_at' => now(),
                            'created_by' => Auth()->User()->id
                        ]);
                    }
                }
                
            }

            foreach($request->pricing as $row) {
                // CREATE PRICING
                if(isset($row['description'])){
                    $ans = json_decode($answer['description'],true);
                    if(is_array($ans)){
                        DB::table('resort_pricing')->insert([
                            'resort_id' => $resort,
                            'price_desc' => $row['description'],
                            'price' => $row['price'],
                            'downpayment_percent' => $row['downpayment_percent'],
                            'created_at' => now(),
                            'created_by' => Auth()->User()->id
                        ]);
                    }
                }
                
            }

            DB::commit();

            (new CaptureRequestController)->create(new Request([
                'resort_id' => $resort,
                'capture_date_from' => $request->capture_date_from,
                'capture_date_to' => $request->capture_date_to,
            ]));

            //notify admin
            (new NotificationController)->create(new Request([
                'resort_id' => $resort,
                'user_id' => '20',
                'message' => "A new resort has been posted. Check resort's available schedule for 360 image capturing.",
                'type' => 'RESORT_POSTED',
                'source' => auth()->id(),
            ]));

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

    public function update(Request $request)
    {
        try{

            Resorts::where('id', $request->id)->update([
                'resort_name' => $request->resort_name,
                'resort_desc' => $request->resort_desc,
                'resort_address' => $request->resort_address,
                'resort_region' => $request->resort_region,
                'resort_province' => $request->resort_province,
                'resort_city' => $request->resort_city,
                'resort_barangay' => $request->resort_barangay,
                'region' => $request->region,
                'province' => $request->province,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'is_for_rent' => $request->is_for_rent,
                'updated_at' => now(),
            ]);


            // UPDATE AMENITIES
            foreach($request->amenities as $row) {

                $check = DB::table('resort_amenities')->where('id', $row['id'])->first();

                if(empty($check))
                {
                    DB::table('resort_amenities')->insert([
                        'resort_id' => $request->id,
                        'description' => $row["description"],
                        'created_at' => now(),
                        'created_by' => Auth()->User()->id
                    ]);
                }
                else if(isset($row['delete']))
                {
                    DB::table('resort_amenities')->where('id', $row['id'])->update([
                        'archive' => 1,
                    ]);
                }
                
            }

            // UPDATE POLICIES
            foreach($request->policies as $row) {

                $check = DB::table('resort_policy')->where('id', $row['id'])->first();

                if(empty($check))
                {
                    DB::table('resort_policy')->insert([
                        'resort_id' => $request->id,
                        'description' => $row["description"],
                        'created_at' => now(),
                        'created_by' => Auth()->User()->id
                    ]);
                }
                else if(isset($row['delete']))
                {
                    DB::table('resort_policy')->where('id', $row['id'])->update([
                        'archive' => 1,
                    ]);
                }
                
            }

            // UPDATE PRICING
            foreach($request->pricing as $row) {

                $check = DB::table('resort_pricing')->where('id', $row['id'])->first();

                if(empty($check))
                {
                    DB::table('resort_pricing')->insert([
                        'resort_id' => $request->id,
                        'price_desc' => $row["price_desc"],
                        'price' => $row["price"],
                        'downpayment_percent' => $row["downpayment_percent"],
                        'created_at' => now(),
                        'created_by' => Auth()->User()->id
                    ]);
                }
                else if(isset($row['delete']))
                {
                    DB::table('resort_pricing')->where('id', $row['id'])->update([
                        'archive' => 1,
                    ]);
                }
                
            }

            return response()->json([
                'response' => 'Update saved',
            ]);

        }catch(\Excetion $e){
            return response()->json([
                'response' => $e->getMessage(),
            ]);
        }
    }

    public function createReservation(Request $request)
    {
        try {

            $screenshot_path = "";
            if ($request->hasFile('screenshot')) {

                $file = $request->file('screenshot');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); 

                $screenshot_path = Storage::disk('public')->url($filename);
            } else {
                throw new \Exception("Image is required", 1);
            }

            $owner = Resorts::where('id', $request->resort_id)->first();

            $reserve = Reservation::insertGetId([
                'resort_id' => $request->resort_id,
                'resort_owner_id' => $request->resort_owner_id,
                'pricing_id' => $request->pricing_id,
                'reserve_date' => date('Y-m-d', strtotime($request->reserve_date)),
                'ref_no' => $request->ref_no,
                'screenshot' => $screenshot_path,
                'confirm_status' => 0, //pending reservation, owner need to confirm 
                'rate_status' => 0, 
                'created_at' => now(),
                'created_by' => Auth()->User()->id
            ]);

            $notif = new NotificationController;
            $notif->create(
                new Request(
                    [
                    'resort_id' => $request->resort_id, 
                    'reservation_id' => $reserve,
                    'user_id' => $owner->created_by,
                    'message' => 'Your resort has been reserved',
                    'type' => 'RESORT_RESERVED',
                    'source' => auth()->id(),
                    'created_by' => auth()->id()
                    ]
                ));
            

            return response()->json([
				'response' => 'Resort has been reserved successfully',
				'reserveDate' => date('Y-m-d', strtotime($request->reserve_date)),
				'reserveDate2' => $request->reserve_date,
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
            $notif = new NotificationController;


            if($request->action == 'confirm'){
                Reservation::where('id', $request->data['reservation_id'])->update([
                    'confirm_status' => 1, //owner confirmed 
                ]);

                
                $notif->create(
                    new Request(
                        [
                        'resort_id' => $request->data['resort_id'], 
                        'reservation_id' => $request->data['reservation_id'],
                        'user_id' => $request->data['created_by'],
                        'message' => 'Reservation is confirmed by the owner.',
                        'type' => 'CONFIRM_RESERVATION',
                        'source' => auth()->id(),
                        'created_by' => auth()->id(),
                        ]
                    ));

                return response()->json([
                    'response' => 'Reservation confirmed',
                ]);

            }else{
                Reservation::where('id', $request->data['reservation_id'])->update([
                    'confirm_status' => 2, //owner reject reservation 
                ]);

                $notif->create(
                    new Request(
                        [
                        'resort_id' => $request->data['resort_id'], 
                        'reservation_id' => $request->data['reservation_id'],
                        'user_id' => $request->data['created_by'],
                        'message' => 'Reservation is rejected by the owner.',
                        'type' => 'REJECT_RESERVATION',
                        'source' => auth()->id(),
                        'created_by' => auth()->id(),
                        ]
                    ));

                return response()->json([
                    'response' => 'Reservation rejected',
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
        return Resorts::where('created_by', auth()->id())->get()->map(function($value) {
            return collect($value)->merge([
                'amenities' => DB::table('resort_amenities')->where('resort_id', $value->id)->where('archive', 0)->get(),
                'policies' => DB::table('resort_policy')->where('resort_id', $value->id)->where('archive', 0)->get(),
                'ratings' => DB::table('resort_rate')->where('resort_id', $value->id)->get(),
                'ratings_avarage' => DB::table('resort_rate')->where('resort_id', $value->id)->avg('rating') ?? 0,
                'images' => DB::table('resort_images')->where('resort_id', $value->id)->where('archive', 0)->get(),
                'pricing' => DB::table('resort_pricing')->where('resort_id', $value->id)->where('archive', 0)->get(),
                'reservation' => Reservation::with('userCreated', 'priceInfo')->where('resort_id', $value->id)->get(),
            ]);
        });
    }

    

    public function reviewResort(Request $request){
        ResortRatings::insert([
            'resort_id' => $request->resort_id,
            'resort_owner_id' => $request->resort_owner_id,
            'reservation_id' => $request->reservation_id,
            'rating' => $request->currentValue,
            'feedback' => $request->comment,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        Reservation::where('id', $request->reservation_id)->update([
            'rate_status' => 1,
        ]);

        //notify owner
        (new NotificationController)->create(new Request([
            'resort_id' => $request->resort_id,
            'user_id' => $request->resort_owner_id,
            'message' => "A previous customer has rated and commented on your resort.",
            'type' => 'RESORT_REVIEWED',
            'source' => auth()->id(),
        ]));

        return response()->json(['response'=>"Resort reviewed successfully."]);
        
    }

    public function showResortReview(Request $request){
        $review = ResortRatings::where('resort_id', $request->resort_id)
                    ->where('reservation_id', $request->reservation_id)
                    ->where('created_by', auth()->id())
                    ->get();

        return $review;
    }
}