<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class LocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $latitude;
    public $longitude;
    public $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($latitude, $longitude, $userId)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        Log::info("evento disparado");
        return new Channel('location-updates');
    }

    public function broadcastAs()
    {
        Log::info("evento disparado2");
        return 'location.updated';
    }
}
