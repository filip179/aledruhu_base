<?php

namespace AppBundle\Controller;

use AppBundle\Form\Handler\ProfileChangePasswordFormHandler;
use AppBundle\Form\Handler\ProfileEditFormHandler;
use AppBundle\Form\Model\ProfileChangePassword;
use AppBundle\Form\Model\ProfileEdit;
use AppBundle\Form\Type\ProfileChangePasswordType;
use AppBundle\Form\Type\ProfileEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends BaseController {
    /**
     * @Route("/edit", name="profile_edit")
     * @Method({"GET"})
     */
    public function editAction(
        ProfileEditFormHandler $editFormHandler,
        ProfileChangePasswordFormHandler $changePasswordFormHandler
    )
    {
        $editHandler = $editFormHandler->buildForm(ProfileEditType::class, new ProfileEdit());
        $changePasswordHandler = $changePasswordFormHandler->buildForm(
            ProfileChangePasswordType::class,
            new ProfileChangePassword()
        );

        return $this->render(
            'AppBundle::controller/profile/edit.html.twig',
            [
                'editForm' => $editHandler->getFormView(),
                'changePasswordForm' => $changePasswordHandler->getFormView(),
            ]
        );
    }

    /**
     * @Route("/update-password", name="profile_update_password")
     * @Method("POST")
     */
    public function postChangePasswordAction(ProfileChangePasswordFormHandler $changePasswordFormHandler)
    {
        $changePasswordHandler = $changePasswordFormHandler->buildForm(
            ProfileChangePasswordType::class,
            new ProfileChangePassword()
        );
        if (!$changePasswordHandler->process()) {
            $this->addFormErrors($changePasswordHandler->getErrors());

            return $this->redirectToRoute("profile_edit");
        }

        $this->addFlashMessage('success', 'profile.change_password.successful');

        return $this->redirectToRoute("profile_edit");
    }

    /**
     * @Route("/update-data", name="profile_update_data")
     * @Method("POST")
     */
    public function postEditAction(ProfileEditFormHandler $editFormHandler)
    {
        $editHandler = $editFormHandler->buildForm(ProfileEditType::class, new ProfileEdit());
        if (!$editHandler->process()) {
            $this->addFormErrors($editHandler->getErrors());

            return $this->redirectToRoute("profile_edit");
        }

        $this->addFlashMessage('success', 'profile.edit.successful');

        return $this->redirectToRoute("profile_edit");
    }
}
