<?php

namespace App\Events\Usdt;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithBroadcasting;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UsdtTransactionEvent implements ShouldBroadcast
{
    use InteractsWithBroadcasting;

    public function __construct(
        public string $product,
        public int $userId
    ) {
    }

    public function broadcastAs()
    {
        return 'transaction.success';
    }

    public function broadcastOn()
    {
        return new Channel('notification.' . $this->userId);
        // return new PrivateChannel('notification.' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'product' => $this->product,
        ];
    }
}
