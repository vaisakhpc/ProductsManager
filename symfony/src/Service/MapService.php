<?php

namespace App\Service;

use App\Model\Bundle;
use App\Model\BundleCollection;
use App\Model\Discount;
use App\Model\Message;
use App\Model\Order;
use App\Model\Item;
use App\Model\ItemCollection;
use App\Model\Product;
use App\Model\ProductCollection;
use App\Entity\Orders;

/**
 * Class MapService
 */
class MapService implements MapperInterface
{
    /**
     * mapToResponse
     * @param array $result
     * @return Product
     */
    public function mapToResponse(array $result): Product
    {
        $discount = $this->mapDiscount($result);
        $bundles = $this->mapBundles($result['bundleItems'] ?? []);
        return new Product(
            $result['sku'],
            $result['name'],
            $result['description'],
            $result['price'],
            $this->mapBundleFlag($result['bundle']),
            $discount,
            $bundles
        );
    }

    /**
     * map To Response Collection
     * @param array $result
     * @return ProductCollection
     */
    public function mapToResponseCollection(array $result): ProductCollection
    {
        $collection = new ProductCollection();
        foreach ($result as $entry) {
            $collection->add($this->mapToResponse($entry));
        }
        return $collection;
    }

    /**
     * mapDiscount
     * @param array $result
     * @param string $key
     * @return Discount|null
     */
    public function mapDiscount(array $result, string $key = 'discount_id'):? Discount
    {
        if (!empty($result[$key])) {
            return new Discount(
                $this->mapDiscountType($result['type']),
                $this->mapDiscountValue($result),
                $result['label'],
                $result['sku']
            );
        } else {
            return null;
        }
    }

    /**
     * mapToMessage
     * @param string $code
     * @param string $message
     * @return Message
     */
    public function mapToMessage(string $code, string $message): Message
    {
        return new Message(
            $code,
            $message
        );
    }

    /**
     * mapOrder
     * @param array $items
     * @param Orders $order
     * @return Order
     */
    public function mapOrder(array $items, Orders $order): Order
    {
        $itemsCollection = $this->prepareItemsCollection($items);
        $total = $this->calculateTotal($items);
        return new Order(
            $order->getId(),
            $order->getCustomerId(),
            $order->getCreatedAt()->format('Y-m-d\TH:i:s.000\Z'),
            $order->getStatus() ? "Active" : "Inactive",
            $total,
            $itemsCollection
        );
    }

    /**
     * mapDiscountType
     * @param int $type
     * @return string
     */
    private function mapDiscountType(int $type): string
    {
        return $type ? "Percent" : "Fixed";
    }

    /**
     * mapBundleFlag
     * @param int $bundle
     * @return string
     */
    private function mapBundleFlag(int $bundle): string
    {
        return $bundle ? "true" : "false";
    }

    /**
     * mapDiscountValue
     * @param array $result
     * @return string
     */
    private function mapDiscountValue(array $result): string
    {
        if (!$result['type']) {
            return number_format((float) $result['value'], 2, '.', '');
        } else {
            return $result['value'];
        }
    }

    /**
     * mapBundles
     * @param array $bundleItems
     * @return BundleCollection|null
     */
    private function mapBundles(array $bundleItems):? BundleCollection
    {
        if (!empty($bundleItems)) {
            $collection = new BundleCollection();
            foreach ($bundleItems as $entry) {
                if ($entry['sku']) {
                    $collection->add($this->mapToBundle($entry));
                }
            }
            return $collection;
        } else {
            return null;
        }
    }

    /**
     * mapToBundle
     * @param array $bundleItem
     * @return Bundle
     */
    private function mapToBundle(array $bundleItem): Bundle
    {
        return new Bundle(
            $bundleItem['sku'],
            $bundleItem['price']
        );
    }

    /**
     * calculateTotal
     * @param array $items
     * @return string
     */
    private function calculateTotal(array $items): string
    {
        $total = 0;
        foreach ($items as $value) {
            $total += $value['price'];
        }
        return round($total, 2);
    }

    /**
     * prepareItemsCollection
     * @param array $items
     * @return ItemCollection
     */
    private function prepareItemsCollection(array $items): ItemCollection
    {
        $collection = new ItemCollection();
        foreach ($items as $entry) {
            $collection->add($this->mapItem($entry));
        }
        return $collection;
    }

    /**
     * mapItem
     * @param array $item
     * @return Item
     */
    private function mapItem(array $item): Item
    {
        return new Item(
            $item['product']['sku'],
            $item['product']['name'],
            $item['product']['description'],
            $item['price'],
            $this->mapBundleFlag($item['product']['bundle']),
            $this->mapDiscount($item['product'])
        );
    }
}
