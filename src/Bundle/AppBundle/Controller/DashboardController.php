<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DashboardController extends BaseController {
    /**
     * @Route("/", name="dashboard")
     * @Method("GET")
     */
    public function loginAction()
    {
        if (($this->getUser() instanceof User) === false) {
            return $this->redirectToRoute('security_login');
        }

        return $this->render("AppBundle::controller/dashboard/index.html.twig");
    }
}
