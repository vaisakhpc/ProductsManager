<?php

namespace Tests\App\Model;

use App\Model\Bundle;
use PHPUnit\Framework\TestCase;

/**
 * Class BundleTest
 */
class BundleTest extends TestCase
{
    private function getObject()
    {
        return new Bundle('SKU001', '92.99');
    }

    public function testGetSku()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getSku(), "SKU001");
    }

    public function testGetOriginalPrice()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getOriginalPrice(), "92.99");
    }

    public function testJsonSerialize()
    {
        $obj = $this->getObject();
        $result = $obj->jsonSerialize();
        $this->assertEquals($result['sku'], "SKU001");
        $this->assertEquals($result['originalPrice'], "92.99");
    }
}
