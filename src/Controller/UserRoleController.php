<?php

namespace App\Controller;

use App\Document\UserGroup;
use App\Document\UserRole;
use App\Form\UserRoleType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/security/role')]
class UserRoleController extends AbstractController
{

    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/list', name: 'security_list_roles')]
    public function addRoleAction(): Response
    {
        $form = $this->createForm(UserRoleType::class);

        return $this->render('security/roles/role.list.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list/tbl', name: 'security_list_roles_tbl', options: ['expose' => true], methods: ['POST'])]
    public function listRolesAction(Request $request): JsonResponse
    {
        $options = json_decode($request->getContent(), true);

        $roles = $this->dm->getRepository(UserRole::class)->getRoles($options);

        $output = [];

        foreach ($roles['data'] as $role) {
            $output['data'][] = [
                'id' => $role->getId(),
                'role' => $role->getRole(),
                'description' => $role->getDescription(),
            ];
        }

        $output['count'] = $roles['count'];

        $response = new JsonResponse($output);
        $response->setEncodingOptions(JSON_PRETTY_PRINT);

        return $response;
    }

    /**
     * @throws MappingException|MongoDBException|Throwable
     */
    #[Route('/delete', name: 'security_delete_role', options: ['expose' => true], methods: ['DELETE'])]
    public function deleteRoleAction(Request $request): JsonResponse
    {
        $get = json_decode($request->getContent(), true);
        $role = $this->dm->getRepository(UserRole::class)->find($get['id']);

        $groupsContainRole = $this->dm->getRepository(UserGroup::class)->findGroupByRole($role->getRole());

        foreach ($groupsContainRole as $group) {
            $group->removeRole($role->getRole());
            $this->dm->persist($group);
        }

        $this->dm->remove($role);
        $this->dm->flush();

        return new JsonResponse(['status' => 'ok']);
    }


    /**
     * @throws MappingException|MongoDBException|Throwable
     */
    #[Route('/register', name: 'security_register_role', options: ['expose' => true], methods: ['POST'])]
    public function registerRoleAction(Request $request): JsonResponse
    {
        $get = json_decode($request->getContent(), true);
        $role = $get['id'] ? $this->dm->getRepository(UserRole::class)->find($get['id']) : new UserRole();

        if (!$get['id']) {
            $isRole = $this->dm->getRepository(UserRole::class)->findOneBy(['role' => $get['role']]);
            if ($isRole) {
                return new JsonResponse(['message' => 'Ya existe un rol con este nombre', 'status' => 'error']);
            }
        }

        $role->setRole($get['role']);
        $role->setDescription($get['description']);

        $this->dm->persist($role);
        $this->dm->flush();
        return new JsonResponse(['message' => 'Rol ' . ($get['id'] ? 'actualizado' : 'registrado') . ' correctamente', 'status' => 'success']);
    }
}