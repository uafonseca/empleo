<?php

namespace App\Form;

use App\Entity\Consulta;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConsultaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    Consulta::TYPE_ADMINISTRATIVO => Consulta::TYPE_ADMINISTRATIVO,
                    Consulta::TYPE_CODIGO_TRABAJO => Consulta::TYPE_CODIGO_TRABAJO,
                    Consulta::TYPE_CONTABLE => Consulta::TYPE_CONTABLE,
                    Consulta::TYPE_LEGAL => Consulta::TYPE_LEGAL,
                    Consulta::TYPE_LOSEP => Consulta::TYPE_LOSEP,
                    Consulta::TYPE_RIESGOS_LABORALES => Consulta::TYPE_RIESGOS_LABORALES,
                ],
                'label' => 'Tipo'
            ])
            ->add('ciudad', null, [
                'label' => 'Ciudad',
                'required' => true
            ])
            ->add('texto', CKEditorType::class, [
                'label' => 'Descripción'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consulta::class,
        ]);
    }
}