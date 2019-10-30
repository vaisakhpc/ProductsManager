<?php

namespace Tests\App\Model;

use App\Model\Discount;
use App\Model\Order;
use App\Model\Item;
use App\Model\ItemCollection;
use PHPUnit\Framework\TestCase;

/**
 * Class OrderTest
 */
class OrderTest extends TestCase
{
    private function getObject()
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

    private function getDiscountObject()
    {
        return new Discount('Fixed', '100', 'Label', 'SKU001');
    }

    public function testGetId()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getId(), "ORD-001");
    }

    public function testGetCustomer()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getCustomer(), "1");
    }

    public function testGetCreatedAt()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getCreatedAt(), "2019-10-27T14:50:30.000Z");
    }

    public function testGetStatus()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getStatus(), "Active");
    }

    public function testGetTotalPrice()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getTotalPrice(), "209.99");
    }

    public function testGetItemCollection()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getItemCollection(), $this->getItemCollectionObject());
    }

    public function testJsonSerialize()
    {
        $obj = $this->getObject();
        $result = $obj->jsonSerialize();
        $this->assertEquals($result['id'], "ORD-001");
        $this->assertEquals($result['customer'], "1");
        $this->assertEquals($result['createdAt'], "2019-10-27T14:50:30.000Z");
        $this->assertEquals($result['status'], "Active");
        $this->assertEquals($result['totalPrice'], "209.99");
        $this->assertInstanceOf(ItemCollection::class, $obj->getItemCollection());
    }
}
