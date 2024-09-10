<?php

namespace App\Tests\Application;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\MeasurementFixtures;
use App\DataFixtures\UserFixtures;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class MeasurementControllerTest extends WebTestCase
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
            MeasurementFixtures::class,
            UserFixtures::class
        ]);
        $this->jwtEncoder = $this->client->getContainer()->get(JWTEncoderInterface::class);
    }

    public function testCreateMeasurementSuccessfully(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('POST', '/api/measurement', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'year' => 2023,
            'sensor_id' => 1,
            'wine_id' => 1,
            'color' => 'red',
            'temperature' => 28.5,
            'graduation' => 12.5,
            'ph' => 3.5
        ]));

        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Measurement created successfully', $responseData['message']);
    }

    public function testCreateMeasurementMissingFields(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('POST', '/api/measurement', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'year' => 2023,
            'sensor_id' => 1,
            // There's no 'wine_id'
            'color' => 'red',
            'temperature' => 28.5,
            'graduation' => 12.5,
            'ph' => 3.5
        ]));

        $this->assertResponseStatusCodeSame(400);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertStringContainsString('Validation failed', $responseData['error']);
    }

    public function testCreateMeasurementConflict(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('POST', '/api/measurement', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'year' => 2021,
            'sensor_id' => 1,
            'wine_id' => 1,
            'color' => 'red',
            'temperature' => 28.5,
            'graduation' => 12.5,
            'ph' => 3.5
        ]));

        $this->assertResponseStatusCodeSame(409);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('There already exits a measurement with that sensor, on that wine and that year', $responseData['error']);
    }

    public function testCreateMeasurementWineNotExist(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('POST', '/api/measurement', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'year' => 2021,
            'sensor_id' => 1,
            'wine_id' => 7,
            'color' => 'red',
            'temperature' => 28.5,
            'graduation' => 12.5,
            'ph' => 3.5
        ]));

        $this->assertResponseStatusCodeSame(409);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Wine referenced does not exists', $responseData['error']);
    }

    public function testCreateMeasurementSensorNotExist(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('POST', '/api/measurement', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'year' => 2021,
            'sensor_id' => 7,
            'wine_id' => 1,
            'color' => 'red',
            'temperature' => 28.5,
            'graduation' => 12.5,
            'ph' => 3.5
        ]));

        $this->assertResponseStatusCodeSame(409);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Sensor referenced does not exists', $responseData['error']);
    }

    public function testCreateMeasurementConflictWineProductionYear(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('POST', '/api/measurement', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'year' => 1900,
            'sensor_id' => 1,
            'wine_id' => 1,
            'color' => 'red',
            'temperature' => 28.5,
            'graduation' => 12.5,
            'ph' => 3.5
        ]));

        $this->assertResponseStatusCodeSame(403);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Year of measurement cant be before wine production year', $responseData['error']);
    }
}