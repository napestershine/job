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

class JobSeekerGeneralInformation extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('objectives', TextareaType::class, [
                'label' => 'Career Objectives',
                'attr' => [
                    'placeholder' => 'Career Objectives',
                ]
            ])
            ->add('available_for', ChoiceType::class, [
                'label' => 'Available For',
                'choices' => [
                    'Part Time' => 'part',
                    'Full Time' => 'full',
                    'Contract' => 'contract',
                ]
            ])
            ->add('minExpectedSalary', null, [
                'label' => 'Min Expected Salary',
            ])
            ->add('maxExpectedSalary', null, [
                'label' => 'Max Expected salary',
            ])
            ->add('no_of_year', null, [
                'label' => 'Total No of Experience (Year)',
            ])
            ->add('no_of_month', null, [
                'label' => 'Month',
            ])
            ->add('preferredLocations', EntityType::class, [
                'label' => 'Preferred Locations',
                'class' => Location::class,
                'multiple' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.deleted = 0')
                        ->orderBy('l.name', 'asc');
                }
            ])
            ->add('preferredCategories', EntityType::class, [
                'label' => 'Preferred Categories',
                'class' => Category::class,
                'multiple' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.deleted = 0')
                        ->andWhere('c.section = :sectionType')->setParameter('sectionType', 'Jobs By Function');
                }
            ])
            ->add('preferredIndustries', EntityType::class, [
                'label' => 'Preferred Industries',
                'class' => Category::class,
                'multiple' => true,
                'attr' => [
                    'class' => 'select2'
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.deleted = 0')
                        ->andWhere('c.section = :sectionType')->setParameter('sectionType', 'Jobs By Industry');
                }
            ])
            ->add('preferredPosition', EntityType::class, [
                'label' => 'Preferred Job Level',
                'class' => JobLevel::class,
                'placeholder' => '--Select Job Level',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->where('l.deleted = 0')
                        ->orderBy('l.sortOrder', 'asc');
                }
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
