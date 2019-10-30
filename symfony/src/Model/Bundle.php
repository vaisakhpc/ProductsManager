<?php

namespace App\Model;

/**
 * Class Attribute
 * @package App\Model
 */
class Bundle
{
    private $sku;

    private $originalPrice;

    public function __construct(
        string $sku,
        string $originalPrice
    ) {
        $this->sku = $sku;
        $this->originalPrice = $originalPrice;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return mixed
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
