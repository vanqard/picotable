<?php
namespace Vanqard\Picotable\Adapter;

use Vanqard\Picotable\Adapter\AbstractAdapter;

/**
 * Class definition for the SQLite table adapter
 * 
 * @author Thunder Raven-Stoker
 * @copyright 2015-2016 Thunder Raven-Stoker <thunder@vanqard.com>
 * @license MIT
 */
class SQLite extends AbstractAdapter
{
	const SCHEMA_FIELD_NAME = 'name';
	const SCHEMA_TYPE_NAME = 'type';
	const SCHEMA_DESCRIBE_QUERY = 'PRAGMA table_info(%s)';
	
	/**
	 * Determines whether field is a primary key column
	 * 
	 * @param array $fieldSpec
	 * @return bool
	 */
	protected function isFieldPrimaryKey($fieldSpec)
	{
		return (bool) $fieldSpec['pk'];
	}
}
