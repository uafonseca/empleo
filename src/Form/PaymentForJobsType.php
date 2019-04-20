<?php

namespace App\Form;

use App\Entity\PaymentForJobs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentForJobsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
//            ->add('aux')/**/
            ->add('anouncements_number_max')
            ->add('visible_days')
            ->add('days_importants')
            ->add('cv_number_max')
            ->add('evaluations_psicological')
            ->add('selection')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaymentForJobs::class,
        ]);
    }
}
