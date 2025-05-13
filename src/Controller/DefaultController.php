<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->redirectToRoute('dashboard');
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboardIndex(): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'Dashboard',
        ]);
    }

    #[Route('/test/connect', name: 'test_connect', options: ['expose' => true], methods: ['POST'])]
    public function testConnect(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse($data);

        $response->setEncodingOptions(JSON_PRETTY_PRINT);

        return $response;
    }
}
