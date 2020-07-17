<?php

namespace App\Form;

use App\Entity\PaymentForJobs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentForJobsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre'
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Precio',
                'currency' => 'USD',
                'attr' => [
                    'type' => 'number',
                ],
            ])
            ->add('anouncements_number_max', IntegerType::class, [
                'label' => 'Cantidad de publicaciones'
            ])
            ->add('visible_days', IntegerType::class, [
                'label' => 'Días de visibilidad'
            ])
            ->add('days_importants', IntegerType::class, [
                'label' => 'Días con prioridad'
            ])
            ->add('cv_number_max',IntegerType::class,[
                'label' => 'Cantidad máxima de CV'
            ])
            ->add('evaluations_psicological',null,[
                'label' => 'Evaluaciones psicológicas'
            ])
            ->add('paypalCode',TextareaType::class,[
                'label' => 'Código PayPal'
            ])
            ->add('selection',null,[
                'label' => 'Selección de candidatos'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaymentForJobs::class,
        ]);
    }
}
