<?php

namespace Yarsha\JobsBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Yarsha\JobsBundle\Entity\Job;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\MainBundle\Entity\Currency;
use Yarsha\MainBundle\MainBundleConstants;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Yarsha\OrganizationBundle\OrganizationConstants;

class JobType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $jobTypes = JobConstants::$jobsTypeDesc;
        if ($options['show_organization'] == false) {
            unset($jobTypes[JobConstants::JOBS_TYPE_NEWSPAPER]);
        }
        $builder
            ->add('title', null, ['attr' => ['placeholder' => 'Title']])
            ->add('type', ChoiceType::class, ['choices' => array_flip($jobTypes)])
            ->add('availability', ChoiceType::class, ['choices' => array_flip(JobConstants::$jobsAvailabilityDesc)])
            ->add('minimumExperienceYear', null, [
                'label' => 'Preferred Year of Experience (Min)'
            ])
            ->add('maximumExperienceYear', null, [
                'label' => 'Preferred Year of Experience (Max)'
            ])
            ->add('numberOfVacancies', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'No Of Vacancies'
                ]
            ])
            ->add('vacancyCode', null, [
                'label' => 'Vacancy Code',
                'attr' => [
                    'placeholder' => 'KJOB/01/17'
                ]
            ])
            ->add('deadline', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker'
                ]
            ])
            ->add('preferredGender', ChoiceType::class, [
                'required' => false,
                'label' => 'Preferred Gender',
                'placeholder' => ' Any ',
                'choices' => [
                    'Male Only' => MainBundleConstants::GENDER_MALE,
                    'Female Only' => MainBundleConstants::GENDER_FEMALE,
                ]
            ])
            ->add('minimumAge', null, [
                'label' => 'Preferred Age (Min)'
            ])
            ->add('maximumAge', null, [
                'label' => 'Preferred Age (Max)'
            ])
            ->add('category', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\Category',
                'placeholder' => '-- Select Category --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.deleted = 0')
                        ->andWhere('c.section = :section')
                        ->setParameter('section', Category::CATEGORY_TYPE_JOB_BY_FUNCTION);
                }
            ])
            ->add('description', CKEditorType::class, [
                'required' => true,
                'label' => 'Job Description',
//                'config_name' => 'simple_editor',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('specification', CKEditorType::class, [
                'required' => false,
//                'config_name' => 'simple_editor',
                'label' => 'Job Specification',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('specificRequirement', CKEditorType::class, [
                'required' => false,
                'label' => 'Other Specific Requirements',
//                'config_name' => 'simple_editor',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('educationDescription', CKEditorType::class, [
                'required' => false,
//                'config_name' => 'simple_editor',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('salaryType', ChoiceType::class, [
                'required' => true,
                'label' => 'Offered Salary Type',
//                'placeholder' => 'Choose Salary Type',
                'choices' => [
                    'Fixed' => Job::JOBS_SALARY_TYPE_FIXED,
                    'Negotiable' => Job::JOBS_SALARY_TYPE_NEGOTIABLE,
                    'Range' => Job::JOBS_SALARY_TYPE_RANGE
                ],
                'attr' => [
                    'class' => 'jobs-salary-type',
                    'id' => 'jobsSalaryType',
//                    'onchange' => 'showSalaryFields(this)'
                ]
            ])
            ->add('salary', NumberType::class, [
                'required' => false
            ])
            ->add('salaryPaymentBasis', ChoiceType::class, [
                'required' => false,
                'label' => 'Payment Basis',
                'choices' => [
                    'Per Year' => JobConstants::JOBS_SALARY_PAYMENT_BASIS_YEAR,
                    'Per Month' => JobConstants::JOBS_SALARY_PAYMENT_BASIS_MONTH,
                    'Per Week' => JobConstants::JOBS_SALARY_PAYMENT_BASIS_WEEK,
                    'Per Day' => JobConstants::JOBS_SALARY_PAYMENT_BASIS_DAY,
                    'Per Hour' => JobConstants::JOBS_SALARY_PAYMENT_BASIS_HOUR
                ],
                'data' => JobConstants::JOBS_SALARY_PAYMENT_BASIS_MONTH
            ])
            ->add('minimumSalary', NumberType::class, [
                'required' => false
            ])
            ->add('maximumSalary', NumberType::class, [
                'required' => false
            ])
//            ->add('level', EntityType::class, [
//                'class' => 'Yarsha\JobsBundle\Entity\JobLevel',
//                'placeholder' => '--Select Level',
//                'required' => false,
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('l')
//                        ->where('l.deleted = 0');
//                }
//            ])
            ->add('locations', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\Location',
                'multiple' => true,
                'placeholder' => 'Select locations',
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('educationDegree', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\EducationDegree',
                'required' => false,
                'placeholder' => '--Select Education --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.deleted = 0')
                        ->orWhere('e.deleted is null');
                }
            ])
            ->add('settings', JobSettingType::class, [
                'required' => false
            ])
            ->add('onlineLink', TextType::class, [
                'required' => false
            ])
            ->add('postalAddress', TextType::class, [
                'required' => false
            ]);


        if ($options['show_organization'] == true) {
            $builder->add('organization', EntityType::class, [
                'class' => 'Yarsha\OrganizationBundle\Entity\Organization',
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    $er->createQueryBuilder('o')
                        ->where('o.status = :status')->setParameter('status',
                            OrganizationConstants::ORGANIZATION_STATUS_APPROVED)
                        ->orWhere('o.isGovermentOrganization != 1')
                        ->orWhere('o.isNewspaperOrganization != 1')
                        ->andWhere('o.type = :type')->setParameter('type',
                            OrganizationConstants::ORGANIZATION_TYPE_EMPLOYER);
                }
            ])
                ->add('file', null, [
                    'required' => false,
                    'label' => 'Upload Document(pdf/doc/docx)'
                ]);
        }

//        $builder
//            ->add('industry')
//            ->add('salaryUnit', EntityType::class, [
//                'class' => Currency::class,
//                'required' => false,
//                'placeholder' => '--Select Salary Unit',
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('s');
//                }
//            ])
//            ->add('specificInstruction', CKEditorType::class, [
//                'config_name' => 'simple_editor',
//                'attr' => [
//                    'class' => 'form-control'
//                ]
//            ])
//        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobsBundle\Entity\Job',
            'show_organization' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobsbundle_job';
    }


}
