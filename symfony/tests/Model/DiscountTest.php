<?php

namespace Tests\App\Model;

use App\Model\Discount;
use PHPUnit\Framework\TestCase;

/**
 * Class DiscountTest
 */
class DiscountTest extends TestCase
{
    private function getObject()
    {
        return new Discount('Fixed', '100', 'Label', 'SKU001');
    }

    public function testGetType()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getType(), "Fixed");
    }

    public function testGetOriginalPrice()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getValue(), "100");
    }

    public function testGetLabel()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getLabel(), "Label");
    }

    public function testGetSku()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getSku(), "SKU001");
    }

    public function testJsonSerialize()
    {
        $obj = $this->getObject();
        $result = $obj->jsonSerialize();
        $this->assertEquals($result['type'], "Fixed");
        $this->assertEquals($result['value'], "100");
        $this->assertEquals($result['label'], "Label");
        $this->assertEquals($result['sku'], "SKU001");
    }
}
