<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chats;
use App\Models\ChatsMessages;
use App\Events\ChatEvent;
use App\Events\AllChatsEvent;
use App\Events\MyEvent;

class ChatController extends Controller
{
    public function index()
    {

        try {

            $chats = Chats::where('user1_id', auth()->id())->orWhere('user2_id', auth()->id())
                            ->with('userInfo1', 'userInfo2', 'userInfoCreated')
                            ->orderBy('updated_at', 'asc')
                            ->get();

            event(new AllChatsEvent($chats));
         
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

    public function indexShow(Request $request)
    {
        try {
            //update chat messages status to seen / read when user opens a chat
            ChatsMessages::where('channel_id', $request->channel_id)->update(['status' => 1]); 

            $chatsMessages = ChatsMessages::where('channel_id', $request->channel_id)->with('userInfo')->orderBy('created_at', 'asc')->get();
         
            return response()->json([
                'response' => 'success',
                'data' => $chatsMessages,
                'channel_id' =>$request->channel_id
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
                $chats->last_message = $request->chats_messages['message_body'];
                $chats->updated_at = now();

                $chats->save();

                $msg_id = ChatsMessages::insertGetId([
                    'channel_id' => $chats->id,
                    'user_id' => $request->chats_messages['user_id'],
                    'message_body' => $request->chats_messages['message_body'],
                    'status' => 0,
                    'created_at' => now()
                ]);
                
                $newMessage = ChatsMessages::where('id', $msg_id)->with('userInfo')->first();
                
                event(new ChatEvent($newMessage));
                $this->index();

            } else {

                $create = new Chats;
                $createMessage = new ChatsMessages;

                $id = $create->insertGetId([
                    'user1_id' => $request->chats['user1_id'],
                    'user2_id' => $request->chats['user2_id'],
                    'created_by' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $msg_id = $createMessage->insertGetId([
                    'channel_id' => $id,
                    'user_id' => $request->chats_messages['user_id'],
                    'message_body' => $request->chats_messages['message_body'],
                    'created_at' => now()
                ]);

                $newMessage = ChatsMessages::where('id', $msg_id)->with('userInfo')->first();

                $isNew = true;

                // event(new ChatEvent($newMessage));
                event(new ChatEvent($newMessage, $isNew));
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

    //seen message
    public function updateReadStatus(Request $request)
    {
        try {
            ChatsMessages::where('channel_id', $request->channel_id)->update(['status' => 1]);
         
            return response()->json([
                'response' => 'success'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage()
            ]);
        }
    }

    //mark as unread
    public function unreadStatus(Request $request)
    {
        try {
            ChatsMessages::where('channel_id', $request->channel_id)->update(['status' => 0]);
         
            return response()->json([
                'response' => 'success'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'response' => $e->getMessage()
            ]);
        }
    }

    public function testingPusher(Request $request)
    {
        $message = $request->input('message');

        event(new ChatEvent($message));

        return response()->json(['message' => 'Message sent successfully.']);
    }
}