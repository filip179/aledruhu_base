<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use Component\Util\RegexProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $entity */
        $entity = $builder->getForm()->getData();
        $passwordRequired = ($entity->getId() !== null) ? false : true;

        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'required' => true,
                    'constraints' => [
                        new Assert\Length(["max" => 100]),
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
                    'required' => true,
                    'constraints' => [
                        new Assert\Email(["strict" => true, 'checkMX' => true]),
                    ],
                ]
            )
            ->add(
                'firstName',
                TextType::class,
                [
                    'required' => true,
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
                'lastName',
                TextType::class,
                [
                    'required' => true,
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
            ->add('enabled', null, ['required' => false])
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'invalid_message' => 'error.password.not_match',
                    'required' => $passwordRequired,
                    'mapped' => false,
                    'first_options' => ['label' => 'password'],
                    'second_options' => ['label' => 'passwordRepeat'],
                ]
            )
            ->add(
                'role',
                null,
                [
                    'required' => true,
                    'multiple' => false,
                    'placeholder' => 'selectCredentials',
                    'attr' => [
                        'class' => 'select2-choice',
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
                'data_class' => User::class,
                'translation_domain' => 'form_user_type',
            ]
        );
    }
}
