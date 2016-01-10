<?php
namespace Vanqard\Picotable;

use Vanqard\Picotable\Interfaces\StorableInterface;
/**
 * Decorator for the spl class ReflectionProperty (but provides a very limited interface)
 * 
 * Retains a reference to the connected object for future use
 * 
 * @author Thunder Raven-Stoker
 * @copyright 2015-2016 Thunder Raven-Stoker <thunder@vanqard.com>
 * @license MIT
 */
class ConnectedProperty
{
    /**
     * The source model object
     * 
     * @var mixed 
     */
    private $consumer;
    
    /**
     * @var \ReflectionProperty
     */
    private $property;

    /**
     * Constructor 
     * @param StorableInterface $consumer
     * @param string $propertyName
     */
    public function __construct(StorableInterface $consumer, $propertyName)
    {
        $property = new \ReflectionProperty($consumer, $propertyName);
        $property->setAccessible(true);
        $this->property = $property;
        $this->consumer = $consumer;
    }

    /**
     * Makes the property accessible to this instance
     * @param bool $flag
     */
    public function setAccessible($flag = true)
    {
        $this->property->setAccessible($flag);
    }

    /**
     * Sets the provided value on the property of the connected object instance
     * 
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->property->setValue($this->consumer, $value);
    }

    /**
     * Retrieves the value of the property from the connected object instance
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->property->getValue($this->consumer);
    }
}
