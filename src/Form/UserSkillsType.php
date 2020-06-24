<?php

namespace App\Form;

use App\Entity\ResumeMetadata;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class UserSkillsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('skils', CollectionType::class,[
                'entry_type' => ResumeMetadataType::class,
                'constraints' => array(new Valid()),
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'block_name' => 'skils',
                'attr' => [
                    'class' => 'skils-collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
    public function getBlockPrefix()
    {
        return 'skills';
    }
}
