<?php

namespace Yarsha\AdminBundle\Form;

use Sonata\AdminBundle\Tests\Form\Type\ModelHiddenTypeTest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class SeekerCallRecordType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'placeholder' => 'Phone Caller Name'
                ]

            ])
            ->add('calledDate', DateType::class, [
                'required' => true,
                'label' => 'Called Date',
                'years' => range(2010, 2050),
                'placeholder' => [
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day'
                ]
            ])
            ->add('followUpDate', DateType::class, [
                'required' => false,
                'label' => 'Follow Up Date',
                'years' => range(2010, 2050),
                'placeholder' => [
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day'
                ]
            ])
            ->add('remark', TextareaType::class)
            ->add('seeker');
//            ->add('feedback', CKEditorType::class, [
//                'attr' => [
//                    'class' => 'form-control'
//                ]
//            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobSeekerBundle\Entity\JobSeekerCallRecord'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_adminbundle_JobSeekerCallRecord';
    }


}
