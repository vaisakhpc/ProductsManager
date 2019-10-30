<?php
namespace Tests\App\Service;

use App\Entity\Discounts;
use App\Entity\Orders;
use App\Model\Discount;
use App\Model\Message;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductCollection;
use App\Service\MapService;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class ShopServiceTest
 */
class MapServiceTest extends TestCase
{
    private function getObject()
    {
        return new MapService;
    }

    private function getSampleBundleItems()
    {
        $bundleItems = [
            [
                "price" => "1989.99",
                "sku" => "SKU003",
            ],
            [
                "price" => "189.99",
                "sku" => "SKU004",
            ],
        ];
        return $bundleItems;
    }

    private function getSampleProducts($single = false)
    {
        $products = [
            [
                "id" => "1",
                "sku" => "SKU001",
                "name" => "I phone X",
                "description" => "Apple company phone",
                "price" => "1999.99",
                "bundle" => 1,
                "discount_id" => 1,
                "type" => 0,
                "value" => "10",
                "label" => '$10 Discount',
            ],
            [
                "id" => "2",
                "sku" => "SKU002",
                "name" => "I phone XI",
                "description" => "Apple company phone",
                "price" => "2189.99",
                "bundle" => 0,
                "discount_id" => 1,
                "type" => 1,
                "value" => "2",
                "label" => '2% Discount',
            ],
        ];
        return $single ? $products[0] : $products;
    }

    private function getSampleRequest()
    {
        return [
            [
                "id" => 1,
                "productId" => 20,
                "price" => "20.99",
                "discountApplied" => 10,
                "orderId" => 10,
                "product" => $this->getSampleProducts(true),
            ],
        ];
    }

    private function getDiscountEntity()
    {
        $discount = new Discounts;
        $discount->setId(1);
        $discount->setProductId(1);
        $discount->setType("Fixed");
        $discount->setLabel("Discount");
        $discount->setValue("10");
        $discount->setCreatedBy(1);
        return $discount;
    }

    private function getOrderEntity()
    {
        $order = new Orders;
        $order->setId(1);
        $order->setCustomerId(1);
        $order->setCreatedAt(new DateTime("2019-10-26 13:09:39"));
        $order->setStatus(1);
        $order->setUpdatedAt(new DateTime("2019-10-26 13:09:39"));
        return $order;
    }

    public function testMapToResponse()
    {
        $obj = $this->getObject();
        $request = $this->getSampleProducts(true);
        $request['bundleItems'] = $this->getSampleBundleItems();
        $this->assertInstanceOf(Product::class, $obj->mapToResponse($request));
    }

    public function testMapToResponseCollection()
    {
        $obj = $this->getObject();
        $request = $this->getSampleProducts();
        $request[0]['bundleItems'] = $this->getSampleBundleItems();
        $this->assertInstanceOf(ProductCollection::class, $obj->mapToResponseCollection($request));
    }

    public function testMapDiscount()
    {
        $obj = $this->getObject();
        $request = $this->getDiscountEntity();
        $request = $request->jsonSerialize() + ['sku' => "SKU001"];
        $this->assertInstanceOf(Discount::class, $obj->mapDiscount($request, 'id'));
    }

    public function testInValidMapDiscount()
    {
        $obj = $this->getObject();
        $request = $this->getDiscountEntity();
        $request->setId(0);
        $request = $request->jsonSerialize() + ['sku' => "SKU001"];
        $this->assertNull($obj->mapDiscount($request, 'id'));
    }

    public function testMapToMessage()
    {
        $obj = $this->getObject();
        $this->assertInstanceOf(Message::class, $obj->mapToMessage(200, 'Valid!'));
    }

    public function testMapOrder()
    {
        $obj = $this->getObject();
        $this->assertInstanceOf(Order::class, $obj->mapOrder($this->getSampleRequest(), $this->getOrderEntity()));
    }
}
