<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 21/02/2019
 * Time: 11:18
 */

namespace App\Form;


use App\Entity\User;
use Doctrine\DBAL\Types\ArrayType;
use function Sodium\add;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', null, array('label'=>false))
            ->add('employer', CheckboxType::class,[
                'label'    => 'Show this entry publicly?',
                'required' => false,
            ])
            ->add('candidate', CheckboxType::class,[
                'required' => false,
            ])
            ->add('companyName',null, array('label'=>false))
            ->add('phone',null, array('label'=>false))
            ->add('plainPassword', RepeatedType::class,array(
                'type'  => PasswordType::class,
                'invalid_message'=>'Las contraseñas deben ser iguales',
                'first_options'=>['attr'=>['placeholder'=>'Contraseña']],
                'second_options'=>['attr'=>['placeholder'=>'Repita su contraseña']],

            ));
    }
    public function buildFormFully(FormBuilderInterface $builder, array $options)
    {

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}