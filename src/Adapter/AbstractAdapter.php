<?php
namespace Vanqard\Picotable\Adapter;

use Vanqard\Picotable\ConnectedProperty;
use Vanqard\Picotable\Column;

/**
 * Base adapter class for connecting storage to the DTO that this package provides
 * 
 * @author Thunder Raven-Stoker
 * @copyright 2015-2016 Thunder Raven-Stoker <thunder@vanqard.com>
 * @license MIT
 */
abstract class AbstractAdapter
{

    /**
     * @var string 
     */
    protected $tableName;
    
    /**
     * @var \Vanqard\Picotable\Dto
     */
    protected $dto;
    
    /**
     * @var \PDO
     */
    protected $pdo;
    
    /**
     * The collection of column objects
     * 
     * @var array
     */
    protected $columns = [];
    
    /**
     * The primary key column
     * 
     * @var \Vanqard\Picotable\Column
     */
    protected $pk;

    /**
     * Constructor 
     * @param \PDO $pdo
     * @param string $tableName
     */
    public function __construct(\PDO $pdo, $tableName)
    {
        $this->tableName = $tableName;
        $this->pdo = $pdo;
    }

    /**
     * Selects a database row from the attached table and loads the object 
     * 
     * @param integer|mixed $primaryKey expected row id
     * @throws \RuntimeException
     */
    public function loadFromDb($primaryKey) {

        $sql = "SELECT * FROM {$this->tableName} 
                WHERE " . $this->pk->getName() . " = :primarykey";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':primarykey', $primaryKey);
        $stmt->execute();

        $row = $stmt->fetch();

        if (empty($row)) {
            throw new \RuntimeException("record not found");
        }

        foreach($row as $columnName => $columnValue) {
            if ( array_key_exists($columnName, $this->columns)) {
                $col = $this->columns[$columnName];

                $col->setValue($columnValue)->loadProperty();
            }
        }
    }

    /**
     * Removes the table row corresponding to this object instance
     * 
     * @return bool
     */
    public function deleteDb()
    {
        $sql = "DELETE FROM {$this->tableName} WHERE {$this->pk->getName()} = :primarykey";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([":primarykey" => $this->pk->getValue()]);
    }

    /**
     * Adds a new records to the table
     * 
     * @return null
     */
    public function insertDb()
    {
        $fields = [];
        foreach($this->columns as $column) {
            $column->updateFromProperty();

            if ($column->isPrimaryKey()) {
                $fields[$column->getName()] = "NULL";
            } else {
                $fields[$column->getName()] = ":{$column->getName()}";
                $values[":{$column->getName()}"] = $column->getValue();
            }
        }

        if (!empty($fields)) {
            $sql = $this->buildInsertQuery($fields);
            $stmt = $this->pdo->prepare($sql);
            if($stmt->execute($values)) {
                $rowId = $this->pdo->lastInsertId();
                $this->pk->setValue($rowId)->loadProperty();
            }
        }
    }

    /**
     * Constructs the insert query - may be overridden in a specific adapter
     * 
     * @param array $fields
     * @return string $sql
     */
    protected function buildInsertQuery($fields)
    {
        $sql = "INSERT INTO {$this->tableName} (";
        $sql .= implode(',',array_keys($fields));
        $sql .= ") VALUES (";
        $sql .= implode(",", array_values($fields)) . ")";
        return $sql;
    }

    /**
     * Updates the underlying table row with only the changed data from the connected object
     * 
     */
    public function updateDb()
    {
        $fields = [];
        foreach ($this->columns as $column) {
            
            if ($column->isChanged() && !$column->isPrimaryKey()) {
                $column->updateFromProperty();
                $fields[$column->getName()] = $column->getName() . " = :{$column->getName()}";
            }
        }
        
        if (!empty($fields)) {
            $sql = $this->buildUpdateQuery($fields);
            $stmt = $this->pdo->prepare($sql);

            $params = [];
            foreach (array_keys($fields) as $param) {
                $params[":{$param}"] = $this->columns[$param]->getValue(); 
            }

            $params[":primarykey"] = $this->pk->getValue();
            
            return $stmt->execute($params);
        }
    }

    /**
     * Constructs the update query - may be overridden in a specific adapter
     * 
     * @param array $fields
     * @return string
     */
    protected function buildUpdateQuery($fields)
    {
        if (empty($fields)) return;

        $sql = "UPDATE {$this->tableName} SET ";
        $sql .= implode(',', $fields);
            
        $sql .= " WHERE {$this->pk->getName()} = :primarykey";

        return $sql;
    }

    /**
     * Initialises the column collection based on the incoming map array
     * 
     * @param array $map
     * @param mixed $consumer - the source model object
     */
    public function setColumnMap($map, $consumer)
    {
        $sql = sprintf(static::SCHEMA_DESCRIBE_QUERY, $this->tableName);
        $stmt = $this->pdo->query($sql);

        $dbMap = [];
        foreach($map as $property => $column) {
            if (!is_string($property)) {
                $property = $column;
            }

            $dbMap[$column] = $property;
        }

        while($row = $stmt->fetch()) {
            $column = new Column([
                'name' => $row[static::SCHEMA_FIELD_NAME],
                'type' => $row[static::SCHEMA_TYPE_NAME],
                'pk' => $this->isFieldPrimaryKey($row)
            ]);

            // Only columns in the map get connected to object properties
            if (in_array($row[static::SCHEMA_FIELD_NAME], array_keys($dbMap))) {
                $connectedProperty = new ConnectedProperty($consumer, $dbMap[$row[static::SCHEMA_FIELD_NAME]]);
                $connectedProperty->setAccessible(true);
                $column->setConnectedProperty($connectedProperty);
            }

            $this->columns[$column->getName()] = $column;

            if($column->isPrimaryKey()) {
                $this->pk = $column;
            }
        }
    }
    
    /**
     * Vendor specific table adapters to provide their own isPk test
     * 
     * @param array $fieldSpec
     * @return bool
     */
    abstract protected function isFieldPrimaryKey(array $fieldSpec);
}
