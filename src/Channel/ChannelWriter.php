<?php

namespace Aztech\Events\Bus\Channel;

use Aztech\Events\Event;

interface ChannelWriter
{

    /**
     *
     * @param Event $event
     * @param string $serializedData
     * @return void
     */
    function write(Event $event, $serializedData);

    /**
     * Performs necessary dispose work
     */
    function dispose();
}
