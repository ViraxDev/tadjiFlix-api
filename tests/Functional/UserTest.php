<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\DisableBootKernelTrait;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;

final class UserTest extends ApiTestCase
{
    use Factories, DisableBootKernelTrait;

    public function testCreateUser(): void
    {
        $client = static::createClient();
        $factory = Factory::create();

        $client->request('POST', '/api/users/register', [
            'json' => [
                'email' => $email = $factory->unique()->email(),
                'plainPassword' => $factory->unique()->password(),
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            '@context' => '/contexts/User',
            '@type' => 'User',
            'email' => $email,
        ]);
    }

    public function testCreateUserTokenSuccessfully(): void
    {
        $client = static::createClient();
        $factory = Factory::create();

        $client->request('POST', '/api/users/register', [
            'json' => [
                'email' => $email = $factory->unique()->email(),
                'plainPassword' => $password = $factory->unique()->password(),
            ],
        ]);

        $response = $client->request('POST', '/api/login', [
            'json' => [
                'username' => $email,
                'password' => $password,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $response->toArray());
    }

    public function testCreateUserTokenWithError(): void
    {
        $client = static::createClient();
        $factory = Factory::create();

        $client->request('POST', '/api/users/register', [
            'json' => [
                'email' => $email = $factory->unique()->email(),
                'plainPassword' => $factory->unique()->password(),
            ],
        ]);

        $response = $client->request('POST', '/api/login', [
            'json' => [
                'username' => $email,
                'password' => 'teddst',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertSame('Invalid credentials.', $response->toArray(false)['message']);
    }
}
