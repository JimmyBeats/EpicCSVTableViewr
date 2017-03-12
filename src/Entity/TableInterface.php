<?php

namespace Jimmy\EpicCSVTableViewr\Entity;

/**
 * An interface for classes representing tables in the database
 */
interface TableInterface
{

    /**
     * @return string The exact name of the database table that the implementing class will talk to
     */
    public function getTableName();

    /**
     * @return array An array of the field names in the database table we will be returning
     */
    public function getFields();

    /**
     * @return mixed The field name which is the primary key for this table
     */
    public function getPrimaryKeyFieldName();

}