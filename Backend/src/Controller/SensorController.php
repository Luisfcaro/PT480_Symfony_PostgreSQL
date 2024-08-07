<?php

namespace App\Controller;

use App\Entity\Sensor;
use App\Repository\SensorRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;

class SensorController extends AbstractController
{

    private $sensorRepository;
    private $entityManager;

    public function __construct(

        SensorRepository $sensorRepository,
        EntityManagerInterface $entityManager,

    ){

        $this->sensorRepository = $sensorRepository;
        $this->entityManager = $entityManager;

    }

    #[Route('api/sensor', name: 'create_sensor', methods: ['POST'])]
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
                response: 401,
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
    public function create_sensor(Request $request): JsonResponse {

        try {
            $sensor_data = json_decode($request->getContent(), true);

            if (!isset($sensor_data['name']) || ($sensor_data['name'] == null) || ($sensor_data['name'] == "")){
                throw new \Exception('The sensor needs a name');
            }

            $sensor_exist = $this->sensorRepository->findOneBy(['name' => $sensor_data['name']]);

            if ($sensor_exist) {
                throw new \Exception("Sensor with that name already exist", 401);
            }

            $sensor = new Sensor();
            $sensor->setName($sensor_data['name']);

            $this->entityManager->persist($sensor);
            $this->entityManager->flush();


            return new JsonResponse([

                'message' => 'Sensor created successfully',
                'sensor' => $sensor->toArray()

            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }

    }

    #[Route('api/sensors_name', name: 'find_sensors_by_name', methods: ['GET'])]
    #[OA\Get(
        path: "/api/sensors_name",
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
    public function find_sensors_by_name(Request $request): JsonResponse {

        try {

            /* JsonDecode can intrepet 0 and 1 as null, for that reason, we get the query param as an string */
            $order = $request->query->get('order');

            /* We make sure that order is not null, and that the value provided on the string is a number */
            if($order === null || !is_numeric($order)) {
                throw new \Exception("Order needs to be specified on query", 400);
            }

            /* We convert the string value to an int value */
            $order = intval($order);

            /* Verifies that order is setted to 0 or 1 */
            if(($order !== 0) && ($order !== 1)){
                throw new \Exception("Order needs to be 0 or 1", 401);
            }

            $sensors = $this->sensorRepository->find_sensors_by_name($order);
            $data = [];

            foreach($sensors as $sensor){
                $data[] = $sensor->toArray();
            }
    
            return new JsonResponse([
                'sensors' => $data
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }

    }
}
