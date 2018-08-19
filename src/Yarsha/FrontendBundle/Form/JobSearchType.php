<?php

namespace Yarsha\FrontendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\JobsBundle\Entity\Job;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class JobSearchType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('category', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\Category',
                'required' => false,
                'placeholder' => '-- Select Category --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                }
            ])
            ->add('organization', EntityType::class, [
                'class' => Organization::class,
                'required' => false,
                'label' => 'Organization',
                'placeholder' => '-- Select Organization --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('o')
                        ->where('o.status = 201');

                }
            ])
            ->add('level', EntityType::class, [
                'class' => JobLevel::class,
                'required' => false,
                'label' => 'Level',
                'placeholder' => 'Level',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l');
                }
            ])
            ->add('educationDegree', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\EducationDegree',
                'required' => false,
                'placeholder' => '--Select Education --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e');
                }
            ])
            ->add('locations', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\Location',
                'required' => false,
                'placeholder' => '--Select Location --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l');
                }
            ])
            ->add('type', ChoiceType::class, [
                'placeholder' => '----',
                'required' => false,
                'choices' => [
                    'free' => JobConstants::JOBS_TYPE_FREE,
                    'featured' => JobConstants::JOBS_TYPE_FEATURED,
                    'hot' => JobConstants::JOBS_TYPE_HOT
                ]
            ])
            ->add('availability', ChoiceType::class, [
                'placeholder' => '----',
                'required' => false,
                'choices' => [
                    'full' => JobConstants::JOBS_AVAILABILITY_FULL_TIME,
                    'part' => JobConstants::JOBS_AVAILABILITY_PART_TIME,
                    'contract' => JobConstants::JOBS_AVAILABILITY_CONTRACT
                ]
            ])
            ->add('title', null, [
                'attr' => [
                    'placeholder' => 'Job Title',

                ],
                'required' => false
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => 'Yarsha\JobsBundle\Entity\Job'
        ]);
    }


}
