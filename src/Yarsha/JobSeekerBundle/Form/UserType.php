<?php

namespace Yarsha\JobSeekerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('middleName')
            ->add('lastName')
            ->add('currentAddress')
            ->add('permanentAddress')
            ->add('dob')
            ->add('gender')
            ->add('nationality')
            ->add('salutation')
            ->add('fatherName')
            ->add('motherName')
            ->add('maritalStatus')
            ->add('religion')
            ->add('mobile')
            ->add('phone')
            ->add('officePhone')
            ->add('availableFor')
            ->add('expectedSalary')
            ->add('presentSalary')
            ->add('hasExperience')
            ->add('noOfYear')
            ->add('noOfMonth')
            ->add('path')
            ->add('curriculumVitaePath')
            ->add('profileCompleted')
            ->add('profileCompletedPercentage')
            ->add('contactEmail')
            ->add('preferredLocations')
            ->add('preferredCategories')
            ->add('preferredIndustries')
            ->add('preferredPosition')
            ->add('degree');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobSeekerBundle\Entity\User'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobseekerbundle_user';
    }


}
