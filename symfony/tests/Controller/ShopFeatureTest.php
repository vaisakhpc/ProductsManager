<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller test
 */
class ShopFeatureTest extends WebTestCase
{
    private $adminToken;

    private $userToken;

    public function __construct()
    {
        $this->adminToken = $this->getToken();
        $this->userToken = $this->getToken(false);
        parent::__construct();
    }

    private function getToken($admin = true)
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            if ($admin) {
                $postData = '{"username": "admin","password": "test"}';
            } else {
                $postData = '{"username": "vpc","password": "customer"}';
            }
            $client = static::createClient();
            $postReponse = $client->request(
                'POST',
                '/api/login_check',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                $postData
            );
            $result = $client->getResponse();
            $content = json_decode($result->getContent(), true);
            return $content['token'] ?? "";
        }
    }

    private function createSampleProduct()
    {
        $client = static::createClient();
        $product = "SKU" . rand(1, 10000);
        $postData = '[
        {
            "sku": "' . $product . '",
            "name": "Samsung Galaxy S10",
            "description": "Samsung Galaxy S10. Brand new phone",
            "price": "299.99",
            "discount": {
                "type": "Fixed",
                "value": "25",
                "label": "fixed amount 25 discount for S10 new"
            }
        }
        ]';
        // creating product frst to test other endpoints
        $postReponse = $client->request(
            'POST',
            '/api/addProduct',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->adminToken],
            $postData
        );
        return $product;
    }

    public function testFetchProducts()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $product = $this->createSampleProduct();
            $client->request('GET', '/api/fetchProducts', [], [], ['HTTP_Authorization' => 'Bearer ' . $this->userToken]);
            $result = $client->getResponse();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }

    public function testFetchProductBySku()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $product = $this->createSampleProduct();
            $client->request('GET', '/api/fetchProduct/' . $product, [], [], ['HTTP_Authorization' => 'Bearer ' . $this->userToken]);
            $result = $client->getResponse();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }

    public function testAddProduct()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $postData = '[
            {
                "sku": "SKU' . rand(1, 10000) . '",
                "name": "Samsung Galaxy S10",
                "description": "Samsung Galaxy S10. Brand new phone",
                "price": "299.99",
                "discount": {
                    "type": "Fixed",
                    "value": "25",
                    "label": "fixed amount 25 discount for S10 new"
                }
            }
            ]';
            $postReponse = $client->request(
                'POST',
                '/api/addProduct',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->adminToken],
                $postData
            );
            $result = $client->getResponse();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }

    public function testCreateBundle()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $product = $this->createSampleProduct();
            $postData = '{
                "sku": "SKUBUNDLE' . rand(1, 10000) . '",
                "name": "Bundle 001",
                "description": "First option Bundle",
                "price": "99.99",
                "discount": {
                    "type": "Fixed",
                    "value": "2.99",
                    "label": "fixed amount 2.99 discount for first bundle"
                },
                "items": [
                    {
                        "sku": "' . $product . '"
                    }
                ]
            }';
            $postReponse = $client->request(
                'POST',
                '/api/createBundle',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->adminToken],
                $postData
            );
            $result = $client->getResponse();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }

    public function testRemoveProduct()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $product = $this->createSampleProduct();
            $postReponse = $client->request(
                'DELETE',
                '/api/removeProduct/' . $product,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->adminToken]
            );
            $result = $client->getResponse();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }

    public function testSetPrice()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $product = $this->createSampleProduct();
            $postData = '{
                "sku" : "' . $product . '",
                "price" : "1989.99"
            }';

            $postReponse = $client->request(
                'POST',
                '/api/setPrice',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->adminToken],
                $postData
            );
            $result = $client->getResponse();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }

    public function testSetDiscount()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $product = $this->createSampleProduct();
            $postData = '{
                "sku": "' . $product . '",
                "type": "Percent",
                "value": "20",
                "label": "percent 25 discount"
            }';

            $postReponse = $client->request(
                'POST',
                '/api/setDiscount',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->adminToken],
                $postData
            );
            $result = $client->getResponse();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }

    public function testRemoveDiscount()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $product = $this->createSampleProduct();
            $postReponse = $client->request(
                'DELETE',
                '/api/removeDiscount/' . $product,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->adminToken]
            );
            $result = $client->getResponse();

            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }

    public function testSubmitOrder()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $product = $this->createSampleProduct();
            $postData = '{
                "customerId": "1",
                "items": [
                    {
                        "sku": "' . $product . '"
                    }
                ]
            }';

            $postReponse = $client->request(
                'POST',
                '/api/submitOrder',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->userToken],
                $postData
            );
            $result = $client->getResponse();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }

    public function testGetOrder()
    {
        if (getenv('FUNCTIONAL_TEST_ENABLE')) {
            $client = static::createClient();
            $product = $this->createSampleProduct();
            $postData = '{
                "customerId": "1",
                "items": [
                    {
                        "sku": "' . $product . '"
                    }
                ]
            }';

            $postReponse = $client->request(
                'POST',
                '/api/submitOrder',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->userToken],
                $postData
            );
            $result = $client->getResponse();
            $content = json_decode($result->getContent(), true);
            $orderId = $content['id'];
            $postReponse = $client->request(
                'GET',
                '/api/getOrder/' . $orderId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $this->userToken]
            );
            $result = $client->getResponse();
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInstanceOf(JsonResponse::class, $result);
        } else {
            self::assertTrue(true);
        }
    }
}
