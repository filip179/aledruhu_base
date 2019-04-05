<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Model\SecurityRequestPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityRequestPasswordType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => SecurityRequestPassword::class,
                'translation_domain' => 'form_security_request_password',
            ]
        );
    }
}
