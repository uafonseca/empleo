<?php

namespace App\Form;

use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Job1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('localtion')
            ->add('type')
            ->add('experience')
            ->add('salary_max')
            ->add('salary_min')
            ->add('qualification')
            ->add('date')
            ->add('dateCreated')
            ->add('description')
            ->add('responsabilities')
            ->add('education')
            ->add('others')
            ->add('country')
            ->add('city')
            ->add('zip_code')
            ->add('your_localtion')
            ->add('company_name')
            ->add('web_address')
            ->add('campany_profile')
            ->add('image')
            ->add('gender')
            ->add('status')
            ->add('expiredDate')
            ->add('applications')
            ->add('video_link')
            ->add('images')
            ->add('category')
            ->add('user')
            ->add('users')
            ->add('service')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
