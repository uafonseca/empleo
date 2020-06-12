<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Category1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null, [
                'label' => 'Nombre'
            ])
            ->add('ico', null,[
                'label' => 'Icono',
                'attr'=>[
                    'data-placement'=>'bottomRight',
                    'class'=>'icp icp-auto',
                ]
            ])
            ->add('description', TextareaType::class,[
                'label' =>'Descripción'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
