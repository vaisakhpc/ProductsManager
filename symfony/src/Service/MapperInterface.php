<?php

namespace App\Service;

use App\Model\Discount;
use App\Model\Message;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductCollection;
use App\Entity\Orders;

interface MapperInterface
{
    public function mapToResponse(array $result): Product;

    public function mapToResponseCollection(array $result): ProductCollection;

    public function mapOrder(array $items, Orders $order): Order;

    public function mapToMessage(string $code, string $message): Message;

    public function mapDiscount(array $result, string $key = 'discount_id'):? Discount;
}
