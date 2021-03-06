<?php

namespace Aztech\Events\Bus\Factory;

class GenericOptionsDescriptor implements OptionsDescriptor
{

    private $optionKeys = array();

    private $optionDefaults = array();

    /**
     *
     * @param string $name
     * @param bool $required
     * @param string $default
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addOption($name, $required = true, $default = null)
    {
        $this->optionKeys[] = $name;

        if (! $required) {
            $this->optionDefaults[$name] = $default;
        }
    }

    public function getOptionDefaults()
    {
        return $this->optionDefaults;
    }

    function getOptionKeys()
    {
        return $this->optionKeys;
    }
}
