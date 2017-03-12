<?php

namespace Jimmy\EpicCSVTableViewr\Entity;

/**
 *  Base object for representing abstractions of database tables for the purposes of fetching records from the database.
 * 
 */
abstract class BaseTable
{

    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function findAll($limit = 0, $offset = 0)
    {
        return $this->repository->findAllFrom($this->getTableName(), $limit, $offset);
    }

    public function countAll()
    {
        return $this->repository->countAllFrom($this->getTableName());
    }

    public function findBy($where_field, $search_string, $limit=0, $offset=0, $wild_search = false)
    {
        return $this->repository->findBy(
            $this->getFields(),
            $this->getTableName(),
            $where_field,
            $search_string,
            $limit,
            $offset,
            $wild_search
        );
    }

    public function findByCount($where_field, $search_string, $wild_search = false)
    {
        return $this->repository->getFindByTotalCount(
            $this->getFields(),
            $this->getTableName(),
            $where_field,
            $search_string,
            $wild_search
        );
    }

    public function find($id)
    {
        return $this->repository->findByPk(
            $this->getTableName(),
            $this->getPrimaryKeyFieldName(),
            $id
        );
    }
    
}