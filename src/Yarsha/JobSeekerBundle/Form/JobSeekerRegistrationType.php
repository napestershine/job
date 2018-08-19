<?php

namespace Yarsha\JobSeekerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\JobsBundle\JobConstants;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\MainBundle\Entity\EducationDegree;
use Yarsha\MainBundle\Entity\Location;
use Yarsha\MainBundle\MainBundleConstants;


class JobSeekerRegistrationType extends AbstractType
{


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//
//        if ($options['registrationType'] == false) {
//            $builder
//                ->add('salutation', null, [
//                    'required' => false,
//                    'attr' => [
//                        'placeholder' => 'Salutation'
//                    ]
//                ]);
//        }

        $country = $options['country'];

        $builder
            ->add('firstName', null, [
                'label' => 'First Name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'First Name',
                    'pattern' => '.{2,20}',
                    'title' => '2 to 20 characters'
                ]
            ])
            ->add('middleName', null, [
                'label' => 'Middle Name',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Middle Name',
                    'pattern' => '.{2,20}',
                    'title' => '2 to 20 characters'
                ]
            ])
            ->add('lastName', null, [
                'label' => 'Last Name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Last Name',
                    'pattern' => '.{2,20}',
                    'title' => '2 to 20 characters'
                ]
            ])
            ->add('currentAddress', null, [
                'label' => 'Current address',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Current Address'
                ]
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'Contact Email',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Email',
                    'onkeyup' => 'checkContactEmail(this.value)'
                ]
            ])
//            ->add('fatherName', null, [
//                'required' => false,
//                'attr' => [
//                    'placeholder' => "Father's Name"
//                ]
//            ])
//            ->add('motherName', null, [
//                'required' => false,
//                'attr' => [
//                    'placeholder' => "Mother's Name"
//                ]
//            ])

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
            ->add('gender', ChoiceType::class, [
                'choices' => array_flip(MainBundleConstants::$genderDesc),
                'placeholder' => ' -- Select Gender -- ',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('mobile', null, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Mobile',
                    'pattern' => '.{8,15}',
                    'title' => '8 to 15 characters'
                ]
            ]);
//            ->add('officePhone', null, [
//                'required' => false,
//                'attr' => [
//                    'placeholder' => 'Office Number'
//                ]
//            ])

        if ($options['is_updating'] === false) {
            $builder
                ->add('username', null, [
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Username (Mobile/Email)',
                        'class' => 'form-usr-name',
                        'disabled' => true,
//                        'onkeyup' => 'checkUsername(this.value)'
                    ]
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'required' => true,
                    'invalid_message' => 'Password fields must match.',
                    'first_options' => [
                        'label' => 'Password',
                        'attr' => [
                            'placeholder' => 'Password',
                            'class' => 'pass'
                        ]
                    ],
                    'second_options' => [
                        'label' => 'Confirm Password',
                        'attr' => [
                            'placeholder' => 'Confirm Password',
                            'class' => 'con_pass'
                        ]
                    ]
                ]);
        }

//            ->add('availableFor', ChoiceType::class, [
//                'required' => true,
//                'choices' => array_flip(JobConstants::$jobsAvailabilityDesc)
//            ])
//            ->add('preferredPosition', EntityType::class, [
//                'multiple' => false,
//                'class' => JobLevel::class,
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('l');
//                }
//            ])
//            ->add('presentSalary', null, [
//                'label' => 'Present Salary',
//                'required' => false,
//                'attr' => [
//                    'placeholder' => 'Present Salary',
//                    'min' => 1
//                ]
//            ]);

        //cv upload
        //photo with cropping

        if ($options['registrationType'] == false or $options['admin'] == true) {
            $builder
                ->add('permanentAddress', null, [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Permanent Address'
                    ]
                ])
                ->add('nationality', null, [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Nationality'
                    ]
                ])
                ->add('religion', null, [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Religion'
                    ]
                ])
                ->add('maritalStatus', ChoiceType::class, [
                    'label' => 'Marital Status',
                    'required' => false,
                    'choices' => array_flip(MainBundleConstants::$maritalStatus)
                ])
                ->add('phone', null, [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Home Phone'
                    ]
                ]);
        }

        if ($options['registrationType'] == true) {
            $builder
                ->add('preferredIndustries', EntityType::class, [
                    'label' => 'Preferred Industries',
                    'class' => Category::class,
                    'multiple' => true,
                    'placeholder' => 'Preferred Industries',
                    'attr' => [
                        'class' => 'select2'
                    ],
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->where('c.deleted = 0')
                            ->andWhere('c.section = :section')
                            ->setParameter('section', Category::CATEGORY_TYPE_JOB_BY_INDUSTRY);
                    }
                ])
                ->add('preferredCategories', EntityType::class, [
                    'label' => 'Preferred Categories',
                    'class' => Category::class,
                    'multiple' => true,
                    'placeholder' => 'Preferred Categories',
                    'attr' => [
                        'class' => 'select2'
                    ],
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->where('c.deleted = 0')
                            ->andWhere('c.section = :section')
                            ->setParameter('section', Category::CATEGORY_TYPE_JOB_BY_FUNCTION);
                    }
                ])
                ->add('preferredLocations', EntityType::class, [
                    'label' => 'Preferred Locations',
                    'required' => false,
                    'class' => Location::class,
                    'multiple' => true,
                    'placeholder' => 'Preferred Locations',
                    'attr' => [
                        'class' => 'select2'
                    ],
                    'query_builder' => function (EntityRepository $er) use ($country) {
                        $qb = $er->createQueryBuilder('l')
                            ->where('l.deleted = 0');

                        if ($country and is_numeric($country)) {
                            $qb->andWhere('l.country = ' . $country);
                        }

                        $qb->orderBy('l.name', 'ASC');

                        return $qb;
                    }
                ])
                ->add('contactEmail', EmailType::class, [
                    'label' => 'Contact Email',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Email',
                        'class' => 'cont_email'
                    ]
                ])
                ->add('hasExperience', null, [
                    'label' => 'Do you have experience?',
                    'required' => false,
                    'attr' => [
                        'class' => 'has-experience-checkbox',
                        'onclick' => 'toggleExperienceBlock()'
                    ]
                ])
                ->add('noOfYear', null, [
                    'label' => 'Number of Year',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Year of Experience',
                        'min' => 1
                    ]
                ])
                ->add('noOfMonth', null, [
                    'label' => 'Number of Month',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Month of Experience',
                        'min' => 1,
                        'max' => 11
                    ]
                ])
                ->add('degree', EntityType::class, [
                    'class' => EducationDegree::class,
                    'label' => 'Education',
                    'required' => true,
                    'placeholder' => '-- Select Education -- ',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('e')
                            ->where('e.deleted = 0')
                            ->orWhere('e.deleted is null')
                            ->orderBy('e.sortOrder', 'ASC');
                    }
                ])
                ->add('minExpectedSalary', null, [
                    'label' => 'Expected Salary',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Min Expected Salary',
                        'min' => 1
                    ]

                ])
                ->add('maxExpectedSalary', null, [
                    'label' => 'Expected Salary',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Max Expected Salary',
                        'min' => 1
                    ]

                ])
                ->add('curriculumVitaeFile', FileType::class, [
                    'label' => 'Curriculum vitae file (pdf / word document):',
                    'required' => false,
                    'attr' => [
                        'class' => 'btn btn-default btn-file image-upload',
                        'onchange' => 'previewFile();',
                        'data-provides' => 'fileinput',
                    ]
                ]);


        }

        if ($options['admin'] == true) {
            $builder
//                ->add('username', TextType::class, [
//                    'required' => true,
//                    'attr' => [
//                        'placeholder' => 'Username (Mobile/Email)',
//                        'class' => 'form-usr-name',
//                        'disabled' => 'disabled'
//                    ]
//                ])
//                ->add('password', RepeatedType::class, [
//                    'type' => PasswordType::class,
//                    'required' => true,
//                    'invalid_message' => 'Password fields must match.',
//                    'first_options' => [
//                        'label' => 'Password',
//                        'attr' => [
//                            'placeholder' => 'Password',
//                            'class' => 'pass'
//                        ]
//                    ],
//                    'second_options' => [
//                        'label' => 'Confirm Password',
//                        'attr' => [
//                            'placeholder' => 'Confirm Password',
//                            'class' => 'con_pass'
//                        ]
//                    ]
//                ])
                ->add('file', FileType::class, [
                    'label' => 'Profile Picture',
                    'required' => false,
                    'attr' => [
                        'class' => 'btn btn-default btn-file image-upload',
                        'onchange' => 'previewFile();',
                        'data-provides' => 'fileinput',
                    ]
                ])
                ->add('objectives', TextareaType::class, [
                    'label' => 'Career Objectives',
                    'required' => false,
                    'attr' => [
                        'class' => 'form form-control'
                    ]
                ])
            ->add('isSearchable');
        }

    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobSeekerBundle\Entity\User',
            'is_updating' => false,
            'admin' => false,
            'registrationType' => true,
            'country' => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fos_user_yarsha_jobseekerbundle_user';
    }

    public function getName()
    {
        return 'fos_user_yarsha_job_seeker_registration';
    }


}
