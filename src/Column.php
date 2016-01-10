<?php
namespace Vanqard\Picotable;

/**
 * Class definition for a Picotable Column object
 * 
 * @author Thunder Raven-Stoker 
 * @copyright 2015-2016 Thunder Raven-Stoker <thunder@vanqard.com>
 * @license MIT
 *
 */
class Column
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var string
     */
    private $type;
    
    /**
     * @var bool
     */
    private $isPk;
    
    /**
     * @var mixed
     */
    private $value;
    
    /**
     * @var bool
     */
    private $isConnected = false;
    
    /**
     * @var \Vanqard\Picotable\ConnectedProperty
     */
    private $connectedProperty;
    
    /**
     * @var bool
     */
    private $isChanged = false;

    /**
     * Constructor
     * 
     * @param array $spec
     */    
    public function __construct($spec)
    {
        $this->name = $spec['name'];
        $this->type = $spec['type'];
        $this->isPk = boolval($spec['pk']);
    }

    /**
     * Getter for the name property of this column
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter for the value property of this column
     * 
     * @param mixed $value
     * @return Column
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Getter for the value property of this column
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Reports on whether the object property that this column is connected to
     * has been changed
     * 
     * @return bool
     */
    public function isChanged()
    {
        if (!$this->isConnected) {
            false;
        }

        $this->isChanged = ($this->value != $this->getPropertyValue());
        
        return $this->isChanged;
    }

    /**
     * Reports on whether this column is a primary key element
     * 
     * @return bool
     */
    public function isPrimaryKey()
    {
        return (bool) $this->isPk;
    }

    /**
     * Attaches an object property to this column
     * 
     * @param ConnectedProperty $property
     * @return Column
     */
    public function setConnectedProperty(ConnectedProperty $property)
    {
        $this->connectedProperty = $property;
        $this->isConnected = true;
        return $this;
    }
    
    /**
     * Disconnects this column from the object property
     * 
     * @return Column
     */
    public function disconnectProperty()
    {
        $this->connectedProperty = null;
        $this->isConnected = false;
        return $this;
    }

    /**
     * Sets the object property value with the value of this column instace
     * 
     * @return Column
     */
    public function loadProperty()
    {
        if ($this->isConnected) {
            $this->connectedProperty->setValue($this->value);
        }
        
        return $this;
    }
    
    /**
     * Retrieves the value of the object property connected to this column
     * 
     * @return mixed
     */
    public function getPropertyValue()
    {
        if ($this->isConnected) {
            return $this->connectedProperty->getValue();
        }
    }

    /**
     * Updates the value of this column instance with the value of the connected
     * object property.
     * 
     * @return Column
     */
    public function updateFromProperty()
    {
        if ($this->isChanged()) {
            $this->value = $this->getPropertyValue();
        }
        
        return $this;
    }
}