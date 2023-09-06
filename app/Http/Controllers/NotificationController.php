<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Models\Notification;

class NotificationController extends Controller
{

	public function index(Request $request)
	{
		try {

			return Notification::where('user_id', auth()->id())->with('userCreated')->get();

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

			$notif = new Notification;

			$notif->insert([
				'user_id' => $request->user_id,
				'message' => $request->message,
				'type' => $request->type,
				'source' => $request->source,
				'status' => 0,
				'created_at' => now(),
				'created_by' => auth()->id(),
			]);

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
}