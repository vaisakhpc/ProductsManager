<?php

namespace App\Model;

/**
 * Class Attribute
 * @package App\Model
 */
class Discount
{
    private $type;

    private $value;

    private $label;

    private $sku;

    public function __construct(
        string $type,
        string $value,
        string $label,
        string $sku
    ) {
        $this->type = $type;
        $this->value = $value;
        $this->label = $label;
        $this->sku = $sku;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
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
