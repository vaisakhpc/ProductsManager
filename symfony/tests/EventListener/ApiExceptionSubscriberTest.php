<?php

namespace Tests\App\EventListener;

use App\EventListener\ApiExceptionSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;
use ErrorException;
use Exception;

/**
 * Class ApiExceptionSubscriberTest
 */
class ApiExceptionSubscriberTest extends TestCase
{
    public function testgetSubscribedEvents()
    {
        $obj = new ApiExceptionSubscriber();
        $result = $obj->getSubscribedEvents();
        $this->assertNotEmpty($result);
    }

    public function testOnKernelExceptionForHttpException()
    {
        $obj = new ApiExceptionSubscriber();
        $eventObject = new GetResponseForExceptionEvent(
            new KernelForTest("localhost", 0),
            new Request(),
            1,
            new HttpException(500, "Invalid!")
        );
        $obj->onKernelException($eventObject);
        $response = json_decode($eventObject->getResponse()->getContent(), true);
        $this->assertEquals(500, $response['error']['code']);
        $this->assertEquals("Invalid!", $response['error']['message']);
    }

    public function testOnKernelExceptionForErrorException()
    {
        $obj = new ApiExceptionSubscriber();
        $eventObject = new GetResponseForExceptionEvent(
            new KernelForTest("localhost", 0),
            new Request(),
            1,
            new ErrorException("Invalid!")
        );
        $obj->onKernelException($eventObject);
        $response = json_decode($eventObject->getResponse()->getContent(), true);
        $this->assertEquals(500, $response['error']['code']);
        $this->assertEquals("Invalid!", $response['error']['message']);
    }

    public function testOnKernelExceptionForGeneralException()
    {
        $obj = new ApiExceptionSubscriber();
        $eventObject = new GetResponseForExceptionEvent(
            new KernelForTest("localhost", 0),
            new Request(),
            1,
            new Exception("Invalid!")
        );
        $obj->onKernelException($eventObject);
        $response = json_decode($eventObject->getResponse()->getContent(), true);
        $this->assertEquals(500, $response['error']['code']);
        $this->assertEquals("Invalid!", $response['error']['message']);
    }
}
