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
                'label' => 'Nombre',
                'required' => true
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Precio',
                'required' => true
            ])
            ->add('anouncements_number_max', IntegerType::class, [
                'label' => 'Cantidad de publicaciones',
                'required' => true
            ])
            ->add('visible_days', IntegerType::class, [
                'label' => 'Días de visibilidad',
                'required' => true
            ])
            ->add('days_importants', IntegerType::class, [
                'label' => 'Días con prioridad',
                'required' => true
            ])
            ->add('cv_number_max', IntegerType::class, [
                'label' => 'Cantidad máxima de CV',
                'required' => true
            ])
            ->add('evaluations_psicological', null, [
                'label' => 'Evaluaciones psicológicas',
                'required' => false
            ])
            ->add('identificador', null, [
                'label' => 'Identificador',
                'required' => false
            ])
            ->add('idClient', null, [
                'label' => 'Id Cliente',
                'required' => false
            ])
            ->add('claveSecreta', null, [
                'label' => 'Clave Secreta',
                'required' => false
            ])
            ->add('contrasennaCodificacion', null, [
                'label' => 'Contraseña de Codificación',
                'required' => false
            ])
            ->add('token', null, [
                'label' => 'Token',
                'required' => false
            ])
            ->add('selection', null, [
                'label' => 'Selección de candidatos',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaymentForJobs::class,
        ]);
    }
}
