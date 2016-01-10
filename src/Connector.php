<?php
namespace Vanqard\Picotable;

use Vanqard\Picotable\Interfaces\StorableInterface;
use Vanqard\Picotable\Adapter\SQLite;
use Vanqard\Picotable\Adapter\MySQL;

/**
 * Class definition for the Connector object, a factory that will connect any
 * object to a database table provided that the object implements the StorableInterface
 * and consumes the Storable trait
 * 
 * @author Thunder Raven-Stoker
 * @copyright 2015-2016 Thunder Raven-Stoker <thunder@vanqard.com>
 * @license MIT
 */
class Connector
{
    /**
     * @var \PDO
     */
    private $pdo;
    
    /**
     * Constructor 
     * @param string $dsn
     * @param string $user
     * @param string $pass
     */
    public function __construct($dsn, $user = null, $pass = null)
    {
        $this->pdo = new \PDO($dsn, $user, $pass);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    /**
     * The connection method - installs a DTO instance in the target object and connects
     * the columns with the properties
     * 
     * @param StorableInterface $model
     * @param string $tableName
     */
    public function connect(StorableInterface $model, $tableName)
    {
        $adapter = $this->getAdapter($tableName);
        
        $dto = new Dto($adapter);
        $model->setDto($dto);
        return $model;
    }
    
    /**
     * Retrieves the table adapter based on the driver used to initialise the PDO object
     * 
     * @param string $tableName
     */
    public function getAdapter($tableName)
    {
        switch($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                $adapter = new MySQL($this->pdo, $tableName);
                break;
            case 'sqlite':
            default:
                $adapter = new SQLite($this->pdo, $tableName);
                break;
        }
        return $adapter;
    }
}
