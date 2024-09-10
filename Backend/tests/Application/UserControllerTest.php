<?php

namespace App\Tests\Application;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\UserFixtures;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class UserControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    protected $client;

    /** @var JWTEncoderInterface */
    protected $jwtEncoder;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = $this->client->getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            UserFixtures::class
        ]);
        $this->jwtEncoder = $this->client->getContainer()->get(JWTEncoderInterface::class);
    }

    public function testRegisterUserSuccess(): void
    {
        $this->client->request('POST', '/api/register', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'name' => 'Luis',
            'surname' => 'Fernández',
            'email' => 'luis@luis.com',
            'password' => 'patata'
        ]));

        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('User registered successfully', $responseData['message']);
    }

    public function testRegisterUserThatAlreadyExists(): void
    {
        $this->client->request('POST', '/api/register', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'name' => 'Luis',
            'surname' => 'Fernández',
            'email' => 'email@email.com',
            'password' => 'patata'
        ]));

        $this->assertResponseStatusCodeSame(409);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('User with this mail already exists', $responseData['error']);
    }

    public function testLoginUserSuccess(): void
    {
        $this->client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'email@email.com',
            'password' => 'password1'
        ]));

        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('User logged', $responseData['message']);
    }

    public function testLoginUserFailPassword(): void
    {
        $this->client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'email@email.com',
            'password' => 'password'
        ]));

        $this->assertResponseStatusCodeSame(402);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Invalid password', $responseData['error']);
    }

    public function testLoginUserFailEmail(): void
    {
        $this->client->request('POST', '/api/login', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'email' => 'lala@lala.com',
            'password' => 'password1'
        ]));

        $this->assertResponseStatusCodeSame(401);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('User not found', $responseData['error']);
    }
}