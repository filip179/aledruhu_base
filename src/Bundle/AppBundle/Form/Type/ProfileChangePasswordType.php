<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Model\ProfileChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileChangePasswordType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class)
            ->add('newPassword', PasswordType::class)
            ->add('newPasswordRepeat', PasswordType::class)
            ->add(
                'Change password',
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
                'data_class' => ProfileChangePassword::class,
                'translation_domain' => 'form_profile_type_change_password',
            ]
        );
    }
}
