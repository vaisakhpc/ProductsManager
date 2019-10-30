<?php
namespace Tests\App\Domain;

use App\Service\UserService;
use App\Domain\UserDomain;
use PHPUnit\Framework\TestCase;

/**
 * Class UserDomainTest
 */
class UserDomainTest extends TestCase
{
    private function getObject($methods)
    {
        $userService = $this->createMock(UserService::class);
        if (!empty($methods)) {
            foreach ($methods as $key => $value) {
                $userService->expects($this->any())
                    ->method($key)
                    ->willReturn($value);
            }
        }

        return new UserDomain(
            $userService
        );
    }

    public function testFetchUserInfo()
    {
        $methodsToMock = [
            'fetchUserInfo' => ['admin_id' => 1],
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertNotEmpty($obj->fetchUserInfo('admin'));
    }
}
