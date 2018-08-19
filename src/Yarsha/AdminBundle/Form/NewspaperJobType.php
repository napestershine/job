<?php

namespace Yarsha\AdminBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\JobsBundle\Form\JobSettingType;
use Yarsha\MainBundle\Entity\Currency;
use Yarsha\MainBundle\MainBundleConstants;
use Yarsha\OrganizationBundle\Entity\Organization;

class NewspaperJobType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organization', EntityType::class, [
                'class' => Organization::class,
                'choice_label' => 'name',
                'placeholder' => '--select organization--',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('o')
                        ->where('o.isNewspaperOrganization = 1')
                        ;
                }
            ])
            ->add('title', null, [
                'attr' => [
                    'placeholder' => 'Title'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'free' => 'free',
                    'featured' => 'featured',
                    'hot' => 'hot',
                    'newspaper' => 'newspaper'
                ]
            ])
            ->add('availability', ChoiceType::class, [
                'choices' => [
                    'full' => 'full',
                    'part' => 'full',
                    'contract' => 'contract'
                ]
            ])
            ->add('minimumExperienceYear')
            ->add('maximumExperienceYear')
            ->add('numberOfVacancies', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'No Of Vacancies'
                ]
            ])
            ->add('vacancyCode')
            ->add('deadline')
            ->add('description')
            ->add('specification')
            ->add('educationDescription')
            ->add('salaryNegotiable')
            ->add('minimumSalary')
            ->add('maximumSalary')
            ->add('preferredGender', ChoiceType::class, [
                'required' => false,
                'label' => 'Preferred Gender',
                'placeholder' => '-- Any --',
                'choices' => array_flip(MainBundleConstants::$genderDesc)
            ])
            ->add('minimumAge')
            ->add('maximumAge')
            ->add('specificRequirement')
            ->add('specificInstruction')
            ->add('category', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\Category',
                'placeholder' => '-- Select Category --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                }
            ])
            ->add('level', EntityType::class, [
                'class' => 'Yarsha\JobsBundle\Entity\JobLevel',
                'placeholder' => '--Select Level',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l');
                }
            ])
            ->add('industry')
            ->add('locations', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\Location',
                'multiple' => true,
            ])
            ->add('educationDegree', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\EducationDegree',
                'required' => false,
                'placeholder' => '--Select Education --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e');
                }
            ])
            ->add('salaryUnit', EntityType::class, [
                'class' => Currency::class,
                'required' => false,
                'placeholder' => '--Select Salary Unit',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s');
                }
            ])
            ->add('settings', JobSettingType::class)
            ->add('file');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobsBundle\Entity\Job'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_newspaper_jobs';
    }


}
