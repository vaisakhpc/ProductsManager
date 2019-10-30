<?php
namespace Tests\App\Service;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;

/**
 * Class UserServiceTest
 */
class UserServiceTest extends TestCase
{
    private function getObject($methods)
    {
        $adminRepository = $this->createMock(AdminRepository::class);
        if (!empty($methods)) {
            foreach ($methods as $key => $value) {
                $adminRepository->expects($this->any())
                    ->method($key)
                    ->willReturn($value);
            }
        }

        return new UserService($adminRepository);
    }

    private function getAdminEntity()
    {
        $admin = new Admin;
        $admin->setName('admin');
        $admin->setActive(true);
        return $admin;
    }

    public function testFetchUserInfo()
    {
        $methodsToMock = [
            'fetchUserInfoFromUsername' => $this->getAdminEntity(),
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertNotEmpty($obj->fetchUserInfo('admin'));
    }

    public function testNonUser()
    {
        $methodsToMock = [
            'fetchUserInfoFromUsername' => null,
        ];
        $obj = $this->getObject($methodsToMock);
        $this->assertEmpty($obj->fetchUserInfo('admin'));
    }
}
