<?php

namespace App\Controller;

use App\Document\User;
use App\Document\UserGroup;
use App\Form\UserEditType;
use App\Form\UserType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Nucleos\UserBundle\Model\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/user')]
final class UserController extends AbstractController
{
    private DocumentManager $dm;
    private UserManager $userManager;

    public function __construct(DocumentManager $dm, UserManager $userManager)
    {
        $this->dm = $dm;
        $this->userManager = $userManager;
    }

    #[Route('/list', name: 'user_list', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN_LISTAR_USUARIOS');

        $form = $this->createForm(UserType::class);
        $formEditUser = $this->createForm(UserEditType::class);

        return $this->render('user/user.list.html.twig', [
            'form' => $form->createView(),
            'formEditUser' => $formEditUser->createView()
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

    /**
     * @throws MappingException|LockException
     */
    #[Route('/register/user', name: 'user_register', options: ['expose' => true], methods: ['POST'])]
    public function userRegisterAction(Request $request): Response
    {
        $get = json_decode($request->getContent(), true);
        $id = $get['id'];

        if (!$id) {
            $isUserInUse = $this->userManager->findUserByUsername($get['username']);
            $isEmailInUse = $this->userManager->findUserByEmail($get['email']);

            if ($isUserInUse || $isEmailInUse) {
                return new JsonResponse([
                    'message' => 'El ' . ($isUserInUse ? 'nombre de usuario' : 'correo electrónico') . ' ya se encuentra registrado',
                    'status' => 'error'
                ]);
            }
        }


        /** @var User $user */
        $user = $id ? $this->dm->getRepository(User::class)->find($id) : new User();

        $user->setNombre($get['name']);
        $user->setEmail($get['email']);
        $user->setPlainPassword($get['password']);
        $user->setEnabled(true);

        if (!$id) {
            $user->setUsername($get['username']);
        }

        $this->userManager->updateUser($user);

        return new JsonResponse(['message' => 'Usuario registrado con éxito', 'status' => 'success']);
    }

    /**
     * @throws MappingException|MongoDBException|LockException|Throwable
     */
    #[Route('/delete/user', name: 'user_delete', options: ['expose' => true], methods: ['DELETE'])]
    public function userDeleteAction(Request $request): Response
    {
        $get = json_decode($request->getContent(), true);

        $user = $this->dm->getRepository(User::class)->find($get['id']);

        $this->dm->remove($user);
        $this->dm->flush();

        return new JsonResponse(['message' => 'Usuario eliminado con éxito', 'status' => 'success']);
    }
}