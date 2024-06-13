<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterSuccess()
    {
        // Create a Symfony test client
        $client = static::createClient();

        // Mock UserRepository to simulate no existing user
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('findExistingUser')->willReturn([]);

        // Mock UserPasswordHasherInterface to simulate password hashing
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasher->method('hashPassword')->willReturn('hashedpassword');

        // Replace services in the container with mocks
        $container = static::getContainer();
        $container->set('App\Repository\UserRepository', $userRepository);
        $container->set('Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface', $passwordHasher);

        // Prepare request payload
        $payload = json_encode([
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'password123'
        ]);

        // Make POST request to register endpoint
        $client->request('POST', '/api/register', [], [], [], $payload);

        // Assert HTTP status code is 200 (OK)
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        // Assert response contains 'Registered Successfully' message
        $this->assertStringContainsString('Registered Successfully', $client->getResponse()->getContent());
    }
}
