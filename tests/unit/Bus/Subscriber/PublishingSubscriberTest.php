<?php

namespace Aztech\Events\Tests\Bus\Subscriber;

use Aztech\Events\Bus\Subscriber\PublishingSubscriber;
use Aztech\Events\Bus\Event;

class PublishingSubscriberTest extends \PHPUnit_Framework_TestCase
{

    private $publisher;

    protected function setUp()
    {
        $this->publisher = $this->getMock('\Aztech\Events\Bus\Publisher');
    }

    public function getConstraintTruthTable()
    {
        return \Aztech\Events\Tests\Category\MatchTruthTable::get();
    }

    /**
     * @dataProvider getConstraintTruthTable
     */
    public function testSupportsRespectsConstraintTruthTable($category, $filter, $expected)
    {
        $subscriber = new PublishingSubscriber($this->publisher, $filter);
        $event = new Event($category);

        $this->assertEquals($expected, $subscriber->supports($event));
    }

    public function testHandleForwardsEventToPublisher()
    {
        $event = new Event('category');

        $this->publisher->expects($this->once())
            ->method('publish')
            ->with($this->equalTo($event));

        $subscriber = new PublishingSubscriber($this->publisher, '#');
        $subscriber->handle($event);
    }
}
