<?php

namespace Yarsha\EmployerBundle\Form;

use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\MainBundle\Entity\Category;
use Yarsha\OrganizationBundle\Entity\Organization;
use Yarsha\OrganizationBundle\Entity\OrganizationSize;
use Yarsha\OrganizationBundle\Entity\OrganizationType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Yarsha\OrganizationBundle\Entity\OrganizationOwnership;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EmployerRegisterOrganizationType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Organization Name',
                ]
            ])
//            ->add('industry', EntityType::class, [
//                'label' => 'Industry',
//                'class' => Category::class,
//                'choice_label' => 'title',
//                'placeholder' => '-- Select Industry --',
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('c')
//                        ->where('c.published = 1')
//                        ->andWhere('c.deleted = 0')
//                        ->andWhere('c.section = :section')
//                        ->setParameter('section', Category::CATEGORY_TYPE_JOB_BY_INDUSTRY);
//                }
//            ])
//            ->add('ownershipType', EntityType::class, [
//                'label' => 'Ownership',
//                'required' => false,
//                'class' => OrganizationOwnership::class,
//                'choice_label' => 'name',
//                'placeholder' => '-- Select Ownership --',
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('o')
//                        ->where('o.status = 1');
//                }
//            ])
            ->add('profile', TextareaType::class, [
                'label' => 'Company Profile',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Profile: Let others know about your company.',
                    'onkeyup' => 'checkWordLength(this)'
                ]
            ])
            ->add('phone', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Phone',
                    'pattern' => "[0-9,]{5,20}",
                    'title' => 'Organization phone 5 to 20 characters'
                ]
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'placeholder' => 'Address',
                    'pattern' => ".{2,100}",
                    'title' => 'Organization name 2 to 100 characters'
                ]
            ])
            ->add('website', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Website',
                    'class' => 'form-usr-name'
                ]
            ]);

        if ($options['admin'] == true) {
            $builder
//                ->add('nature', TextType::class, [
//                    'required' => false,
//                    'attr' => [
//                        'placeholder' => 'Organization Nature'
//                    ]
//                ])
                ->add('category', EntityType::class, [
                    'label' => 'Category',
                    'class' => Category::class,
                    'choice_label' => 'title',
                    'placeholder' => '-- Select Category --',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->where('c.published = 1')
                            ->andWhere('c.deleted = 0')
                            ->andWhere('c.section = :section')
                            ->setParameter('section', Category::CATEGORY_TYPE_JOB_BY_FUNCTION);
                    }
                ])
//                ->add('size', EntityType::class, [
//                    'label' => 'Organization Size',
//                    'required' => false,
//                    'class' => OrganizationSize::class,
//                    'choice_label' => 'name',
//                    'placeholder' => '-- Select Organization Size --',
//                    'query_builder' => function (EntityRepository $er) {
//                        return $er->createQueryBuilder('s')
//                            ->where('s.status = 1');
//                    }
//                ])
                ->add('file', FileType::class, [
                    'label' => 'Company Logo',
                    'required' => false,
                    'attr' => [
                        'class' => 'btn btn-default btn-file image-upload',
                        'onchange' => 'previewFile();',
                        'data-provides' => 'fileinput',
                    ]
                ])
//                ->add('type', EntityType::class, [
//                    'label' => 'Type',
//                    'required' => false,
//                    'class' => OrganizationType::class,
//                    'choice_label' => 'name',
//                    'placeholder' => '-- Select Type --',
//                    'query_builder' => function (EntityRepository $er) {
//                        return $er->createQueryBuilder('t')
//                            ->where('t.status = 1');
//                    }
//                ])
//                ->add('fax', TextType::class, [
//                    'required' => false
//                ])
//                ->add('postBox', TextType::class, [
//                    'required' => false
//                ])
                ->add('categoryType', ChoiceType::class, [
                    'label' => 'Employer Type',
                    'required' => false,
                    'choices' => array_flip(Organization::$organizationCategoryTypes)
                ]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\OrganizationBundle\Entity\Organization',
            'admin' => false
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_organizationbundle_organization_register';
    }


}
