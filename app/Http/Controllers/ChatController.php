<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chats;

class ChatController extends Controller
{
	public function index(Request $request)
	{

        try {

            $chats = Chats::where('created_by', auth()->id())->with('userInfo1', 'userInfo2', 'userInfoCreated', 'chatsMessages.userInfo')->get();
         
            return response()->json([
                'response' => 'success',
                'data' => $chats
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
	}

    public function create(Request $request)
    {

        try {

            $chats = Chats::create($request->chats);

            $chats->chatsMessages()->createByMany($request->meessages);
         
            return response()->json([
                'response' => 'success',
                'data' => $chats
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'authenticated' => true,
                'response' => $e->getMessage(),
                'token' => ''
            ]);
        }
    }
}