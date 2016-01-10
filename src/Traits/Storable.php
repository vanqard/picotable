<?php
namespace Vanqard\Picotable\Traits;

use Vanqard\Picotable\Dto;

/**
 * Trait definition for the StorableInterface
 * 
 * Adding this trait to any object will provide a means to persist that object's data
 * to a database table
 * 
 * @author Thunder Raven-Stoker
 * @copyright 2015-2016 Thunder Raven-Stoker <thunder@vanqard.com>
 * @license MIT
 */
trait Storable
{
	/**
	 * @var \Vanqard\Picotable\Dto
	 */
	private $dto;

	/**
	 * Setter for the $dto property
	 * 
	 * @param Dto $dto
	 * @return $this fluent interface
	 */
	public function setDto(Dto $dto)
	{
		$dto->setConsumer($this);
		$this->dto = $dto;
		return $this;
	}

	/**
	 * Loads the object properties from the database based on the provided
	 * primary key value. An integer value is expected but not enforced
	 * 
	 * @param integer|mixed $pk
	 */
	public function _load($pk)
	{
		$this->dto->loadFromDb($pk);
	}

	/**
	 * Persists the object properties to the connected database table
	 */
	public function _save()
	{
		$this->dto->saveToDb();
	}
	
	/**
	 * Removes the database table row that this object is connected to.
	 */
	public function _delete()
	{
		$this->dto->deleteFromDb();
	}
}