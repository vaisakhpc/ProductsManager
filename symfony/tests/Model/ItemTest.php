<?php

namespace Tests\App\Model;

use App\Model\Item;
use App\Model\Discount;
use PHPUnit\Framework\TestCase;

/**
 * Class ItemTest
 */
class ItemTest extends TestCase
{
    private function getObject()
    {
        return new Item('SKU001', 'Name', 'Description', '99.99', 'true', $this->getDiscountObject());
    }

    private function getDiscountObject()
    {
        return new Discount('Fixed', '100', 'Label', 'SKU001');
    }

    public function testGetSku()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getSku(), "SKU001");
    }

    public function testGetName()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getName(), "Name");
    }

    public function testGetDescription()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getDescription(), "Description");
    }

    public function testGetPrice()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getPrice(), "99.99");
    }

    public function testGetBundleFlag()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getBundleFlag(), "true");
    }

    public function testGetDiscount()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getDiscount(), $this->getDiscountObject());
    }

    public function testJsonSerialize()
    {
        $obj = $this->getObject();
        $result = $obj->jsonSerialize();
        $this->assertEquals($result['sku'], "SKU001");
        $this->assertEquals($result['name'], "Name");
        $this->assertEquals($result['description'], "Description");
        $this->assertEquals($result['price'], "99.99");
        $this->assertEquals($result['bundleFlag'], "true");
        $this->assertInstanceOf(Discount::class, $obj->getDiscount());
    }
}
