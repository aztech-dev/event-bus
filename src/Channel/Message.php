<?php

namespace Aztech\Events\Bus\Channel;

/**
 * Class used to hold a reference to a received event and the associated data when using channels
 * in ack mode.
 *
 * @author thibaud
 *
 */
class Message
{
    /**
     * Identifier of the source message
     * @var mixed
     */
    private $correlationData;

    /**
     * Data contained in the message
     * @var mixed
     */
    private $data;

    /**
     * Create a new instance using a message identifier and the associated data.
     *
     * @param mixed $correlationData A valid message identifier in the channel provider's scope.
     * @param mixed $data Data contained in the message.
     */
    public function __construct($correlationData, $data)
    {
        $this->correlationData = $correlationData;
        $this->data = $data;
    }

    /**
     * Returns the data contained in the received message.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the message identifier.
     *
     * @abstract The returned data must be a valid identifier in the scope of the provider that
     * created the current instance, and is not guaranteed to have any meaning in the client scope.
     * This identifier  is used by the message provider when attempting to ack a received message.
     *
     * @return mixed
     */
    public function getCorrelationData()
    {
        return $this->correlationData;
    }
}
