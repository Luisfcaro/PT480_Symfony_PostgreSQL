<?php

namespace App\Controller;

use App\Entity\Wine;
use App\Repository\WineRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;

class WineController extends AbstractController
{

    private $wineRepository;
    private $entityManager;

    public function __construct(

        WineRepository $wineRepository,
        EntityManagerInterface $entityManager,

    ){

        $this->wineRepository = $wineRepository;
        $this->entityManager = $entityManager;

    }

    #[Route('api/wine', name: 'create_wine', methods: ['POST'])]
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
                            example: "Missing fields: name, year"
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
    public function create_wine(Request $request): JsonResponse
    {
        try {
            $wine_data = json_decode($request->getContent(), true);

            $requiredFields = ['name', 'year'];
            $missingFields = [];

            foreach ($requiredFields as $field) {
                if (!isset($wine_data[$field]) || $wine_data[$field] == "" || $wine_data[$field] === null) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                throw new \Exception('Missing fields: ' . implode(', ', $missingFields), 400);
            }

            $wine_exist = $this->wineRepository->findOneBy(['name' => $wine_data['name'], 'year' => $wine_data['year']]);

            if($wine_exist){
                throw new \Exception("There already exits a wine with that name and year of production", 409);
                
            }

            $wine = new Wine();
            $wine->setName($wine_data['name']);
            $wine->setYear($wine_data['year']);
            $this->entityManager->persist($wine);
            $this->entityManager->flush();

            return new JsonResponse([
                'message' => 'Wine created successfully',
                'wine' => $wine->toArray()
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }
}