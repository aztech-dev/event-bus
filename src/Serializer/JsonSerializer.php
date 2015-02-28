<?php

namespace Aztech\Events\Bus\Serializer;

use Aztech\Events\Bus\AbstractEvent;
use Aztech\Events\Bus\Serializer;
use Aztech\Events\Event;
use Instantiator\Instantiator;

class JsonSerializer implements Serializer
{

    private $instantiator;

    private $includeClassMetadata = true;

    public function __construct($includeClassMetadata = true)
    {
        $this->instantiator = new Instantiator();
        $this->includeClassMetadata = (bool) $includeClassMetadata;
    }

    public function serialize(Event $object)
    {
        $properties = $this->getProperties($object);
        $class = get_class($object);

        $dataObj = new \stdClass();

        if ($this->includeClassMetadata) {
            $dataObj->class = $class;
        }

        $dataObj->properties = $properties;
        $dataObj->category = $object->getCategory();

        // PHP 5.3 compatibility
        $unescapedSlashes = defined('JSON_UNESCAPED_SLASHES') ? JSON_UNESCAPED_SLASHES : 64;
        $unescapedUnicode = defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 256;

        return json_encode($dataObj, $unescapedSlashes | $unescapedUnicode);
    }

    public function deserialize($serializedObject)
    {
        $dataObj = json_decode($serializedObject, true);

        $class = $dataObj['class'];
        $properties = $dataObj['properties'];

        if (empty($class) || ! class_exists($class)) {
            return null;
        }

        $object = $this->instantiator->instantiate($class);

        $this->setProperties($object, $properties);
        $this->restoreState($object);

        return $object;
    }

    /**
     * @param object $object
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @return array
     */
    private function getProperties($object)
    {
        if ($object instanceof AbstractEvent) {
            $properties = $object->getProperties();
        }
        else {
            $properties = $this->getPropertiesViaReflection($object);
        }

        return $properties;
    }

    /**
     * @param object $object
     * @return array
     */
    private function getPropertiesViaReflection($object)
    {
        $reflectionObject = new \ReflectionClass(get_class($object));
        $reflectionProperties = $this->getReflectionProperties($reflectionObject);
        $properties = array();

        foreach ($reflectionProperties as $reflectionProperty) {
            $properties[$reflectionProperty->getName()] = $this->getPropertyValue($object, $reflectionProperty);
        }

        return $properties;
    }

    private function getPropertyValue($object, $reflectionProperty)
    {
        $this->ensurePropertyIsAccessible($reflectionProperty);
        $value = $reflectionProperty->getValue($object);
        $this->restorePropertyAccessibility($reflectionProperty);

        if (is_object($value)) {
            $value = $this->getPropertiesViaReflection($value);
        }

        return $value;
    }

    /**
     *
     * @param $reflectionObject
     * @return \ReflectionProperty[]
     */
    private function getReflectionProperties(\ReflectionClass $reflectionObject)
    {
        $properties = $reflectionObject->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);

        if ($reflectionObject->getParentClass()) {
            $properties = array_merge($this->getReflectionProperties($reflectionObject->getParentClass()), $properties);
        }

        return $properties;
    }

    private function ensurePropertyIsAccessible(\ReflectionProperty $property)
    {
        if ($property->isPrivate() || $property->isProtected()) {
            $property->setAccessible(true);
        }
    }

    private function restorePropertyAccessibility(\ReflectionProperty $property)
    {
        if ($property->isPrivate() || $property->isProtected()) {
            $property->setAccessible(false);
        }
    }

    /**
     *
     * @param Event|AbstractEvent $object
     * @param array $properties
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function setProperties($object, $properties)
    {
        if ($object instanceof AbstractEvent) {
            $object->setProperties($properties);
        }
        else {
            $this->setPropertiesViaReflection($object, $properties);
        }
    }

    private function setPropertiesViaReflection($object, $properties)
    {
        $reflectionObject = new \ReflectionClass(get_class($object));
        $reflectionProperties = $this->getPropertiesViaReflection($reflectionObject);

        foreach ($reflectionProperties as $reflectionProperty) {
            if (! array_key_exists($reflectionProperty->getName(), $properties)) {
                // Skip properties that are not present in the serialization array
                continue;
            }

            $this->ensurePropertyIsAccessible($reflectionProperty);
            $reflectionProperty->setValue($object, $properties[$reflectionProperty->getName()]);
            $this->restorePropertyAccessibility($reflectionProperty);
        }
    }

    private function restoreState($object)
    {
        if (method_exists($object, '__wakeup')) {
            $object->__wakeup();
        }
    }
}
