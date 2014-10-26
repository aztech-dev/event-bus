<?php

namespace Aztech\Events\Bus\Channel;

interface AcknowledgeableChannelReader extends ChannelReader
{
    /**
     * @return Message
     */
    public function readAck();

    /**
     *
     * @param Message $ack
     */
    public function acknowledge(Message $ack);

}
