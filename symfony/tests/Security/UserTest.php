<?php
namespace Tests\App\Security;

use App\Security\User;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class UserTest
 */
class UserTest extends TestCase
{
    private function getObject()
    {
        return new User('admin', 'test', 'salt', ['ROLE_ADMIN']);
    }

    private function getObjectWithWrongData($field)
    {
        switch ($field) {
            case 'username':
                return new User('wrong_admin', 'test', 'salt', ['ROLE_ADMIN']);
                break;

            case 'password':
                return new User('admin', 'wrong_password', 'salt', ['ROLE_ADMIN']);
                break;

            case 'salt':
                return new User('admin', 'test', 'wrong_salt', ['ROLE_ADMIN']);
                break;

            default:
                return;
                break;
        }
    }

    public function testGetUserName()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getUsername(), 'admin');
    }

    public function testGetPassword()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getPassword(), 'test');
    }

    public function testGetSalt()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getSalt(), 'salt');
    }

    public function testGetRoles()
    {
        $obj = $this->getObject();
        $this->assertEquals($obj->getRoles(), ['ROLE_ADMIN']);
    }

    public function testValidCredentials()
    {
        $obj = $this->getObject();
        $this->assertTrue($obj->isEqualTo($obj));
    }

    public function testInValidUsername()
    {
        $obj = $this->getObject();
        $wrongObj = $this->getObjectWithWrongData('username');
        $this->assertFalse($obj->isEqualTo($wrongObj));
    }

    public function testInValidPassword()
    {
        $obj = $this->getObject();
        $wrongObj = $this->getObjectWithWrongData('password');
        $this->assertFalse($obj->isEqualTo($wrongObj));
    }

    public function testInValidSalt()
    {
        $obj = $this->getObject();
        $wrongObj = $this->getObjectWithWrongData('salt');
        $this->assertFalse($obj->isEqualTo($wrongObj));
    }

    // not implemented as of now
    public function testEraseCredentials()
    {
        $obj = $this->getObject();
        $this->assertInstanceOf(User::class, $obj->eraseCredentials());
    }
}
