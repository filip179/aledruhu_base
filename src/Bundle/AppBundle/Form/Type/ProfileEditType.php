<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use AppBundle\Form\Model\ProfileEdit;
use Component\Util\RegexProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileEditType extends AbstractType {
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'disabled' => true,
                    'data' => $user->getUsername(),
                    'constraints' => [
                        new Assert\Length(["max" => 50]),
                        new Assert\Regex(
                            [
                                "pattern" => RegexProvider::$baseNameRegex,
                                "message" => "constraint.username.wrong_characters",
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'disabled' => true,
                    'data' => $user->getEmail(),
                ]
            )
            ->add(
                'firstname',
                TextType::class,
                [
                    'data' => $user->getFirstname(),
                    'constraints' => [
                        new Assert\Length(["max" => 50]),
                        new Assert\Regex(
                            [
                                "pattern" => RegexProvider::$baseNameRegex,
                                "message" => "constraint.first_name.wrong_characters",
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'data' => $user->getLastname(),
                    'constraints' => [
                        new Assert\Length(["max" => 75]),
                        new Assert\Regex(
                            [
                                "pattern" => RegexProvider::$baseNameRegex,
                                "message" => "constraint.last_name.wrong_characters",
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'attr' => [
                        'class' => 'btn green-meadow',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ProfileEdit::class,
                'translation_domain' => 'form_profile_type_edit',
            ]
        );
    }
}
