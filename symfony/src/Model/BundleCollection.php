<?php

namespace App\Model;

class BundleCollection
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
    public function add(Bundle $bundle) : array
    {
        $this->data[] = $bundle;
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
