<?php

namespace Aztech\Events\Bus;

use Aztech\Events\Dispatcher;

/**
 *
 * @author thibaud
 */
interface Processor
{

    /**
     * Processes the next available event and submits it to the given event dispatcher
     * @param Dispatcher $dispatcher
     * @return void
     */
    function processNext(Dispatcher $dispatcher);

    /**
     * Performs any cleanup work
     */
    function dispose();
}
