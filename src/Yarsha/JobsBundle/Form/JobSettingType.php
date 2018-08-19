<?php

namespace Yarsha\JobsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobSettingType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('applyOnline', CheckboxType::class, [
            'required' => false,
            'attr' => [
                'data-class' => 0,
                'class' => 'apply-job-online'
            ]
        ])
            ->add('applyEmail', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-status' => 0,
                    'class' => 'apply-job-email',
                ]
            ])
            ->add('uploadDocument', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-status' => 0,
                    'class' => 'uploadDoc',
                ]
            ])
            ->add('applyPost', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'data-status' => 0,
                    'class' => 'apply-job-post',
                ]
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Yarsha\JobsBundle\Entity\JobSetting'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'yarsha_jobsbundle_jobsetting';
    }


}
