<?php

namespace App\Service;

use App\Model\Discount;
use App\Model\Message;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductCollection;

interface ShopServiceInterface
{
    public function fetchProductList(): ProductCollection;

    public function fetchProductBySku(string $sku): Product;

    public function addProducts(array $request, array $user): Message;

    public function removeProduct(string $sku): Message;

    public function setPrice(array $request): Product;

    public function setDiscount(array $request, array $user):? Discount;

    public function removeDiscount(string $sku) : Message;

    public function createBundle(array $request, array $user): Product;

    public function submitOrder(array $request): Order;

    public function getOrder(string $orderId): Order;
}
