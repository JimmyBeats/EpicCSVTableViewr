<?php

namespace Jimmy\EpicCSVTableViewr\Entity;

use mysqli;
use RuntimeException;
use ReflectionClass;

/**
 * Basic repository class to encapsulate the database calls (using MySQLi). This is the only class that directly
 * references the MySQLi class, so the data store could easily be swapped out later by changing only this class.
 *
 * In real life I would have probably used something like Doctrine or Eloquent but I know you wanted to see my raw skillz
 *
 */

class Repository
{

    /**
     * @var mysqli Say no more!
     */
    private $mysqli;

    /**
     * Repository constructor - normal db init stuff
     *
     * @param $host
     * @param $username
     * @param $password
     * @param $database
     */
    public function __construct($host, $username, $password, $database)
    {
        $this->mysqli = new mysqli($host, $username, $password, $database);
        if ($this->mysqli->connect_error) {
            throw new RuntimeException('Could not connect to DB ' . $this->mysqli->connect_errno
                . ' ' . $this->mysqli->connect_error);
        }
    }

    /**
     * Returns the relevant `Table` class for the table name that is supplied. In this basic repository implementation,
     * the Repository class passes itself into the constructor of each table class. Thus any database calls the table
     * class needs to make will be made via the repository (thus no table class will be aware of what the actual underlying
     * datastore or access layer actually is). In real life I would have used dependency injection, or at least a factory,
     * rather than instantiating the tables from within this class.
     *
     * @param $table_name
     * @return mixed
     */
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
     * Generic function for returning all records from a table
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
        // would not do in real life. I'd explicitly select the fields using the field
        // list in each table class
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
     * This returns a count of all records in the table - is copy pasta from the method above as I was
     * running out of time. Given more time, I would have abstracted these more into smaller methods
     *
     * @param $table
     * @return mixed
     */
    public function countAllFrom($table)
    {
        // Make sure the table name does not have any dodgy chars in it
        // which might offer a way for SQL Injection
        $table = preg_replace('/[^A-Za-z0-9_]+/', '', $table);

        // Select all the records from the current table. Yes I am using "Select *" here which I
        // would not do in real life. I'd explicitly select the fields, probably by using a field
        // list in each table class, or perhaps using annotations like Doctrine does
        $query = "SELECT COUNT(*) FROM `$table`";

        if ($result = $this->mysqli->query($query)) {
            return $result->fetch_row()[0];
        }
    }

    /**
     * Generic function for returning single record from a table based on pk
     *
     * @param $table
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function findByPk($table, $pk_field, $id)
    {

        // Make sure the table name does not have any dodgy chars in it
        // which might offer a way for SQL Injection
        $table = preg_replace('/[^A-Za-z0-9_]+/', '', $table);

        // Select all the records from the current table. Yes I am using "Select *" here which I
        // would not do in real life. I'd explicitly select the fields, probably by using a field
        // list in each table class, or perhaps using annotations like Doctrine does
        $query = "SELECT * FROM `$table` where $pk_field = " . (int)$id;

        if ($result = $this->mysqli->query($query)) {
            return $result->fetch_assoc();
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

        // Prepare the query
        $stmt = $this->prepareFindByQuery(
            $select_fields,
            $from_table,
            $search_fields,
            $search_string,
            $limit,
            $offset,
            $wild_search
        );

        // Execute the query and return the results
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Returns the total number of records for a given query
     *
     * @param array $select_fields
     * @param $from_table
     * @param array $search_fields
     * @param $search_string
     * @param bool $wild_search
     * @return mixed
     */
    public function getFindByTotalCount(
        array $select_fields,
        $from_table,
        array $search_fields,
        $search_string,
        $wild_search = false)
    {
        // Prepare the query
        $stmt = $this->prepareFindByQuery(
            [],
            $from_table,
            $search_fields,
            $search_string,
            0,
            0,
            $wild_search
        );

        // Execute the query and return the count
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_row()[0];
    }

    /**
     * Builds the query for the findBy operation and returns a MySQLi prepared statement ready to be executed
     *
     * This method is a little bit messy - with more time I would have broken it down into a few smaller methods
     *
     * @param array $select_fields
     * @param $from_table
     * @param array $search_fields
     * @param $search_string
     * @param int $limit
     * @param int $offset
     * @param bool $wild_search
     * @return \mysqli_stmt
     */
    private function prepareFindByQuery(
        array $select_fields,
        $from_table,
        array $search_fields,
        $search_string,
        $limit=0,
        $offset=0,
        $wild_search = false)
    {

        // Build the query
        $query = "SELECT ";

        // If we have fields defined, this will be a select query, otherwise it is a count query
        if (count($select_fields)) {
            $fields = implode(',', $select_fields);
            $query .= "$fields ";
        } else {
            $query .= "COUNT(*) ";
        }

        // Build the rest of the query
        $query .= "FROM `$from_table` WHERE ";
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
        return $stmt;
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