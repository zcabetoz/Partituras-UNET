<?php

namespace App\Controller;

use App\Document\UserGroup;
use App\Document\UserRole;
use App\Form\UserGroupType;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/user/group')]
class UserGroupController extends AbstractController
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    #[Route('/list', name: 'security_list_groups')]
    public function listAction(): Response
    {
        $form = $this->createForm(UserGroupType::class);

        return $this->render('security/groups/group.list.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/listDT', name: 'security_list_groups_tbl', options: ['expose' => true], methods: ['GET'])]
    public function groupsListDTAction(): JsonResponse
    {
        $groups = $this->dm->getRepository(UserGroup::class)->findAll();

        $data = array_map(fn(UserGroup $grupo) => $grupo->toArray(), $groups);

        return new JsonResponse($data);
    }

    /**
     * @throws MappingException|LockException
     */
    #[Route('/get/roles/from/group', name: 'security_get_roles_from_group', options: ['expose' => true], methods: ['GET'])]
    public function getRolesFromGroupAction(Request $request): JsonResponse
    {
        $groupId = $request->query->get('id');
        $exclude = $request->query->get('exclude');

        $group = $this->dm->getRepository(UserGroup::class)->find($groupId);

        $rolesFromGroup = $this->dm->getRepository(UserRole::class)->getRolesFromGroup($group->getRoles(), $exclude);

        $data = [];
        foreach ($rolesFromGroup as $role) {
            $data[] = $role->toArray();
        }

        return new JsonResponse($data);
    }

    /**
     * @throws MappingException|MongoDBException|Throwable
     */
    #[Route('/register/group', name: 'security_register_group', options: ['expose' => true], methods: ['POST'])]
    public function registerGroupAction(Request $request): JsonResponse
    {
        $get = json_decode($request->getContent(), true);
        $group = $get['id'] ? $this->dm->getRepository(UserGroup::class)->find($get['id']) : new UserGroup('');

        if (!$get['id']) {
            $isGroup = $this->dm->getRepository(UserGroup::class)->findOneBy(['name' => $get['nameGroup']]);
            if ($isGroup) {
                return new JsonResponse(['message' => 'Ya existe un rol con este nombre', 'status' => 'error']);
            }
        }

        $group->setName($get['nameGroup']);
        $this->dm->persist($group);
        $this->dm->flush();

        $message = 'Grupo ' . ($get['id'] ? 'actualizado' : 'creado') . ' con exito';

        return new JsonResponse(['message' => $message, 'status' => 'success']);
    }

    /**
     * @throws MappingException|MongoDBException|Throwable
     */
    #[Route('/delete', name: 'security_delete_group', options: ['expose' => true], methods: ['DELETE'])]
    public function deleteGroupAction(Request $request): JsonResponse
    {
        $get = json_decode($request->getContent(), true);
        $role = $this->dm->getRepository(UserGroup::class)->find($get['id']);

        $this->dm->remove($role);
        $this->dm->flush();

        return new JsonResponse(['status' => 'ok']);
    }

    /**
     * @throws MappingException|MongoDBException|Throwable|LockException
     */
    #[Route('/check/role/group', name: 'security_check_role_group', options: ['expose' => true], methods: ['POST'])]
    public function checkRoleGroupAction(Request $request): JsonResponse
    {
        $get = json_decode($request->getContent(), true);
        $group = $this->dm->getRepository(UserGroup::class)->find($get['idGroup']);
        $role = $this->dm->getRepository(UserRole::class)->find($get['idRole']);

        if ($group->hasRole($role->getRole())) {
            $group->removeRole($role->getRole());
        } else {
            $group->addRole($role->getRole());
        }

        $this->dm->persist($group);
        $this->dm->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}