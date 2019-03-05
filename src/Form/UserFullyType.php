<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 22/02/2019
 * Time: 12:58
 */

namespace App\Form;


use App\Entity\Category;
use App\Entity\User;
use Doctrine\DBAL\Types\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserFullyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,array('required'=>false))
            ->add('username',null,array('required'=>true))
            ->add('email',null,array('required'=>true))
            ->add('imageFile',FileType::class,array('required'=>false))
            ->add('companyName',null,array('required'=>false))
            ->add('phone',null,array('required'=>false))
            ->add('status',ChoiceType::class,[
                'choices' => [
                    'A tiempo completol' => 'ful',
                    'A tiempo parcial' => 'partial',
                    'Temporal' => 'temporary',
                    'Independiente' => 'freelance',
                ],
            ])
            ->add('gender',ChoiceType::class,[
                'choices' => [
                    'Femenino' => 'F',
                    'Másculino' => 'M',
                    'Otro' => 'O',
                ],
            ])
            ->add('qualification',null,array('required'=>false))
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Categoría',
                'choice_label' => 'name',
            ])
            ->add('age',\Symfony\Component\Form\Extension\Core\Type\IntegerType::class,array('required'=>false))
            ->add('experience',\Symfony\Component\Form\Extension\Core\Type\IntegerType::class,array('required'=>false))
            ->add('address',null,array('required'=>false))
            ->add('about',TextareaType::class,array('required'=>false))
            ->add('videoIntro',UrlType::class,array('required'=>false))
            ->add('socialFacebook',null,array('required'=>false))
            ->add('socialTwitter',null,array('required'=>false))
            ->add('socialGoogle',null,array('required'=>false));
//            ->add('password')
//            ->add('socialGoogle')
//            ->add('socialGoogle')
//            ->add('socialGoogle');
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}