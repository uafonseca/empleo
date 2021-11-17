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
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [ //
                'label' => false,
                'attr' => ['placeholder' => 'Nombre(s)']
            ])
            ->add('email', EmailType::class, [ //
                'label' => false,
                'attr' => ['placeholder' => 'Correo electrónico']
            ])
            ->add('username', null, [ //
                'label' => false,
                'attr' => ['placeholder' => 'Nombre de usuario']
            ])
            ->add('employer', ChoiceType::class, [
                'choices' => array(
                    'Registrarme como empleador' => true,
                    'Registrarme como usuario' => false,
                ),
                'expanded' => true,
                'label' => false,
                'required' => true,
                'data' => false
            ])
            ->add('phone', IntegerType::class, [ //
                'label' => false,
                'attr' => ['placeholder' => 'Teléfono'],
                'constraints' => [new Regex('/[^0-9]/')],
            ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraseñas deben ser iguales',
                'first_options' => ['attr' => ['placeholder' => 'Contraseña'], 'label' => false],
                'second_options' => ['attr' => ['placeholder' => 'Repita su contraseña', 'label' => false]],
                'label' => false,
                'constraints' => [new Length(['min' => 6])],
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