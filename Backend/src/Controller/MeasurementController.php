<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Repository\MeasurementRepository;
use App\Repository\SensorRepository;
use App\Repository\WineRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;

class MeasurementController extends AbstractController
{

    private $measurementRepository;
    private $sensorRepository;
    private $wineRepository;
    private $entityManager;

    public function __construct(

        MeasurementRepository $measurementRepository,
        SensorRepository $sensorRepository,
        WineRepository $wineRepository,
        EntityManagerInterface $entityManager,

    ){

        $this->measurementRepository = $measurementRepository;
        $this->sensorRepository = $sensorRepository;
        $this->wineRepository = $wineRepository;
        $this->entityManager = $entityManager;

    }


    #[Route('api/measurement', name: 'create_measurement', methods: ['POST'])]
    #[OA\Post(
        path: '/api/measurement',
        summary: 'Create a new measurement',
        tags: ["Measurement Management"],
        parameters: [
            new OA\Parameter(
                name: "Token",
                in: "header",
                description: "Authentication token required for access.",
                required: true,
                schema: new OA\Schema(
                    type: "string",
                    example: "your-authentication-token"
                )
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                required: ['year', 'sensor_id', 'wine_id', 'color', 'temperature', 'graduation', 'ph'],
                properties: [
                    new OA\Property(property: 'year', type: 'integer', example: 2023),
                    new OA\Property(property: 'sensor_id', type: 'integer', example: 1),
                    new OA\Property(property: 'wine_id', type: 'integer', example: 1),
                    new OA\Property(property: 'color', type: 'string', example: 'red'),
                    new OA\Property(property: 'temperature', type: 'number', format: 'float', example: 18.5),
                    new OA\Property(property: 'graduation', type: 'number', format: 'float', example: 13.5),
                    new OA\Property(property: 'ph', type: 'number', format: 'float', example: 3.5)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Measurement created successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Measurement created successfully'),
                        new OA\Property(
                            property: "sensor",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "id",
                                    type: "integer",
                                    example: 1
                                ),
                                new OA\Property(
                                    property: "year",
                                    type: "int",
                                    example: "2006"
                                ),
                                new OA\Property(
                                    property: "sensor_id",
                                    type: "int",
                                    example: "2"
                                ),
                                new OA\Property(
                                    property: "wine_id",
                                    type: "int",
                                    example: "1"
                                ),
                                new OA\Property(
                                    property: "color",
                                    type: "string",
                                    example: "red"
                                ),
                                new OA\Property(
                                    property: "temperature",
                                    type: "float",
                                    example: "29.0"
                                ),
                                new OA\Property(
                                    property: "graduation",
                                    type: "float",
                                    example: "2.2"
                                ),
                                new OA\Property(
                                    property: "ph",
                                    type: "float",
                                    example: "1.1"
                                )
                        ])
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Missing fields: year, sensor_id, wine_id, ... Invalid fields: ...')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Unauthorized access, token required')
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Year of measurement cant be before wine production year')
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Conflict',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'There already exists a measurement with that sensor, on that wine and that year')
                    ]
                )
            )
        ],
    )]
    public function create_measurement(Request $request): JsonResponse
    {
        try {
            $measurement_data = json_decode($request->getContent(), true);
            

            $requiredFields = ['year', 'sensor_id', 'wine_id', 'color', 'temperature', 'graduation', 'ph'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (!isset($measurement_data[$field]) || $measurement_data[$field] == "" || $measurement_data[$field] === null) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                throw new \Exception('Missing fields: ' . implode(', ', $missingFields), 400);
            }

            /* We make sure that int and float values are correctly passed to the endpoint */

            $year = $measurement_data['year'] ?? null;
            $sensorId = $measurement_data['sensor_id'] ?? null;
            $wineId = $measurement_data['wine_id'] ?? null;
            $temperature = $measurement_data['temperature'] ?? null;
            $graduation = $measurement_data['graduation'] ?? null;
            $ph = $measurement_data['ph'] ?? null;
            
            $errors = [];

            if ($year === null || !is_numeric($year)) {
                $errors[] = 'year has to be an int value';
            }
            if ($sensorId === null || !is_numeric($sensorId)) {
                $errors[] = 'sensor_id has to be an int value';
            }
            if ($wineId === null || !is_numeric($wineId)) {
                $errors[] = 'wine_id has to be an int value';
            }
            if ($temperature === null || !is_float($temperature)) {
                $errors[] = 'temperature has to be an float value';
            }
            if ($graduation === null || !is_float($graduation)) {
                $errors[] = 'graduation has to be an float value';
            }
            if ($ph === null || !is_float($ph)) {
                $errors[] = 'ph has to be an float value';
            }

            if (!empty($errors)) {
                throw new \Exception('Invalid fields: ' . implode(', ', $errors), 400);
            }

            $year  = intval($measurement_data['year']);
            $temperature = floatval($measurement_data['temperature']);
            $graduation = floatval($measurement_data['graduation']);
            $ph = floatval($measurement_data['ph']);

            /* We make sure that the sensor and the wine we are referencing exist */

            $sensor = $this->sensorRepository->findOneBy(['id' => $sensorId]);

            if (!$sensor) {
                throw new \Exception('Sensor referenced does not exist', 409);
            }

            $wine = $this->wineRepository->findOneBy(['id' => $wineId]);

            if (!$wine) {
                throw new \Exception('Wine referenced does not exist', 409);
            }

            /* We make sure that the year of the measurement isn't before the year of the wine */

            if($year < $wine->getYear()){
                throw new \Exception('Year of measurement cant be before wine production year', 403);
            }

            /* Then we make sure that theres no other measuremente with the same values */

            $measurement_exist = $this->measurementRepository->findOneBy(['sensor_id' => $sensor, 'wine_id' => $wine, 'year' => $year]);

            if($measurement_exist){
                throw new \Exception("There already exits a measurement with that sensor, on that wine and that year", 409);
            }

            $measurement = new Measurement();
            $measurement->setYear($year);
            $measurement->setSensorId($sensor);
            $measurement->setWineId($wine);
            $measurement->setColor($measurement_data['color']);
            $measurement->setTemperature($temperature);
            $measurement->setGraduation($graduation);
            $measurement->setPh($ph);

            $this->entityManager->persist($measurement);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Measurement created successfully',
                'measurement' => $measurement->toArray()
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
