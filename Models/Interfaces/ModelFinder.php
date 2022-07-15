<?php
namespace Models\Interfaces;

interface ModelFinder{

    /**
     * @param int $id
     * Get single row/object by its id
     * @return mixed
     */
    public function findOne(int $id);

    /**
     * Get all rows / object
     * @return array|null
     */
    public function findAll() : ?array;
}