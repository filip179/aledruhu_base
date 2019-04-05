<?php

namespace AppBundle\Form\Model;

use Component\Form\Model\FormModelInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SecurityResetPassword implements FormModelInterface {
    /**
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @Assert\NotBlank()
     */
    private $passwordRepeat;

    private $user;

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPasswordRepeat()
    {
        return $this->passwordRepeat;
    }

    /**
     * @param mixed $passwordRepeat
     */
    public function setPasswordRepeat($passwordRepeat)
    {
        $this->passwordRepeat = $passwordRepeat;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
