<?php

namespace App\Form;

use App\Entity\Consulta;
use App\Entity\State;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('city', EntityType::class, [
                'label' => 'Ciudad',
                'class' => State::class,
                'required' => true,
                'query_builder'=> function(EntityRepository $entityRepository){
                    return $entityRepository->createQueryBuilder('c')
                        ->join('c.contry', 'country')
                        ->where('country.name =:name')
                        ->setParameter('name','Ecuador');
                }
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