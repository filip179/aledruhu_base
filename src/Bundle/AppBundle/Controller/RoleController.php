<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\AdminRoleManager;
use AppBundle\Entity\Role;
use AppBundle\Form\Handler\RoleHandler;
use AppBundle\Form\Type\RoleType;
use AppBundle\Util\DatatableDataProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/roles")
 */
class RoleController extends BaseController {
    /**
     * @Route("/list", name="role_list")
     * @Method("GET")
     */
    public function listAction()
    {
        return $this->render('AppBundle::controller/role/list.html.twig');
    }

    /**
     * @Route("/datatable", name="role_datatable")
     * @Method("POST")
     */
    public function datatableAction(
        AdminRoleManager $roleManager,
        DatatableDataProvider $datatableDataProvider,
        Request $request
    )
    {
        $filters = [
            'name' => ($request->get('name')) ?? null,
        ];

        $query = $roleManager->getRolesQuery($filters);
        $recordsTotal = $roleManager->getRolesCount();

        $actions = [
            'edit' => [
                'route' => 'role_edit',
                'params' => [
                    'key' => 'role',
                    'value' => 'id',
                ],
            ],
            'delete' => [
                'route' => 'role_delete',
                'params' => [
                    'key' => 'role',
                    'value' => 'id',
                ],
            ],
        ];

        $fields = ['id', 'name', 'createdAt', 'updatedAt'];

        $data = $datatableDataProvider->getData($query, $recordsTotal, $actions, $fields);

        return new JsonResponse($data);
    }

    /**
     * @Route("/create", name="role_create")
     * @Method({"GET"})
     */
    public function createAction(RoleHandler $roleHandler)
    {
        $form = $roleHandler->buildForm(RoleType::class, new Role())->getFormView();

        return $this->render('AppBundle::controller/role/create.html.twig', ['form' => $form]);
    }

    /**
     * @Route("/edit/{role}", name="role_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Role $role, RoleHandler $roleHandler, Request $request)
    {
        $form = $roleHandler->buildForm(RoleType::class, $role)->getFormView();

        if ($request->isMethod('GET')) {
            return $this->render(
                'AppBundle::controller/role/edit.html.twig',
                [
                    'form' => $form,
                    'entity' => $role,
                ]
            );
        }
        $this->addFlashMessage('success', 'role.edit.successful');

        return $this->redirectToRoute("role_list");
    }

    /**
     * @Route("/delete/{role}", name="role_delete")
     * @Method("POST")
     */
    public function deleteAction(Role $role, AdminRoleManager $roleManager)
    {
        $roleManager->removeRole($role);
        $this->addFlashMessage('success', 'role.delete.successful');

        return $this->redirectToRoute("role_list");
    }

    /**
     * @Route("/postCreate", name="role_post_create")
     * @Method({"POST"})
     */
    public function postCreateAction(RoleHandler $roleHandler)
    {
        $createHandler = $roleHandler->buildForm(RoleType::class, new Role());

        if (!$createHandler->process()) {
            $this->addFormErrors($createHandler->getErrors());

            return $this->redirectToRoute('role_create');
        }

        $this->addFlashMessage('success', 'role.create.successful');

        return $this->redirectToRoute("role_list");
    }

    /**
     * @Route("/postEdit/{role}", name="role_post_edit")
     * @Method({"POST"})
     */
    public function postEditAction(Role $role, RoleHandler $roleHandler)
    {
        $editHandler = $roleHandler->buildForm(RoleType::class, $role);

        if (!$editHandler->process()) {
            $this->addFormErrors($editHandler->getErrors());

            return $this->redirectToRoute(
                "role_edit",
                [
                    'role' => $role->getId(),
                ]
            );
        }

        $this->addFlashMessage('success', 'role.edit.successful');

        return $this->redirectToRoute("role_list");
    }
}
