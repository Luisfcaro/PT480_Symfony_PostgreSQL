<?php

namespace App\Tests\Application;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\SensorFixtures;
use App\DataFixtures\UserFixtures;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class SensorControllerTest extends WebTestCase
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
            SensorFixtures::class,
            UserFixtures::class
        ]);
        $this->jwtEncoder = $this->client->getContainer()->get(JWTEncoderInterface::class);
    }

    public function testCreateSensor(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('POST', '/api/sensor', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'name' => 'sensor nuevo'
        ]));

        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Sensor created successfully', $responseData['message']);
    }

    public function testCreateSensorWithDuplicateName(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('POST', '/api/sensor', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'name' => 'Test Sensor 1'
        ]));

        $this->assertResponseStatusCodeSame(409);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Sensor with that name already exists', $responseData['error']);
    }

    public function testCreateSensorWithoutToken(): void
    {
        $this->client->request('POST', '/api/sensor', [], [], [
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'name' => 'sensor nuevo'
        ]));

        $this->assertResponseStatusCodeSame(401);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('No token found', $responseData['error']);
    }

    public function testGetAllSensorByNameSuccesfull(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('GET', '/api/allSensorByName', [
            'order' => 0
        ], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ]);

        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Sensors founded', $responseData['message']);
    }

    public function testGetAllSensorByNameWithoutToken(): void
    {
        $this->client->request('GET', '/api/allSensorByName', [
            'order' => 0
        ], [], [
            'CONTENT_TYPE' => 'application/json'
        ]);

        $this->assertResponseStatusCodeSame(401);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('No token found', $responseData['error']);
    }
}