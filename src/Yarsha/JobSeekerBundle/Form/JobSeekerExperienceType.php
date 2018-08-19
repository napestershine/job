<?php

namespace Yarsha\JobSeekerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\JobsBundle\Entity\JobLevel;
use Yarsha\JobSeekerBundle\Entity\User;
use Yarsha\MainBundle\Entity\Country;
use Yarsha\MainBundle\Entity\Category;

class JobSeekerExperienceType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('organizationName', null, [
            'label' => 'Organization Name',
            'attr' => [
                'placeholder' => 'Organization Name'

            ]
        ])
            ->add('employmentType', ChoiceType::class, [
                'label' => 'Employment Type',
                'choices' => [
                    'Full Time' => 'full',
                    'Part Time' => 'part',
                    'Contract' => 'contract'
                ]
            ])
            ->add('designation', null, [
                'attr' => [
                    'placeholder' => 'Designation'

                ]
            ])
            ->add('fromYear', ChoiceType::class, [
                'label' => 'From',
                'required' => true,
                'placeholder' => 'Year',
                'choices' => range(date('Y') - 40, date('Y')),
                'choice_label' => function ($value, $key, $index) {
                    return $value;
                }
            ])
            ->add('fromMonth', ChoiceType::class, [
                'label' => '',
                'required' => true,
                'choices' => [
                    'Jan' => 'Jan',
                    'Feb' => 'Feb',
                    'Mar' => 'Mar',
                    'Apr' => 'Apr',
                    'May' => 'May',
                    'Jun' => 'Jun',
                    'Jul' => 'Jul',
                    'Aug' => 'Aug',
                    'Sep' => 'Sep',
                    'Oct' => 'Oct',
                    'Nov' => 'Nov',
                    'Dec' => 'Dec'
                ],
                'placeholder' => 'Month'
            ])
            ->add('fromDay', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Day',
                'choices' => range(1, 31),
                'choice_label' => function ($value, $key, $index) {
                    return $value;
                }
            ])
            ->add('toYear', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Year',
                'choices' => range(date('Y') - 40, date('Y')),
                'choice_label' => function ($value, $key, $index) {
                    return $value;
                }
            ])
            ->add('toMonth', ChoiceType::class, [
                'choices' => [
                    'Jan' => 'Jan',
                    'Feb' => 'Feb',
                    'Mar' => 'Mar',
                    'Apr' => 'Apr',
                    'May' => 'May',
                    'Jun' => 'Jun',
                    'Jul' => 'Jul',
                    'Aug' => 'Aug',
                    'Sep' => 'Sep',
                    'Oct' => 'Oct',
                    'Nov' => 'Nov',
                    'Dec' => 'Dec'
                ],
                'placeholder' => 'Month',
                'required' => false
            ])
            ->add('toDay', ChoiceType::class, [
                'required' => false,
                'placeholder' => 'Day',
                'choices' => range(1, 31),
                'choice_label' => function ($value, $key, $index) {
                    return $value;
                }
            ])
            ->add('roles', CKEditorType::class, [
                'label' => 'Duties and Responsibilities',
                'config_name' => 'simple_editor'
            ])
            ->add('organizationType', EntityType::class, [
                'label' => 'JOb by Function',
                'class' => Category::class,
                'placeholder' => 'Organization Type',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.deleted = 0')
                        ->andWhere('c.section = :section')
                        ->setParameter('section', Category::CATEGORY_TYPE_JOB_BY_FUNCTION);
                }
            ])
            ->add('jobLevel', EntityType::class, [
                'class' => JobLevel::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('j')
                        ->where('j.deleted = 0');
                }
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) {
                    $er->findBy([
                        'deleted' => 0
                    ]);
                }
            ])
            ->add('currentlyWorking', null, [
                'label' => 'Currently Working',
                'required' => false,
                'attr' => [
                    'class' => 'has-experience-checkbox'

                ]
            ]);
//            ->add('jobSeeker', EntityType::class, [
//                'class' => User::class
//            ])
//            ->add('delete', ButtonType::class, ['attr' => ['class' => 'delete-experience']]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobSeekerBundle\Entity\JobSeekerExperience'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobseekerbundle_jobseekerexperience';
    }


}
