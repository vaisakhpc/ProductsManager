<?php

namespace App\Model;

class ProductCollection
{
    private $data = [];

    /**
     * retrieve the count of $data
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->data);
    }

    /**
     * add item to $data
     *
     * @return array
     */
    public function add(Product $job) : array
    {
        $this->data[] = $job;
        return $this->data;
    }

    /**
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->data;
    }
}
