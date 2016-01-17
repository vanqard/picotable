<?php
namespace Vanqard\Picotable\Adapter;

use Vanqard\Picotable\Adapter\AbstractAdapter;

/**
 * Class definition for the MySQL specific table adapter
 * 
 * @author Thunder Raven-Stoker 
 * @copyright 2015-2016 Thunder Raven-Stoker <thunder@vanqard.com>
 * @license MIT
 */
class MySQL extends AbstractAdapter
{
    const SCHEMA_FIELD_NAME = 'Field';
    const SCHEMA_TYPE_NAME = 'Type';
    const SCHEMA_DESCRIBE_QUERY = 'DESCRIBE %s';
        
    /**
     * Determines whether field is a primary key column
     * 
     * @param array $fieldSpec
     * @return bool
     */
    protected function isFieldPrimaryKey(array $fieldSpec)
    {
        return ($fieldSpec['Key'] == 'PRI');
    }
}
