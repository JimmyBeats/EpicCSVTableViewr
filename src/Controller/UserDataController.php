<?php

namespace Jimmy\EpicCSVTableViewr\Controller;

/**
 *  Controller for accessing and returning user data
 *
 *  Didn't have time to add docblocks above the methods, sorry about that! Hopefully all pretty straightfoward though.
 *
 *  Some of the stuff around the paging would have been better to be abstracted out of here for better re-use but again
 *  time was an issue.
 *
 */
class UserDataController
{

    private $app;
    private $request;

    public function __construct(array $app, array $get)
    {
        $this->app = $app;
        $this->request = $get;
    }

    public function getJsonResponse()
    {

        $records = $this->getRecords();

        return json_encode([
            'records' => $records,
            'queryRecordCount' => $this->getTotalRecordCount(),
            'totalRecordCount' => null,
        ]);

    }

    public function getUserDetail()
    {
        if (isset($this->request['user_id'])) {

            // Get the table
            $user_table = $this->app['repository']->getTable('User');

            // Find the user from the repo
            return $user_table->find((int)$this->request['user_id']);

        }
    }



    public function getSearchQuery()
    {
        if (isset($this->request['queries']['search'])) {
            return $this->request['queries']['search'];
        }
    }

    public function getPageNumber()
    {
        return isset($this->request['page']) ? (int)$this->request['page'] : 1;
    }

    public function getPerPage()
    {
        return isset($this->request['perPage']) ? (int)$this->request['perPage'] : 10;
    }

    public function getOffset()
    {
        return ($this->getPerPage() * $this->getPageNumber()) - $this->getPerPage();
    }

    public function getRecords()
    {
        // Get the table
        $user_table = $this->app['repository']->getTable('User');

        // Get the users from the table
        if ($search = $this->getSearchQuery()) {
            $users = $user_table->findByWildNameSearch($this->getSearchQuery(), $this->getPerPage(), $this->getOffset());
        } else {
            $users = $user_table->findAll($this->getPerPage(), $this->getOffset());
        }
        return $users;
    }

    public function getTotalRecordCount()
    {
        // Get the table
        $user_table = $this->app['repository']->getTable('User');

        // Get the users from the table
        if ($search = $this->getSearchQuery()) {
            $count = $user_table->countByWildNameSearch($this->getSearchQuery());
        } else {
            $count = $user_table->countAll();
        }
        return (int)$count;
    }


}