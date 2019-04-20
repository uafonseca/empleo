<?php

namespace App\Form;

use App\Entity\PaymentForServices;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentForServicesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
//            ->add('aux')
            ->add('anouncements_number_max')
            ->add('visible_days')
            ->add('days_importants')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaymentForServices::class,
        ]);
    }
}
