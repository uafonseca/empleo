<?php

namespace App\Form;

use App\Entity\Company;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('archivoFile', FileType::class, [
                'label' => 'Imagen',
                'required' => false,
            ])
            ->add('name', null, [
                'label' => 'Nombre',
                'required' => true
            ])
            ->add('url', UrlType::class, [
                'label' => 'Sitio Web',
                'required' => true
            ])
            ->add('description', CKEditorType::class, [
                'label' => 'Descripción',
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
