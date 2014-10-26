<?php

namespace Aztech\Events\Bus;

use Aztech\Events\Event as EventInterface;

interface Publisher
{

    /**
     * @return void
     */
    public function publish(EventInterface $event);
}
