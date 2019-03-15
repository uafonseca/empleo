<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 22/02/2019
 * Time: 12:58
 */

namespace App\Form;


use App\Entity\Category;
use App\Entity\Profession;
use App\Entity\Title;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFullyEmployerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array('required'=>true))
            ->add('username',null,array('required'=>true))
            ->add('email',null,array('required'=>true))
            ->add('imageFile',FileType::class,array('required'=>false))
            ->add('companyName',null,array('required'=>false))
            ->add('phone',null,array('required'=>false))
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Categoría',
                'choice_label' => 'name',
                'multiple'=>true,
                'required'=>true,
            ])
            ->add('address',null,array('required'=>true))
            ->add('about',TextareaType::class,array('required'=>true))
            ->add('videoIntro',UrlType::class,array('required'=>false))
            ->add('socialFacebook',null,array('required'=>false))
            ->add('socialTwitter',null,array('required'=>false))
            ->add('socialGoogle',null,array('required'=>false));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}