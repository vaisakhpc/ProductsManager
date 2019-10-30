<?php

namespace App\Domain;

use App\Service\ShopServiceInterface;
use App\Model\Product;
use App\Model\ProductCollection;
use App\Model\Message;
use App\Model\Discount;
use App\Model\Order;

class ShopDomain implements ShopDomainInterface
{
    /**
     * @var ShopServiceInterface
     */
    private $shopService;

    /**
     * constructor
     * @param ShopServiceInterface $shopService
     */
    public function __construct(ShopServiceInterface $shopService)
    {
        $this->shopService = $shopService;
    }

    /**
     * fetch By Id
     * @param int $id
     * @return ProductCollection
     */
    public function fetchProductList(): ProductCollection
    {
        return $this->shopService->fetchProductList();
    }

    /**
     * fetchProductBySku
     * @param string $sku
     * @return Product
     */
    public function fetchProductBySku(string $sku): Product
    {
        return $this->shopService->fetchProductBySku($sku);
    }

    /**
     * addProducts
     * @param array $request
     * @param array $user
     * @return Message
     */
    public function addProducts(array $request, array $user): Message
    {
        return $this->shopService->addProducts($request, $user);
    }

    /**
     * createBundle
     * @param array $request
     * @param array $user
     * @return Product
     */
    public function createBundle(array $request, array $user): Product
    {
        return $this->shopService->createBundle($request, $user);
    }

    /**
     * removeProduct
     * @param string $sku
     * @return Message
     */
    public function removeProduct(string $sku): Message
    {
        return $this->shopService->removeProduct($sku);
    }

    /**
     * setPrice
     * @param array $request
     * @return Product
     */
    public function setPrice(array $request): Product
    {
        return $this->shopService->setPrice($request);
    }

    /**
     * setDiscount
     * @param array $request
     * @param array $user
     * @return Discount|null
     */
    public function setDiscount(array $request, array $user):? Discount
    {
        return $this->shopService->setDiscount($request, $user);
    }

    /**
     * removeDiscount
     * @param string $sku
     * @return Message
     */
    public function removeDiscount(string $sku): Message
    {
        return $this->shopService->removeDiscount($sku);
    }

    /**
     * submitOrder
     * @param array $request
     * @return Order
     */
    public function submitOrder(array $request): Order
    {
        return $this->shopService->submitOrder($request);
    }

    /**
     * getOrder
     * @param string $id
     * @return Order
     */
    public function getOrder(string $orderId): Order
    {
        return $this->shopService->getOrder($orderId);
    }
}
