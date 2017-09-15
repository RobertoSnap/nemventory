<?php

namespace App\Events;

use App\Models\Warehouse;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WarehouseMovement implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $warehouseId;
    public $message;
    public $heading;
    public $type;
    public $dismiss;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($warehouseId, $message, $heading = "", $type = "info", $dismiss = true)
    {
        $this->warehouseId = $warehouseId;
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
        return new Channel('warehouse.'.$this->warehouseId);
    }
}
