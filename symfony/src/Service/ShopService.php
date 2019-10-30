<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use App\Repository\ProductsRepository;
use App\Service\MapperInterface;
use App\Model\Message;
use App\Model\Product;
use App\Model\Discount;
use App\Model\Order;
use App\Model\ProductCollection;

/**
 * Class ShopService
 */
class ShopService implements ShopServiceInterface
{
    private $productRepository;

    private $mapper;

    private $validateService;

    /**
     * ShopService constructor
     * @param MapperInterface $mapper
     * @param ProductsRepository $productRepository
     * @param ValidateServiceInterface $validateService
     */
    public function __construct(
        MapperInterface $mapper,
        ProductsRepository $productRepository,
        ValidateServiceInterface $validateService
    ) {
        $this->productRepository = $productRepository;
        $this->mapper = $mapper;
        $this->validateService = $validateService;
    }

    /**
     * fetchProductList
     * @return ProductCollection
     */
    public function fetchProductList(): ProductCollection
    {
        $response = $this->productRepository->findAllProducts();
        if ($response) {
            foreach ($response as $key => $value) {
                if ($value['bundle']) {
                    $bundle = $this->productRepository->findAllProductsInBundle($value['id']);
                    $response[$key]['bundleItems'] = $bundle;
                }
            }
            $response = $this->mapper->mapToResponseCollection($response);
        } else {
            throw new NotFoundHttpException("No Products found");
        }
        return $response;
    }

    /**
     * fetchProductBySku
     * @param string $sku
     * @return Product
     */
    public function fetchProductBySku(string $sku): Product
    {
        $response = $this->productRepository->fetchProductBySku($sku);
        if ($response) {
            if ($response['bundle']) {
                $bundle = $this->productRepository->findAllProductsInBundle($response['id']);
                $response['bundleItems'] = $bundle;
            }
            $response = $this->mapper->mapToResponse($response);
        } else {
            throw new NotFoundHttpException("No Product found with this SKU");
        }
        return $response;
    }

    /**
     * addProducts
     * @param array $request
     * @param array $user
     * @return Message
     */
    public function addProducts(array $request, array $user): Message
    {
        $errors = $this->validateService->validateProductRequest($request);
        if (empty($errors)) {
            $count = $this->productRepository->addProducts($request, $user);
            if ($count) {
                $message = ($count == 1 ? ($count . " product is added.") : ($count . " products are added."));
                return $this->mapper->mapToMessage(200, $message);
            } else {
                throw new UnprocessableEntityHttpException("Some error occurred, please try again.");
            }
        } else {
            throw new UnprocessableEntityHttpException(implode(",", $errors));
        }
    }

    /**
     * removeProduct
     * @param string $sku
     * @return Message
     */
    public function removeProduct(string $sku): Message
    {
        $product = $this->productRepository->fetchProductBySku($sku);
        if (empty($product)) {
            throw new NotFoundHttpException("No Product found with this SKU " . $sku);
        } else {
            $response = $this->productRepository->removeProduct($product['id']);
            if ($response) {
                return $this->mapper->mapToMessage(200, 'Product successfully removed');
            } else {
                throw new UnprocessableEntityHttpException("Some error occurred, please try again.");
            }
        }
    }

    /**
     * setPrice
     * @param array $request
     * @return Product
     */
    public function setPrice(array $request): Product
    {
        $product = $this->productRepository->fetchProductBySku($request['sku'] ?? "");
        if (empty($product)) {
            throw new NotFoundHttpException("No Product found with this SKU " . $request['sku']);
        } else {
            $response = $this->productRepository->updateProductPrice($product['id'], $request['price']);
            $product['price'] = $response->getPrice();
            if ($response->getBundle()) {
                $bundle = $this->productRepository->findAllProductsInBundle($product['id']);
                $product['bundleItems'] = $bundle;
            }
            $response = $this->mapper->mapToResponse($product);
        }
        return $response;
    }

    /**
     * setDiscount
     * @param array $request
     * @param array $user
     * @return Discount|null
     */
    public function setDiscount(array $request, array $user):? Discount
    {
        $errors = $this->validateService->validateDiscountRequest($request);
        if (empty($errors)) {
            $product = $this->productRepository->fetchProductBySku($request['sku'] ?? "");
            if (empty($product)) {
                throw new NotFoundHttpException("No Product found with this SKU " . $request['sku']);
            } else {
                $response = $this->productRepository->setDiscount($product['id'], $request, $user);
                $serialized = ['sku' => $request['sku']] + $response->jsonSerialize();
                $response = $this->mapper->mapDiscount($serialized, 'id');
            }
            return $response;
        } else {
            throw new UnprocessableEntityHttpException(implode(",", $errors));
        }
    }

    /**
     * removeDiscount
     * @param string $sku
     * @return Message
     */
    public function removeDiscount(string $sku): Message
    {
        $product = $this->productRepository->fetchProductBySku($sku);
        if (empty($product)) {
            throw new NotFoundHttpException("No Product found with this SKU " . $sku);
        } else {
            $discount = $this->productRepository->fetchDiscountForProduct($product['id']);
            if ($discount) {
                $response = $this->productRepository->deleteDiscount($discount);
                if ($response) {
                    return $this->mapper->mapToMessage(200, 'Discount for product ' . $sku . ' successfully removed');
                } else {
                    throw new UnprocessableEntityHttpException("Some error occurred, please try again.");
                }
            } else {
                throw new UnprocessableEntityHttpException("No discount available yet for this product.");
            }
        }
    }

    /**
     * createBundle
     * @param array $request
     * @param array $user
     * @return Product
     */
    public function createBundle(array $request, array $user): Product
    {
        $errors = $this->validateService->validateProductRequest([$request]);
        if (empty($errors)) {
            $response = $this->productRepository->addBundle($request, $user);
            if ($response) {
                $response = $this->productRepository->fetchProductBySku($request['sku']);
                if ($response['bundle']) {
                    $bundle = $this->productRepository->findAllProductsInBundle($response['id']);
                    $response['bundleItems'] = $bundle;
                }
                $response = $this->mapper->mapToResponse($response);
            } else {
                throw new UnprocessableEntityHttpException("Some error occurred, please try again.");
            }
        } else {
            throw new UnprocessableEntityHttpException(implode(",", $errors));
        }

        return $response;
    }

    /**
     * submitOrder
     * @param array $request
     * @return Order
     */
    public function submitOrder(array $request): Order
    {
        $items = [];
        $order = $this->productRepository->createOrder($request['customerId']);
        if ($order) {
            $products = $request['items'];
            foreach ($products as $value) {
                $items[$value['sku']] = $this->prepareItem($value);
            }
            $response = $this->productRepository->saveOrderContents($items, $order->getId());
            return $this->mapper->mapOrder($items, $order);
        } else {
            throw new UnprocessableEntityHttpException("Some error occurred, please try again.");
        }
    }

    /**
     * prepareItem
     * @param array $value
     * @return array
     */
    private function prepareItem(array $value): array
    {
        $response = $this->productRepository->fetchProductBySku($value['sku']);
        $item = [
            'product' => $response,
            'price' => $response['price'],
            'product_id' => $response['id'],
            'discount_applied' => '',
        ];
        if ($response['discount_id']) {
            if ($response['type']) {
                $item['price'] = round(($item['price'] - ($item['price'] * ($response['value'] / 100))), 2);
            } else {
                $item['price'] -= round($response['value'], 2);
            }
            $item['discount_applied'] = $response['discount_id'];
        }
        return $item;
    }

    /**
     * getOrder
     * @param string $orderId
     * @return Order
     */
    public function getOrder(string $orderId): Order
    {
        $items = [];
        $order = $this->productRepository->getOrderDetails($orderId);
        if ($order) {
            $orderContents = $this->productRepository->getOrderItems($orderId);
            foreach ($orderContents as $key => $value) {
                $response = $this->productRepository->fetchProductById($value->getProductId());
                $items[$key] = $value->jsonSerialize();
                $items[$key]['product'] = $response;
            }
            return $this->mapper->mapOrder($items, $order);
        } else {
            throw new NotFoundHttpException("No Order found with this id " . $orderId);
        }
    }
}
