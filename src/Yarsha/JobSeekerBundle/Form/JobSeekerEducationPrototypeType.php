<?php

namespace Yarsha\JobSeekerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class JobSeekerEducationPrototypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('educations', CollectionType::class, [
            'required' => true,
            'label' => false,
            'entry_type' => JobSeekerEducationType::class,
            'entry_options' => [
                'label' => false
            ],
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
        ]);
    }



    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Yarsha\JobSeekerBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobseekerbundle_user';
    }


}
