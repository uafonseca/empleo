<?php

namespace App\Form;

use App\Entity\Policy;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class Policy1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('welcome_message',CKEditorType::class,[
                'label' => 'Mensaje de bienvenida',
                'required' => true
            ])
            ->add('privacity',CKEditorType::class,[
                'label' => 'Privacidad',
                'required' => true
            ])
            ->add('information',CKEditorType::class,[
                'label' => 'Información',
                'required' => true
            ])
            ->add('security',CKEditorType::class,[
                'label' => 'Seguridad',
                'required' => true
            ])
            ->add('updated',CKEditorType::class,[
                'label' => 'Actualización',
                'required' => true
            ])
            ->add('terms',CollectionType::class,[
                'entry_type' => TextareaType::class,
                'constraints' => array(new Valid()),
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'block_name' => 'terminos',
                'attr' => [
                    'class' => 'terms-collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
            ->add('conditions',CollectionType::class,[
                'entry_type' => TextareaType::class,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'block_name' => 'condiciones',
                'attr' => [
                    'class' => 'conditions-collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
            ->add('terms_header',CKEditorType::class,[
                'label' => 'Cabecera de términos',
                'required' => true
            ])
            ->add('conditions_header',CKEditorType::class,[
                'label' => 'Cabecera de condiciones',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Policy::class,
        ]);
    }

    public function getBlockPrefix()
    {
            return 'policy';
    }
}
