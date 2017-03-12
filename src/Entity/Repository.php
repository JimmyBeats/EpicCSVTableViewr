<?php

namespace Jimmy\EpicCSVTableViewr\Entity;

use mysqli;
use RuntimeException;
use ReflectionClass;

/**
 * Basic repository class to encapsulate the database calls (using MySQLi). This is the only class that directly
 * references the MySQLi class, so the data store could easily be swapped out later by editing only this class.
 */

class Repository
{

    private $mysqli;

    
    public function __construct($host, $username, $password, $database)
    {
        $this->mysqli = new mysqli($host, $username, $password, $database);
        if ($this->mysqli->connect_error) {
            throw new RuntimeException('Could not connect to DB ' . $this->mysqli->connect_errno
                . ' ' . $this->mysqli->connect_error);
        }
    }

    public function getTable($table_name)
    {
        $table_class_name = 'Jimmy\\EpicCSVTableViewr\\Entity\\' . $table_name . 'Table';
        if (class_exists($table_class_name)) {
            $class = new $table_class_name($this);
            if($class instanceof BaseTable) {
                return $class;
            }
        }
        throw new RuntimeException('Could not find table ' . $table_name);
    }

    /**
     * Generic function for returning all records from a table (without any query params)
     *
     * @param $table
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function findAllFrom($table, $limit=0, $offset=0)
    {

        // Make sure the table name does not have any dodgy chars in it
        // which might offer a way for SQL Injection
        $table = preg_replace('/[^A-Za-z0-9_]+/', '', $table);

        // Select all the records from the current table. Yes I am using "Select *" here which I
        // would not do in real life. I'd explicitly select the fields, probably by using a field
        // list in each table class, or perhaps using annotations like Doctrine does
        $query = "SELECT * FROM `$table`";

        // Add limit and offset if they have been specified
        if ($limit > 0) {
            $query .= " LIMIT " . (int)$limit;
        }

        if ($offset > 0) {
            $query .= " OFFSET " . (int)$offset;
        }

        if ($result = $this->mysqli->query($query)) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }

    /**
     * Generic function for finding records by field values
     *
     * @param array $select_fields
     * @param $from_table
     * @param array $search_fields
     * @param $search_string
     * @param int $limit
     * @param int $offset
     * @param bool $wild_search
     * @return mixed
     */
    public function findBy(
        array $select_fields,
        $from_table,
        array $search_fields,
        $search_string,
        $limit=0,
        $offset=0,
        $wild_search = false)
    {
        $fields = implode(',', $select_fields);

        // Build the query
        $query = "SELECT $fields FROM `$from_table` WHERE ";
        $idx = 0;
        $params = [];
        foreach ($search_fields as $search_field) {
            $query .= ($idx++ > 0 ? " OR " : "") . "$search_field LIKE ?";
            $params[] = "{$search_string}" . ($wild_search ? "%" : "");
        }

        // Add limit and offset if they have been specified
        if ($limit > 0) {
            $query .= " LIMIT " . (int)$limit;
        }

        if ($offset > 0) {
            $query .= " OFFSET " . (int)$offset;
        }

        // Using Reflection to handle the fact we have a variable amount of search_fields, thus number of params
        $stmt = $this->mysqli->prepare($query);
        $params = array_merge(array(str_repeat('s', count($params))), $this->refValues($params));
        $ref = new ReflectionClass('mysqli_stmt');
        $method = $ref->getMethod("bind_param");
        $method->invokeArgs($stmt, $params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Parameters in prepared statements need to be be references rather than values, this is a nasty
     * little workaround to give them what they want
     *
     * @param $arr
     * @return array
     */
    private function refValues($arr){
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }

}