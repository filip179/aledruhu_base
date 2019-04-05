<?php

namespace AppBundle\Controller;

use AppVerk\UserBundle\Service\Acl\AclProvider;
use AppBundle\Entity\User;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;

class BaseController extends AbstractController {
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param TranslatorInterface $translator
     *
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    protected function serializedResponse($data, array $groups = ['Default']): Response
    {
        $context = new SerializationContext();
        $context->setGroups($groups);

        $response = $this->serializer->serialize($data, 'json', $context);

        return new Response($response);
    }

    protected function addFormErrors(array $errors = [])
    {
        foreach ($errors as $field => $error) {
            $translatedField = $this->translator->trans($field, [], 'fields');
            $message = $translatedField . ' : ' . $error;
            $this->addFlashMessage('danger', $message);
        }
    }

    protected function addFlashMessage(string $type, string $message, $domain = 'messages')
    {
        $this->addFlash($type, $this->translator->trans($message, [], $domain));
    }
}
