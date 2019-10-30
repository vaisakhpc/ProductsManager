<?php
namespace Tests\App\Service;

use App\Entity\Discounts;
use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Entity\Products;
use App\Model\Discount;
use App\Model\Message;
use App\Model\Order;
use App\Model\Product;
use App\Model\ProductCollection;
use App\Repository\ProductsRepository;
use App\Service\MapService;
use App\Service\ShopService;
use App\Service\ValidateService;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class ShopServiceTest
 */
class ShopServiceTest extends TestCase
{
    private function getObject($methods, $validFlag = true)
    {
        $productRepository = $this->createMock(ProductsRepository::class);
        if (!empty($methods)) {
            foreach ($methods as $key => $value) {
                $productRepository->expects($this->any())
                    ->method($key)
                    ->willReturn($value);
            }
        }

        $validateService = $this->createMock(ValidateService::class);
        $validateService->expects($this->any())
            ->method('validateProductRequest')
            ->willReturn($validFlag ? [] : ['Invalid!']);
        $validateService->expects($this->any())
            ->method('validateDiscountRequest')
            ->willReturn($validFlag ? [] : ['Invalid!']);

        return new ShopService(
            new MapService(),
            $productRepository,
            $validateService
        );
    }

    private function getSampleProducts($single = false)
    {
        $products = [
            [
                "id" => "1",
                "sku" => "SKU001",
                "name" => "I phone X",
                "description" => "Apple company phone",
                "price" => "1999.99",
                "bundle" => 1,
                "discount_id" => 1,
                "type" => 0,
                "value" => "10",
                "label" => '$10 Discount',
            ],
            [
                "id" => "2",
                "sku" => "SKU002",
                "name" => "I phone XI",
                "description" => "Apple company phone",
                "price" => "2189.99",
                "bundle" => 0,
                "discount_id" => 1,
                "type" => 1,
                "value" => "2",
                "label" => '2% Discount',
            ],
        ];
        return $single ? $products[0] : $products;
    }

    private function getSampleBundleItems()
    {
        $bundleItems = [
            [
                "price" => "1989.99",
                "sku" => "SKU003",
            ],
            [
                "price" => "189.99",
                "sku" => "SKU004",
            ],
        ];
        return $bundleItems;
    }

    private function getProductEntity()
    {
        $productEntity = new Products;
        $productEntity->setSku("SKU001");
        $productEntity->setName("I phone X");
        $productEntity->setDescription("Apple company phone");
        $productEntity->setPrice("1999.99");
        $productEntity->setCreatedAt(new DateTime("2019-10-26 13:09:39"));
        $productEntity->setUpdatedAt(new DateTime("2019-10-26 13:09:39"));
        $productEntity->setActive(1);
        $productEntity->setCreatedBy(1);
        $productEntity->setBundle(1);
        return $productEntity;
    }

    private function getDiscountEntity()
    {
        $discount = new Discounts;
        $discount->setId(1);
        $discount->setProductId(1);
        $discount->setType("Fixed");
        $discount->setLabel("Discount");
        $discount->setValue("10");
        $discount->setCreatedBy(1);
        return [$discount];
    }

    private function getOrderEntity()
    {
        $order = new Orders;
        $order->setId(1);
        $order->setCustomerId(1);
        $order->setCreatedAt(new DateTime("2019-10-26 13:09:39"));
        $order->setStatus(1);
        $order->setUpdatedAt(new DateTime("2019-10-26 13:09:39"));
        return $order;
    }

    private function getSampleSetDiscountRequest($flag = true)
    {
        return [
            "sku" => "SKU003",
            "type" => "Percent",
            "value" => $flag ? "87" : "invalid price",
            "label" => "fixed amount 25 discount for S10 new",
        ];
    }

    private function getSampleOrderRequest()
    {
        return [
            'customerId' => 1,
            'items' => [
                [
                    'sku' => "SKU001",
                ],
                [
                    'sku' => "SKU002",
                ],
            ],
        ];
    }

    private function getOrderItemEntityCollection()
    {
        $orderItem = new OrderItems();
        $orderItem->setProductId(1);
        $orderItem->setOrderId(1);
        $orderItem->setPrice(190.00);
        $orderItem->setDiscountApplied(10);
        return [$orderItem];
    }

    public function testFetchProductList()
    {
        $methodsToMock = [
            'findAllProducts' => $this->getSampleProducts(),
            'findAllProductsInBundle' => $this->getSampleBundleItems(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(ProductCollection::class, $obj->fetchProductList());
    }

    public function testEmptyProductList()
    {
        $methodsToMock = [
            'findAllProducts' => [],
            'findAllProductsInBundle' => [],
        ];
        $this->expectException(NotFoundHttpException::class);
        $obj = $this->getObject($methodsToMock);
        $obj->fetchProductList();
    }

    public function testFetchProductBySku()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProducts(true),
            'findAllProductsInBundle' => $this->getSampleBundleItems(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Product::class, $obj->fetchProductBySku("SKU001"));
    }

    public function testEmptyProductBySku()
    {
        $methodsToMock = [
            'findAllProducts' => [],
            'findAllProductsInBundle' => [],
        ];
        $this->expectException(NotFoundHttpException::class);
        $obj = $this->getObject($methodsToMock);
        $obj->fetchProductBySku("SKU001");
    }

    public function testAddProducts()
    {
        $methodsToMock = [
            'addProducts' => 1,
        ];
        $obj = $this->getObject($methodsToMock);
        $result = $obj->addProducts(["sku" => "SKU001"], ['admin_id' => 1]);
        $this->assertEquals($result->getCode(), "200");
        $this->assertInstanceOf(Message::class, $result);
    }

    public function testInvalidAddProductsErrorFromRepository()
    {
        $methodsToMock = [
            'addProducts' => 0,
        ];
        $obj = $this->getObject($methodsToMock);
        $this->expectException(UnprocessableEntityHttpException::class);
        $result = $obj->addProducts(["sku" => "SKU001"], ['admin_id' => 1]);
    }

    public function testInvalidAddProductsErrorAfterValidation()
    {
        $methodsToMock = [
            'addProducts' => 1,
        ];
        $obj = $this->getObject($methodsToMock, false);
        $this->expectException(UnprocessableEntityHttpException::class);
        $result = $obj->addProducts(["sku" => "SKU001"], ['admin_id' => 1]);
    }

    public function testRemoveProduct()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProducts(true),
            'removeProduct' => true,
        ];
        $obj = $this->getObject($methodsToMock);
        $result = $obj->removeProduct("SKU001");
        $this->assertEquals($result->getCode(), "200");
        $this->assertInstanceOf(Message::class, $result);
    }

    public function testInvlidRemoveProductWithNoSku()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
        ];
        $obj = $this->getObject($methodsToMock);
        $this->expectException(NotFoundHttpException::class);
        $result = $obj->removeProduct("SKU001");
    }

    public function testInvalidRemoveProductWithIssueInRepository()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProducts(true),
            'removeProduct' => false,
        ];
        $obj = $this->getObject($methodsToMock);
        $this->expectException(UnprocessableEntityHttpException::class);
        $result = $obj->removeProduct("SKU001");
    }

    public function testValidSetPrice()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProducts(true),
            'updateProductPrice' => $this->getProductEntity(),
            'findAllProductsInBundle' => $this->getSampleBundleItems(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Product::class, $obj->setPrice(["sku" => "SKU001", "price" => "199.99"]));
    }

    public function testInValidSetPrice()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
            'updateProductPrice' => $this->getProductEntity(),
            'findAllProductsInBundle' => $this->getSampleBundleItems(),
        ];
        $this->expectException(NotFoundHttpException::class);
        $obj = $this->getObject($methodsToMock);
        $obj->setPrice(["sku" => "SKU001", "price" => "199.99"]);
    }

    public function testValidSetDiscount()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProducts(true),
            'setDiscount' => $this->getDiscountEntity()[0],
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Discount::class, $obj->setDiscount($this->getSampleSetDiscountRequest(), ['admin_id' => 1]));
    }

    public function testInValidSetDiscountForInvalidProduct()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
        ];
        $this->expectException(NotFoundHttpException::class);
        $obj = $this->getObject($methodsToMock);
        $obj->setDiscount($this->getSampleSetDiscountRequest(), ['admin_id' => 1]);
    }

    public function testInValidSetDiscountWithFailedValidation()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
        ];
        $this->expectException(UnprocessableEntityHttpException::class);
        $obj = $this->getObject($methodsToMock, false);
        $obj->setDiscount($this->getSampleSetDiscountRequest(), ['admin_id' => 1]);
    }

    public function testRemoveDiscount()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProducts(true),
            'fetchDiscountForProduct' => $this->getDiscountEntity(),
            'deleteDiscount' => true,
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Message::class, $obj->removeDiscount("SKU001"));
    }

    public function testInValidRemoveDiscountForInvalidProduct()
    {
        $methodsToMock = [
            'fetchProductBySku' => [],
        ];
        $this->expectException(NotFoundHttpException::class);
        $obj = $this->getObject($methodsToMock);
        $obj->removeDiscount("SKU001");
    }

    public function testInValidRemoveDiscountWithRepositoryException()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProducts(true),
            'fetchDiscountForProduct' => $this->getDiscountEntity(),
            'deleteDiscount' => false,
        ];
        $this->expectException(UnprocessableEntityHttpException::class);
        $obj = $this->getObject($methodsToMock);
        $obj->removeDiscount("SKU001");
    }

    public function testInValidRemoveDiscountWithNonExistingDiscount()
    {
        $methodsToMock = [
            'fetchProductBySku' => $this->getSampleProducts(true),
            'fetchDiscountForProduct' => null,
        ];
        $this->expectException(UnprocessableEntityHttpException::class);
        $obj = $this->getObject($methodsToMock);
        $obj->removeDiscount("SKU001");
    }

    public function testCreateBundle()
    {
        $methodsToMock = [
            'addBundle' => true,
            'fetchProductBySku' => $this->getSampleProducts(true),
            'findAllProductsInBundle' => $this->getSampleBundleItems(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Product::class, $obj->createBundle(["sku" => "SKU001"], ['admin_id' => 1]));
    }

    public function testInvalidCreateBundle()
    {
        $methodsToMock = [
            'addBundle' => true,
            'fetchProductBySku' => $this->getSampleProducts(true),
            'saveOrderContents' => $this->getSampleBundleItems(),
        ];
        $obj = $this->getObject($methodsToMock, false);
        $this->expectException(UnprocessableEntityHttpException::class);
        $obj->createBundle(["sku" => "SKU001"], ['admin_id' => 1]);
    }

    public function testSubmitOrder()
    {
        $methodsToMock = [
            'createOrder' => $this->getOrderEntity(),
            'fetchProductBySku' => $this->getSampleProducts(true),
            'saveOrderContents' => true,
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Order::class, $obj->submitOrder($this->getSampleOrderRequest()));
    }

    public function testSubmitOrderWithPercentDiscount()
    {
        $sampleProduct = $this->getSampleProducts(true);
        $sampleProduct['type'] = 1;
        $methodsToMock = [
            'createOrder' => $this->getOrderEntity(),
            'fetchProductBySku' => $sampleProduct,
            'saveOrderContents' => true,
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Order::class, $obj->submitOrder($this->getSampleOrderRequest()));
    }

    public function testInvalidSubmitOrder()
    {
        $methodsToMock = [
            'createOrder' => false,
        ];
        $obj = $this->getObject($methodsToMock);
        $this->expectException(UnprocessableEntityHttpException::class);
        $obj->submitOrder($this->getSampleOrderRequest());
    }

    public function testGetOrder()
    {
        $methodsToMock = [
            'getOrderDetails' => $this->getOrderEntity(),
            'fetchProductById' => $this->getSampleProducts(true),
            'getOrderItems' => $this->getOrderItemEntityCollection(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertInstanceOf(Order::class, $obj->getOrder(1));
    }

    public function testInvalidGetOrder()
    {
        $methodsToMock = [
            'getOrderDetails' => null,
        ];
        $obj = $this->getObject($methodsToMock);
        $this->expectException(NotFoundHttpException::class);
        $obj->getOrder(1);
    }
}
