<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 23/02/2019
 * Time: 20:27
 */

namespace App\Form;


use App\Entity\Metadata;
use App\Entity\Resume;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResumeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('user',EntityType::class,['class'=>User::class])
            ->add('aboutMe',TextareaType::class);
//            ->add('record',EntityType::class,['class' => Metadata::class, 'placeholder' => 'Antecedentes',])
//            ->add('experience',EntityType::class,['class' => Metadata::class, 'placeholder' => 'Experiencia',])
//            ->add('skils',EntityType::class,['class' => Metadata::class, 'placeholder' => 'Habilidades',])
//            ->add('calification',EntityType::class,['class' => Metadata::class ,'placeholder' => 'Calificación',]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resume::class,
        ]);
    }
}