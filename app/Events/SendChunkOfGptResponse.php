<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendChunkOfGptResponse implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    public $message;
    public $stop;
    /**
     * Create a new event instance.
     */
    public function __construct($finish, $mes)
    {
        $this->message = $mes;
        $this->stop = $finish;
    }

    public function via()
    {
        return ['broadcast'];
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.' . auth()->id()),
        ];
    }

    public function broadcastAs()
    {
        return 'chunks';
    }
}
