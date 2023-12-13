<?php

namespace App\Http\Controllers;

use DB;
use Storage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Resorts;
use App\Models\ResortRatings;
use App\Models\Reservation;
use App\Models\ResortPricings;
use App\Models\Notification;
use App\Mail\MailResortReserve;
use App\Mail\MailConfirmReservation;
use App\Mail\MailRejectReservation;
use App\Http\Controllers\NotificationController;

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

    public function createReservation(Request $request)
    {
        try {

            if($request->pricing_id == 0 || $request->ref_no == "" || !$request->hasFile('screenshot')){
                return response()->json([
                    'status' => 'error',
                    'response' => 'Please fill up necessary fields',
                ]);
            }

            $screenshot_path = "";
            if ($request->hasFile('screenshot')) {

                $file = $request->file('screenshot');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public', $filename); 

                $screenshot_path = Storage::disk('public')->url($filename);
            } else {
                throw new \Exception("Payment screenshot is required", 1);
            }

            $ownerInfo = User::findorfail($request->resort_owner_id);
            $resortInfo = Resorts::findorfail($request->resort_id);
            $pricingInfo = ResortPricings::findorfail($request->pricing_id);

            // add data to reservation table
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

            //send email notification
            Mail::to($ownerInfo->email)->send(new MailResortReserve(
                $resortInfo->resort_name,
                $pricingInfo->price_desc,
                Carbon::parse($pricingInfo->time_from)->format('h:i a'),
                Carbon::parse($pricingInfo->time_to)->format('h:i a'),
                Carbon::parse($request->reserve_date)->format('M d, Y'),
                auth()->user()->name,
                auth()->user()->email,
                auth()->user()->contact_no,
                $screenshot_path
            ));

            $userName = auth()->user()->name;
            //notification
            (new NotificationController)->create(
                new Request(
                    [
                    'resort_id' => $request->resort_id, 
                    'reservation_id' => $reserve,
                    'user_id' => $request->resort_owner_id,
                    'message' => ("Customer $userName reserved your resort"),
                    'type' => 'RESORT_RESERVED',
                    'source' => auth()->id(),
                    'created_by' => auth()->id()
                    ]
                ));
            
            // activity log
            (new ActivityLogController)->create(new Request([
                'activity' => ("User $userName has reserved a resort")
            ]));
            

            return response()->json([
                'status' => 'success',
				'response' => 'Resort has been reserved successfully',
			]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'response' => $e->getMessage(),
            ]);
        }
    }

    public function confirmReservation(Request $request)
    {
        try {
            $notif = new NotificationController;
            $userInfo = User::findorfail($request->data['created_by']);
            $resortInfo = Resorts::findorfail($request->data['resort_id']);
            $reserveInfo = Reservation::findorfail($request->data['reservation_id']);
            $priceInfo = ResortPricings::findorfail($reserveInfo->pricing_id);

            if($request->action == 'confirm'){

                Reservation::where('id', $request->data['reservation_id'])->update([
                    'confirm_status' => 1, //owner confirmed 
                    'note' => $request->note ?? 'No note from the owner'
                ]);

                // send email notification to user
                Mail::to($userInfo->email)->send(new MailConfirmReservation(
                    $resortInfo->resort_name,
                    $priceInfo->price_desc,
                    Carbon::parse($priceInfo->time_from)->format('h:i a'),
                    Carbon::parse($priceInfo->time_to)->format('h:i a'),
                    Carbon::parse($reserveInfo->reserve_date)->format('M d, Y'),
                    $reserveInfo->ref_no,
                    auth()->user()->name,
                    auth()->user()->email,
                    auth()->user()->contact_no,
                    $resortInfo->resort_address,
                    $request->note ?? 'No note from the owner'
                ));

                
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


                $userName = auth()->user()->name;
                (new ActivityLogController)->create(new Request([
                    'activity' => ("Owner $userName has confirmed a reservation")
                ]));

                return response()->json([
                    'status' => 'success',
                    'response' => 'Reservation confirmed',
                ]);

            }else{

                Reservation::where('id', $request->data['reservation_id'])->update([
                    'confirm_status' => 2, //owner reject reservation 
                    'note' => $request->note ?? 'No note from the owner'
                ]);

                Mail::to($userInfo->email)->send(new MailRejectReservation(
                    $resortInfo->resort_name,
                    $priceInfo->price_desc,
                    Carbon::parse($priceInfo->time_from)->format('h:i a'),
                    Carbon::parse($priceInfo->time_to)->format('h:i a'),
                    Carbon::parse($reserveInfo->reserve_date)->format('M d, Y'),
                    $reserveInfo->ref_no,
                    auth()->user()->name,
                    auth()->user()->email,
                    auth()->user()->contact_no,
                    $resortInfo->resort_address,
                    $request->note ?? 'No note from the owner'
                ));

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

                $userName = auth()->user()->name;
                (new ActivityLogController)->create(new Request([
                    'activity' => ("Owner $userName has reject a reservation")
                ]));

                return response()->json([
                    'status' => 'success',
                    'response' => 'Reservation rejected',
                ]);
                
            }

            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'response' => $e->getMessage(),
            ]);
        }
    }

}