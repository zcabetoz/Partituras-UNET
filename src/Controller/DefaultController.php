<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->redirectToRoute('dashboard');
    }

    #[Route('/dashboard', name: 'dashboard', options: ['expose' => true])]
    public function dashboardIndex(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'Dashboard',
        ]);
    }

    #[Route('/test/connect', name: 'test_connect', options: ['expose' => true])]
    public function testConnect(): JsonResponse
    {
        $data = ['response' => 'ok'];

        $response = new JsonResponse($data);

        $response->setEncodingOptions(JSON_PRETTY_PRINT);

        return $response;
    }
}
