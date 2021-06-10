<?php

namespace Modules\ConnectionModule\Events;

use Illuminate\Queue\SerializesModels;

class ConnectionEvent
{
    use SerializesModels;

    public $type;
    public $connection;
    public $toUser;

    public function __construct($type,$connection,$toUser)
    {
        $this->type=$type;
        $this->connection=$connection;
        $this->toUser=$toUser;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
