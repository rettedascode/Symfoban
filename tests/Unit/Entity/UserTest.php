<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setName('Test User');
        $user->setPassword('hashed_password');

        $this->assertNull($user->getId());
        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('Test User', $user->getName());
        $this->assertSame('hashed_password', $user->getPassword());
    }

    public function testUserRoles(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        // Default role should be ROLE_USER
        $this->assertContains('ROLE_USER', $user->getRoles());

        // Add admin role
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertSame('test@example.com', $user->getUserIdentifier());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->setEmail('test@example.com');

        // This method should exist and not throw an error
        $user->eraseCredentials();
        $this->assertTrue(true); // If we get here, the method works
    }
}

