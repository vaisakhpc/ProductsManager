<?php

namespace App\Model;

/**
 * Class Attribute
 * @package App\Model
 */
class Item
{
    /**
     * @var string
     */
    private $sku;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $price;

    /**
     * @var string
     */
    private $bundleFlag;

    private $discount;

    public function __construct(
        string $sku,
        string $name,
        string $description,
        string $price,
        string $bundleFlag = null,
        Discount $discount = null
    ) {
        $this->sku = $sku;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->bundleFlag = $bundleFlag;
        $this->discount = $discount;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getBundleFlag()
    {
        return $this->bundleFlag;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
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
