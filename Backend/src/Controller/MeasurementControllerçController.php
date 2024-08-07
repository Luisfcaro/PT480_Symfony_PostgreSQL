<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MeasurementControllerçController extends AbstractController
{
    #[Route('/measurement/controller/', name: 'app_measurement_controller_')]
    public function index(): Response
    {
        return $this->render('measurement_controllerç/index.html.twig', [
            'controller_name' => 'MeasurementControllerçController',
        ]);
    }
}
