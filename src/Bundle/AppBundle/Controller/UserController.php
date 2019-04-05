<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\AdminUserManager;
use AppBundle\Entity\User;
use AppBundle\Form\Handler\UserFormHandler;
use AppBundle\Form\Type\UserType;
use AppBundle\Util\DatatableDataProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/users")
 */
class UserController extends BaseController {
    /**
     * @Route("/list", name="user_list")
     * @Method("GET")
     */
    public function listAction()
    {
        return $this->render('AppBundle::controller/user/list.html.twig');
    }

    /**
     * @Route("/datatable", name="user_datatable")
     * @Method("POST")
     */
    public function datatableAction(
        AdminUserManager $userManager,
        DatatableDataProvider $datatableDataProvider,
        Request $request
    )
    {
        $filters = [
            'username' => ($request->get('username')) ?? null,
            'firstName' => ($request->get('firstName')) ?? null,
            'lastName' => ($request->get('lastName')) ?? null,
            'email' => ($request->get('email')) ?? null,
            'translatedRole' => ($request->get('translatedRole')) ?? null,
        ];

        $query = $userManager->getUsersQuery($filters);
        $recordsTotal = $userManager->getUsersCount();

        $actions = [
            'edit' => [
                'route' => 'user_edit',
                'params' => [
                    'key' => 'user',
                    'value' => 'id',
                ],
            ],
            'delete' => [
                'route' => 'user_delete',
                'params' => [
                    'key' => 'user',
                    'value' => 'id',
                ],
            ],
        ];

        $fields = ['id', 'username', 'firstName', 'lastName', 'email', 'role', 'createdAt', 'updatedAt'];

        $data = $datatableDataProvider->getData($query, $recordsTotal, $actions, $fields);

        return new JsonResponse($data);
    }

    /**
     * @Route("/create", name="user_create")
     * @Method({"GET"})
     */
    public function createAction(UserFormHandler $createUserFormHandler)
    {
        $form = $createUserFormHandler->buildForm(
            UserType::class,
            new User()
        )->getFormView();

        return $this->render("AppBundle::controller/user/create.html.twig", ['form' => $form]);
    }

    /**
     * @Route("/edit/{user}", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(User $user, UserFormHandler $createUserFormHandler, Request $request)
    {
        $form = $createUserFormHandler->buildForm(
            UserType::class,
            $user
        )->getFormView();

        if ($request->isMethod('GET')) {
            return $this->render(
                'AppBundle::controller/user/edit.html.twig',
                [
                    'form' => $form,
                    'entity' => $user,
                ]
            );
        }
        $this->addFlashMessage('success', 'user.edit.successful');

        return $this->redirectToRoute("user_list");
    }

    /**
     * @Route("/delete/{user}", name="user_delete")
     * @Method("POST")
     */
    public function deleteAction(User $user, AdminUserManager $userManager)
    {
        $userManager->removeUser($user);
        $this->addFlashMessage('success', 'user.delete.successful');

        return $this->redirectToRoute("user_list");
    }

    /**
     * @Route("/postCreate", name="user_post_create")
     * @Method({"POST"})
     */
    public function postCreateAction(UserFormHandler $createUserFormHandler)
    {
        $createHandler = $createUserFormHandler->buildForm(
            UserType::class,
            new User()
        );

        if (!$createHandler->process()) {
            $this->addFormErrors($createHandler->getErrors());

            return $this->redirectToRoute('user_create');
        }

        $this->addFlashMessage('success', 'user.create.successful');

        return $this->redirectToRoute("user_list");
    }

    /**
     * @Route("/postEdit/{user}", name="user_post_edit")
     * @Method({"POST"})
     */
    public function postEditAction(User $user, UserFormHandler $createUserFormHandler)
    {
        $editHandler = $createUserFormHandler->buildForm(UserType::class, $user);

        if (!$editHandler->process()) {
            $this->addFormErrors($editHandler->getErrors());

            return $this->redirectToRoute(
                "user_edit",
                [
                    'user' => $user->getId(),
                ]
            );
        }

        $this->addFlashMessage('success', 'user.edit.successful');

        return $this->redirectToRoute("user_list");
    }
}
