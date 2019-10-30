<?php
namespace Tests\App\Security;

use App\Security\User;
use App\Security\UserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use stdClass;

/**
 * Class UserProviderTest
 */
class UserProviderTest extends TestCase
{
    private function getObject()
    {
        return new UserProvider;
    }

    private function getUserObject()
    {
        return new User('admin', 'test', 'salt', ['ROLE_ADMIN']);
    }

    public function testValidLoadUserByUsernameForAdmin()
    {
        $obj = $this->getObject();
        $user = $obj->loadUserByUsername('admin');
        $this->assertInstanceOf(User::class, $user);
    }

    public function testValidLoadUserByUsernameForCustomer()
    {
        $obj = $this->getObject();
        $user = $obj->loadUserByUsername('customer');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($user->getRoles()[0], 'ROLE_USER');
    }

    public function testInValidLoadUserByUsername()
    {
        $obj = $this->getObject();
        $this->expectException(UsernameNotFoundException::class);
        $user = $obj->loadUserByUsername();
    }

    public function testValidRefreshUser()
    {
        $obj = $this->getObject();
        $user = $obj->refreshUser($this->getUserObject());
        $this->assertInstanceOf(User::class, $user);
    }

    public function testInValidRefreshUserInstance()
    {
        $user = $this->getMockBuilder(UserInterface::class)
            ->setMockClassName('UserInterface')
            ->getMock();
        $obj = $this->getObject();
        $this->expectException(UnsupportedUserException::class);
        $user = $obj->refreshUser($user);
    }

    public function testValidSupportsClassMethod()
    {
        $obj = $this->getObject();
        $this->assertTrue($obj->supportsClass(User::class));
    }

    public function testInValidSupportsClassMethod()
    {
        $obj = $this->getObject();
        $this->assertFalse($obj->supportsClass(stdClass::class));
    }
}
