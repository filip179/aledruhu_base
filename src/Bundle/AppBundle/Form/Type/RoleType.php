<?php

namespace AppBundle\Form\Type;

use AppVerk\UserBundle\Service\Acl\AclProvider;
use AppBundle\Entity\Role;
use Component\Util\RegexProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RoleType extends AbstractType {
    /** @var AclProvider */
    private $aclProvider;

    public function __construct(AclProvider $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Role $role */
        $role = $builder->getForm()->getData();
        $credentials = $role->getCredentials();

        if (!$credentials) {
            $credentials = [];
        }
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'constraints' => [
                        new Assert\Length(['max' => 50]),
                        new Assert\Regex(
                            [
                                'pattern' => RegexProvider::$baseNameRegex,
                                'message' => 'constraint.name.wrong_characters',
                            ]
                        ),
                    ],

                ]
            )
            ->add(
                'checkAllPermissions',
                CheckboxType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['class' => 'checkAllPermissions'],
                ]
            );

        $aclChoices = $this->aclProvider->getAclForChoice();
        foreach ($aclChoices as $section => $roles) {
            $builder->add(
                'permissions',
                ChoiceType::class,
                [
                    'choices' => $roles,
                    'label' => false,
                    'required' => true,
                    'expanded' => true,
                    'multiple' => true,
                    'mapped' => false,
                    'data' => $credentials,
                    'property_path' => 'permissions[' . $section . ']',
                ]
            );
        }

        $builder
            ->add(
                'deletable',
                CheckboxType::class,
                [
                    'data' => $role->isDeletable(),
                    'required' => false,
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
                'data_class' => Role::class,
                'translation_domain' => 'form_role_type_edit',
            ]
        );
    }
}
