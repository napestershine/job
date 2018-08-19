<?php

namespace Yarsha\JobSeekerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\MainBundle\Entity\Location;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class JobSeekerProfileType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('objectives', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Career Objectives',
                ]
            ])
            ->add('salutation')
            ->add('father_name')
            ->add('mother_name')
            ->add('current_address')
            ->add('permanent_address')
            ->add('dob', DateType::class, [
                'required' => true,
                'label' => 'Date Of Birth',
                'years' => range(1950, 2010),
                'placeholder' => [
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day'
                ]
            ])
            ->add('marital_status', ChoiceType::class, [
                'choices' => [
                    'SINGLE' => 'SINGLE',
                    'MARRIED' => 'MARRIED',
                    'DIVORCED' => 'DIVORCED'
                ]
            ])
            ->add('nationality')
            ->add('religion')
            ->add('mobile')
            ->add('phone')
            ->add('office_phone')
            ->add('available_for', ChoiceType::class, [
                'choices' => [
                    'Part Time' => 'part',
                    'Full Time' => 'full',
                    'Contract' => 'contract',
                ]
            ])
            ->add('expected_salary')
            ->add('present_salary')
            ->add('has_experience')
            ->add('no_of_year')
            ->add('no_of_month')
            ->add('preferredLocations', EntityType::class, [
                'class' => Location::class,
                'multiple' => true,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('preferredCategories', EntityType::class, [
                'class' => Category::class,
                'multiple' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.section = :sectionType')->setParameter('sectionType', 'Jobs By Function');
                }
            ])
            ->add('preferredIndustries', EntityType::class, [
                'class' => Category::class,
                'multiple' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.section = :sectionType')->setParameter('sectionType', 'Jobs By Industry');
                }
            ])
            ->add('preferredPosition', EntityType::class, [
                'class' => JobLevel::class,
                'placeholder' => '--Select Job Level',
                'required' => false
            ]);
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
