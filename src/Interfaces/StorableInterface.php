<?php
namespace Vanqard\Picotable\Interfaces;

/**
 * Storable interface definition
 * 
 * Implement this in any class that adds the Storable trait
 * @author Thunder Raven-Stoker
 * @copyright 2015-2016 Thunder Raven-Stoker <thunder@vanqard.com>
 * @license MIT
 */
interface StorableInterface
{
    /**
     * Provides a mechanism to load object properties from a database table
     * 
     * @param integer|mixed $pk - expects an auto_increment style integer
     */
    public function _load($pk);
    
    /**
     * Commit the object properties to a database row
     */
    public function _save();
    
    /**
     * Delete the row that this object corresponds to
     */
    public function _delete();
}
