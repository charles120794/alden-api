<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActivityLogEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $activity_log;

    public function __construct($activity_log)
    {
        $this->activity_log = $activity_log;
    }

    public function broadcastOn()
      {
          return ['activity-log-channel'];
      }

      public function broadcastAs()
      {
          return 'activity-log-event';
      }
}
