<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chats;
use App\Models\ChatsMessages;

class ChatController extends Controller
{
	public function index(Request $request)
	{

        try {

            $chats = Chats::where('user1_id', auth()->id())->orWhere('user2_id', auth()->id())
            ->with('userInfo1', 'userInfo2', 'userInfoCreated', 'chatsMessages.userInfo')->get();
         
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

            $chats = Chats::where('user1_id', $request->chats['user1_id'])
                ->where('user2_id', $request->chats['user2_id'])
                ->first();

            if(!empty($chats)) {

                $chats->user1_id = $request->chats['user1_id'];
                $chats->user2_id = $request->chats['user2_id'];

                $chats->save();

                ChatsMessages::insert([
                    'channel_id' => $chats->id,
                    'user_id' => $request->chats_messages['user_id'],
                    'message_body' => $request->chats_messages['message_body'],
                    'status' => 0
                ]);

            } else {

                $create = new Chats;
                $createMessage = new ChatsMessages;

                $id = $create->insertGetId([
                    'user1_id' => $request->chats['user1_id'],
                    'user2_id' => $request->chats['user2_id'],
                    'created_by' => auth()->id()
                ]);

                $createMessage->insert([
                    'channel_id' => $id,
                    'user_id' => $request->chats_messages['user_id'],
                    'message_body' => $request->chats_messages['message_body']
                ]);
            }
         
            return response()->json([
                'response' => 'success',
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