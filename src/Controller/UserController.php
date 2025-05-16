<?php

namespace App\Controller;

use App\Document\User;
use App\Form\UserType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/list', name: 'user_list', methods: ['GET'])]
    public function index(): Response
    {
        $form = $this->createForm(UserType::class);

        return $this->render('user/user.list.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list/tbl', name: 'user_list_tbl', options: ['expose' => true], methods: ['GET'])]
    public function userListTblAction(Request $request): JsonResponse
    {
        $get = $request->query->all();

        $output = [];

        $users = $this->dm->getRepository(User::class)->gerUsers($get);

        foreach ($users['data'] as $user) {
            $output['data'][] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getNombre(),
                'username' => $user->getUsername(),
            ];
        }

        $output['count'] = $users['count'];

        $response = new JsonResponse($output);
        $response->setEncodingOptions(JSON_PRETTY_PRINT);

        return $response;
    }
}