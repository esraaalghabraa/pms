<?php

namespace App\Events;

use App\Models\Registration\RegistrationRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class RequestSentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(private RegistrationRequest $requestRegistration)
    {

    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin-channel');
    }

    public function broadcastAs():string
    {
        return 'request.sent';
    }

    /**
     * @return array
     */
    public function broadcastWith():array
    {
        return[
            'user_id'=>$this->requestRegistration->owner_id,
            'message'=>$this->requestRegistration->toArray(),
        ];
    }
}
