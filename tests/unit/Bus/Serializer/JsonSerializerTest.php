<?php

namespace Aztech\Events\Tests\Bus\Serializer;

use Aztech\Events\Bus\Serializer\JsonSerializer;
use Aztech\Events\Bus\Event;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{

    public function testDeserializationReturnsNullWithUnknownEventClasses()
    {
        $object = new Event('category');
        $serializer = new JsonSerializer();

        $serializedObject = $serializer->serialize($object);
        $unserializedObject = $serializer->deserialize($serializedObject);

        $this->assertEquals($object, $unserializedObject);

        $object = new Event('category', array('property' => 'value'));
        $serializer = new JsonSerializer();

        $serializedObject = $serializer->serialize($object);
        $serializedObject = json_decode($serializedObject);
        $serializedObject->class = '\Aztech\Events\Tests\Bus\Serializer\SomeClassHopefullyNoOneWillEverDeclare';
        $serializedObject = json_encode($serializedObject);

        $this->assertNull($serializer->deserialize($serializedObject));
    }

    public function testSerializationPassReturnsIdenticalObject()
    {
        $object = new Event('category');
        $serializer = new JsonSerializer();

        $serializedObject = $serializer->serialize($object);
        $unserializedObject = $serializer->deserialize($serializedObject);

        $this->assertEquals($object, $unserializedObject);

        $object = new Event('category', array('property' => 'value'));
        $serializer = new JsonSerializer();

        $serializedObject = $serializer->serialize($object);
        $unserializedObject = $serializer->deserialize($serializedObject);

        $this->assertEquals($object, $unserializedObject);
    }

    public function testSerializationPassReturnsIdenticalObjectWithNonAbstractEvents()
    {
        $object = $this->getMock('\Aztech\Events\Event');
        $serializer = new JsonSerializer();

        $serializedObject = $serializer->serialize($object);
        $unserializedObject = $serializer->deserialize($serializedObject);

        $this->assertEquals($object, $unserializedObject);
    }

    public function testSerializationPassReturnsObjectWithCorrectPropertiesForSleepableEvents()
    {
        $object = new JsonSleepable();
        $serializer = new JsonSerializer();

        $object->notRestorable = 'bla';
        $object->other = true;
        $object->restorable = 'restore-me';

        $serializedObject = $serializer->serialize($object);
        $unserializedObject = $serializer->deserialize($serializedObject);

        $this->assertEquals($object->other, $unserializedObject->other);
        $this->assertEquals($object->restorable, $unserializedObject->restorable);
        // Ensure that object is still usable after serialization
        $this->assertTrue($object->wakeupInvoked);
        $this->assertTrue($unserializedObject->wakeupInvoked);
        $this->assertEquals(null, $unserializedObject->notRestorable);
    }
}

class JsonSleepable implements \Aztech\Events\Event
{

    public $notRestorable = null;

    public $restorable = '';

    public $other = '';

    public $wakeupInvoked = false;

    public function getId()
    {
        return 1;
    }

    public function getCategory()
    {
        return 'category';
    }

    public function __sleep()
    {
        return array('restorable', 'other');
    }

    public function __wakeup()
    {
        $this->wakeupInvoked = true;
    }

}
