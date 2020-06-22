<?php

namespace App\Form;

use App\Entity\Ocupation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OcupationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',null,[
                'label' => 'Cargo'
            ])
            ->add('company',null,[
                'label' => 'Compañía'
            ])
            ->add('periode',null,[
                'label' => 'Periodo'
            ])
            ->add('context',TextareaType::class,[
                'label' => 'Descripción'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ocupation::class,
        ]);
    }
}
