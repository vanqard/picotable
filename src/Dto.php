<?php
namespace Vanqard\Picotable;

/**
 * Class defintion for the data transfer object - provides a hook into the persistence mechanism
 * allowing for an object to be connected to a row from a database table
 * 
 * @author Thunder Raven-Stoker
 * @copyright 2015-2016 Thunder Raven-Stoker <thunder@vanqard.com>
 * @license MIT
 */
class Dto
{
	/**
	 * @var \Vanqard\Picotable\Adapter\AbstractAdapter
	 */
	private $table;

	/**
	 * @var array
	 */
	private $columnMap = [];

	/**
	 * The connected object
	 * 
	 * @var mixed
	 */
	private $consumer;

	/**
	 * Boolean flag to indicate whether this object
	 * has been loaded from the database (not new) or still
	 * needs to be inserted
	 * 
	 * @var bool
	 */
	private $isNew = true;

	/**
	 * Constructor
	 * 
	 * @param string $table
	 */
	public function __construct($table)
	{
		$this->table = $table;
	}

	/**
	 * Retrieves the column map property and triggers the generation of column objects
	 * 
	 * @return Dto
	 */
	private function initColumns()
	{
		$map = new \ReflectionProperty($this->consumer, '_columnMap');
		$map->setAccessible(true);
		$map = $map->getValue($this->consumer);
		$this->table->setColumnMap($map, $this->consumer);

		return $this;
	}

	/**
	 * Attaches the consuming object to this DTO instance
	 * 
	 * @param mixed $consumer
	 * @return Dto
	 */
	public function setConsumer($consumer)
	{
		$this->consumer = $consumer;
		$this->initColumns();
		
		return $this;
	}

	/**
	 * Triggers the column collection to be loaded with values from the 
	 * connected database table with the row identified by the $primaryKey parameter
	 * 
	 * @param integer|mixed $primaryKey
	 * @return Dto
	 */
	public function loadFromDb($primaryKey)
	{
		$this->table->loadFromDb($primaryKey);
		$this->isNew = false;
		
		return $this;
	}

	/**
	 * Triggers the persistence of the connected object's properties to the 
	 * connected database table
	 * 
	 * @return Dto
	 */
	public function saveToDb()
	{
		if ($this->isNew) {
			$this->table->insertDb();
			$this->isNew = false;
		} else {
			$this->table->updateDb();
		}
		
		return $this;
	}

	/**
	 * Triggers the deletion of the database row that this DTO instance corresponds to
	 * 
	 * @return Dto
	 */
	public function deleteFromDb()
	{
		$this->table->deleteDb();
		
		return $this;
	}
}