<?php

namespace Aztech\Events\Bus\Channel;

class NullChannelProvider implements ChannelProvider
{

    public function createChannel(array $options = array())
    {
        return new ReadWriteChannel(new NullChannelReader(), new NullChannelWriter());
    }
}
