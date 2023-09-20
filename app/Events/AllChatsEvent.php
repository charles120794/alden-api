<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AllChatsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    $allchats;
    
    public function __construct($allchats)
    {
        $this->allchats = $allchats;
    }

    public function broadcastOn()
    {
        return ['all-chat-channel'];
    }

    public function broadcastAs()
    {
        return 'all-chat-event';
    }
}
