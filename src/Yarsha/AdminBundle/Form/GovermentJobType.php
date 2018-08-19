<?php

namespace Yarsha\AdminBundle\Form;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yarsha\JobsBundle\Form\JobSettingType;
use Yarsha\MainBundle\Entity\Currency;
use Yarsha\MainBundle\MainBundleConstants;
use Yarsha\OrganizationBundle\Entity\Organization;

class GovermentJobType extends AbstractType
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
                        ->where('o.isGovermentOrganization = 1')
                        ;
                }
            ])
            ->add('title', null, [
                'attr' => [
                    'placeholder' => 'Title'
                ]
            ])
            ->add('minimumExperienceYear', NumberType::class, [
                'required' => true
            ])
            ->add('maximumExperienceYear', NumberType::class, [
                'required' => true
            ])
            ->add('numberOfVacancies', IntegerType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'No Of Vacancies'
                ]
            ])
            ->add('vacancyCode')
            ->add('deadline', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'js-datepicker'
                ]
            ])
            ->add('description', CKEditorType::class, [
                'required' => true,
                'label' => 'Vacancy details'
            ])
            ->add('educationDegree', EntityType::class, [
                'class' => 'Yarsha\MainBundle\Entity\EducationDegree',
                'required' => true,
                'placeholder' => '--Select Education --',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e');
                }
            ])
            ->add('file', FileType::class, [
                'required' => false
            ]);
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
