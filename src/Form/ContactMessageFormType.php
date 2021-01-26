<?php

namespace App\Form;

use App\Entity\ContactMessage;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ContactMessageFormType extends AbstractType
{


    /** @var TokenStorageInterface  */
    private $tokenStorage;

    /**
     * ContactMessageFormType constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('creator', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('entity')
                        ->where('entity=:user')
                        ->setParameter('user', $this->tokenStorage->getToken()->getUser());
                },
                'label' => false
            ])
            ->add('destinatario', EntityType::class, [
                'class' => User::class,
                'query_builder' => function (EntityRepository $repository) {
                    return $repository->createQueryBuilder('entity');
                },
                'label' => false
            ])
            ->add('context', TextareaType::class, [
                'label' => 'Texto a enviar',
                'attr' => [
                    'rows' => 10
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactMessage::class,
        ]);
    }
}
