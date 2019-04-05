<?php

namespace AppBundle\Controller;

use AppBundle\Doctrine\AdminManager;
use AppBundle\Doctrine\AdminRoleManager;
use AppBundle\Doctrine\AdminUserManager;
use AppBundle\Form\Handler\SecurityRequestPasswordFormHandler;
use AppBundle\Form\Handler\SecurityResetPasswordFormHandler;
use AppBundle\Form\Model\SecurityRequestPassword;
use AppBundle\Form\Model\SecurityResetPassword;
use AppBundle\Form\Type\SecurityRequestPasswordType;
use AppBundle\Form\Type\SecurityResetPasswordType;
use AppBundle\Security\User\GoogleLoginService;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityController extends BaseController
{
    protected $userManager;
    protected $roleManager;
    protected $tokenStorage;
    protected $sessions;
    protected $eventDispatcher;
    protected $googleLogin;

    public function __construct(
        SerializerInterface $serializer,
        AdminUserManager $manager,
        AdminRoleManager $roleManager,
        TokenStorage $tokenStorage,
        Session $session,
        EventDispatcher $eventDispatcher,
        GoogleLoginService $googleLogin
    )
    {
        $this->userManager = $manager;
        $this->roleManager = $roleManager;
        $this->tokenStorage = $tokenStorage;
        $this->sessions = $session;
        $this->eventDispatcher = $eventDispatcher;
        $this->googleLogin = $googleLogin;

        parent::__construct($serializer);
    }

    /**
     * @Route("/login", name="security_login")
     * @Method({"GET"})
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'AppBundle::controller/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'google_url' => $this->googleLogin->getURL(),
            ]
        );
    }

    /**
     * @Route("/logout", name="security_logout")
     * @Method("GET")
     */
    public function logoutAction()
    {
        return $this->redirectToRoute('security_login');
    }

    /**
     * @Route("/check", name="security_check")
     * @Method({"POST","GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkAction(
        Request $request
    )
    {
        $userDetails = $this->googleLogin->getDetails($request);

        if (!(bool)preg_match('/^.*@domain.pl$/', $userDetails->getEmail())) {
            $session = $request->getSession();
            $request->attributes->set('_security.last_error', new AuthenticationException('Not domain.'));

            $session->set('_security.last_error',
                new AuthenticationException('Not domain.'));

            return $this->redirectToRoute('security_login');
        }

        $role = $this->roleManager->findRoleByName('ROLE_USER');
        $user = $this->userManager->findUserByEmail($userDetails->getEmail());

        if ($user) {
            $this->manualLogin($user, $request, [$role->getName()]);
            return $this->redirectToRoute('dashboard');
        }

        $user = $this->createNewUserFromGoogle($userDetails, $role);
        $this->manualLogin($user, $request, [$role->getName()]);

        return $this->redirectToRoute('dashboard');
    }

    private function manualLogin($user, $request, $role)
    {
        $token = new UsernamePasswordToken($user, null, 'app', $role);
        $this->tokenStorage->setToken($token);

        $this->sessions->set('_security_main', serialize($token));

        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch("security.interactive_login", $event);
    }

    private function createNewUserFromGoogle($userDetails, $role)
    {
        $this->userManager->createUser(
            $userDetails->getEmail(),
            $userDetails->getEmail(),
            $userDetails->getId(),
            $role
        );
        return $this->userManager->findUserByEmail($userDetails->getEmail());
    }

    /**
     * @Route("/password-request", name="security_password_request")
     * @Method("POST")
     * @param SecurityRequestPasswordFormHandler $formHandler
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public
    function passwordRequestAction(SecurityRequestPasswordFormHandler $formHandler)
    {
        $handler = $formHandler->buildForm(SecurityRequestPasswordType::class, new SecurityRequestPassword());
        if (!$handler->process()) {
            $this->addFlashMessage('danger', $formHandler->getErrorsAsString());

            return $this->redirectToRoute('security_login');
        }
        $this->addFlashMessage('success', 'security.password_request.successful');

        return $this->redirectToRoute('security_login');
    }

    /**
     * @Route("/password-reset/{token}", name="security_password_reset")
     * @Method({"GET", "POST"})
     * @param $token
     * @param SecurityResetPasswordFormHandler $formHandler
     * @param AdminManager $adminManager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public
    function resetPasswordAction(
        $token,
        SecurityResetPasswordFormHandler $formHandler,
        AdminManager $adminManager,
        Request $request
    )
    {
        if (!$user = $adminManager->findAdminByToken($token)) {
            $this->addFlashMessage('danger', "security.reset_password.token.expired");

            return $this->redirectToRoute('security_login');
        }

        if ($request->isMethod(Request::METHOD_GET)) {
            return $this->render(
                'AppBundle::controller/security/password_change.html.twig',
                [
                    'token' => $token,
                ]
            );
        }

        $resetPassword = new SecurityResetPassword();
        $resetPassword->setUser($user);

        $handler = $formHandler->buildForm(SecurityResetPasswordType::class, $resetPassword);
        if ($handler->process()) {
            $this->addFlashMessage('success', 'security.reset_password.successful');

            return $this->redirectToRoute("security_login");
        }

        $this->addFlashMessage('danger', $formHandler->getErrorsAsString());

        return $this->redirectToRoute('security_password_reset', ['token' => $token]);
    }
}
