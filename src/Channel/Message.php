<?php

namespace Aztech\Events\Bus\Channel;

class Message
{
    private $correlationData;

    private $data;

    public function __construct($correlationData, $data)
    {
        $this->correlationData = $correlationData;
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getCorrelationData()
    {
        return $this->correlationData;
    }
}
