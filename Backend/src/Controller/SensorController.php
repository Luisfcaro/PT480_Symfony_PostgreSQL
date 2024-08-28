<?php

namespace App\Controller;

use App\Service\Sensor\SensorServiceInterface;
use App\DTO\Sensor\CreateSensorDTO;
use App\DTO\Sensor\GetAllSensorByNameDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;

class SensorController extends AbstractController
{
    private $sensorService;
    private $sensorSerializer;
    private $entityManager;

    public function __construct(
        SensorServiceInterface $sensorService,
        SerializerInterface $sensorSerializer,
        EntityManagerInterface $entityManager,
    )
    {
        $this->sensorService = $sensorService;
        $this->sensorSerializer = $sensorSerializer;
        $this->entityManager = $entityManager;
    }

    #[Route('api/sensor', name: 'createSensor', methods: ['POST'])]
    #[OA\Post(
        path: "/api/sensor",
        summary: "Create a new sensor",
        tags: ["Sensor Management"],
        description: "This endpoint allows you to create a new sensor by providing a name for it. If a sensor with the given name already exists, it will return an error.",
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
            description: "Sensor data",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(
                        property: "name",
                        type: "string",
                        description: "Name of the sensor",
                        example: "Temperature Sensor"
                    )
                ],
                required: ["name"]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Sensor created successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Sensor created successfully"
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
                                    property: "name",
                                    type: "string",
                                    example: "Temperature Sensor"
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Bad request, invalid input",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "The sensor needs a name"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: "Conflict, sensor with the same name already exists",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Sensor with that name already exists"
                        )
                    ]
                )
            )
        ]
    )]
    public function createSensor(Request $request): JsonResponse
    {
        try {
            $sensorData = json_decode($request->getContent(), true);

            $createSensorDTO = new CreateSensorDTO();
            $createSensorDTO->setName($sensorData['name']);

            $sensor = $this->sensorService->createSensor($createSensorDTO);

            $serializedSensor = json_decode($this->sensorSerializer->serialize($sensor, 'json'));

            return new JsonResponse([
                'message' => 'Sensor created successfully',
                'sensor' => $serializedSensor
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }

    }

    #[Route('api/allSensorByName', name: 'allSensorByName', methods: ['GET'])]
    #[OA\Get(
        path: "/api/allSensorByName",
        summary: "Find sensors by name with sorting",
        tags: ["Sensor Management"],
        description: "Retrieve a list of sensors ordered by name. The 'order' query parameter specifies the sorting order. Set 'order' to 0 for ascending and to 1 for descending. The 'Token' header is required for authentication.",
        parameters: [
            new OA\Parameter(
                name: "order",
                in: "query",
                description: "Sorting order for sensors. Set to 0 for ascending or 1 for descending.",
                required: true,
                schema: new OA\Schema(
                    type: "integer",
                    example: 0
                )
            ),
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
        responses: [
            new OA\Response(
                response: 200,
                description: "List of sensors retrieved successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "sensors",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(
                                        property: "id",
                                        type: "integer",
                                        example: 1
                                    ),
                                    new OA\Property(
                                        property: "name",
                                        type: "string",
                                        example: "Temperature Sensor"
                                    )
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Bad request, invalid input",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Order needs to be specified on query"
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid query parameter value",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Order needs to be 0 or 1"
                        )
                    ]
                )
            )
        ]
    )]
    public function getAllSensorByName(Request $request): JsonResponse
    {
        try {
            /* JsonDecode can intrepet 0 and 1 as null, for that reason, we get the query param as an string */
            $orderData = $request->query->get('order');

            if ($orderData === null) {
                throw new \Exception("Order param needs to be specified", 401);
            }

            $getAllSensorByNameDTO = new GetAllSensorByNameDTO();
            $getAllSensorByNameDTO->setOrder($orderData);

            $sensors = $this->sensorService->getAllSensorByName($getAllSensorByNameDTO);

            $sensorsSerialized = [];

            foreach($sensors as $sensor){
                $sensorsSerialized[] = json_decode($this->sensorSerializer->serialize($sensor, 'json'));
            }
    
            return new JsonResponse([
                'sensors' => $sensorsSerialized
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
