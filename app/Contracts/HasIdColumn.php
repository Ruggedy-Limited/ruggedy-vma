<?php

namespace App\Contracts;

interface HasIdColumn
{
    /**
     * Get the value of the $id property which relates to the PRIMARY KEY `id` column in the database
     *
     * @return mixed
     */
    public function getId();

    /**
     * Set the value of the $id property which relates to the PRIMARY KEY `id` column in the database
     *
     * @param int $id
     * @return mixed
     */
    public function setId($id);
}