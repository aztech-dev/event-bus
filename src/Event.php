<?php

namespace Aztech\Events\Bus;

class Event extends AbstractEvent
{

    protected $category = '';

    protected $properties = array();

    /**
     * @param string $category
     */
    public function __construct($category, array $properties = array())
    {
        parent::__construct();

        $this->category = $category;
        $this->properties = $properties;
    }

    public function getCategory()
    {
        return $this->category;
    }

    /**
     *
     * @param unknown $method
     * @param unknown $args
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __call($method, $args) {
        if (substr($method, 0, 3) == 'get') {
            return $this->__get(lcfirst(substr($method, 3)));
        }

        return null;
    }

    /**
     * @param string $name
     */
    public function __get($name)
    {
        if (! array_key_exists($name, $this->properties)) {
            return null;
        }

        return $this->properties[$name];
    }

    public function __set($name, $value)
    {
        $this->properties[$name] = $value;
    }
}
