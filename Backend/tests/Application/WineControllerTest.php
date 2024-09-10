<?php

namespace App\Tests\Application;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\WineFixtures;
use App\DataFixtures\UserFixtures;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

class WineControllerTest extends WebTestCase
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
            WineFixtures::class,
            UserFixtures::class
        ]);
        $this->jwtEncoder = $this->client->getContainer()->get(JWTEncoderInterface::class);
    }

    public function testCreateWineSuccessfully(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);
 
        $this->client->request('POST', '/api/wine', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'name' => 'Granadino',
            'year' => 2021
        ]));

        $this->assertResponseStatusCodeSame(201);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Wine created successfully', $responseData['message']);
    }

    public function testCreateWineConflict(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);
 
        $this->client->request('POST', '/api/wine', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ], json_encode([
            'name' => 'The First Wine',
            'year' => 2020
        ]));
 
        $this->assertResponseStatusCodeSame(409);
 
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('There already exists a wine with that name and year of production', $responseData['error']);
    }

    public function testGetWinesWithMeasurementsSuccessfully(): void
    {
        $token = $this->jwtEncoder->encode([
            'email' => 'email@email.com',
            'exp' => time() + (60 * 60),
        ]);

        $this->client->request('GET', '/api/winesWithMeasurements', [], [], [
            'HTTP_Token' => $token,
            'CONTENT_TYPE' => 'application/json'
        ]);

        $this->assertResponseStatusCodeSame(200);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Wines founded', $responseData['message']);
    }
}