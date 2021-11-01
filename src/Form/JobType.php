<?php

/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 2/18/2019
 * Time: 2:28 PM
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Job;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', null, [
                'label' => 'Título'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Seleccione categoría',
                'choice_label' => 'name',
                'label' => 'Categoría'
            ])
            ->add('localtion', null, [
                'label' => 'Ubicación'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Tiempo completo' => 'ful',
                    'Por horas' => 'partial',
                    'Temporal' => 'temporary',
                    'Prácticas profesionales' => 'practica_profesional',
                    'Por obra' => 'obra',
                    'Por contrato' => 'contrato',
                    'Servicios profesionales' => 'servicios_profesionales',
                ],
                'placeholder' => 'Seleccione tipo',
                'label' => 'Tipo'
            ])
            ->add('experience', ChoiceType::class, [
                'choices' => [
                    'Un año' => 'Un año',
                    'De uno a 2 años' => 'De uno a 2 años',
                    'De 2 a 3 años' => 'De 2 a 3 años',
                    'Más de 3 años' => 'Más de 3 años',
                ],
                'placeholder' => 'Seleccione experiencia',
                'label' => 'Experiencia'
            ])
            ->add('salary_min', IntegerType::class, [
                'label' => 'Salario mínimo'
            ])
            ->add('qualification', ChoiceType::class, [
                'choices' => [
                    'Urgente' => 'Urgente',
                    'Inmediato' => 'Inmediato',
                ],
                'placeholder' => 'Seleccione prioridad',
                'label' => 'Tipo de prioridad'
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fecha'
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Descripción'
            ])
            ->add('responsabilities', CKEditorType::class, [
                'label' => 'Responsabilidades'
            ])
            ->add('education', CKEditorType::class, [
                'label' => 'Educación'
            ])
            ->add('others', CKEditorType::class, [
                'label' => 'Otros'
            ])
            ->add('country', null, [
                'attr' => ['id' => 'country'],
                'label' => 'País'
            ])
            ->add('city', null, [
                'attr' => ['id' => 'city'],
                'label' => 'Ciudad'
            ])
            //            ->add('gender', ChoiceType::class, [
            //                'choices' => [
            //                    'Femenino' => 'femenino',
            //                    'Másculino' => 'masculino',
            //                    'Cualquier' => 'cualquier',
            //                ],
            //                'placeholder' => 'Género',
            //                'label'=>'Género'
            //            ])
            ->add('zip_code', null, [
                'attr' => ['id' => 'postal_code'],
                'label' => 'Código postal'
            ])
            ->add('imageFile', FileType::class, array(
                'required' => true,
            ))
            ->add('your_localtion', null, [
                'attr' => ['id' => 'autocomplete'],
                'label' => 'Ubicación'
            ])
            ->add('company', CompanyType::class, [
                'label' => 'Datos de la compañía'
            ])
            ->add('save', SubmitType::class, ['label' => 'Publicar su trabajo']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
            //            'error_mapping' => [
            //                '.' => 'city',
            //
            //            ],
        ]);
    }
}
