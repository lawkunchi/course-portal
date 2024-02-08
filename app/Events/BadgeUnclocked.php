<?php

    namespace App\Events;

    use Illuminate\Broadcasting\Channel;
    use Illuminate\Broadcasting\InteractsWithSockets;
    use Illuminate\Broadcasting\PresenceChannel;
    use Illuminate\Broadcasting\PrivateChannel;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    use Illuminate\Foundation\Events\Dispatchable;
    use Illuminate\Queue\SerializesModels;

    class BadgeUnclocked {
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public $badge;

        /**
         * Create a new event instance.
         */
        public function __construct($badge) {
            $this->badge = $badge;
        }

    }
