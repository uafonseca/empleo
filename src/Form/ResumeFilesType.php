<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 27/02/2019
 * Time: 13:30
 */

namespace App\Form;


use App\Entity\Resume;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResumeFilesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cvFile',FileType::class,array('required'=>false,'label'=>'CV (PDF)'))
            ->add('cartFile',FileType::class,array('required'=>false,'label'=>'Carta de presentación (PDF)'));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resume::class,
        ]);
    }
}