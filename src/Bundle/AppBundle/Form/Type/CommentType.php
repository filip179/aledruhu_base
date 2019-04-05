<?php

namespace AppBundle\Form\Type;

use AppBundle\Doctrine\CommentManager;
use AppBundle\Entity\Comment;
use AppVerk\UserBundle\Service\Acl\AclProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CommentType extends AbstractType
{
    /** @var AclProvider */
    private $aclProvider;
    /**
     * @var CommentManager
     */
    private $manager;

    public function __construct(AclProvider $aclProvider, CommentManager $manager)
    {
        $this->aclProvider = $aclProvider;
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id',
                HiddenType::class
            )
            ->add(
                'text',
                TextareaType::class,
                [ 'required' => true, ]
            )
            ->add(
                'parentEntity',
                HiddenType::class
            )
            ->add(
                'parentId',
                HiddenType::class
            )
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
                'data_class' => Comment::class,
                'translation_domain' => 'form_comment_type',
            ]
        );
    }
}
