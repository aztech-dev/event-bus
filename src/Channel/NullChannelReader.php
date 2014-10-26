<?php

namespace Aztech\Events\Bus\Channel;

class NullChannelReader implements ChannelReader
{

    public function read()
    {
        return null;
    }

    public function dispose()
    {

    }
}
