<?php

namespace Tests\App\Model;

use App\Model\Discount;
use App\Model\Item;
use App\Model\ItemCollection;
use PHPUnit\Framework\TestCase;

/**
 * Class ItemCollectionTest
 */
class ItemCollectionTest extends TestCase
{
    private function getObject()
    {
        return new ItemCollection();
    }

    private function getItemObject()
    {
        return new Item('SKU001', 'Name', 'Description', '99.99', 'true', $this->getDiscountObject());
    }

    private function getDiscountObject()
    {
        return new Discount('Fixed', '100', 'Label', 'SKU001');
    }

    public function testAddMethod()
    {
        $item = $this->getItemObject();
        $obj = $this->getObject();
        $result = $obj->add($item);
        $this->assertInstanceOf(Item::class, $result[0]);
    }

    public function testcountMethod()
    {
        $item = $this->getItemObject();
        $obj = $this->getObject();
        $obj->add($item);
        $result = $obj->count();
        $this->assertEquals($result, 1);
    }

    public function testJsonSerialize()
    {
        $item = $this->getItemObject();
        $obj = $this->getObject();
        $obj->add($item);
        $result = $obj->jsonSerialize();
        $this->assertEquals($result, [$item]);
    }
}
