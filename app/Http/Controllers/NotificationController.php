<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Models\Reservation;
use App\Models\Notification;
use App\Events\MyEvent;

class NotificationController extends Controller
{

	public function index(Request $request)
	{
		try {

			return Notification::where('user_id', auth()->id())->with('userCreated', 'resortInfo.createdUser', 'reservationInfo.priceInfo')->orderBy('created_at', 'desc')->get();

		} catch (\Exception $e) {
			return response()->json([
                'response' => $e->getMessage(),
            ]);
		}
	}

	public function show(Request $request)
	{
		try {

			$notif = Notification::where('id', $request->id)->firstOrFail();

			$notif->status = $request->status;

			$notif->save();

			return $notif;

		} catch (\Exception $e) {
			return response()->json([
                'response' => $e->getMessage(),
            ]);
		}
	}


	public function create(Request $request)
	{
		try {
			// $reservation_id = null;
			
			// if(isset($request->fromReserve)){

			// 	$reservation_id = Reservation::select('id')->where('resort_id', $request->resort_id)->where(DB::raw('DATE_FORMAT(`created_at`, "%Y-%m-%d")'), now()->toDateString())->first();

			// }else if (isset($request->reserve_date)){

			// 	$reservation_id = Reservation::select('id')->where('resort_id', $request->resort_id)->where('created_at', $request->reserve_date)->first();

			// }

			Notification::insert([
				'resort_id' => $request->resort_id,
				'reservation_id' => $reservation_id ?? null,
				'user_id' => $request->user_id,
				'message' => $request->message,
				'type' => $request->type,
				'source' => $request->source,
				'status' => 0,
				'created_at' => now(),
				'created_by' => auth()->id(),
			]);

			return response()->json([
				'response' => 'Successfully Created!',
			]);

		} catch (\Exception $e) {
			return response()->json([
          'response' => $e->getMessage(),
      ]);
		}
	}

	public function submit(Request $request)
	{
		try {

			return event(new MyEvent($request->message));

		} catch (\Exception $e) {

			return response()->json([
          'response' => $e->getMessage(),
      ]);
		}
	}

	public function update(Request $request)
	{
		try {

			Notification::where('id', $request->id)->update(['status' => 1]);

		} catch (\Exception $e) {
			return response()->json([
                'response' => $e->getMessage(),
            ]);
		}
	}

	public function notifiReservation()
    {
        $reservation = Reservation::where('created_by', auth()->id())->where('rate_status', 0)->get();

        foreach ($reservation as $reserve) {

            if($reserve->reserve_date < now()) {
                //check first if notification already exists
                $count = Notification::query()
                    ->where('resort_id',  $reserve->resort_id)
                    ->where('reservation_id',  $reserve->id)
                    ->where('user_id',  $reserve->created_by)
                    ->where('type',  'TO_REVIEW')
                    ->where('source',  20)
                    ->count();

                if($count == 0) {
										//add to db if notification does not exist
                    Notification::insert([
                        'resort_id' => $reserve->resort_id,
                        'reservation_id' => $reserve->id,
                        'user_id' => $reserve->created_by,
                        'message' => "Please rate your experience",
                        'type' => 'TO_REVIEW',
                        'status' => 0,
                        'created_at' => now(),
                        'created_by' => 20,
                        'source' => 20,
                    ]);
									}
            }
        }
    }
}