<?php
	/**
	 * Created by PhpStorm.
	 * User: FonsecaGay
	 * Date: 01/04/2019
	 * Time: 19:21
	 */
	
	namespace App\Form;
	
	
	use App\Entity\Anouncement;
	use App\Entity\Image;
	use App\Entity\Job;
	use App\Entity\Service;
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
						'class' => Service::class,
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
				->add('city', null, ['attr' => ['id' => 'city']])
				->add('state')
				->add('postalCode', null, ['attr' => ['id' => 'postal_code']])
				->add(
					'country',
					null,
					[
						'attr' => ['id' => 'country'],
					]
				)
				->add('videoLink')
				->add(
					'imageFile',
					FileType::class,
					[
						'required' => true,
					]
				)
				->add('images',CollectionType::class,[
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