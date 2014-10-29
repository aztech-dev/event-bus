<?php

namespace Aztech\Events\Tests\Bus\Channel;

use Aztech\Events\Bus\Channel\Message;
class MessageTest extends \PHPUnit_Framework_TestCase
{

    public function testPropertiesAreCorrectlyAssigned()
    {
        $message = new Message('correlation', 'data');

        $this->assertEquals('correlation', $message->getCorrelationData());
        $this->assertEquals('data', $message->getData());
    }
}
