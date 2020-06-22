<?php

namespace App\Form;

use App\Entity\StaticPage;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaticPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('context',CKEditorType::class,[
                'label' => 'Contenido'
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Tipo de página',
                'choices' => [
                    StaticPage::TYPE_ABOUT => StaticPage::TYPE_ABOUT,
                    StaticPage::TYPE_CONTACT => StaticPage::TYPE_CONTACT,
                    StaticPage::TYPE_PRICE => StaticPage::TYPE_PRICE,
                    StaticPage::TYPE_HOW_WORKING => StaticPage::TYPE_HOW_WORKING,
                    StaticPage::TYPE_FAQ => StaticPage::TYPE_FAQ,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StaticPage::class,
        ]);
    }
}
