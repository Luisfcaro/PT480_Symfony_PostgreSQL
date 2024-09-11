<?php

namespace App\Controller;

use App\Service\Wine\WineServiceInterface;
use App\DTO\Wine\CreateWineDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

class WineController extends AbstractController
{
    private $wineService;
    private $wineSerializer;

    public function __construct(
        WineServiceInterface $wineService,
        SerializerInterface $wineSerializer,
    ){
        $this->wineService = $wineService;
        $this->wineSerializer = $wineSerializer;
    }

    #[Route('api/wine', name: 'createWine', methods: ['POST'])]
    #[OA\Post(
        path: "/api/wine",
        summary: "Create a new wine",
        tags: ["Wine Management"],
        description: "Create a new wine entry. The request body must include 'name' and 'year' fields. The 'Token' header is required for authentication.",
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
            content: new OA\JsonContent(
                type: "object",
                required: ["name", "year"],
                properties: [
                    new OA\Property(
                        property: "name",
                        type: "string",
                        description: "Name of the wine.",
                        example: "Chardonnay"
                    ),
                    new OA\Property(
                        property: "year",
                        type: "integer",
                        description: "Year of production of the wine.",
                        example: 2021
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Wine created successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Wine created successfully"
                        ),
                        new OA\Property(
                            property: "wine",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "name",
                                    type: "string",
                                    example: "Chardonnay"
                                ),
                                new OA\Property(
                                    property: "year",
                                    type: "integer",
                                    example: 2021
                                )
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Bad request, missing fields or invalid input",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Validation Failed: Field [name] ..."
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: "Conflict",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "There already exists a wine with that name and year of production"
                        )
                    ]
                )
            )
        ]
    )]
    public function createWine(Request $request): JsonResponse
    {
        try {
            $wineData = json_decode($request->getContent(), true);

            $createWineDTO = new CreateWineDTO();
            $createWineDTO->setName($wineData['name'] ?? null);
            $createWineDTO->setYear($wineData['year'] ?? null);

            $wine = $this->wineService->createWine($createWineDTO);

            $serializedWine = json_decode($this->wineSerializer->serialize($wine, 'json'));

            return new JsonResponse([
                'message' => 'Wine created successfully',
                'wine' => $serializedWine
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }

    #[Route('api/winesWithMeasurements', name: 'getWinesWithMeasurements', methods: ['GET'])]
    #[OA\Get(
        path: "/api/winesWithMeasurements",
        summary: "Find all wines with its measurements",
        tags: ["Wine Management"],
        description: "Retrieves all wines and his measurements",
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
        responses: [
            new OA\Response(
                response: 200,
                description: "Wines Founded",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Wines founded"
                        ),
                        new OA\Property(
                            property: "wines",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "id",
                                    type: "int",
                                    example: "1"
                                ),
                                new OA\Property(
                                    property: "name",
                                    type: "string",
                                    example: "The one"
                                ),
                                new OA\Property(
                                    property: "year",
                                    type: "int",
                                    example: "2003"
                                ),
                                new OA\Property(
                                    property: "measurements",
                                    type: "object",
                                    properties: [
                                        new OA\Property(
                                            property: "id",
                                            type: "int",
                                            example: "2"
                                        ),
                                        new OA\Property(
                                            property: "year",
                                            type: "int",
                                            example: "2004"
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
                                        ),           new OA\Property(
                                            property: "color",
                                            type: "string",
                                            example: "red"
                                        ),           new OA\Property(
                                            property: "temperature",
                                            type: "float",
                                            example: "29.2"
                                        ),
                                        new OA\Property(
                                            property: "graduation",
                                            type: "float",
                                            example: "1.1"
                                        ),
                                        new OA\Property(
                                            property: "ph",
                                            type: "float",
                                            example: "0.3"
                                        ),
                                    ]
                                ),
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Something unexpected happenned"
                        )
                    ]
                )
            ),
        ]
    )]
    public function getWinesWithMeasurements(Request $request) : JsonResponse
    {
        try {
            $wines = $this->wineService->findAllWineWithITSMeasurements();

            $winesSerialized = json_decode($this->wineSerializer->serialize($wines, 'json'));

            return new JsonResponse([
                'message' => 'Wines founded',
                'wines' => $winesSerialized
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
