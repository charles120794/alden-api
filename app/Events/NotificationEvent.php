<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notifications;

    public function __construct()
    {
        $this->notifications = Notification::where('user_id', auth()->id())->with('userCreated', 'resortInfo.createdUser', 'reservationInfo.priceInfo')->orderBy('created_at', 'desc')->get();
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
