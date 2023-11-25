<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notifications = "sample";

    public function __construct($notifications)
    {
        // $this->notifications = $notifications;
    }

    public function broadcastOn()
      {
          return ['notif-channel'];
      }

      public function broadcastAs()
      {
          return 'notif-event';
      }
}
