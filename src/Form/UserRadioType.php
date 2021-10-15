<?php
/**
 * Created by PhpStorm.
 * User: ubel
 * Date: 17/07/20
 * Time: 22:22
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;

class UserRadioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('employer', RadioType::class, array(
                'label'    => 'Empleador',
                'required' => false
            ))
            ->add('candidate', RadioType::class, array(
                'label'    => 'Candidato',
                'required' => false
            ));
    }

    public function getBlockPrefix()
    {
        return 'UserRadioType';
    }
}