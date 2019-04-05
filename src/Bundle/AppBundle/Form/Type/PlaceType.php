<?php

namespace AppBundle\Form\Type;

use AppVerk\UserBundle\Service\Acl\AclProvider;
use AppBundle\Doctrine\PlaceTypeManager;
use AppBundle\Entity\Place;
use Component\Util\RegexProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PlaceType extends AbstractType
{
    /** @var AclProvider */
    private $aclProvider;
    /**
     * @var PlaceTypeManager
     */
    private $placeTypeManager;

    public function __construct(AclProvider $aclProvider, PlaceTypeManager $placeTypeManager)
    {
        $this->aclProvider = $aclProvider;
        $this->placeTypeManager = $placeTypeManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        /**
         * @var \AppBundle\Entity\PlaceType $placeType
         */
        foreach ($this->placeTypeManager->getPlacesForChoice() as $placeType) {
            $choices[$placeType->getName()] = $placeType;
        }
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'constraints' => [
                        new Assert\Length(['max' => 150]),
                        new Assert\Regex(
                            [
                                'pattern' => RegexProvider::$baseNameRegex,
                                'message' => 'constraint.name.wrong_characters',
                            ]
                        ),
                    ],

                ]
            )
            ->add('capacity'
            )
            ->add('additionalContact', TextType::class, [
                'required' => false])
            ->add('number', TextType::class, [
                'required' => false])
            ->add('additionalInfo', TextareaType::class, [
                'required' => false])
            ->add('city', TextType::class)
            ->add('postcode', TextType::class,
                [
                    'constraints' => [
                        new Assert\Regex(['pattern' => '/\d{2}-\d{3}/'])
                    ]
                ])
            ->add('owner', TextType::class, [
                'required' => false])
            ->add('placeType', ChoiceType::class, [
                'choices' => $choices,
                'required' => true
            ])
            ->add('latitude',
                TextType::class,
                [
                    'required' => false,
                    'constraints' => [
                        new Assert\Regex(['pattern' => '/^(\-?\d+(\.\d+)?)$/'])
                    ]
                ])
            ->add('longitude',
                TextType::class,
                [
                    'required' => false,
                    'constraints' => [
                        new Assert\Regex(['pattern' => '/^(\-?\d+(\.\d+)?)$/'])
                    ]
                ])
            ->add('pictures', FileType::class, [
                'multiple' => true,
                'required' => false,
            ])
            ->add('submit',
                SubmitType::class, [
                    'attr' => [
                        'class' => 'btn green-meadow'
                    ]
                ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Place::class,
                'translation_domain' => 'form_place_type',
            ]
        );
    }
}
