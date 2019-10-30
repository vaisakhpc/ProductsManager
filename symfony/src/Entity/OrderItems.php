<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItems
 *
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemsRepository")
 * @ORM\Table(name="order_items")
 * @ORM\Entity
 */
class OrderItems
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    private $productId;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=6, scale=2, nullable=false)
     */
    private $price;

    /**
     * @var int|null
     *
     * @ORM\Column(name="discount_applied", type="integer", nullable=true)
     */
    private $discountApplied;

    /**
     * @var int|null
     *
     * @ORM\Column(name="order_id", type="integer", nullable=false)
     */
    private $orderId;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return int|null
     */
    public function getDiscountApplied()
    {
        return $this->discountApplied;
    }

    /**
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function setDiscountApplied(?int $discountApplied): self
    {
        $this->discountApplied = $discountApplied;

        return $this;
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
