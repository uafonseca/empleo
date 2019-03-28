<?php
/**
 * Created by PhpStorm.
 * User: Ubel
 * Date: 2/18/2019
 * Time: 2:28 PM
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'placeholder' => 'Categoría',
                'choice_label' => 'name',
            ])
            ->add('localtion')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'A tiempo completol' => 'ful',
                    'A tiempo parcial' => 'partial',
                    'Temporal' => 'temporary',
                    'Independiente' => 'freelance',
                ],
                'placeholder' => 'Tipo'
            ])
            ->add('experience', ChoiceType::class, [
                'choices' => [
                    'Un año' => 'Un año',
                    'De uno a 2 años' => 'De uno a 2 años',
                    'De 2 a 3 años' => 'De 2 a 3 años',
                    'Más de 3 años' => 'Más de 3 años',
                ],
                'placeholder' => 'Experiencia'
            ])
            ->add('salary_max', IntegerType::class,[
                'invalid_message' => 'You entered an invalid value, it should include %num% ',
                'invalid_message_parameters' => array('%num%' => 'form.salary_min'),
            ])
            ->add('salary_min', IntegerType::class)
            ->add('qualification', ChoiceType::class, [
                'choices' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                ],
                'placeholder' => 'Calificación'
            ])
            ->add('date', DateType::class, ['widget' => 'single_text',])
            ->add('description', TextareaType::class)
            ->add('responsabilities', TextareaType::class)
            ->add('education', TextareaType::class)
            ->add('others', TextareaType::class)
            ->add('country', CountryType::class)
            ->add('city')
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Femenino' => 'femenino',
                    'Másculino' => 'masculino',
                    'Cualquier' => 'cualquier',
                ],
                'placeholder' => 'Género'
            ])
            ->add('zip_code')
            ->add('imageFile', FileType::class, array(
                'required' => true,
            ))
            ->add('your_localtion')
            ->add('company_name')
            ->add('web_address', UrlType::class)
            ->add('campany_profile', TextareaType::class)
            ->add('save', SubmitType::class, ['label' => 'Publicar su trabajo']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
            'error_mapping' => [
                '.' => 'city',

            ],
        ]);
    }
}