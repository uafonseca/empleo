<?php

namespace App\Form;

use App\Entity\Metadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MetadataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type')
            ->add('header')
            ->add('context')
            ->add('description')
            ->add('date_create')
            ->add('extra')
            ->add('resume')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Metadata::class,
        ]);
    }
}
