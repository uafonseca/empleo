<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 21/02/2019
 * Time: 11:18
 */

namespace App\Form;


use App\Entity\User;
use PhpParser\Node\Expr\BinaryOp\NotEqual;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [ //
                'label' => 'Nombre(s)'
            ])
            ->add('email', EmailType::class, [ //
                'label' => 'Correo electrónico',
            ])
            ->add('username', null, [ //
                'label' => 'Nombre de usuario',
            ])
//            ->add('employer', ChoiceType::class, [
//                'choices' => array(
//                    'Empleador' => true,
//                    'Usuario' => false,
//                ),
//                'expanded' => true,
//                'label' => 'Tipo de usuario',
//                'required' => true,
//                'data' => false
//            ])
            ->add('phone', null, [ //
                'label' => 'Teléfono',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}