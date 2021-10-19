<?php

namespace App\Form;

use App\Entity\Slide;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SlideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'Título'])
            ->add('shortDescription', TextareaType::class, ['label' => 'Descripción'])

            ->add(
                'imageFile',
                FileType::class,
                [
                        'multiple' => false,
                        'required'=>true,
                        'attr' => [
                            'accept' => 'image/*',
                            'class' => 'file-to-upload'
                        ],
                        'label' => 'Banner'
                    ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Slide::class,
        ]);
    }
}
