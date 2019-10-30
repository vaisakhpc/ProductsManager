<?php
namespace Tests\App\Domain;

use App\Domain\ShopDomain;
use PHPUnit\Framework\TestCase;
use App\Service\ShopService;
use App\Model\Bundle;
use App\Model\BundleCollection;
use App\Model\ProductCollection;
use App\Model\Discount;
use App\Model\Item;
use App\Model\ItemCollection;
use App\Model\Message;
use App\Model\Order;
use App\Model\Product;

/**
 * Class ShopDomainTest
 */
class ShopDomainTest extends TestCase
{
    private function getObject($methods)
    {
        $shopService = $this->createMock(ShopService::class);
        if (!empty($methods)) {
            foreach ($methods as $key => $value) {
                $shopService->expects($this->any())
                    ->method($key)
                    ->willReturn($value);
            }
        }

        return new ShopDomain(
            $shopService
        );
    }

    private function getSampleProductCollectionObject()
    {
        $obj = new ProductCollection();
        $result = $obj->add($this->getSampleProductObject());
        return $obj;
    }

    private function getSampleProductObject()
    {
        $discount = $this->getDiscountObject();
        $bundles = $this->getBundleCollectionObject();
        return new Product('SKU002', 'Name', 'Description', '100.09', 'true', $discount, $bundles);
    }

    private function getDiscountObject()
    {
        return new Discount('Fixed', '100', 'Label', 'SKU001');
    }

    private function getBundleObject()
    {
        return new Bundle('SKU001', '92.99');
    }

    private function getBundleCollectionObject()
    {
        $bundle = $this->getBundleObject();
        $obj = new BundleCollection();
        $result = $obj->add($bundle);
        return $obj;
    }

    private function getMessageObject()
    {
        return new Message('200', 'Success!');
    }

    private function getOrderObject()
    {
        return new Order(
            'ORD-001',
            '1',
            '2019-10-27T14:50:30.000Z',
            'Active',
            '209.99',
            $this->getItemCollectionObject()
        );
    }

    private function getItemCollectionObject()
    {
        $item = $this->getItemObject();
        $obj = new ItemCollection();
        $result = $obj->add($item);
        return $obj;
    }

    private function getItemObject()
    {
        return new Item('SKU001', 'Name', 'Description', '99.99', 'true', $this->getDiscountObject());
    }

    public function testFetchProductList()
    {
        $methodsToMock = [
            'fetchProductList' => $this->getSampleProductCollectionObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(ProductCollection::class, $obj->fetchProductList());
    }

    public function testFetchProductBySku()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProductObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Product::class, $obj->fetchProductBySku('SKU001'));
    }

    public function testAddProducts()
    {
        $methodsToMock = [
            'addProducts' => $this->getMessageObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Message::class, $obj->addProducts(["sku" => "SKU001", "price" => "300.99"], ['admin_id' => 1]));
    }

    public function testCreateBundle()
    {
        $methodsToMock = [
            'createBundle' => $this->getSampleProductObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Product::class, $obj->createBundle(["sku" => "SKU001", "price" => "300.99"], ['admin_id' => 1]));
    }

    public function testRemoveProduct()
    {
        $methodsToMock = [
            'removeProduct' => $this->getMessageObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Message::class, $obj->removeProduct("SKU001"));
    }

    public function testSetPrice()
    {
        $methodsToMock = [
            'setPrice' => $this->getSampleProductObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Product::class, $obj->setPrice(["sku" => "SKU001", "price" => "300.99"]));
    }

    public function testSetDiscount()
    {
        $methodsToMock = [
            'setDiscount' => $this->getDiscountObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Discount::class, $obj->setDiscount(["sku" => "SKU001", "value" => "30"], ['admin_id' => 1]));
    }

    public function testRemoveDiscount()
    {
        $methodsToMock = [
            'removeDiscount' => $this->getMessageObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Message::class, $obj->removeDiscount("SKU001"));
    }

    public function testSubmitOrder()
    {
        $methodsToMock = [
            'submitOrder' => $this->getOrderObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Order::class, $obj->submitOrder(["customerId" => 1, "items" => ['sku' => "SKU001"]]));
    }

    public function testGetOrder()
    {
        $methodsToMock = [
            'getOrder' => $this->getOrderObject(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Order::class, $obj->getOrder(1));
    }
}
