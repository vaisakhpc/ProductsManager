<?php

namespace Tests\App\Model;

use App\Model\Message;
use PHPUnit\Framework\TestCase;

/**
 * Class MessageTest
 */
class MessageTest extends TestCase
{
    private function getObject()
    {
        return new Message('200', 'Success!');
    }

    public function testGetCode()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getCode(), "200");
    }

    public function testGetMessage()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getMessage(), "Success!");
    }

    public function testJsonSerialize()
    {
        $obj = $this->getObject();
        $result = $obj->jsonSerialize();
        $this->assertEquals($result['code'], "200");
        $this->assertEquals($result['message'], "Success!");
    }
}
