<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $isNew;

    public function __construct($message, $isNew = false)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
      if($this->isNew) {
        return ['chat-channel-new']; 
      } esle {
        return ['chat-channel-' . $this->message->channel_id];
      }
    }

    public function broadcastAs()
    {
      if($this->isNew) {
        return 'chat-event';
      } esle {
        return 'chat-event-' . $this->message->channel_id;
      }
    }
}
