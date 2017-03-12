<?php

namespace Jimmy\EpicCSVTableViewr\Controller;

/**
 *  Controller for accessing and returning user data
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

    public function getJsonResponse()
    {


        echo json_encode([
            'records' => $this->getRecords(),
            'queryRecordCount' => 10,
            'totalRecordCount' => 10,
        ]);
        exit;
    }

}