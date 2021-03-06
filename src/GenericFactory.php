<?php

namespace Aztech\Events\Bus;

use Aztech\Events\Bus\Channel\ChannelProcessor;
use Aztech\Events\Bus\Channel\ChannelProvider;
use Aztech\Events\Bus\Channel\ChannelPublisher;
use Aztech\Events\Bus\Factory\OptionsDescriptor;
use Aztech\Events\Bus\Factory\OptionsValidator;
use Aztech\Events\Bus\Factory\NullOptionsDescriptor;
use Aztech\Events\Bus\Channel\AcknowledgeableChannelReader;
use Aztech\Events\Bus\Channel\TransactionalChannelProcessor;

class GenericFactory implements Factory
{

    protected $optionsDescriptor = null;

    protected $optionsValidator = null;

    protected $serializer;

    protected $channelProvider;

    public function __construct(Serializer $serializer, ChannelProvider $channelProvider, OptionsDescriptor $descriptor = null)
    {
        $this->serializer = $serializer;
        $this->channelProvider = $channelProvider;
        $this->optionsDescriptor = $descriptor ?: new NullOptionsDescriptor();
        $this->optionsValidator = new OptionsValidator();
    }

    public function createProcessor(array $options = array())
    {
        $options = $this->validateOptions($options);

        $channel = $this->channelProvider->createChannel($options);
        $reader = $channel->getReader();

        if ($reader instanceof AcknowledgeableChannelReader) {
            return new TransactionalChannelProcessor($reader, $this->serializer);
        }

        return new ChannelProcessor($reader, $this->serializer);
    }

    public function createPublisher(array $options = array())
    {
        $options = $this->validateOptions($options);

        $channel = $this->channelProvider->createChannel($options);
        $writer = $channel->getWriter();

        return new ChannelPublisher($writer, $this->serializer);
    }

    protected function validateOptions(array $options)
    {
        return $this->optionsValidator->validate($this->optionsDescriptor, $options);
    }
}
