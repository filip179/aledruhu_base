<?php

namespace AppBundle\Form\Model;

use Component\Form\Model\FormModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileChangePassword implements FormModelInterface {
    /**
     * @Assert\NotBlank()
     */
    private $oldPassword;

    /**
     * @Assert\NotBlank()
     */
    private $newPassword;

    /**
     * @Assert\NotBlank()
     */
    private $newPasswordRepeat;

    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param mixed $oldPassword
     */
    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     */
    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @return mixed
     */
    public function getNewPasswordRepeat()
    {
        return $this->newPasswordRepeat;
    }

    /**
     * @param mixed $newPasswordRepeat
     */
    public function setNewPasswordRepeat($newPasswordRepeat)
    {
        $this->newPasswordRepeat = $newPasswordRepeat;
    }
}
