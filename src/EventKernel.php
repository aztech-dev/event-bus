<?php

namespace Aztech\Events\Bus;

use Aztech\Events\Callback;
use Aztech\Events\Dispatcher;
use Aztech\Events\Subscriber;

class EventKernel implements Processor
{

    private $processor;

    private $dispatcher;

    private $run;

    public function __construct(Processor $processor, Dispatcher $dispatcher)
    {
        $this->processor = $processor;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Binds a subscriber to events matching the specified filter.
     *
     * @param string $filter
     * @param Subscriber|callable $subscriber
     * @throws \InvalidArgumentException
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function on($filter, $subscriber)
    {
        if (is_callable($subscriber) && ! ($subscriber instanceof Subscriber)) {
            $subscriber = new Callback($subscriber);
        }
        elseif (! ($subscriber instanceof Subscriber)) {
            throw new \InvalidArgumentException('Subscriber must be a callable or a Subscriber instance.');
        }

        $this->dispatcher->addListener($filter, $subscriber);
    }

    /**
     * (non-PHPdoc)
     * @see \Aztech\Events\Bus\Processor::dispose()
     */
    public function dispose()
    {
        $this->processor->dispose();
    }

    /**
     * Runs the processing loop until stop() is called on the kernel.
     * @desc This method is blocking for its caller, in order to call stop, either run() must be dispatched in a child thread
     * (using pthread for example), or called from one of the bound subscribers (ie, when receiving a specific event).
     *
     */
    public function run()
    {
        /** @codeCoverageIgnoreStart */
        $this->run = true;

        while ($this->run) {
            $this->processNext($this->dispatcher);
        }
        /** @codeCoverageIgnoreEnd */
    }

    public function stop()
    {
        $this->run = false;
    }

    public function processNext(Dispatcher $dispatcher)
    {
        $this->processor->processNext($dispatcher);
    }
}
