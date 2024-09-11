<?php

namespace App\Controller;

use App\DTO\Measurement\CreateMeasurementDTO;
use App\Service\Measurement\MeasurementServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class MeasurementController extends AbstractController
{
    private $measurementService;
    private $measurementSerializer;

    public function __construct
    (
        MeasurementServiceInterface $measurementService,
        SerializerInterface $measurementSerializer,
    ){
        $this->measurementService = $measurementService;
        $this->measurementSerializer = $measurementSerializer;
    }


    #[Route('api/measurement', name: 'createMeasurement', methods: ['POST'])]
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
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Measurement created successfully'
                        ),
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
                                    type: "object",
                                    properties: [
                                        new OA\Property(
                                            property: "id",
                                            type: "int",
                                            example: "3"
                                        ),
                                        new OA\Property(
                                            property: "name",
                                            type: "string",
                                            example: "Termperature Sensor"
                                        ),
                                    ]
                                ),
                                new OA\Property(
                                    property: "wine_id",
                                    type: "object",
                                    properties: [
                                        new OA\Property(
                                            property: "id",
                                            type: "int",
                                            example: "6"
                                        ),
                                        new OA\Property(
                                            property: "name",
                                            type: "string",
                                            example: "The Second"
                                        ),
                                        new OA\Property(
                                            property: "year",
                                            type: "int",
                                            example: "2004"
                                        ),
                                    ]
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
                        new OA\Property(property: 'error', type: 'string', example: 'Validation Failed: Field [year] ..')
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
    public function createMeasurement(Request $request): JsonResponse
    {
        try {
            $measurementData = json_decode($request->getContent(), true);

            $createMeasurementDTO = new CreateMeasurementDTO();
            $createMeasurementDTO->setYear($measurementData['year'] ?? null);
            $createMeasurementDTO->setSensorId($measurementData['sensor_id'] ?? null);
            $createMeasurementDTO->setWineId($measurementData['wine_id'] ?? null);
            $createMeasurementDTO->setColor($measurementData['color'] ?? null);
            $createMeasurementDTO->setTemperature($measurementData['temperature'] ?? null);
            $createMeasurementDTO->setGraduation($measurementData['graduation'] ?? null);
            $createMeasurementDTO->setPh($measurementData['ph'] ?? null);

            $measurement = $this->measurementService->createMeasurement($createMeasurementDTO);

            $serializedMeasurement = json_decode($this->measurementSerializer->serialize($measurement, 'json'));

            return new JsonResponse([
                'message' => 'Measurement created successfully',
                'measurement' => $serializedMeasurement
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
