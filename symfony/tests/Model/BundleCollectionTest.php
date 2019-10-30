<?php

namespace Tests\App\Model;

use App\Model\Bundle;
use App\Model\BundleCollection;
use PHPUnit\Framework\TestCase;

/**
 * Class BundleCollectionTest
 */
class BundleCollectionTest extends TestCase
{
    private function getObject()
    {
        return new BundleCollection();
    }

    private function getBundleObject()
    {
        return new Bundle('SKU001', '92.99');
    }

    public function testAddMethod()
    {
        $bundle = $this->getBundleObject();
        $obj = $this->getObject();
        $result = $obj->add($bundle);
        $this->assertInstanceOf(Bundle::class, $result[0]);
    }

    public function testcountMethod()
    {
        $bundle = $this->getBundleObject();
        $obj = $this->getObject();
        $obj->add($bundle);
        $result = $obj->count();
        $this->assertEquals($result, 1);
    }

    public function testJsonSerialize()
    {
        $bundle = $this->getBundleObject();
        $obj = $this->getObject();
        $obj->add($bundle);
        $result = $obj->jsonSerialize();
        $this->assertEquals($result, [$bundle]);
    }
}
