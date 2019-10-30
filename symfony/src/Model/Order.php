<?php

namespace App\Model;

use DateTime;

/**
 * Class Attribute
 * @package App\Model
 */
class Order
{
    private $id;

    private $customer;

    private $createdAt;

    private $status;

    private $totalPrice;

    private $itemCollection;

    public function __construct(
        string $id,
        string $customer,
        string $createdAt,
        string $status,
        float $totalPrice,
        ItemCollection $itemCollection
    ) {
        $this->id = $id;
        $this->customer = $customer;
        $this->createdAt = $createdAt;
        $this->status = $status;
        $this->totalPrice = $totalPrice;
        $this->itemCollection = $itemCollection;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @return mixed
     */
    public function getItemCollection()
    {
        return $this->itemCollection;
    }

    /**
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return get_object_vars($this);
    }
}
