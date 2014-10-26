<?php

namespace Aztech\Events\Bus\Channel;

use Aztech\Events\Bus\Processor;
use Aztech\Events\Dispatcher;
use Aztech\Events\Bus\Serializer;

class TransactionChannelProcessor implements Processor
{
    private $reader;

    private $serializer;

    public function __construct(AcknowledgeableChannelReader $channelReader, Serializer $serializer)
    {
        $this->reader = $channelReader;
        $this->serializer = $serializer;
    }

    public function processNext(Dispatcher $dispatcher)
    {
        $next = $this->reader->readAck();

        if ($next == null) {
            return;
        }

        $event = $this->serializer->deserialize($next->getData());

        if ($event == null) {
            return;
        }

        $dispatcher->dispatch($event);

        $this->reader->acknowledge($message);
    }

    public function dispose()
    {
        $this->reader->dispose();
    }
}
