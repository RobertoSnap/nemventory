<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserMovement implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	protected $userId;
	public $message;
	public $heading;
	public $type;
	public $dismiss;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId, $message, $heading = "", $type = "info", $dismiss = true)
    {
	    $this->userId = $userId;
	    $this->message = $message;
	    $this->heading = $heading;
	    $this->type    = $type;
	    $this->dismiss = $dismiss;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
	    return new PrivateChannel('user.'.$this->userId);
    }
}
