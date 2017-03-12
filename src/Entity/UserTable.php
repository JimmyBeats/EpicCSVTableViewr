<?php

namespace Jimmy\EpicCSVTableViewr\Entity;

/**
 *  Object to represent an abstraction of the User Table for the purposes of fetching records
 *  from the database.
 *
 *  Could be used to implement custom calls to the repository where required.
 *
 *  Also allows us to explicitly control which fields are made available to the application (so we could exclude
 *  sensitive fields such as password fields from being made available through the DBAL)
 */
class UserTable extends BaseTable implements TableInterface
{

    public function getTableName()
    {
        return "user";
    }

    public function getPrimaryKeyFieldName()
    {
        return "user_id";
    }

    public function getFields()
    {
        return [
            'user_id',
            'last_name',
            'first_name',
            'email',
            'role',
            'department',
            'dob',
            'street_address_1',
            'street_address_2',
            'suburb',
            'state',
            'postcode',
            'country'
        ];
    }

    /**
     * Searches for users by the supplied name - adds a wildcard so that just the start of a name can be moved in.
     * Searches on both the last name and first name fields
     *
     * @param $search_string string The name we are going to search for
     * @return null|array of users
     */
    public function findByWildNameSearch($search_string, $limit=0, $offset=0)
    {
        return $this->findBy(['first_name', 'last_name'], $search_string, $limit, $offset, true);
    }

    public function countByWildNameSearch($search_string)
    {
        return $this->findByCount(['first_name', 'last_name'], $search_string, true);
    }

}