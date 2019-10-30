<?php
namespace App\Repository;

use App\Entity\Bundles;
use App\Entity\Discounts;
use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Entity\Products;
use Doctrine\ORM\EntityManagerInterface;

class ProductsRepository
{
    private $repository;

    private $entityManager;

    /**
     * ProductsRepository constructor
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Products::class);
        $this->entityManager = $entityManager;
    }

    /**
     * findAllProducts
     * @return array
     */
    public function findAllProducts(): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p.id,p.sku,p.name,p.description,p.price,p.bundle,d.id as discount_id,d.type,d.value,d.label')
            ->from('App\Entity\Products', 'p')
            ->leftJoin(
                'App\Entity\Discounts',
                'd',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'p.id = d.productId'
            )
            ->orderBy('p.sku', 'ASC');
        return $qb->getQuery()->getResult();
    }

    /**
     * fetchProductBySku
     * @param string $sku
     * @return array|null
     */
    public function fetchProductBySku(string $sku):? array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p.id,p.sku,p.name,p.description,p.price,p.bundle,d.id as discount_id,d.type,d.value,d.label')
            ->from('App\Entity\Products', 'p')
            ->leftJoin(
                'App\Entity\Discounts',
                'd',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'p.id = d.productId'
            )
            ->where('p.sku = :sku')
            ->setParameter('sku', $sku);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * findAllProductsInBundle
     * @param string $id \
     * @return array
     */
    public function findAllProductsInBundle(string $id): array
    {
        $conn = $this->entityManager->getConnection();
        $sql = 'SELECT (select price from products where id=b.simple_product_id) as price,(select sku from products where id=b.simple_product_id) as sku FROM products p left join bundles b on p.id=b.bundle_product_id where p.id=:id';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll();
    }

    /**
     * addProducts
     * @param array $request
     * @param array $user
     * @return int
     */
    public function addProducts(array $request, array $user): int
    {
        $createdBy = !empty($user['admin_id']) ? $user['admin_id'] : null;
        $count = 0;
        foreach ($request as $key => $value) {
            $product = $this->saveProduct($createdBy, $value);
            if ($product) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * removeProduct
     * @param string $id
     * @return bool
     */
    public function removeProduct(string $id): bool
    {
        try {
            $product = $this->repository->find($id);
            $this->entityManager->remove($product);
            $this->entityManager->flush();
            $discounts = $this->entityManager->getRepository(Discounts::class)->findByProductId($id);
            $this->removeDiscount($discounts);
            if ($product->getBundle()) {
                $bundles = $this->entityManager->getRepository(Bundles::class)->findByBundleProductId($id);
                if (!empty($bundles)) {
                    foreach ($bundles as $bundle) {
                        $this->entityManager->remove($bundle);
                        $this->entityManager->flush();
                    }
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * updateProductPrice
     * @param string $id
     * @param string $price
     * @return mixed -> false or App/Entity/Products
     */
    public function updateProductPrice(string $id, string $price)
    {
        try {
            $product = $this->repository->find($id);
            $product->setPrice($price);
            $this->entityManager->flush();
            return $product;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * removeDiscount
     * @param array $discounts
     */
    private function removeDiscount(array $discounts)
    {
        if (!empty($discounts)) {
            $discount = $discounts[0];
            $this->entityManager->remove($discount);
            $this->entityManager->flush();
        }
    }

    /**
     * addDiscount
     * @param array $request
     * @param string $createdBy
     * @param string $id
     * @return Discounts
     */
    private function addDiscount(array $request, string $createdBy, string $id): Discounts
    {
        $discount = new Discounts;
        $discount->setProductId($id);
        $discount->setType($request['type'] === "Fixed" ? 0 : 1);
        $discount->setLabel($request['label']);
        $discount->setValue($request['value']);
        $discount->setCreatedBy($createdBy);
        $this->entityManager->persist($discount);
        $this->entityManager->flush();
        return $discount;
    }

    /**
     * setDiscount
     * @param string $id
     * @param array $request
     * @param array $user
     * @return mixed -> false or App/Entity/Discounts
     */
    public function setDiscount(string $id, array $request, array $user)
    {
        try {
            $createdBy = !empty($user['admin_id']) ? $user['admin_id'] : null;
            $existingDiscount = $this->entityManager->getRepository(Discounts::class)->findByProductId($id);
            $this->removeDiscount($existingDiscount);
            $discount = $this->addDiscount($request, $createdBy, $id);
            return $discount;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * fetchDiscountForProduct
     * @param string $id
     * @return array|null
     */
    public function fetchDiscountForProduct(string $id):? array
    {
        return $this->entityManager->getRepository(Discounts::class)->findByProductId($id);
    }

    /**
     * deleteDiscount
     * @param array $discount
     * @return bool
     */
    public function deleteDiscount(array $discount): bool
    {
        try {
            $this->removeDiscount($discount);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * saveProduct
     * @param string $createdBy
     * @param array $value
     * @param int $bundle
     * @return Products
     */
    private function saveProduct(string $createdBy, array $value, int $bundle = 0): Products
    {
        $product = new Products;
        $product->setSku($value['sku']);
        $product->setName($value['name']);
        $product->setDescription($value['description']);
        $product->setPrice($value['price']);
        $product->setCreatedBy($createdBy);
        $product->setBundle($bundle);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        if (!empty($value['discount'])) {
            $productId = $product->getId();
            $disc = $value['discount'];
            $discount = $this->addDiscount($disc, $createdBy, $productId);
        }
        return $product;
    }

    /**
     * addBundle
     * @param array $request
     * @param array $user
     * @return bool
     */
    public function addBundle(array $request, array $user): bool
    {
        try {
            $createdBy = !empty($user['admin_id']) ? $user['admin_id'] : null;
            $product = $this->saveProduct($createdBy, $request, 1);
            $items = $request['items'];
            $skus = array_column($items, 'sku');
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('p.id')
                ->from('App\Entity\Products', 'p')
                ->where("p.sku IN (:sku)")
                ->setParameter('sku', $skus);

            $bundleItems = $qb->getQuery()->getResult();
            foreach ($bundleItems as $value) {
                $bundle = new Bundles;
                $bundle->setBundleProductId($product->getId());
                $bundle->setSimpleProductId($value['id']);
                $this->entityManager->persist($bundle);
                $this->entityManager->flush();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * createOrder
     * @param string $customerId
     * @return mixed -> false or App/Entity/Orders
     */
    public function createOrder(string $customerId)
    {
        try {
            $order = new Orders;
            $order->setCustomerId($customerId);
            $this->entityManager->persist($order);
            $this->entityManager->flush();
            return $order;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * saveOrderContents
     * @param array $items
     * @param string $orderId
     * @return bool
     */
    public function saveOrderContents(array $items, string $orderId): bool
    {
        try {
            foreach ($items as $key => $value) {
                $orderItem = new OrderItems;
                $orderItem->setProductId($value['product_id']);
                $orderItem->setOrderId($orderId);
                $orderItem->setPrice($value['price']);
                $orderItem->setDiscountApplied($value['discount_applied'] ?: null);
                $this->entityManager->persist($orderItem);
                $this->entityManager->flush();
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * getOrderDetails
     * @param string $orderId
     * @return Orders|null
     */
    public function getOrderDetails(string $orderId):? Orders
    {
        return $this->entityManager->getRepository(Orders::class)->find($orderId);
    }

    /**
     * getOrderItems
     * @param string $orderId
     * @return array
     */
    public function getOrderItems(string $orderId): array
    {
        return $this->entityManager->getRepository(OrderItems::class)->findByOrderId($orderId);
    }

    /**
     * fetchProductById
     * @param string $id
     * @return array
     */
    public function fetchProductById(string $id): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p.id,p.sku,p.name,p.description,p.price,p.bundle,d.id as discount_id,d.type,d.value,d.label')
            ->from('App\Entity\Products', 'p')
            ->leftJoin(
                'App\Entity\Discounts',
                'd',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'p.id = d.productId'
            )
            ->where('p.id = :id')
            ->setParameter('id', $id);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
