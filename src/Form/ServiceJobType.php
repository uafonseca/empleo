<?php
    /**
     * Created by PhpStorm.
     * User: FonsecaGay
     * Date: 01/04/2019
     * Time: 19:21
     */
    
    namespace App\Form;

    use App\Entity\Anouncement;
use App\Entity\Country;
use App\Entity\Image;
    use App\Entity\Job;
    use App\Entity\Profession;
    use App\Entity\Service;
use App\Entity\State;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use Symfony\Component\Form\Extension\Core\Type\CollectionType;
    use Symfony\Component\Form\Extension\Core\Type\DateType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\Form\Extension\Core\Type\IntegerType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\UrlType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    
    class ServiceJobType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('title')
                ->add(
                    'profession',
                    EntityType::class,
                    [
                        'class' => Profession::class,
                        'placeholder' => 'Mi profesión',
                        'choice_label' => 'name',
                    ]
                )
                ->add(
                    'type',
                    ChoiceType::class,
                    [
                        'choices' => [
                            'Tiempo completo' => 'ful',
                            'Por horas' => 'partial',
                            'Temporal' => 'temporary',
                            'Prácticas profesionales' => 'practica_profesional',
                            'Por obra' => 'obra',
                            'Por contrato' => 'contrato',
                        ],
                        'placeholder' => 'Tipo',
                    ]
                )
                ->add(
                    'experience',
                    ChoiceType::class,
                    [
                        'choices' => [
                            'Un año' => 'Un año',
                            'De uno a 2 años' => 'De uno a 2 años',
                            'De 2 a 3 años' => 'De 2 a 3 años',
                            'Más de 3 años' => 'Más de 3 años',
                        ],
                        'placeholder' => 'Experiencia',
                    ]
                )
                ->add('value', IntegerType::class)
                ->add('description', TextareaType::class)
                ->add('location', null, ['attr' => ['id' => 'autocomplete']])
                ->add('addres1')
                ->add('addres2')
				->add('contact')
                ->add('city', null, ['attr' => ['id' => 'city']])
                ->add(
                    'state',
                    EntityType::class,
                    [
                        'class' => State::class,
                        'choice_label' => 'Name',
                        'query_builder' => function (EntityRepository $entityRepository) {
                            return $entityRepository->createQueryBuilder('c')
                            ->join('c.contry', 'contry')
                            ->where('contry.name=:name')
                            ->setParameter('name', 'Ecuador')
                            ;
                        }
                        ]
                )
                ->add('postalCode', null, ['attr' => ['id' => 'postal_code']])
                ->add(
                    'country',
                    EntityType::class,
                    [
                    'class' => Country::class,
                    'choice_label' => 'Name',
                    'query_builder' => function (EntityRepository $entityRepository) {
                        return $entityRepository->createQueryBuilder('c')
                        ->where('c.name=:countri')
                        ->setParameter('countri', 'Ecuador')
                        ;
                    }
                ]
                )
                ->add('videoLink', UrlType::class, [
                    'required' => false
                ])
                ->add(
                    'imageFile',
                    FileType::class,
                    [
                        'required' => true,
                    ]
                )
                ->add('images', CollectionType::class, [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => true,
                    'label'=> false
                ])
                ->add('save', SubmitType::class, ['label' => 'Publicar su trabajo']);
            ;
        }
        
        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver->setDefaults(
                [
                    'data_class' => Anouncement::class,
                ]
            );
        }
    }
