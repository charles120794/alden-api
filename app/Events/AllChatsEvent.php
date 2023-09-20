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

    public $allchats;
    public $allchats_user1info;
    public $allchats_user2info;

    public function __construct($allchats)
    {
        $this->allchats = $allchats;
        $this->allchats_user1info = $allchats->user_info1;
        $this->allchats_user2info = $allchats->user_info2;
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
