<?php
namespace App\Controller;

use App\Domain\ShopDomainInterface;
use App\Domain\UserDomainInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class ShopController extends AbstractController
{
    /**
     * @var ShopDomain
     */
    private $shopDomain;

    private $userDomain;

    private $jwtEncoder;

    /**
     * ShopController constructor.
     * @param shopDomain $shopDomain
     * @param jwtEncoder $shopDomain
     * @param userDomain $userDomain
     */
    public function __construct(ShopDomainInterface $shopDomain, JWTEncoderInterface $jwtEncoder, UserDomainInterface $userDomain)
    {
        $this->shopDomain = $shopDomain;
        $this->jwtEncoder = $jwtEncoder;
        $this->userDomain = $userDomain;
    }

    /**
     *
     * @return JsonResponse
     */
    public function fetchProducts(): JsonResponse
    {
        return new JsonResponse($this->serializeObject($this->shopDomain->fetchProductList()));
    }

    /**
     *
     * @return JsonResponse
     */
    public function fetchProductBySku(string $sku): JsonResponse
    {
        return new JsonResponse($this->serializeObject($this->shopDomain->fetchProductBySku($sku)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addProducts(Request $request): JsonResponse
    {
        $user = $this->stripUserInfoFromRequestHeaders($request);
        $requestArray = json_decode($request->getContent(), true);
        return new JsonResponse($this->serializeObject($this->shopDomain->addProducts($requestArray, $user)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createBundle(Request $request): JsonResponse
    {
        $user = $this->stripUserInfoFromRequestHeaders($request);
        $requestArray = json_decode($request->getContent(), true);
        return new JsonResponse($this->serializeObject($this->shopDomain->createBundle($requestArray, $user)));
    }

    /**
     * @param string $sku
     * @return JsonResponse
     */
    public function removeProduct(string $sku): JsonResponse
    {
        return new JsonResponse($this->serializeObject($this->shopDomain->removeProduct($sku)));
    }

    /**
     * @param string $sku
     * @return JsonResponse
     */
    public function removeDiscount(string $sku): JsonResponse
    {
        return new JsonResponse($this->serializeObject($this->shopDomain->removeDiscount($sku)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setPrice(Request $request): JsonResponse
    {
        $requestArray = json_decode($request->getContent(), true);
        return new JsonResponse($this->serializeObject($this->shopDomain->setPrice($requestArray)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setDiscount(Request $request): JsonResponse
    {
        $user = $this->stripUserInfoFromRequestHeaders($request);
        $requestArray = json_decode($request->getContent(), true);
        return new JsonResponse($this->serializeObject($this->shopDomain->setDiscount($requestArray, $user)));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function submitOrder(Request $request): JsonResponse
    {
        $requestArray = json_decode($request->getContent(), true);
        return new JsonResponse($this->serializeObject($this->shopDomain->submitOrder($requestArray)));
    }

    /**
     * @param int $orderId
     * @return JsonResponse
     */
    public function getOrder(int $orderId): JsonResponse
    {
        return new JsonResponse($this->serializeObject($this->shopDomain->getOrder($orderId)));
    }

    /**
     * @param mixed $sku
     * @return array
     */
    private function serializeObject($obj): array
    {
        $arr = [];
        $_arr = is_object($obj) ? $obj->jsonSerialize() : $obj;
        foreach ($_arr as $key => $val) {
            if (is_array($val) || is_object($val)) {
                $val = $this->serializeObject($val);
            }
            $arr[$key] = $val;
        }
        return $arr;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function stripUserInfoFromRequestHeaders(Request $request): array
    {
        $authorization = $request->headers->get('authorization');
        $token = explode("Bearer ", $authorization);
        $token = $token[1] ?? "";
        $userInfo = $this->jwtEncoder->decode($token);
        $user = $this->userDomain->fetchUserInfo($userInfo['username']);
        return $user;
    }
}
