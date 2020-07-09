<?php
	/**
	 * Created by PhpStorm.
	 * User: Ubel
	 * Date: 27/04/2019
	 * Time: 1:11
	 */
	
	namespace App\Form;
	
	
	use App\Entity\Image;
	use Symfony\Component\Form\AbstractType;
	use Symfony\Component\Form\Extension\Core\Type\FileType;
	use Symfony\Component\Form\FormBuilderInterface;
	use Symfony\Component\OptionsResolver\OptionsResolver;
	
	class ImageType extends AbstractType
	{
		public function buildForm(FormBuilderInterface $builder, array $options)
		{
			$builder
				->add(
					'imageFile',FileType::class,[
						'multiple' => false,
						'required'=>false,
						'attr' => [
							'accept' => 'image/*',
                            'class' => 'file-to-upload'
						],
                        'label' => false
					]
				);
		}
		
		public function configureOptions(OptionsResolver $resolver)
		{
			$resolver->setDefaults(
				[
					'data_class' => Image::class
				]
			);
		}
	}